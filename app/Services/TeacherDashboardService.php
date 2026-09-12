<?php

namespace App\Services;

use App\Models\DailyProgress;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class TeacherDashboardService
{
    /**
     * Retrieve optimized daily dashboard statistics and scheduled student lists for a teacher.
     *
     * @return array{
     *     hasSchedulesToday: bool,
     *     todaySchedules: Collection<int, Schedule>,
     *     students: Collection<int, Student>,
     *     loggedToday: list<int>,
     *     pendingStudents: Collection<int, Student>,
     *     pendingByGradeLevel: Collection<string, Collection<int, Student>>,
     *     todayStudentsCount: int,
     *     completedCount: int,
     *     pendingCount: int,
     *     weeklyPlanReminder: mixed
     * }
     */
    public function getDailyDashboardData(?Teacher $teacher, Carbon $date): array
    {
        if (!$teacher) {
            return $this->emptyState();
        }

        $dateString = $date->toDateString();

        // 1. Fetch active schedules for today (Single fast indexed query)
        $schedules = Schedule::query()
            ->with(['gradeLevel:id,name', 'subject:id,name,name_ar,color'])
            ->where('teacher_id', $teacher->id)
            ->where('day_of_week', $date->dayOfWeek)
            ->whereDoesntHave('exceptions', fn ($query) => $query->whereDate('date', $dateString))
            ->orderBy('start_time')
            ->get();

        if ($schedules->isEmpty()) {
            return $this->emptyState($teacher);
        }

        $scheduledGradeLevelIds = $schedules->pluck('grade_level_id')->unique()->values();

        // 2. Fetch only students in the scheduled grade levels for today (Single batch query)
        $students = Student::query()
            ->whereIn('grade_level_id', $scheduledGradeLevelIds)
            ->with('gradeLevel:id,name,track')
            ->orderBy('full_name')
            ->get();

        if ($students->isEmpty()) {
            return $this->emptyState($teacher, $schedules);
        }

        // 3. Fetch all daily progress records logged today for this teacher (Single batch query)
        $scheduleIds = $schedules->pluck('id')->values();
        $subjectIds = $schedules->pluck('subject_id')->unique()->values();

        $loggedProgress = DailyProgress::query()
            ->where('teacher_id', $teacher->id)
            ->whereDate('date', $dateString)
            ->where(function ($query) use ($scheduleIds, $subjectIds) {
                $query->whereIn('schedule_id', $scheduleIds)
                    ->orWhere(function ($subQuery) use ($subjectIds) {
                        $subQuery->whereNull('schedule_id')
                            ->whereIn('subject_id', $subjectIds);
                    });
            })
            ->get(['id', 'student_id', 'schedule_id', 'subject_id']);

        // 4. In-Memory matching: determine pending vs completed students for today's sessions
        $pendingStudents = $students->filter(function (Student $student) use ($schedules, $loggedProgress): bool {
            $studentSchedules = $schedules->where('grade_level_id', $student->grade_level_id);

            // If teacher has any lesson for this student's grade that hasn't been logged yet, student is pending
            return $studentSchedules->contains(function (Schedule $schedule) use ($student, $loggedProgress): bool {
                return !$loggedProgress->contains(function (DailyProgress $progress) use ($student, $schedule): bool {
                    return $progress->student_id === $student->id
                        && ($progress->schedule_id === $schedule->id || ($progress->schedule_id === null && $progress->subject_id === $schedule->subject_id));
                });
            });
        })->values();

        $completedStudents = $students->reject(fn (Student $student) => $pendingStudents->contains('id', $student->id))->values();
        $pendingByGradeLevel = $pendingStudents->groupBy(fn (Student $s) => $s->gradeLevel?->name ?? 'غير محدد');

        $weeklyPlanReminder = $teacher->user?->unreadNotifications()
            ->where('data->type', 'weekly_plan_reminder')
            ->latest()
            ->first();

        return [
            'hasSchedulesToday' => true,
            'todaySchedules' => $schedules,
            'students' => $students,
            'loggedToday' => $completedStudents->pluck('id')->all(),
            'pendingStudents' => $pendingStudents,
            'pendingByGradeLevel' => $pendingByGradeLevel,
            'todayStudentsCount' => $students->count(),
            'completedCount' => $completedStudents->count(),
            'pendingCount' => $pendingStudents->count(),
            'weeklyPlanReminder' => $weeklyPlanReminder,
        ];
    }

    /**
     * Return safe empty state when teacher has no schedules or data today.
     *
     * @return array<string, mixed>
     */
    private function emptyState(?Teacher $teacher = null, ?Collection $schedules = null): array
    {
        $weeklyPlanReminder = $teacher?->user?->unreadNotifications()
            ->where('data->type', 'weekly_plan_reminder')
            ->latest()
            ->first();

        return [
            'hasSchedulesToday' => ($schedules && $schedules->isNotEmpty()),
            'todaySchedules' => $schedules ?? collect(),
            'students' => collect(),
            'loggedToday' => [],
            'pendingStudents' => collect(),
            'pendingByGradeLevel' => collect(),
            'todayStudentsCount' => 0,
            'completedCount' => 0,
            'pendingCount' => 0,
            'weeklyPlanReminder' => $weeklyPlanReminder,
        ];
    }
}
