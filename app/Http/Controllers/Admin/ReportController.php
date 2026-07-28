<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\WeeklyReport;
use Meneses\LaravelMpdf\Facades\LaravelMpdf as PDF;
use App\Models\DailyProgress;
use App\Notifications\WeeklyReportAvailable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function index()
    {
        $students = Student::with(['gradeLevel'])->orderBy('full_name')->get();
        $reports  = WeeklyReport::with(['student.gradeLevel', 'generatedBy'])
            ->latest('generated_at')->paginate(20);
        return view('admin.reports.index', compact('students', 'reports'));
    }

    public function generate(Request $request, Student $student)
    {
        $request->validate([
            'week_start' => 'required|date',
        ], [
            'week_start.required' => 'تاريخ بداية الأسبوع مطلوب',
            'week_start.date'     => 'التاريخ غير صالح',
        ]);

        $weekStart = Carbon::parse($request->week_start)->startOfDay();
        $weekEnd   = $weekStart->copy()->addDays(6)->endOfDay();

        $progress = DailyProgress::where('student_id', $student->id)
            ->whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->with(['subject', 'teacher'])
            ->get();

        $assignments = \App\Models\TeacherStudentAssignment::where('student_id', $student->id)
            ->with(['subject', 'teacher'])
            ->get();

        $pdf = \PDF::loadView('pdf.weekly-report', [
            'student'     => $student->load('gradeLevel'),
            'progress'    => $progress,
            'assignments' => $assignments,
            'weekStart'   => $weekStart,
            'weekEnd'     => $weekEnd,
        ], [], [
            'mode' => 'utf-8',
            'format' => 'A4',
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'autoArabic' => true,
                // ===== الشعار كخلفية شفافة =====
'show_watermark_image'     => true,
'watermark_image_path'     => public_path('images/logo.jpg'),
'watermark_image_alpha'    => 0.3,     // قللناها من 0.08 لـ 0.05
'watermark_image_size'     => 'D', // عرض × ارتفاع بالمليمتر (بدل 'F')
'watermark_image_position' => 'P'
        ]);

        $fileName = "report_{$student->id}_{$weekStart->toDateString()}.pdf";
        $filePath = "reports/{$student->id}/{$fileName}";

        Storage::put($filePath, $pdf->output());

        $report = WeeklyReport::create([
            'student_id'      => $student->id,
            'week_start_date' => $weekStart->toDateString(),
            'week_end_date'   => $weekEnd->toDateString(),
            'file_path'       => $filePath,
            'generated_by'    => auth()->id(),
            'generated_at'    => now(),
        ]);

        $student->user->notify(new WeeklyReportAvailable($report));

        return back()->with('success',
            "تم توليد التقرير الأسبوعي للطالب {$student->full_name} بنجاح");
    }

    public function download(WeeklyReport $report)
    {
        abort_unless(Storage::exists($report->file_path), 404, 'الملف غير موجود');
        return Storage::download(
            $report->file_path,
            "تقرير_{$report->student->full_name}_{$report->week_start_date}.pdf"
        );
    }
}
