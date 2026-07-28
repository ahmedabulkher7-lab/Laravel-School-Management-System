<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GradeLevel;
use Illuminate\Http\Request;

class GradeLevelController extends Controller
{
    public function index()
    {
        $gradeLevels = GradeLevel::withCount('students')->orderBy('order')->get();
        return view('admin.grade-levels.index', compact('gradeLevels'));
    }

    public function create()
    {
        return view('admin.grade-levels.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:100|unique:grade_levels,name',
            'order' => 'required|integer|min:1',
        ], [
            'name.required' => 'اسم المرحلة مطلوب',
            'name.unique'   => 'هذه المرحلة موجودة مسبقاً',
            'order.required'=> 'الترتيب مطلوب',
        ]);

        GradeLevel::create($request->only('name', 'order'));
        return redirect()->route('admin.grade-levels.index')
            ->with('success', 'تم إضافة المرحلة الدراسية بنجاح');
    }

    public function edit(GradeLevel $gradeLevel)
    {
        return view('admin.grade-levels.edit', compact('gradeLevel'));
    }

    public function update(Request $request, GradeLevel $gradeLevel)
    {
        $request->validate([
            'name'  => 'required|string|max:100|unique:grade_levels,name,' . $gradeLevel->id,
            'order' => 'required|integer|min:1',
        ]);
        $gradeLevel->update($request->only('name', 'order'));
        return redirect()->route('admin.grade-levels.index')
            ->with('success', 'تم تحديث المرحلة بنجاح');
    }

    public function destroy(GradeLevel $gradeLevel)
    {
        $gradeLevel->delete();
        return redirect()->route('admin.grade-levels.index')
            ->with('success', 'تم حذف المرحلة بنجاح');
    }
}
