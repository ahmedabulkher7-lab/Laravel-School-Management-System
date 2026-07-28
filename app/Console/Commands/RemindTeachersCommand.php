<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Teacher;
use App\Models\DailyProgress;
use App\Notifications\TeacherProgressReminder;
use Carbon\Carbon;

class RemindTeachersCommand extends Command
{
    protected $signature   = 'notifications:remind-teachers';
    protected $description = 'Send reminder notifications to teachers who have not logged daily progress';

    public function handle(): int
    {
        $today = Carbon::today()->toDateString();

        Teacher::with(['students', 'user'])->get()->each(function (Teacher $teacher) use ($today) {
            if (!$teacher->user) return;

            $assignedCount = $teacher->students()->count();
            if ($assignedCount === 0) return;

            $loggedCount = DailyProgress::where('teacher_id', $teacher->id)
                ->whereDate('date', $today)
                ->count();

            $missing = $assignedCount - $loggedCount;

            if ($missing > 0) {
                $teacher->user->notify(new TeacherProgressReminder($today, $missing));
            }
        });

        $this->info('Teacher reminders sent successfully.');
        return Command::SUCCESS;
    }
}
