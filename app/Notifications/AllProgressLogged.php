<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Teacher;

class AllProgressLogged extends Notification
{
    use Queueable;

    public function __construct(public Teacher $teacher, public string $date) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'         => 'all_progress_logged',
            'message'      => "المعلم {$this->teacher->full_name} أتم تسجيل التقدم اليومي لجميع طلابه بتاريخ {$this->date}",
            'teacher_id'   => $this->teacher->id,
            'teacher_name' => $this->teacher->full_name,
            'date'         => $this->date,
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
