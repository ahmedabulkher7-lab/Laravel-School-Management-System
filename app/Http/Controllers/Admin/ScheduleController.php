<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveScheduleGridRequest;
use App\Models\Schedule;
use App\Models\GradeLevel;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $track = $request->query('track');
        $gradeLevels = GradeLevel::query()->orderBy('track')->orderBy('order')->get();
        $selectedGrade = $gradeLevels->firstWhere('id', (int) $request->query('grade_level_id'));
        if ($selectedGrade && $track && $track !== 'all' && $selectedGrade->track->value !== $track) {
            $selectedGrade = null;
        }

        $subjects = $selectedGrade ? $selectedGrade->subjects()->orderBy('name')->get() : collect();
        $teacherOptions = $subjects->mapWithKeys(function (Subject $subject) use ($selectedGrade): array {
            $teachers = Teacher::query()
                ->whereHas('subjects', fn ($query) => $query->whereKey($subject->id))
                ->whereHas('gradeLevels', fn ($query) => $query->whereKey($selectedGrade->id))
                ->orderBy('full_name')->get(['id', 'full_name']);

            return [$subject->id => $teachers];
        });
        $slots = $selectedGrade
            ? $this->timeSlotsFor($selectedGrade)
            : collect();
        $scheduleGrid = $selectedGrade
            ? Schedule::query()->where('grade_level_id', $selectedGrade->id)->get()
                ->keyBy(fn (Schedule $schedule) => $schedule->day_of_week . '-' . $schedule->start_time->format('H:i') . '-' . $schedule->end_time->format('H:i'))
            : collect();

        return view('admin.schedules.index', compact('track', 'gradeLevels', 'selectedGrade', 'subjects', 'teacherOptions', 'slots', 'scheduleGrid'));
    }

    public function saveGrid(SaveScheduleGridRequest $request, \App\Actions\Schedules\SaveScheduleGridAction $action)
    {
        $validated = $request->validated();
        $gradeLevel = GradeLevel::with(['subjects:id', 'teachers.subjects'])->findOrFail($validated['grade_level_id']);

        $action->execute(
            gradeLevel: $gradeLevel,
            slots: $validated['slots'],
            cells: $validated['cells'] ?? []
        );

        return redirect()->route('admin.schedules.index', [
            'track' => $gradeLevel->track->value,
            'grade_level_id' => $gradeLevel->id,
        ])->with('success', 'تم حفظ جدول الحصص للصف بنجاح.');
    }

    private function timeSlotsFor(GradeLevel $gradeLevel)
    {
        $saved = Schedule::where('grade_level_id', $gradeLevel->id)->orderBy('start_time')->get(['start_time', 'end_time'])
            ->map(fn (Schedule $schedule) => ['start_time' => $schedule->start_time->format('H:i'), 'end_time' => $schedule->end_time->format('H:i')])
            ->unique(fn (array $slot) => $slot['start_time'] . $slot['end_time'])->values();

        return $saved->isNotEmpty() ? $saved : collect([
            ['start_time' => '08:00', 'end_time' => '08:50'], ['start_time' => '08:50', 'end_time' => '09:40'],
            ['start_time' => '09:40', 'end_time' => '10:30'], ['start_time' => '10:30', 'end_time' => '11:20'],
            ['start_time' => '11:20', 'end_time' => '12:10'], ['start_time' => '12:10', 'end_time' => '13:00'],
        ]);
    }

    public function create()
    {
        $gradeLevels = GradeLevel::orderBy('order')->get();
        $subjects    = Subject::orderBy('name')->get();
        $teachers    = Teacher::with('subjects')->get();
        return view('admin.schedules.create', compact('gradeLevels', 'subjects', 'teachers'));
    }

    public function store(\App\Http\Requests\Admin\StoreScheduleRequest $request)
    {
        Schedule::create($request->validated());
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

    public function update(\App\Http\Requests\Admin\StoreScheduleRequest $request, Schedule $schedule)
    {
        $schedule->update($request->validated());
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
