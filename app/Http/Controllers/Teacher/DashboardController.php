<?php
namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\DailyProgress;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $teacher = auth()->user()->teacher;
        $students = $teacher?->students()->with('gradeLevel')->get() ?? collect();
        $today    = Carbon::today()->toDateString();

        $loggedToday = DailyProgress::where('teacher_id', $teacher?->id)
            ->whereDate('date', $today)->pluck('student_id')->toArray();

        $pendingStudents = $students->whereNotIn('id', $loggedToday);

        return view('teacher.dashboard',
            compact('teacher', 'students', 'pendingStudents', 'loggedToday', 'today'));
    }
}
