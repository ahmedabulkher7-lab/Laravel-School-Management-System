<?php
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\WeeklyReport;
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
        abort_unless(Storage::exists($report->file_path), 404, 'الملف غير موجود');
        return Storage::download(
            $report->file_path,
            "تقرير_أسبوعي_{$report->week_start_date}.pdf"
        );
    }
}
