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
        @ini_set('memory_limit', '512M');
        @set_time_limit(180);

        $temporaryDirectory = storage_path("app/private/report-archives");
        if (!File::isDirectory($temporaryDirectory)) {
            @File::makeDirectory($temporaryDirectory, 0755, true, true);
        }
        if (!is_writable($temporaryDirectory)) {
            $temporaryDirectory = sys_get_temp_dir();
        }

        $archivePath = $temporaryDirectory . DIRECTORY_SEPARATOR . "weekly-reports-" . uniqid("", true) . ".zip";

        if (class_exists(ZipArchive::class)) {
            $this->createWithZipArchive($archivePath, $reports);
        } else {
            $this->createWithPurePhp($archivePath, $reports);
        }

        return response()->download(
            $archivePath,
            "weekly-reports-{$weekStart->toDateString()}.zip"
        )->deleteFileAfterSend(true);
    }

    private function createWithZipArchive(string $archivePath, Collection $reports): void
    {
        $zip = new ZipArchive();
        if ($zip->open($archivePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            // Fallback to pure PHP if opening fails
            $this->createWithPurePhp($archivePath, $reports);
            return;
        }

        foreach ($reports as $report) {
            $filePath = Storage::path($report->file_path);
            if (file_exists($filePath)) {
                $fileName = $this->formatReportFileName($report);
                $zip->addFile($filePath, $fileName);
            }
        }

        $zip->close();
    }

    private function createWithPurePhp(string $archivePath, Collection $reports): void
    {
        $handle = fopen($archivePath, 'wb');
        if (!$handle) {
            abort(500, "تعذر إنشاء ملف التقارير المضغوط.");
        }

        $entries = [];
        $offset = 0;

        foreach ($reports as $report) {
            $filePath = Storage::path($report->file_path);
            if (!file_exists($filePath)) {
                continue;
            }

            $data = file_get_contents($filePath);
            $entryName = str_replace('\\', '/', $this->formatReportFileName($report));
            $uncompressedSize = strlen($data);
            $crc = crc32($data);

            if (function_exists('gzdeflate')) {
                $compressedData = gzdeflate($data);
                $compressionMethod = 8;
                $compressedSize = strlen($compressedData);
            } else {
                $compressedData = $data;
                $compressionMethod = 0;
                $compressedSize = $uncompressedSize;
            }

            $modTime = time();
            $dtime = dechex((int) date('Y', $modTime) - 1980 << 25 | (int) date('m', $modTime) << 21 | (int) date('d', $modTime) << 16 | (int) date('H', $modTime) << 11 | (int) date('i', $modTime) << 5 | (int) date('s', $modTime) >> 1);
            $hexdtime = pack('V', hexdec($dtime));

            $localHeaderOffset = $offset;
            $localHeader = "\x50\x4b\x03\x04"
                . "\x14\x00"
                . "\x00\x08" // UTF-8 flag
                . pack('v', $compressionMethod)
                . $hexdtime
                . pack('V', $crc)
                . pack('V', $compressedSize)
                . pack('V', $uncompressedSize)
                . pack('v', strlen($entryName))
                . "\x00\x00"
                . $entryName;

            fwrite($handle, $localHeader . $compressedData);
            $offset += strlen($localHeader) + $compressedSize;

            $entries[] = [
                'name' => $entryName,
                'crc' => $crc,
                'compressed_size' => $compressedSize,
                'uncompressed_size' => $uncompressedSize,
                'compression_method' => $compressionMethod,
                'hexdtime' => $hexdtime,
                'offset' => $localHeaderOffset,
            ];
        }

        $centralDirStart = $offset;
        $centralDirRecords = '';

        foreach ($entries as $entry) {
            $cd = "\x50\x4b\x01\x02"
                . "\x14\x00"
                . "\x14\x00"
                . "\x00\x08"
                . pack('v', $entry['compression_method'])
                . $entry['hexdtime']
                . pack('V', $entry['crc'])
                . pack('V', $entry['compressed_size'])
                . pack('V', $entry['uncompressed_size'])
                . pack('v', strlen($entry['name']))
                . "\x00\x00"
                . "\x00\x00"
                . "\x00\x00"
                . "\x00\x00"
                . "\x20\x00\x00\x00"
                . pack('V', $entry['offset'])
                . $entry['name'];

            $centralDirRecords .= $cd;
        }

        fwrite($handle, $centralDirRecords);
        $centralDirSize = strlen($centralDirRecords);

        $eocd = "\x50\x4b\x05\x06"
            . "\x00\x00"
            . "\x00\x00"
            . pack('v', count($entries))
            . pack('v', count($entries))
            . pack('V', $centralDirSize)
            . pack('V', $centralDirStart)
            . "\x00\x00";

        fwrite($handle, $eocd);
        fclose($handle);
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
