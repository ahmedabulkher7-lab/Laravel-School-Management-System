<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StudyTrack;
use App\Http\Controllers\Controller;
use App\Models\DailyProgress;
use App\Models\GradeLevel;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TeacherEvaluationStatusController extends Controller
{
    public function index(\App\Http\Requests\Admin\FilterTeacherEvaluationStatusRequest $request): View
    {
        $filters = $request->validated();

        $date = Carbon::parse($filters['date'] ?? now())->startOfDay();
        $track = $filters['track'] ?? null;
        $gradeLevelId = $filters['grade_level_id'] ?? null;
        $teacherId = $filters['teacher_id'] ?? null;
        $subjectId = $filters['subject_id'] ?? null;
        $studentId = $filters['student_id'] ?? null;
        $attendance = $filters['attendance'] ?? null;
        $interaction = $filters['interaction'] ?? null;
        $status = $filters['status'] ?? 'all';

        $actionResult = app(\App\Actions\Evaluations\FilterTeacherEvaluationStatusAction::class)->execute($filters);

        $teacherStatuses = $actionResult['teacherStatuses'];
        $filterTeachers = $actionResult['teachers'];
        $filterGradeLevels = $actionResult['gradeLevels'];
        $filterSubjects = $actionResult['subjects'];
        $filterStudents = $actionResult['students'];
        $tracks = StudyTrack::cases();

        $overview = [
            'teachers' => $teacherStatuses->count(),
            'complete' => $teacherStatuses->where('completion_status', 'complete')->count(),
            'incomplete' => $teacherStatuses->filter(fn (array $item) => $item['completion_status'] !== 'complete')->count(),
            'remaining_students' => $teacherStatuses->sum('pending_students_count'),
        ];

        return view('admin.teacher-evaluation-status.index', compact(
            'date',
            'track',
            'gradeLevelId',
            'teacherId',
            'subjectId',
            'studentId',
            'attendance',
            'interaction',
            'status',
            'teacherStatuses',
            'overview',
            'filterTeachers',
            'filterGradeLevels',
            'filterSubjects',
            'filterStudents',
            'tracks',
        ));
    }
}
