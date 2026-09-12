<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Teacher;
use App\Notifications\TeacherProgressReminder;
use App\Services\ScheduledEvaluationService;
use Carbon\Carbon;

class RemindTeachersCommand extends Command
{
    protected $signature   = 'notifications:remind-teachers';
    protected $description = 'Send reminder notifications to teachers who have not logged daily progress';

    public function handle(): int
    {
        $today = Carbon::today()->toDateString();

        $evaluations = app(ScheduledEvaluationService::class);
        Teacher::with('user')->get()->each(function (Teacher $teacher) use ($today, $evaluations) {
            if (!$teacher->user) return;

            $missing = $evaluations->lessonsForTeacher($teacher, Carbon::parse($today))->sum('remaining');

            if ($missing > 0) {
                $teacher->user->notify(new TeacherProgressReminder($today, $missing));
            }
        });

        $this->info('Teacher reminders sent successfully.');
        return Command::SUCCESS;
    }
}
