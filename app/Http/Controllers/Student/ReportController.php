<?php
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\WeeklyReport;
use App\Services\WeeklyReportService;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function index()
    {
        $student = auth()->user()->student;
        $reports = WeeklyReport::where('student_id', $student?->id)
            ->latest('generated_at')->paginate(15);
        return view('student.reports', compact('student', 'reports'));
    }

    public function download(WeeklyReport $report)
    {
        $student = auth()->user()->student;
        abort_if($report->student_id !== $student?->id, 403, 'غير مصرح لك بتنزيل هذا التقرير');
        app(WeeklyReportService::class)->regenerate($report);

        abort_unless(Storage::exists($report->file_path), 404, 'الملف غير موجود');

        $student->loadMissing('gradeLevel');
        $studentName = $student->full_name;
        $gradeName = $student->gradeLevel?->name ?? '';
        $trackValue = $student->gradeLevel?->track?->value ?? $student->track?->value ?? $student->track ?? '';
        $trackLabel = $trackValue === 'languages' ? 'لغات' : ($trackValue === 'arabic' ? 'عربي' : '');
        $date = $report->week_start_date instanceof \Carbon\Carbon
            ? $report->week_start_date->format('Y-m-d')
            : \Carbon\Carbon::parse($report->week_start_date)->format('Y-m-d');

        $cleanName = str_replace(
            ['/', '\\', ':', '*', '?', '"', '<', '>', '|'],
            '-',
            implode(' ', array_filter([$studentName, $gradeName, $trackLabel, $date]))
        );

        return Storage::download(
            $report->file_path,
            "{$cleanName}.pdf"
        );
    }
}
