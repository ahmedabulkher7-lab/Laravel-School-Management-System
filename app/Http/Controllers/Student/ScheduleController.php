<?php
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Schedule;

class ScheduleController extends Controller
{
    public function index()
    {
        $student   = auth()->user()->student;
        $schedules = Schedule::where('grade_level_id', $student?->grade_level_id)
            ->with(['subject', 'teacher'])
            ->orderBy('day_of_week')->orderBy('start_time')
            ->get()->groupBy('day_of_week');
        return view('student.schedule', compact('student', 'schedules'));
    }
}
