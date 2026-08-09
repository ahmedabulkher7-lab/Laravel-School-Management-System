<?php
namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Student;

class StudentController extends Controller
{
    public function index()
    {
        $teacher  = auth()->user()->teacher;
        $students = $teacher
            ? Student::whereIn('grade_level_id', $teacher->gradeLevels()->select('grade_levels.id'))->with('gradeLevel')->orderBy('full_name')->get()
            : collect();
        return view('teacher.students.index', compact('students', 'teacher'));
    }
}
