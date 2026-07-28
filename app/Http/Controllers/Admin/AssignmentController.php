<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\TeacherStudentAssignment;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function index()
    {
        $teachers    = Teacher::with(['subject', 'gradeLevels', 'students.gradeLevel'])->get();
        $students    = Student::with('gradeLevel')->orderBy('full_name')->get();
        $assignments = TeacherStudentAssignment::with(['teacher.subject', 'student', 'subject'])->get();
        return view('admin.assignments.index', compact('teachers', 'students', 'assignments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        TeacherStudentAssignment::firstOrCreate([
            'teacher_id' => $request->teacher_id,
            'student_id' => $request->student_id,
            'subject_id' => $request->subject_id,
        ]);

        return back()->with('success', 'تم تعيين الطالب للمعلم بنجاح');
    }

    public function destroy(TeacherStudentAssignment $assignment)
    {
        $assignment->delete();
        return back()->with('success', 'تم إلغاء التعيين بنجاح');
    }
}
