<?php
namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Services\TeacherDashboardService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request, TeacherDashboardService $dashboardService): View
    {
        $teacher = $request->user()->teacher;
        $today = Carbon::today();

        $dashboardData = $dashboardService->getDailyDashboardData($teacher, $today);

        return view('teacher.dashboard', array_merge($dashboardData, [
            'teacher' => $teacher,
            'today' => $today->toDateString(),
        ]));
    }
}

