<?php
namespace App\Livewire\Teacher;

use Livewire\Component;
use App\Models\DailyProgress;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Notifications\AllProgressLogged;
use App\Services\WeeklyReportService;
use Carbon\Carbon;

class DailyProgressLog extends Component
{
    public int    $studentId;
    public int    $teacherId;
    public string $date;
    public string $attendanceStatus  = 'present';
    public string $interactionLevel  = 'engaged';
    public bool   $homeworkSubmitted = false;
    public ?float $score             = null;
    public string $comment           = '';
    public bool   $saved             = false;
    public ?int   $existingId        = null;
    public ?int   $subjectId         = null;

    protected function rules(): array
    {
        return [
            'date'              => 'required|date',
            'attendanceStatus'  => 'required|in:present,absent,late',
            'interactionLevel'  => 'required|in:engaged,not_engaged',
            'homeworkSubmitted' => 'boolean',
            'score'             => 'nullable|numeric|min:0|max:10',
            'comment'           => 'nullable|string|max:500',
        ];
    }

    protected function messages(): array
    {
        return [
            'date.required'             => 'التاريخ مطلوب',
            'attendanceStatus.required' => 'حالة الحضور مطلوبة',
            'score.min'                 => 'الدرجة يجب أن تكون بين 0 و 10',
            'score.max'                 => 'الدرجة يجب أن تكون بين 0 و 10',
            'score.numeric'             => 'الدرجة يجب أن تكون رقماً',
        ];
    }

    public function mount(int $studentId, int $teacherId): void
    {
        $this->studentId = $studentId;
        $this->teacherId = $teacherId;
        $this->date      = Carbon::today()->toDateString();

        // Resolve the subject for this teacher based on the student's grade level subjects
        $this->resolveSubjectId();
        $this->loadExisting();
    }

    public function updatedDate(): void
    {
        $this->saved = false;
        $this->loadExisting();
    }

    /**
     * Pick the subject_id: use the first subject that both the teacher teaches
     * AND is linked to the student's grade level.
     */
    private function resolveSubjectId(): void
    {
        $teacher = Teacher::with(['subjects', 'gradeLevels'])->find($this->teacherId);
        $student = Student::with('gradeLevel.subjects')->find($this->studentId);

        if (!$teacher || !$student || !$student->gradeLevel) {
            $this->subjectId = null;
            return;
        }

        $teacherSubjectIds  = $teacher->subjects->pluck('id');
        $gradeLevelSubjectIds = $student->gradeLevel->subjects->pluck('id');

        // Intersection: subjects teacher teaches that are also in the student's grade level
        $shared = $teacherSubjectIds->intersect($gradeLevelSubjectIds);

        $this->subjectId = $shared->first();
    }

    private function loadExisting(): void
    {
        if (!$this->subjectId) {
            $this->existingId = null;
            return;
        }

        $existing = DailyProgress::where('student_id', $this->studentId)
            ->where('subject_id', $this->subjectId)
            ->whereDate('date', $this->date)
            ->first();

        if ($existing) {
            $this->existingId        = $existing->id;
            $this->attendanceStatus  = $existing->attendance_status;
            $this->interactionLevel  = $existing->interaction_level;
            $this->homeworkSubmitted = (bool) $existing->homework_submitted;
            $this->score             = $existing->score;
            $this->comment           = $existing->comment ?? '';
        } else {
            $this->existingId        = null;
            $this->attendanceStatus  = 'present';
            $this->interactionLevel  = 'engaged';
            $this->homeworkSubmitted = false;
            $this->score             = null;
            $this->comment           = '';
        }
    }

    public function save(): void
    {
        $this->validate();
        $teacher = Teacher::find($this->teacherId);

        if (!$teacher) {
            $this->addError('general', 'لم يتم العثور على بيانات المعلم.');
            return;
        }

        DailyProgress::updateOrCreate(
            [
                'student_id' => $this->studentId,
                'subject_id' => $this->subjectId,
                'date'       => $this->date,
            ],
            [
                'teacher_id'         => $teacher->id,
                'attendance_status'  => $this->attendanceStatus,
                'interaction_level'  => $this->interactionLevel,
                'homework_submitted' => $this->homeworkSubmitted,
                'score'              => $this->score,
                'comment'            => $this->comment ?: null,
            ]
        );

        $this->saved      = true;
        $this->existingId = DailyProgress::where([
            'student_id' => $this->studentId,
            'subject_id' => $this->subjectId,
        ])->whereDate('date', $this->date)->value('id');

        $weekStart = Carbon::parse($this->date)->startOfWeek(Carbon::SUNDAY)->startOfDay();
        app(WeeklyReportService::class)->generateIfReady(
            Student::findOrFail($this->studentId),
            $weekStart
        );

        $this->notifyAdminIfAllLogged($teacher);
        $this->dispatch('progress-saved', studentId: $this->studentId);
    }

    private function notifyAdminIfAllLogged(Teacher $teacher): void
    {
        // Count students in this teacher's grade levels
        $assignedCount = Student::whereIn('grade_level_id',
            $teacher->gradeLevels()->pluck('grade_levels.id')
        )->count();

        $loggedCount = DailyProgress::where('teacher_id', $teacher->id)
            ->whereDate('date', $this->date)
            ->distinct('student_id')
            ->count('student_id');

        if ($loggedCount >= $assignedCount && $assignedCount > 0) {
            foreach (User::role('admin')->get() as $admin) {
                $admin->notify(new AllProgressLogged($teacher, $this->date));
            }
        }
    }

    public function render()
    {
        $student = Student::with(['gradeLevel', 'gradeLevel.subjects'])->find($this->studentId);
        $teacher = Teacher::with('subjects')->find($this->teacherId);

        // The specific subject name for display
        $subjectName = null;
        if ($this->subjectId && $teacher) {
            $subject = $teacher->subjects->firstWhere('id', $this->subjectId);
            $subjectName = $subject?->name_ar ?? $subject?->name;
        }

        return view('livewire.teacher.daily-progress-log', compact('student', 'teacher', 'subjectName'));
    }
}
