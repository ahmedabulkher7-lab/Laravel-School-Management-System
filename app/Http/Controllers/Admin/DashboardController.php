<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\WeeklyReport;
use App\Models\DailyProgress;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'students'   => Student::count(),
            'teachers'   => Teacher::count(),
            'reports'    => WeeklyReport::count(),
            'attendance' => $this->attendanceRate(),
        ];

        $recentProgress = DailyProgress::with(['student', 'subject', 'teacher'])
            ->latest()->limit(8)->get();

        return view('admin.dashboard', compact('stats', 'recentProgress'));
    }

    private function attendanceRate(): string
    {
        $total   = DailyProgress::count();
        $present = DailyProgress::where('attendance_status', 'present')->count();
        if ($total === 0) return '0%';
        return round(($present / $total) * 100, 1) . '%';
    }
}
