<?php
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\DailyProgress;

class PerformanceController extends Controller
{
    public function index()
    {
        $student  = auth()->user()->student;
        $progress = DailyProgress::where('student_id', $student?->id)
            ->with('subject')->orderBy('date')->get()->groupBy('subject_id');
        return view('student.performance', compact('student', 'progress'));
    }
}
