<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\WeeklyReport;
use App\Services\WeeklyReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use ZipArchive;

class ReportController extends Controller
{
    public function index()
    {
        $weekStart = $this->weekStart(request('week_start'));
        $reportsByStudent = WeeklyReport::whereDate('week_start_date', $weekStart->toDateString())
            ->get()
            ->keyBy('student_id');

        $students = Student::with(['gradeLevel.subjects', 'gradeLevel.teachers.subjects'])
            ->orderBy('full_name')->get()
            ->map(function (Student $student) use ($weekStart, $reportsByStudent): Student {
                $student->report_readiness = app(WeeklyReportService::class)->readiness($student, $weekStart);
                $student->ready_weekly_report = $reportsByStudent->get($student->id);

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
        $report = app(WeeklyReportService::class)->generate($student, $weekStart);

        return back()->with('success',
            "تم توليد التقرير الأسبوعي للطالب {$student->full_name} بنجاح");
    }

    public function download(WeeklyReport $report)
    {
        app(WeeklyReportService::class)->regenerate($report);

        return $this->streamDownload($report);
    }

    public function downloadStudent(Request $request, Student $student)
    {
        $request->validate([
            'week_start' => 'required|date',
        ]);

        $report = app(WeeklyReportService::class)->generate(
            $student,
            Carbon::parse($request->week_start)->startOfDay(),
        );

        return $this->streamDownload($report);
    }

    /**
     * بدء توليد كل التقارير في الخلفية.
     */
    public function generateAll(Request $request)
    {
        $request->validate(['week_start' => 'required|date']);
        $weekStart = Carbon::parse($request->week_start)->startOfDay();
        $cacheKey  = "report_gen_{$weekStart->toDateString()}";

        // لو عملية توليد شغّالة بالفعل، لا تبدأ واحدة جديدة
        $current = Cache::get($cacheKey);
        if ($current && $current['status'] === 'running') {
            return response()->json(['message' => 'التوليد جارٍ بالفعل.', 'progress' => $current]);
        }

        // تصفير الـ Cache وبدء الـ process في الخلفية
        Cache::put($cacheKey, ['status' => 'starting', 'total' => 0, 'done' => 0, 'failed' => 0], now()->addHours(2));

        $phpBinary  = PHP_BINARY;
        $artisan    = base_path('artisan');
        $force      = $request->boolean('force') ? '--force' : '';
        $dateArg    = $weekStart->toDateString();

        // تشغيل الـ command في الخلفية (Windows: start /B)
        if (PHP_OS_FAMILY === 'Windows') {
            pclose(popen("start /B {$phpBinary} {$artisan} reports:generate-week {$dateArg} {$force} > NUL 2>&1", 'r'));
        } else {
            pclose(popen("{$phpBinary} {$artisan} reports:generate-week {$dateArg} {$force} > /dev/null 2>&1 &", 'r'));
        }

        return response()->json(['message' => 'بدأ التوليد.', 'cache_key' => $cacheKey]);
    }

    /**
     * إرجاع حالة التوليد الجارية.
     */
    public function generateStatus(Request $request)
    {
        $request->validate(['week_start' => 'required|date']);
        $weekStart = Carbon::parse($request->week_start)->toDateString();
        $progress  = Cache::get("report_gen_{$weekStart}", ['status' => 'idle', 'total' => 0, 'done' => 0, 'failed' => 0]);
        return response()->json($progress);
    }

    /**
     * تحميل ZIP للتقارير المولَّدة مسبقاً فقط — سريع جداً.
     */
    public function downloadAll(Request $request, \App\Actions\Reports\ExportWeeklyReportsArchive $archiveAction)
    {
        $request->validate(['week_start' => 'required|date']);
        $weekStart = Carbon::parse($request->week_start)->startOfDay();

        $reports = WeeklyReport::with(['student.gradeLevel'])
            ->whereDate('week_start_date', $weekStart->toDateString())
            ->get()
            ->filter(fn ($r) => Storage::exists($r->file_path));

        if ($reports->isEmpty()) {
            return back()->with('error', 'لا توجد تقارير مولَّدة لهذا الأسبوع. اضغط "توليد التقارير" أولاً.');
        }

        return $archiveAction->execute($weekStart, $reports);
    }

    private function streamDownload(WeeklyReport $report, ?\App\Actions\Reports\ExportWeeklyReportsArchive $archiveAction = null)
    {
        $archiveAction ??= app(\App\Actions\Reports\ExportWeeklyReportsArchive::class);
        $report->loadMissing(['student.gradeLevel']);
        abort_unless(Storage::exists($report->file_path), 404, 'الملف غير موجود');
        
        $fileName = $archiveAction->formatReportFileName($report);
        return Storage::download(
            $report->file_path,
            $fileName
        );
    }

    private function weekStart(?string $date): Carbon
    {
        return Carbon::parse($date ?: now())->startOfWeek(Carbon::SUNDAY)->startOfDay();
    }

}
