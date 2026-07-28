<?php
namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\DailyProgress;
use Carbon\Carbon;

class ProgressController extends Controller
{
    public function log()
    {
        $teacher  = auth()->user()->teacher;
        $students = $teacher?->students()->with('gradeLevel')->orderBy('full_name')->get() ?? collect();
        $today    = Carbon::today()->toDateString();
        return view('teacher.progress.log', compact('teacher', 'students', 'today'));
    }

    public function history()
    {
        $teacher = auth()->user()->teacher;
        $history = DailyProgress::where('teacher_id', $teacher?->id)
            ->with(['student', 'subject'])
            ->latest('date')
            ->paginate(25);
        return view('teacher.progress.history', compact('history'));
    }
}
