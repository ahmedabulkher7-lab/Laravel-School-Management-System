<?php
namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\DailyProgress;
use App\Models\Student;
use App\Models\GradeLevel;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $teacher = auth()->user()->teacher;
        $students = $teacher
            ? Student::whereIn('grade_level_id', $teacher->gradeLevels()->select('grade_levels.id'))->with('gradeLevel')->get()
            : collect();
        $today    = Carbon::today()->toDateString();

        $loggedToday = DailyProgress::where('teacher_id', $teacher?->id)
            ->whereDate('date', $today)->pluck('student_id')->toArray();

        $pendingStudents = $students->whereNotIn('id', $loggedToday);

        // Group pending students by grade level for better overview
        $pendingByGradeLevel = $pendingStudents->groupBy(fn($s) => $s->gradeLevel?->name ?? 'غير محدد');
        $weeklyPlanReminder = $teacher?->user?->unreadNotifications()
            ->where('data->type', 'weekly_plan_reminder')
            ->latest()
            ->first();

        return view('teacher.dashboard',
            compact('teacher', 'students', 'pendingStudents', 'pendingByGradeLevel', 'loggedToday', 'today', 'weeklyPlanReminder'));
    }
}
