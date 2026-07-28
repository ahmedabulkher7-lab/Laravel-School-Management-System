<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\GradeLevel;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::with('gradeLevels')->latest()->paginate(20);
        return view('admin.subjects.index', compact('subjects'));
    }

    public function create()
    {
        $gradeLevels = GradeLevel::orderBy('order')->get();
        return view('admin.subjects.create', compact('gradeLevels'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'              => 'required|string|max:255',
            'name_ar'           => 'nullable|string|max:255',
            'color'             => 'required|string|max:7',
            'grade_level_ids'   => 'nullable|array',
            'grade_level_ids.*' => 'exists:grade_levels,id',
        ], [
            'name.required'  => 'اسم المادة مطلوب',
            'color.required' => 'لون المادة مطلوب',
        ]);

        $subject = Subject::create($request->only('name', 'name_ar', 'color'));
        if ($request->filled('grade_level_ids')) {
            $subject->gradeLevels()->sync($request->grade_level_ids);
        }

        return redirect()->route('admin.subjects.index')
            ->with('success', 'تم إضافة المادة الدراسية بنجاح');
    }

    public function edit(Subject $subject)
    {
        $gradeLevels = GradeLevel::orderBy('order')->get();
        return view('admin.subjects.edit', compact('subject', 'gradeLevels'));
    }

    public function update(Request $request, Subject $subject)
    {
        $request->validate([
            'name'              => 'required|string|max:255',
            'name_ar'           => 'nullable|string|max:255',
            'color'             => 'required|string|max:7',
            'grade_level_ids'   => 'nullable|array',
            'grade_level_ids.*' => 'exists:grade_levels,id',
        ]);

        $subject->update($request->only('name', 'name_ar', 'color'));
        $subject->gradeLevels()->sync($request->grade_level_ids ?? []);

        return redirect()->route('admin.subjects.index')
            ->with('success', 'تم تحديث المادة بنجاح');
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();
        return redirect()->route('admin.subjects.index')
            ->with('success', 'تم حذف المادة بنجاح');
    }
}
