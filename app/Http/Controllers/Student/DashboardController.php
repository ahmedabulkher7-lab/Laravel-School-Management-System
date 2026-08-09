<?php
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\DailyProgress;

class DashboardController extends Controller
{
    public function index()
    {
        $student = auth()->user()->student()
            ->with('gradeLevel.subjects')
            ->firstOrFail();

        $recentProgress = DailyProgress::where('student_id', $student?->id)
            ->with('subject')->latest('date')->limit(7)->get();
        $subjectCount = $student->subjects->count();

        return view('student.dashboard', compact('student', 'recentProgress', 'subjectCount'));
    }
}
