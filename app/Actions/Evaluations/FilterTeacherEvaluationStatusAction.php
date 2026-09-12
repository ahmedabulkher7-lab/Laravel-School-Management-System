<?php

namespace App\Actions\Evaluations;

use App\Models\DailyProgress;
use App\Models\GradeLevel;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class FilterTeacherEvaluationStatusAction
{
    public function execute(array $filters): array
    {
        $date = Carbon::parse($filters["date"] ?? now())->startOfDay();
        $track = $filters["track"] ?? null;
        $gradeLevelId = $filters["grade_level_id"] ?? null;
        $teacherId = $filters["teacher_id"] ?? null;
        $subjectId = $filters["subject_id"] ?? null;
        $studentId = $filters["student_id"] ?? null;
        $attendance = $filters["attendance"] ?? null;
        $interaction = $filters["interaction"] ?? null;
        $status = $filters["status"] ?? "all";

        $eligibleGradeLevels = GradeLevel::with("subjects:id")
            ->when($track, fn ($query) => $query->where("track", $track))
            ->when($gradeLevelId, fn ($query) => $query->whereKey($gradeLevelId))
            ->orderBy("track")
            ->orderBy("order")
            ->get();
        $eligibleGradeLevelIds = $eligibleGradeLevels->pluck("id");

        $studentsByGradeLevel = Student::with("gradeLevel")
            ->whereIn("grade_level_id", $eligibleGradeLevelIds)
            ->when($studentId, fn ($query) => $query->whereKey($studentId))
            ->orderBy("full_name")
            ->get()
            ->groupBy("grade_level_id");

        $teachers = Teacher::with([
            "subjects:id,name,name_ar",
            "gradeLevels" => fn ($query) => $query
                ->whereIn("grade_levels.id", $eligibleGradeLevelIds)
                ->with("subjects:id"),
            "schedules" => fn ($query) => $query
                ->where("day_of_week", $date->dayOfWeek)
                ->when($gradeLevelId, fn ($schedule) => $schedule->where("grade_level_id", $gradeLevelId))
                ->when($subjectId, fn ($schedule) => $schedule->where("subject_id", $subjectId)),
        ])
            ->whereHas("schedules", fn ($query) => $query
                ->where("day_of_week", $date->dayOfWeek)
                ->whereIn("grade_level_id", $eligibleGradeLevelIds)
                ->when($subjectId, fn ($schedule) => $schedule->where("subject_id", $subjectId)))
            ->when($teacherId, fn ($query) => $query->whereKey($teacherId))
            ->when($subjectId, fn ($query) => $query->whereHas("subjects", fn ($subjects) => $subjects->whereKey($subjectId)))
            ->orderBy("full_name")
            ->get();

        $loggedStudentIdsByTeacher = DailyProgress::query()
            ->whereDate("date", $date)
            ->whereIn("teacher_id", $teachers->pluck("id"))
            ->when($subjectId, fn ($query) => $query->where("subject_id", $subjectId))
            ->when($studentId, fn ($query) => $query->where("student_id", $studentId))
            ->when($attendance, fn ($query) => $query->where("attendance_status", $attendance))
            ->when($interaction, fn ($query) => $query->where("interaction_level", $interaction))
            ->get(["teacher_id", "student_id"])
            ->groupBy("teacher_id")
            ->map(fn (Collection $records) => $records->pluck("student_id")->unique()->values());

        $teacherStatuses = $teachers->map(function (Teacher $teacher) use ($studentsByGradeLevel, $loggedStudentIdsByTeacher, $subjectId): array {
            $scheduledGradeLevelIds = $teacher->schedules->pluck("grade_level_id")->unique();
            $scheduledSubjectIds = $teacher->schedules->pluck("subject_id")->unique();
            $relevantGradeLevels = $teacher->gradeLevels
                ->filter(fn (GradeLevel $gradeLevel) => $scheduledGradeLevelIds->contains($gradeLevel->id));

            $assignedStudents = $relevantGradeLevels
                ->flatMap(fn (GradeLevel $gradeLevel) => $studentsByGradeLevel->get($gradeLevel->id, collect()))
                ->unique("id")
                ->sortBy("full_name")
                ->values();

            $evaluatedStudentIds = $loggedStudentIdsByTeacher->get($teacher->id, collect());
            [$evaluatedStudents, $pendingStudents] = $assignedStudents
                ->partition(fn (Student $student) => $evaluatedStudentIds->contains($student->id));

            $totalCount = $assignedStudents->count();
            $completedCount = $evaluatedStudents->count();
            $pendingCount = $pendingStudents->count();

            $completionStatus = "not_started";
            if ($totalCount > 0 && $completedCount === $totalCount) {
                $completionStatus = "complete";
            } elseif ($completedCount > 0) {
                $completionStatus = "started";
            }

            return [
                "teacher" => $teacher,
                "subjects" => $subjectId
                    ? $teacher->subjects->where("id", $subjectId)
                    : $teacher->subjects->whereIn("id", $scheduledSubjectIds),
                "grade_levels" => $relevantGradeLevels->values(),
                "assigned_students" => $assignedStudents,
                "logged_students" => $evaluatedStudents->values(),
                "remaining_students" => $pendingStudents->values(),
                "assigned_count" => $totalCount,
                "logged_count" => $completedCount,
                "remaining_count" => $pendingCount,
                "complete" => $totalCount > 0 && $pendingCount === 0,
                "total_students_count" => $totalCount,
                "completed_students_count" => $completedCount,
                "pending_students_count" => $pendingCount,
                "completion_percentage" => $totalCount > 0 ? (int) round(($completedCount / $totalCount) * 100) : 0,
                "completion_status" => $completionStatus,
                "evaluated_students" => $evaluatedStudents->values(),
            ];
        });

        if ($status !== "all") {
            $teacherStatuses = $teacherStatuses->filter(function (array $item) use ($status): bool {
                return match ($status) {
                    "complete" => $item["complete"],
                    "incomplete" => $item["assigned_count"] > 0 && !$item["complete"],
                    "started" => $item["logged_count"] > 0 && !$item["complete"],
                    "not_started" => $item["assigned_count"] > 0 && $item["logged_count"] === 0,
                    default => true,
                };
            })->values();
        }

        return [
            "date" => $date,
            "filters" => $filters,
            "teacherStatuses" => $teacherStatuses,
            "teachers" => Teacher::orderBy("full_name")->get(["id", "full_name"]),
            "gradeLevels" => GradeLevel::orderBy("track")->orderBy("order")->get(["id", "name", "track"]),
            "subjects" => Subject::orderBy("name")->get(["id", "name", "name_ar"]),
            "students" => Student::orderBy("full_name")->get(["id", "full_name", "grade_level_id"]),
            "summary" => [
                "teachers_count" => $teacherStatuses->count(),
                "total_students" => $teacherStatuses->sum("total_students_count"),
                "completed_students" => $teacherStatuses->sum("completed_students_count"),
                "pending_students" => $teacherStatuses->sum("pending_students_count"),
            ],
        ];
    }
}
