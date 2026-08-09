<?php
namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\GradeLevel;
use App\Enums\StudyTrack;
use Carbon\Carbon;

class ProgressController extends Controller
{
    public function log()
    {
        $teacher = auth()->user()->teacher;

        if (!$teacher) {
            return view('teacher.progress.log', [
                'teacher'      => null,
                'tracks'       => [],
                'gradeLevels'  => collect(),
                'students'     => collect(),
                'selectedTrack' => null,
                'selectedGradeLevel' => null,
                'today'        => Carbon::today()->toDateString(),
            ]);
        }

        // Get all tracks this teacher is assigned to (derived from grade levels)
        $teacherGradeLevelIds = $teacher->gradeLevels()->pluck('grade_levels.id');

        // Get grade levels grouped by track
        $gradeLevels = GradeLevel::whereIn('id', $teacherGradeLevelIds)
            ->orderBy('track')
            ->orderBy('order')
            ->get();

        // Get unique tracks the teacher teaches in
        $tracks = $gradeLevels->pluck('track')->unique()->values();

        // Filter by selected track/grade level from query string
        $selectedTrack = request('track');
        $selectedGradeLevelId = request('grade_level_id');

        // Grade levels filtered by selected track
        $filteredGradeLevels = $selectedTrack
            ? $gradeLevels->where('track', $selectedTrack)->values()
            : collect();

        // Students filtered by selected grade level
        $students = collect();
        $selectedGradeLevel = null;
        if ($selectedGradeLevelId) {
            $selectedGradeLevel = GradeLevel::find($selectedGradeLevelId);
            $students = Student::where('grade_level_id', $selectedGradeLevelId)
                ->with('gradeLevel')
                ->orderBy('full_name')
                ->get();
        }

        $today = Carbon::today()->toDateString();

        return view('teacher.progress.log', compact(
            'teacher',
            'tracks',
            'gradeLevels',
            'filteredGradeLevels',
            'students',
            'selectedTrack',
            'selectedGradeLevel',
            'today'
        ));
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
