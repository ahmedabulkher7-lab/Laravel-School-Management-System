<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveWeeklyPlanRequest;
use App\Models\GradeLevel;
use App\Models\WeeklyPlan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class WeeklyPlanController extends Controller
{
    public function index()
    {
        $teacher = Auth::user()?->teacher;
        abort_unless($teacher, 403);

        $weekStart = $this->weekStart(request('week_start'));
        $gradeLevels = $teacher->gradeLevels()
            ->with(['subjects' => fn ($query) => $query->orderBy('name')])
            ->orderBy('order')
            ->get();
        $teacherSubjectIds = $teacher->subjects()->pluck('subjects.id');
        $selectedTrack = request('track');
        $selectedGradeLevelId = request('grade_level_id');
        $tracks = $gradeLevels->pluck('track')->unique()->values();
        $filteredGradeLevels = $selectedTrack
            ? $gradeLevels->where('track', $selectedTrack)->values()
            : collect();
        $selectedGradeLevel = $filteredGradeLevels->firstWhere('id', (int) $selectedGradeLevelId);
        if (!$selectedGradeLevelId || !$selectedGradeLevel) {
            $selectedGradeLevel = null;
        }
        $plans = WeeklyPlan::where('teacher_id', $teacher->id)
            ->where('week_start', $weekStart->toDateString())
            ->get()
            ->keyBy(fn ($plan) => $plan->grade_level_id . '-' . $plan->subject_id);
        $completion = $gradeLevels->mapWithKeys(function ($gradeLevel) use ($teacherSubjectIds, $plans): array {
            $subjectIds = $gradeLevel->subjects->pluck('id')->intersect($teacherSubjectIds);
            $plannedCount = $subjectIds->filter(
                fn ($subjectId) => $plans->has($gradeLevel->id . '-' . $subjectId)
            )->count();

            return [$gradeLevel->id => [
                'total' => $subjectIds->count(),
                'planned' => $plannedCount,
                'complete' => $subjectIds->isNotEmpty() && $plannedCount === $subjectIds->count(),
            ]];
        });
        $trackCompletion = $tracks->mapWithKeys(function ($track) use ($gradeLevels, $completion): array {
            $trackValue = $track->value ?? $track;
            $trackGrades = $gradeLevels->where('track', $trackValue);

            return [$trackValue => [
                'total' => $trackGrades->count(),
                'completed' => $trackGrades->filter(fn ($gradeLevel) => $completion[$gradeLevel->id]['complete'])->count(),
                'complete' => $trackGrades->isNotEmpty()
                    && $trackGrades->every(fn ($gradeLevel) => $completion[$gradeLevel->id]['complete']),
            ]];
        });

        return view('teacher.weekly-plans.index', compact(
            'teacher', 'gradeLevels', 'teacherSubjectIds', 'plans', 'weekStart',
            'tracks', 'selectedTrack', 'filteredGradeLevels', 'selectedGradeLevel',
            'completion', 'trackCompletion'
        ));
    }

    public function store(SaveWeeklyPlanRequest $request, GradeLevel $gradeLevel)
    {
        $teacher = Auth::user()?->teacher;
        abort_unless($teacher, 403);

        abort_unless($teacher->gradeLevels()->whereKey($gradeLevel->id)->exists(), 403);

        $allowedSubjectIds = $teacher->subjects()
            ->whereIn('subjects.id', $gradeLevel->subjects()->pluck('subjects.id'))
            ->pluck('subjects.id');
        $plans = collect($request->validated('plans'));

        abort_unless($plans->pluck('subject_id')->diff($allowedSubjectIds)->isEmpty(), 403);

        $weekStart = $this->weekStart($request->validated('week_start'));
        DB::transaction(function () use ($plans, $teacher, $gradeLevel, $weekStart): void {
            foreach ($plans as $plan) {
                WeeklyPlan::updateOrCreate(
                    [
                        'teacher_id' => $teacher->id,
                        'grade_level_id' => $gradeLevel->id,
                        'subject_id' => $plan['subject_id'],
                        'week_start' => $weekStart->toDateString(),
                    ],
                    [
                        'class_work' => $plan['class_work'] ?? null,
                        'homework' => $plan['homework'] ?? null,
                        'online_games' => $plan['online_games'] ?? null,
                    ]
                );
            }
        });

        return redirect()->route('teacher.weekly-plans.index', ['week_start' => $weekStart->toDateString()])
            ->with('success', "تم حفظ الخطة الأسبوعية لصف {$gradeLevel->name}");
    }

    private function weekStart(?string $date): Carbon
    {
        return Carbon::parse($date ?: now())->startOfWeek(Carbon::SUNDAY)->startOfDay();
    }
}
