<?php

namespace App\Console\Commands;

use App\Models\Student;
use App\Services\WeeklyReportService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class GenerateWeeklyReports extends Command
{
    protected $signature = 'reports:generate-week
                            {week_start : تاريخ بداية الأسبوع Y-m-d}
                            {--force : إعادة توليد التقارير الموجودة}';

    protected $description = 'توليد تقارير PDF الأسبوعية لكل الطلاب في الخلفية';

    public function handle(WeeklyReportService $service): int
    {
        $weekStart = Carbon::parse($this->argument('week_start'))->startOfDay();
        $force     = $this->option('force');

        $students = Student::with(['user', 'gradeLevel.subjects'])->orderBy('full_name')->get();
        $total    = $students->count();

        if ($total === 0) {
            $this->error('لا يوجد طلاب.');
            return self::FAILURE;
        }

        // تخزين حالة التقدم في الـ Cache
        $cacheKey = "report_gen_{$weekStart->toDateString()}";
        Cache::put($cacheKey, [
            'status'    => 'running',
            'total'     => $total,
            'done'      => 0,
            'failed'    => 0,
            'started_at'=> now()->toISOString(),
        ], now()->addHours(2));

        $done   = 0;
        $failed = 0;

        foreach ($students as $student) {
            try {
                $filePath = "reports/{$student->id}/report_{$student->id}_{$weekStart->toDateString()}.pdf";

                if (!$force && Storage::exists($filePath)) {
                    $done++;
                } else {
                    $service->generate($student, $weekStart);
                    $done++;
                }
            } catch (\Throwable $e) {
                $failed++;
                $this->error("فشل الطالب {$student->id}: {$e->getMessage()}");
            }

            // تحديث الـ Cache بعد كل طالب
            Cache::put($cacheKey, [
                'status'    => 'running',
                'total'     => $total,
                'done'      => $done,
                'failed'    => $failed,
                'started_at'=> Cache::get($cacheKey)['started_at'],
            ], now()->addHours(2));
        }

        Cache::put($cacheKey, [
            'status'     => 'done',
            'total'      => $total,
            'done'       => $done,
            'failed'     => $failed,
            'finished_at'=> now()->toISOString(),
        ], now()->addHours(2));

        $this->info("✅ تم توليد {$done}/{$total} تقرير. فشل: {$failed}");
        return self::SUCCESS;
    }
}
