<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StudyTrack;
use App\Http\Controllers\Controller;
use App\Models\DailyProgress;
use App\Models\GradeLevel;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TeacherEvaluationStatusController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'date' => ['nullable', 'date'],
            'teacher_id' => ['nullable', 'integer', 'exists:teachers,id'],
            'grade_level_id' => ['nullable', 'integer', 'exists:grade_levels,id'],
            'track' => ['nullable', Rule::in(StudyTrack::values())],
            'subject_id' => ['nullable', 'integer', 'exists:subjects,id'],
            'student_id' => ['nullable', 'integer', 'exists:students,id'],
            'attendance' => ['nullable', Rule::in(['present', 'absent', 'late'])],
            'interaction' => ['nullable', Rule::in(['engaged', 'not_engaged'])],
            'status' => ['nullable', Rule::in(['all', 'complete', 'incomplete', 'started', 'not_started'])],
        ]);

        $date = Carbon::parse($filters['date'] ?? now())->startOfDay();
        $track = $filters['track'] ?? null;
        $gradeLevelId = $filters['grade_level_id'] ?? null;
        $teacherId = $filters['teacher_id'] ?? null;
        $subjectId = $filters['subject_id'] ?? null;
        $studentId = $filters['student_id'] ?? null;
        $attendance = $filters['attendance'] ?? null;
        $interaction = $filters['interaction'] ?? null;
        $status = $filters['status'] ?? 'all';

        $eligibleGradeLevels = GradeLevel::with('subjects:id')
            ->when($track, fn ($query) => $query->where('track', $track))
            ->when($gradeLevelId, fn ($query) => $query->whereKey($gradeLevelId))
            ->orderBy('track')
            ->orderBy('order')
            ->get();
        $eligibleGradeLevelIds = $eligibleGradeLevels->pluck('id');

        $studentsByGradeLevel = Student::with('gradeLevel')
            ->whereIn('grade_level_id', $eligibleGradeLevelIds)
            ->when($studentId, fn ($query) => $query->whereKey($studentId))
            ->orderBy('full_name')
            ->get()
            ->groupBy('grade_level_id');

        $teachers = Teacher::with([
            'subjects:id,name,name_ar',
            'gradeLevels' => fn ($query) => $query
                ->whereIn('grade_levels.id', $eligibleGradeLevelIds)
                ->with('subjects:id'),
            'schedules' => fn ($query) => $query
                ->where('day_of_week', $date->dayOfWeek)
                ->when($gradeLevelId, fn ($schedule) => $schedule->where('grade_level_id', $gradeLevelId))
                ->when($subjectId, fn ($schedule) => $schedule->where('subject_id', $subjectId)),
        ])
            ->whereHas('schedules', fn ($query) => $query
                ->where('day_of_week', $date->dayOfWeek)
                ->whereIn('grade_level_id', $eligibleGradeLevelIds)
                ->when($subjectId, fn ($schedule) => $schedule->where('subject_id', $subjectId)))
            ->when($teacherId, fn ($query) => $query->whereKey($teacherId))
            ->when($subjectId, fn ($query) => $query->whereHas('subjects', fn ($subjects) => $subjects->whereKey($subjectId)))
            ->orderBy('full_name')
            ->get();

        $loggedStudentIdsByTeacher = DailyProgress::query()
            ->whereDate('date', $date)
            ->whereIn('teacher_id', $teachers->pluck('id'))
            ->when($subjectId, fn ($query) => $query->where('subject_id', $subjectId))
            ->when($studentId, fn ($query) => $query->where('student_id', $studentId))
            ->when($attendance, fn ($query) => $query->where('attendance_status', $attendance))
            ->when($interaction, fn ($query) => $query->where('interaction_level', $interaction))
            ->get(['teacher_id', 'student_id'])
            ->groupBy('teacher_id')
            ->map(fn (Collection $records) => $records->pluck('student_id')->unique()->values());

        $teacherStatuses = $teachers->map(function (Teacher $teacher) use ($studentsByGradeLevel, $loggedStudentIdsByTeacher, $subjectId): array {
            $scheduledGradeLevelIds = $teacher->schedules->pluck('grade_level_id')->unique();
            $scheduledSubjectIds = $teacher->schedules->pluck('subject_id')->unique();
            $relevantGradeLevels = $teacher->gradeLevels
                ->filter(fn (GradeLevel $gradeLevel) => $scheduledGradeLevelIds->contains($gradeLevel->id));

            $assignedStudents = $relevantGradeLevels
                ->flatMap(fn (GradeLevel $gradeLevel) => $studentsByGradeLevel->get($gradeLevel->id, collect()))
                ->unique('id')
                ->sortBy('full_name')
                ->values();
            $loggedStudentIds = $loggedStudentIdsByTeacher->get($teacher->id, collect());
            $loggedStudents = $assignedStudents
                ->filter(fn (Student $student) => $loggedStudentIds->contains($student->id))
                ->values();
            $remainingStudents = $assignedStudents
                ->reject(fn (Student $student) => $loggedStudentIds->contains($student->id))
                ->values();

            return [
                'teacher' => $teacher,
                'grade_levels' => $relevantGradeLevels,
                'subjects' => $subjectId
                    ? $teacher->subjects->where('id', $subjectId)
                    : $teacher->subjects->whereIn('id', $scheduledSubjectIds),
                'assigned_students' => $assignedStudents,
                'logged_students' => $loggedStudents,
                'remaining_students' => $remainingStudents,
                'assigned_count' => $assignedStudents->count(),
                'logged_count' => $loggedStudents->count(),
                'remaining_count' => $remainingStudents->count(),
                'complete' => $assignedStudents->isNotEmpty() && $remainingStudents->isEmpty(),
            ];
        });

        $teacherStatuses = match ($status) {
            'complete' => $teacherStatuses->filter(fn (array $item) => $item['complete'])->values(),
            'incomplete' => $teacherStatuses->filter(fn (array $item) => $item['assigned_count'] > 0 && !$item['complete'])->values(),
            'started' => $teacherStatuses->filter(fn (array $item) => $item['logged_count'] > 0 && !$item['complete'])->values(),
            'not_started' => $teacherStatuses->filter(fn (array $item) => $item['assigned_count'] > 0 && $item['logged_count'] === 0)->values(),
            default => $teacherStatuses,
        };

        $overview = [
            'teachers' => $teacherStatuses->count(),
            'complete' => $teacherStatuses->where('complete', true)->count(),
            'incomplete' => $teacherStatuses->filter(fn (array $item) => $item['assigned_count'] > 0 && !$item['complete'])->count(),
            'remaining_students' => $teacherStatuses->sum('remaining_count'),
        ];

        $filterTeachers = Teacher::orderBy('full_name')->get(['id', 'full_name']);
        $filterGradeLevels = GradeLevel::orderBy('track')->orderBy('order')->get(['id', 'name', 'track']);
        $filterSubjects = Subject::orderBy('name')->get(['id', 'name', 'name_ar']);
        $filterStudents = Student::orderBy('full_name')->get(['id', 'full_name']);
        $tracks = StudyTrack::cases();

        return view('admin.teacher-evaluation-status.index', compact(
            'date',
            'track',
            'gradeLevelId',
            'teacherId',
            'subjectId',
            'studentId',
            'attendance',
            'interaction',
            'status',
            'teacherStatuses',
            'overview',
            'filterTeachers',
            'filterGradeLevels',
            'filterSubjects',
            'filterStudents',
            'tracks',
        ));
    }
}
