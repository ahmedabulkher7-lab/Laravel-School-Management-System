<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\WeeklyReport;
use App\Services\WeeklyReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function index()
    {
        $weekStart = $this->weekStart(request('week_start'));
        $students = Student::with(['gradeLevel.subjects', 'gradeLevel.teachers.subjects'])
            ->orderBy('full_name')->get()
            ->map(function (Student $student) use ($weekStart): Student {
                $student->report_readiness = app(WeeklyReportService::class)->readiness($student, $weekStart);
                return $student;
            });
        $status = request('status', 'all');
        if (in_array($status, ['ready', 'pending'], true)) {
            $students = $students->filter(fn (Student $student) =>
                ($status === 'ready') === $student->report_readiness['ready']
            )->values();
        }
        $readyCount = $students->filter(fn (Student $student) => $student->report_readiness['ready'])->count();
        $pendingCount = $students->count() - $readyCount;
        $reports  = WeeklyReport::with(['student.gradeLevel', 'generatedBy'])
            ->latest('generated_at')->paginate(20);
        return view('admin.reports.index', compact('students', 'reports', 'weekStart', 'status', 'readyCount', 'pendingCount'));
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
        $student->load(['gradeLevel.subjects', 'gradeLevel.teachers.subjects']);
        $readiness = app(WeeklyReportService::class)->readiness($student, $weekStart);
        abort_unless($readiness['ready'], 422, 'لا يمكن توليد التقرير قبل اكتمال تقييم جميع المواد والمدرسين للأسبوع.');
        $report = app(WeeklyReportService::class)->generateIfReady($student, $weekStart);

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

    private function weekStart(?string $date): Carbon
    {
        return Carbon::parse($date ?: now())->startOfWeek(Carbon::SUNDAY)->startOfDay();
    }

}
