<?php
namespace App\Http\Controllers\Admin;

use App\Enums\StudyTrack;
use App\Http\Controllers\Controller;
use App\Models\GradeLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class GradeLevelController extends Controller
{
    public function index()
    {
        $gradeLevels = GradeLevel::withCount('students')->orderBy('track')->orderBy('order')->get();
        return view('admin.grade-levels.index', compact('gradeLevels'));
    }

    public function create()
    {
        $tracks = StudyTrack::cases();

        return view('admin.grade-levels.create', compact('tracks'));
    }

    public function store(\App\Http\Requests\StoreGradeLevelRequest $request)
    {
        GradeLevel::create($request->validated());
        return redirect()->route('admin.grade-levels.index')
            ->with('success', 'تم إضافة المرحلة الدراسية بنجاح');
    }

    public function edit(GradeLevel $gradeLevel)
    {
        $tracks = StudyTrack::cases();

        return view('admin.grade-levels.edit', compact('gradeLevel', 'tracks'));
    }

    public function update(\App\Http\Requests\StoreGradeLevelRequest $request, GradeLevel $gradeLevel)
    {
        DB::transaction(function () use ($request, $gradeLevel): void {
            $gradeLevel->update($request->validated());

            // A shared grade can contain Arabic and Languages students, so their
            // individual tracks must remain unchanged when it becomes "Both".
            if ($request->input('track') !== StudyTrack::Both->value) {
                $gradeLevel->students()->update(['track' => $request->input('track')]);
            }
        });
        return redirect()->route('admin.grade-levels.index')
            ->with('success', 'تم تحديث المرحلة بنجاح');
    }

    public function destroy(GradeLevel $gradeLevel)
    {
        if ($gradeLevel->students()->exists()) {
            return redirect()->route('admin.grade-levels.index')
                ->with('warning', 'لا يمكن حذف هذا الصف لأنه مرتبط بطلاب. انقل الطلاب إلى صف آخر أولاً.');
        }

        $gradeLevel->delete();
        return redirect()->route('admin.grade-levels.index')
            ->with('success', 'تم حذف المرحلة بنجاح');
    }
}
