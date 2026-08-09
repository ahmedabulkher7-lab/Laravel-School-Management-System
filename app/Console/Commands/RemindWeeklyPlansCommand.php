<?php

namespace App\Console\Commands;

use App\Models\Teacher;
use App\Models\WeeklyPlan;
use App\Notifications\WeeklyPlanReminder;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RemindWeeklyPlansCommand extends Command
{
    protected $signature = 'notifications:remind-weekly-plans';
    protected $description = 'Remind teachers every Friday to enter next week plans';

    public function handle(): int
    {
        $weekStart = Carbon::now()->next(Carbon::SUNDAY)->startOfDay();
        $weekStartValue = $weekStart->toDateString();

        Teacher::with(['user', 'subjects', 'gradeLevels.subjects'])->get()->each(
            function (Teacher $teacher) use ($weekStartValue): void {
                if (!$teacher->user) {
                    return;
                }

                $missingGrades = $teacher->gradeLevels->filter(function ($gradeLevel) use ($teacher, $weekStartValue): bool {
                    $subjectIds = $gradeLevel->subjects->pluck('id')->intersect($teacher->subjects->pluck('id'));
                    if ($subjectIds->isEmpty()) {
                        return false;
                    }

                    $plannedSubjectIds = WeeklyPlan::where('teacher_id', $teacher->id)
                        ->where('grade_level_id', $gradeLevel->id)
                        ->where('week_start', $weekStartValue)
                        ->pluck('subject_id');

                    return $subjectIds->diff($plannedSubjectIds)->isNotEmpty();
                });

                if ($missingGrades->isEmpty()) {
                    return;
                }

                $alreadySent = $teacher->user->notifications()
                    ->where('created_at', '>=', now()->startOfDay())
                    ->where('data->type', 'weekly_plan_reminder')
                    ->where('data->week_start', $weekStartValue)
                    ->exists();

                if (!$alreadySent) {
                    $teacher->user->notify(new WeeklyPlanReminder(
                        $weekStartValue,
                        $missingGrades->pluck('name')->values()->all()
                    ));
                }
            }
        );

        $this->info('Weekly plan reminders sent successfully.');
        return self::SUCCESS;
    }
}
