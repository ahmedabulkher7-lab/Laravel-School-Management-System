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

    public function store(\App\Http\Requests\Admin\StoreSubjectRequest $request)
    {
        $validated = $request->validated();
        $subject = Subject::create($validated);
        if (!empty($validated['grade_level_ids'])) {
            $subject->gradeLevels()->sync($validated['grade_level_ids']);
        }

        return redirect()->route('admin.subjects.index')
            ->with('success', 'تم إضافة المادة الدراسية بنجاح');
    }

    public function edit(Subject $subject)
    {
        $gradeLevels = GradeLevel::orderBy('order')->get();
        return view('admin.subjects.edit', compact('subject', 'gradeLevels'));
    }

    public function update(\App\Http\Requests\Admin\StoreSubjectRequest $request, Subject $subject)
    {
        $validated = $request->validated();
        $subject->update($validated);
        $subject->gradeLevels()->sync($validated['grade_level_ids'] ?? []);

        return redirect()->route('admin.subjects.index')
            ->with('success', 'تم تحديث المادة الدراسية بنجاح');
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();
        return redirect()->route('admin.subjects.index')
            ->with('success', 'تم حذف المادة بنجاح');
    }
}
