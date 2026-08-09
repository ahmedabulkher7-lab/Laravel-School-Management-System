<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DailyProgress;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    public function index(Request $request)
    {
        $query = DailyProgress::with(['student', 'subject', 'teacher']);

        if ($request->filled('student_id')) $query->where('student_id', $request->student_id);
        if ($request->filled('subject_id')) $query->where('subject_id', $request->subject_id);
        if ($request->filled('teacher_id')) $query->where('teacher_id', $request->teacher_id);
        if ($request->filled('date_from'))  $query->whereDate('date', '>=', $request->date_from);
        if ($request->filled('date_to'))    $query->whereDate('date', '<=', $request->date_to);
        if ($request->filled('attendance')) $query->where('attendance_status', $request->attendance);

        $progressRecords = $query->latest('date')->paginate(25)->withQueryString();
        $students = Student::orderBy('full_name')->get();
        $subjects = Subject::orderBy('name')->get();
        $teachers = Teacher::with('subjects')->get();

        return view('admin.progress.index',
            compact('progressRecords', 'students', 'subjects', 'teachers'));
    }
}
