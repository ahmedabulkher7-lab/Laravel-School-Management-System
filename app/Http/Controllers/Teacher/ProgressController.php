<?php
namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\DailyProgress;
use App\Models\Student;
use App\Services\TeacherProgressLogService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    public function log(Request $request, TeacherProgressLogService $progressLogService)
    {
        $teacher = $request->user()->teacher;
        $today = Carbon::today();

        $data = $progressLogService->getProgressLogData(
            $teacher,
            $today,
            $request->query('subject_id') ? (int) $request->query('subject_id') : null,
            $request->query('track'),
            $request->query('grade_level_id') ? (int) $request->query('grade_level_id') : null
        );

        return view('teacher.progress.log', array_merge($data, [
            'teacher' => $teacher,
        ]));
    }


    public function history()
    {
        $teacher = auth()->user()->teacher;
        $history = \App\Models\DailyProgress::where('teacher_id', $teacher?->id)
            ->with(['student', 'subject'])
            ->latest('date')
            ->paginate(25);
        return view('teacher.progress.history', compact('history'));
    }
}
