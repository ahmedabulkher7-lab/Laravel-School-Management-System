<?php
namespace App\Http\Controllers\Admin;

use App\Enums\StudyTrack;
use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\Subject;
use App\Models\GradeLevel;
use App\Models\User;
use App\Http\Requests\StoreTeacherRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Teacher::class);
        $teachers = Teacher::with(['subjects', 'gradeLevels'])->latest()->paginate(20);
        return view('admin.teachers.index', compact('teachers'));
    }

    public function create()
    {
        $this->authorize('create', Teacher::class);
        $subjects = Subject::orderBy('name')->get();
        $gradeLevels = GradeLevel::orderBy('track')->orderBy('order')->get();
        $tracks = StudyTrack::cases();
        return view('admin.teachers.create', compact('subjects', 'gradeLevels', 'tracks'));
    }

    public function store(StoreTeacherRequest $request)
    {
        DB::transaction(function () use ($request) {
            $user = User::create([
                'name'     => $request->full_name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ]);
            $user->assignRole('teacher');

            $teacher = Teacher::create([
                'user_id'   => $user->id,
                'full_name' => $request->full_name,
                'phone'     => $request->phone,
            ]);

            $teacher->subjects()->sync($request->subject_ids);
            $teacher->gradeLevels()->sync($request->grade_level_ids);
        });

        return redirect()->route('admin.teachers.index')
            ->with('success', 'تم إضافة المعلم بنجاح');
    }

    public function edit(Teacher $teacher)
    {
        $this->authorize('update', $teacher);
        $subjects = Subject::orderBy('name')->get();
        $gradeLevels = GradeLevel::orderBy('track')->orderBy('order')->get();
        $tracks = StudyTrack::cases();
        $teacher->load(['subjects', 'gradeLevels']);
        return view('admin.teachers.edit', compact('teacher', 'subjects', 'gradeLevels', 'tracks'));
    }

    public function update(StoreTeacherRequest $request, Teacher $teacher)
    {
        $this->authorize('update', $teacher);
        DB::transaction(function () use ($request, $teacher) {
            $userUpdate = ['name' => $request->full_name, 'email' => $request->email];
            if ($request->filled('password')) {
                $userUpdate['password'] = Hash::make($request->password);
            }
            $teacher->user->update($userUpdate);

            $teacher->update([
                'full_name' => $request->full_name,
                'phone'     => $request->phone,
            ]);

            $teacher->subjects()->sync($request->subject_ids);
            $teacher->gradeLevels()->sync($request->grade_level_ids);
        });

        return redirect()->route('admin.teachers.index')
            ->with('success', 'تم تحديث بيانات المعلم بنجاح');
    }

    public function destroy(Teacher $teacher)
    {
        $this->authorize('delete', $teacher);
        $teacher->user->delete();
        return redirect()->route('admin.teachers.index')
            ->with('success', 'تم حذف المعلم بنجاح');
    }
}
