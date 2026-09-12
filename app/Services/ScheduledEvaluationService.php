<?php

namespace App\Services;

use App\Models\DailyProgress;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ScheduledEvaluationService
{
    public function lessonsForTeacher(Teacher $teacher, Carbon $date): Collection
    {
        return Schedule::query()
            ->with(['gradeLevel', 'subject'])
            ->where('teacher_id', $teacher->id)
            ->where('day_of_week', $date->dayOfWeek)
            ->whereDoesntHave('exceptions', fn ($query) => $query->whereDate('date', $date))
            ->orderBy('start_time')
            ->get()
            ->map(fn (Schedule $schedule) => $this->lessonStatus($schedule, $date));
    }

    public function lessonsForStudent(Student $student, Carbon $weekStart): Collection
    {
        $weekEnd = $weekStart->copy()->addDays(4);

        return Schedule::query()
            ->with(['subject', 'teacher'])
            ->where('grade_level_id', $student->grade_level_id)
            ->whereIn('day_of_week', [0, 1, 2, 3, 4])
            ->orderBy('day_of_week')->orderBy('start_time')
            ->get()
            ->map(function (Schedule $schedule) use ($student, $weekStart) {
                $date = $weekStart->copy()->addDays($schedule->day_of_week);
                $isCancelled = $schedule->exceptions()->whereDate('date', $date)->exists();

                return [
                    'schedule' => $schedule,
                    'date' => $date,
                    'completed' => !$isCancelled && DailyProgress::query()
                        ->where('student_id', $student->id)
                        ->where('schedule_id', $schedule->id)
                        ->whereDate('date', $date)
                        ->exists(),
                    'cancelled' => $isCancelled,
                ];
            })
            ->reject(fn (array $lesson) => $lesson['cancelled'])
            ->values();
    }

    public function lessonStatus(Schedule $schedule, Carbon $date): array
    {
        $studentIds = Student::query()->where('grade_level_id', $schedule->grade_level_id)->pluck('id');
        $completed = DailyProgress::query()
            ->where('schedule_id', $schedule->id)
            ->whereDate('date', $date)
            ->whereIn('student_id', $studentIds)
            ->distinct('student_id')->count('student_id');

        return [
            'schedule' => $schedule,
            'date' => $date,
            'total' => $studentIds->count(),
            'completed' => $completed,
            'remaining' => max(0, $studentIds->count() - $completed),
            'complete' => $studentIds->isNotEmpty() && $completed === $studentIds->count(),
        ];
    }
}
