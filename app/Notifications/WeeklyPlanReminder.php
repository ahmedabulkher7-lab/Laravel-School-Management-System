<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WeeklyPlanReminder extends Notification
{
    use Queueable;

    public function __construct(public string $weekStart, public array $gradeNames) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'weekly_plan_reminder',
            'message' => 'يرجى إدخال الجدول الأسبوعي للطلاب للأسبوع القادم: ' . implode('، ', $this->gradeNames),
            'week_start' => $this->weekStart,
            'grades' => $this->gradeNames,
            'url' => route('teacher.weekly-plans.index', ['week_start' => $this->weekStart]),
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
