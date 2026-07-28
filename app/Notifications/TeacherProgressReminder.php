<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class TeacherProgressReminder extends Notification
{
    use Queueable;

    public function __construct(public string $date, public int $missingCount) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'    => 'teacher_reminder',
            'message' => "لم تقم بتسجيل التقدم اليومي لـ {$this->missingCount} طالب(ين) بتاريخ {$this->date}",
            'date'    => $this->date,
            'missing' => $this->missingCount,
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
