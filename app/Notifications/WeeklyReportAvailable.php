<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\WeeklyReport;

class WeeklyReportAvailable extends Notification
{
    use Queueable;

    public function __construct(public WeeklyReport $report) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'       => 'weekly_report',
            'message'    => "تقرير أسبوعي جديد متاح للفترة من {$this->report->week_start_date->format('Y-m-d')} إلى {$this->report->week_end_date->format('Y-m-d')}",
            'report_id'  => $this->report->id,
            'week_start' => $this->report->week_start_date->format('Y-m-d'),
            'week_end'   => $this->report->week_end_date->format('Y-m-d'),
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
