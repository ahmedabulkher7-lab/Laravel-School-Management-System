<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\GradeLevel;
use App\Models\User;
use App\Http\Requests\StoreStudentRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Student::class);
        $students = Student::with('gradeLevel')->latest()->paginate(20);
        return view('admin.students.index', compact('students'));
    }

    public function create()
    {
        $this->authorize('create', Student::class);
        $gradeLevels = GradeLevel::orderBy('order')->get();
        return view('admin.students.create', compact('gradeLevels'));
    }

    public function store(StoreStudentRequest $request)
    {
        DB::transaction(function () use ($request) {
            $user = User::create([
                'name'     => $request->full_name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ]);
            $user->assignRole('student');

            Student::create([
                'user_id'         => $user->id,
                'full_name'       => $request->full_name,
                'date_of_birth'   => $request->date_of_birth,
                'grade_level_id'  => $request->grade_level_id,
                'guardian_name'   => $request->guardian_name,
                'guardian_phone'  => $request->guardian_phone,
                'phone'           => $request->phone,
                'enrollment_date' => $request->enrollment_date,
            ]);
        });

        return redirect()->route('admin.students.index')
            ->with('success', 'تم إضافة الطالب بنجاح');
    }

    public function show(Student $student)
    {
        $this->authorize('view', $student);
        $student->load(['gradeLevel', 'assignments.teacher', 'assignments.subject',
                        'dailyProgress' => fn($q) => $q->with('subject')->latest('date')->limit(30),
                        'weeklyReports' => fn($q) => $q->latest('generated_at')]);
        return view('admin.students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        $this->authorize('update', $student);
        $gradeLevels = GradeLevel::orderBy('order')->get();
        return view('admin.students.edit', compact('student', 'gradeLevels'));
    }

    public function update(StoreStudentRequest $request, Student $student)
    {
        $this->authorize('update', $student);
        DB::transaction(function () use ($request, $student) {
            $userUpdate = ['name' => $request->full_name, 'email' => $request->email];
            if ($request->filled('password')) {
                $userUpdate['password'] = Hash::make($request->password);
            }
            $student->user->update($userUpdate);

            $student->update([
                'full_name'       => $request->full_name,
                'date_of_birth'   => $request->date_of_birth,
                'grade_level_id'  => $request->grade_level_id,
                'guardian_name'   => $request->guardian_name,
                'guardian_phone'  => $request->guardian_phone,
                'phone'           => $request->phone,
                'enrollment_date' => $request->enrollment_date,
            ]);
        });

        return redirect()->route('admin.students.index')
            ->with('success', 'تم تحديث بيانات الطالب بنجاح');
    }

    public function destroy(Student $student)
    {
        $this->authorize('delete', $student);
        $student->user->delete(); // cascades to student record
        return redirect()->route('admin.students.index')
            ->with('success', 'تم حذف الطالب بنجاح');
    }
}
