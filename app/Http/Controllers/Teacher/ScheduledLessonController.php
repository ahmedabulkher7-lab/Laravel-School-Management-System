<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Student;
use App\Services\ScheduledEvaluationService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScheduledLessonController extends Controller
{
    public function index(Request $request, ScheduledEvaluationService $evaluations)
    {
        $teacher = $request->user()->teacher;
        $date = Carbon::parse($request->query('date', now()))->startOfDay();
        $lessons = $teacher ? $evaluations->lessonsForTeacher($teacher, $date) : collect();

        return view('teacher.lessons.index', compact('teacher', 'date', 'lessons'));
    }

    public function show(Request $request, Schedule $schedule, ScheduledEvaluationService $evaluations)
    {
        abort_unless($request->user()->teacher?->id === $schedule->teacher_id, 403);
        $date = Carbon::parse($request->query('date', now()))->startOfDay();
        abort_unless($schedule->day_of_week === $date->dayOfWeek, 404);
        abort_if($schedule->exceptions()->whereDate('date', $date)->exists(), 404);

        $students = Student::query()->where('grade_level_id', $schedule->grade_level_id)
            ->with('gradeLevel')->orderBy('full_name')->get();
        $lesson = $evaluations->lessonStatus($schedule, $date);

        return view('teacher.lessons.show', compact('schedule', 'date', 'students', 'lesson'));
    }
}
