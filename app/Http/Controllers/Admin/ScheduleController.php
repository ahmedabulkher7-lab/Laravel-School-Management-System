<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\GradeLevel;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index()
    {
        $schedules   = Schedule::with(['gradeLevel', 'subject', 'teacher'])
            ->orderBy('grade_level_id')->orderBy('day_of_week')->orderBy('start_time')->get();
        $gradeLevels = GradeLevel::orderBy('order')->get();
        return view('admin.schedules.index', compact('schedules', 'gradeLevels'));
    }

    public function create()
    {
        $gradeLevels = GradeLevel::orderBy('order')->get();
        $subjects    = Subject::orderBy('name')->get();
        $teachers    = Teacher::with('subjects')->get();
        return view('admin.schedules.create', compact('gradeLevels', 'subjects', 'teachers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'grade_level_id' => 'required|exists:grade_levels,id',
            'subject_id'     => 'required|exists:subjects,id',
            'teacher_id'     => 'required|exists:teachers,id',
            'day_of_week'    => 'required|integer|between:0,6',
            'start_time'     => 'required|date_format:H:i',
            'end_time'       => 'required|date_format:H:i|after:start_time',
        ], [
            'end_time.after' => 'وقت النهاية يجب أن يكون بعد وقت البداية',
        ]);
        Schedule::create($request->all());
        return redirect()->route('admin.schedules.index')
            ->with('success', 'تم إضافة الحصة بنجاح');
    }

    public function edit(Schedule $schedule)
    {
        $gradeLevels = GradeLevel::orderBy('order')->get();
        $subjects    = Subject::orderBy('name')->get();
        $teachers    = Teacher::with('subjects')->get();
        return view('admin.schedules.edit', compact('schedule', 'gradeLevels', 'subjects', 'teachers'));
    }

    public function update(Request $request, Schedule $schedule)
    {
        $request->validate([
            'grade_level_id' => 'required|exists:grade_levels,id',
            'subject_id'     => 'required|exists:subjects,id',
            'teacher_id'     => 'required|exists:teachers,id',
            'day_of_week'    => 'required|integer|between:0,6',
            'start_time'     => 'required|date_format:H:i',
            'end_time'       => 'required|date_format:H:i|after:start_time',
        ]);
        $schedule->update($request->all());
        return redirect()->route('admin.schedules.index')
            ->with('success', 'تم تحديث الحصة بنجاح');
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();
        return redirect()->route('admin.schedules.index')
            ->with('success', 'تم حذف الحصة بنجاح');
    }
}
