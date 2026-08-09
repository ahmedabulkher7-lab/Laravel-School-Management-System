<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StudyTrack;
use App\Http\Controllers\Controller;
use App\Models\GradeLevel;
use App\Models\WeeklyPlan;
use Carbon\Carbon;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as Pdf;

class WeeklyPlanController extends Controller
{
    public function index()
    {
        $weekStart = $this->weekStart(request('week_start'));
        $track = request('track');
        $gradeLevels = GradeLevel::with(['subjects', 'teachers.subjects'])
            ->when($track, fn ($query) => $query->where('track', $track))
            ->orderBy('track')
            ->orderBy('order')
            ->get();
        $plans = $this->plansFor($weekStart, $gradeLevels);
        $summaries = $gradeLevels->map(fn ($gradeLevel) => $this->summary($gradeLevel, $plans));
        $tracks = StudyTrack::cases();

        return view('admin.weekly-plans.index', compact(
            'weekStart', 'track', 'tracks', 'summaries'
        ));
    }

    public function download(GradeLevel $gradeLevel)
    {
        $weekStart = $this->weekStart(request('week_start'));
        $gradeLevel->load(['subjects', 'teachers.subjects']);
        $plans = $this->plansFor($weekStart, collect([$gradeLevel]));
        $summary = $this->summary($gradeLevel, $plans);

        abort_unless($summary['complete'], 403, 'لا يمكن تحميل الجدول قبل تسجيل جميع المدرسين لخططهم.');

        $pdf = Pdf::loadView('pdf.weekly-plan', [
            'gradeLevel' => $gradeLevel,
            'summary' => $summary,
            'weekStart' => $weekStart,
            'weekEnd' => $weekStart->copy()->addDays(6),
        ], [], [
            'mode' => 'utf-8',
            'format' => 'A4',
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'autoArabic' => true,
            'show_watermark_image' => true,
            'watermark_image_path' => public_path('images/logo.jpg'),
            'watermark_image_alpha' => 0.3,
            'watermark_image_size' => 'D',
            'watermark_image_position' => 'P',
        ]);

        return $pdf->download("weekly-plan-{$gradeLevel->id}-{$weekStart->toDateString()}.pdf");
    }

    private function plansFor(Carbon $weekStart, $gradeLevels)
    {
        return WeeklyPlan::with(['teacher', 'subject'])
            ->where('week_start', $weekStart->toDateString())
            ->whereIn('grade_level_id', $gradeLevels->pluck('id'))
            ->get()
            ->groupBy(fn ($plan) => $plan->grade_level_id . '-' . $plan->subject_id . '-' . $plan->teacher_id);
    }

    private function summary($gradeLevel, $plans): array
    {
        $rows = [];
        $requiredCount = 0;
        $completedCount = 0;

        foreach ($gradeLevel->subjects as $subject) {
            $teachers = $gradeLevel->teachers->filter(
                fn ($teacher) => $teacher->subjects->contains('id', $subject->id)
            );
            $requiredCount += $teachers->count();
            $subjectPlans = collect();

            foreach ($teachers as $teacher) {
                $plan = $plans->get($gradeLevel->id . '-' . $subject->id . '-' . $teacher->id)?->first();
                if ($plan) {
                    $completedCount++;
                    $subjectPlans->push($plan);
                }
            }

            $rows[] = [
                'subject' => $subject,
                'teachers' => $teachers,
                'plans' => $subjectPlans,
                'complete' => $teachers->isNotEmpty() && $subjectPlans->count() === $teachers->count(),
            ];
        }

        return [
            'id' => $gradeLevel->id,
            'name' => $gradeLevel->name,
            'track' => $gradeLevel->track->value ?? $gradeLevel->track,
            'rows' => collect($rows),
            'required' => $requiredCount,
            'completed' => $completedCount,
            'unassigned' => collect($rows)->filter(fn ($row) => $row['teachers']->isEmpty())->count(),
            'complete' => $requiredCount > 0
                && $requiredCount === $completedCount
                && collect($rows)->every(fn ($row) => $row['teachers']->isNotEmpty()),
        ];
    }

    private function weekStart(?string $date): Carbon
    {
        return Carbon::parse($date ?: now())->startOfWeek(Carbon::SUNDAY)->startOfDay();
    }
}
