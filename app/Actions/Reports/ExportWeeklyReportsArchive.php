<?php

namespace App\Actions\Reports;

use App\Models\WeeklyReport;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

class ExportWeeklyReportsArchive
{
    public function execute(Carbon $weekStart, Collection $reports): BinaryFileResponse
    {
        abort_unless(class_exists(ZipArchive::class), 500, "امتداد ZIP غير متاح على الخادم.");

        set_time_limit(120);

        $temporaryDirectory = storage_path("app/private/report-archives");
        File::ensureDirectoryExists($temporaryDirectory);
        $archivePath = $temporaryDirectory . DIRECTORY_SEPARATOR . "weekly-reports-" . uniqid("", true) . ".zip";
        $zip = new ZipArchive();

        if ($zip->open($archivePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            abort(500, "تعذر إنشاء ملف التقارير المضغوط.");
        }

        foreach ($reports as $report) {
            $fileName = $this->formatReportFileName($report);
            $zip->addFile(
                Storage::path($report->file_path),
                $fileName
            );
        }

        $zip->close();

        return response()->download(
            $archivePath,
            "weekly-reports-{$weekStart->toDateString()}.zip"
        )->deleteFileAfterSend(true);
    }

    public function formatReportFileName(WeeklyReport $report): string
    {
        $report->loadMissing(["student.gradeLevel"]);
        $student = $report->student;
        $studentName = $student?->full_name ?? "طالب_{$report->student_id}";
        $gradeName = $student?->gradeLevel?->name ?? "";

        $trackValue = $student?->gradeLevel?->track?->value ?? $student?->track?->value ?? $student?->track ?? "";
        $trackLabel = $trackValue === "languages" ? "لغات" : ($trackValue === "arabic" ? "عربي" : "");

        $date = $report->week_start_date instanceof Carbon
            ? $report->week_start_date->format("Y-m-d")
            : Carbon::parse($report->week_start_date)->format("Y-m-d");

        $cleanName = str_replace(
            ["/", "\\", ":", "*", "?", "\"", "<", ">", "|"],
            "-",
            implode(" ", array_filter([$studentName, $gradeName, $trackLabel, $date]))
        );

        return "{$cleanName}.pdf";
    }
}
