<?php
namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;

class StudentController extends Controller
{
    public function index()
    {
        $teacher  = auth()->user()->teacher;
        $students = $teacher?->students()->with('gradeLevel')->orderBy('full_name')->get() ?? collect();
        return view('teacher.students.index', compact('students', 'teacher'));
    }
}
