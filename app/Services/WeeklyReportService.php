<?php

namespace App\Services;

use App\Models\DailyProgress;
use App\Models\Student;
use App\Models\WeeklyReport;
use Carbon\Carbon;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as PDF;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class WeeklyReportService
{
    public function readiness(Student $student, Carbon $weekStart): array
    {
        $student->loadMissing(['gradeLevel.subjects', 'gradeLevel.teachers.subjects']);
        $gradeLevel = $student->gradeLevel;
        $required = 0;
        $completed = 0;
        $missing = [];

        // From now on a report is measured against the lessons that were actually
        // scheduled for this student's class, not against five calendar days.
        if ($gradeLevel) {
            $lessonsBySubject = app(ScheduledEvaluationService::class)
                ->lessonsForStudent($student, $weekStart)
                ->groupBy(fn (array $lesson) => $lesson['schedule']->subject_id);

            foreach ($gradeLevel->subjects as $subject) {
                $lessons = $lessonsBySubject->get($subject->id, collect());
                if ($lessons->isEmpty()) {
                    continue;
                }
                $required++;
                $loggedLessons = $lessons->where('completed', true)->count();
                if ($loggedLessons === $lessons->count()) {
                    $completed++;
                } else {
                    $missing[] = ($subject->name_ar ?? $subject->name) . " ({$loggedLessons}/{$lessons->count()} lessons)";
                }
            }

            return ['ready' => $required > 0 && $completed === $required, 'required' => $required, 'completed' => $completed, 'missing' => $missing];
        }

        if (!$gradeLevel) {
            return ['ready' => false, 'required' => 0, 'completed' => 0, 'missing' => ['لا توجد مرحلة دراسية']];
        }

        foreach ($gradeLevel->subjects as $subject) {
            $teachers = $gradeLevel->teachers->filter(
                fn ($teacher) => $teacher->subjects->contains('id', $subject->id)
            );
            $required++;

            if ($teachers->isEmpty()) {
                $missing[] = $subject->name_ar ?? $subject->name;
                continue;
            }

            $loggedDays = DailyProgress::where('student_id', $student->id)
                ->where('subject_id', $subject->id)
                ->whereIn('teacher_id', $teachers->pluck('id'))
                ->whereBetween('date', [$weekStart->toDateString(), $weekStart->copy()->addDays(4)->toDateString()])
                ->distinct('date')
                ->count('date');

            if ($loggedDays >= 5) {
                $completed++;
            } else {
                $missing[] = ($subject->name_ar ?? $subject->name) . " ({$loggedDays}/5 أيام)";
            }
        }

        return [
            'ready' => $required > 0 && $completed === $required,
            'required' => $required,
            'completed' => $completed,
            'missing' => $missing,
        ];
    }

    /**
     * Creates or refreshes a report even when some progress entries are still missing.
     */
    public function generate(Student $student, Carbon $weekStart): WeeklyReport
    {
        $student->loadMissing(['user', 'gradeLevel.subjects']);
        $weekEnd = $weekStart->copy()->addDays(6)->endOfDay();
        $fileName = "report_{$student->id}_{$weekStart->toDateString()}.pdf";
        $filePath = "reports/{$student->id}/{$fileName}";
        $report = WeeklyReport::firstOrCreate(
            [
                'student_id' => $student->id,
                'week_start_date' => $weekStart->toDateString(),
            ],
            [
                'week_end_date' => $weekEnd->toDateString(),
                'file_path' => $filePath,
                'generated_by' => Auth::id() ?? User::role('admin')->value('id'),
                'generated_at' => now(),
            ]
        );

        if (!$report->wasRecentlyCreated && Storage::exists($filePath)) {
            $this->regenerate($report);
        }

        if ($report->wasRecentlyCreated || !Storage::exists($filePath)) {
            $progress = DailyProgress::where('student_id', $student->id)
                ->whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()])
                ->with(['subject', 'teacher'])
                ->get();
            $pdf = PDF::loadView('pdf.weekly-report', [
                'student' => $student,
                'progress' => $progress,
                'subjects' => $student->gradeLevel?->subjects ?? collect(),
                'weekStart' => $weekStart,
                'weekEnd' => $weekEnd,
            ], [], [
                'mode' => 'utf-8',
                'format' => 'A4',
                'default_font' => 'alexandria',
                'custom_font_dir' => resource_path('fonts'),
                'custom_font_data' => [
                    'alexandria' => [
                        'R' => 'Alexandria.ttf',
                        'B' => 'Alexandria.ttf',
                        'I' => 'Alexandria.ttf',
                        'BI' => 'Alexandria.ttf',
                        'useOTL' => 0x80,
                    ],
                ],
                'autoScriptToLang' => false,
                'autoLangToFont' => false,
                'autoArabic' => true,
                'show_watermark_image' => true,
                'watermark_image_path' => public_path('images/logo.jpg'),
                'watermark_image_alpha' => 0.06,
                'watermark_image_size' => 'D',
                'watermark_image_position' => 'P',
            ]);
            Storage::put($filePath, $pdf->output());
        }

        return $report;
    }

    /**
     * Keeps automatic report creation tied to a fully completed school week.
     */
    public function generateIfReady(Student $student, Carbon $weekStart): ?WeeklyReport
    {
        return $this->readiness($student, $weekStart)['ready']
            ? $this->generate($student, $weekStart)
            : null;
    }

    public function regenerate(WeeklyReport $report): void
    {
        $report->loadMissing('student.gradeLevel.subjects');

        $student = $report->student;
        $weekStart = $report->week_start_date->copy()->startOfDay();
        $weekEnd = $report->week_end_date->copy()->endOfDay();
        $progress = DailyProgress::where('student_id', $student->id)
            ->whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->with(['subject', 'teacher'])
            ->get();
        $pdf = PDF::loadView('pdf.weekly-report', [
            'student' => $student,
            'progress' => $progress,
            'subjects' => $student->gradeLevel?->subjects ?? collect(),
            'weekStart' => $weekStart,
            'weekEnd' => $weekEnd,
        ], [], [
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => 'alexandria',
            'custom_font_dir' => resource_path('fonts'),
            'custom_font_data' => [
                'alexandria' => [
                    'R' => 'Alexandria.ttf',
                    'B' => 'Alexandria.ttf',
                    'I' => 'Alexandria.ttf',
                    'BI' => 'Alexandria.ttf',
                    'useOTL' => 0x80,
                ],
            ],
            'autoScriptToLang' => false,
            'autoLangToFont' => false,
            'autoArabic' => true,
            'show_watermark_image' => true,
            'watermark_image_path' => public_path('images/logo.jpg'),
            'watermark_image_alpha' => 0.06,
            'watermark_image_size' => 'D',
            'watermark_image_position' => 'P',
        ]);

        Storage::put($report->file_path, $pdf->output());
    }
}
