<?php
namespace App\Livewire\Teacher;

use Livewire\Component;
use App\Models\DailyProgress;
use App\Models\Student;
use App\Models\User;
use App\Notifications\AllProgressLogged;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;

class DailyProgressLog extends Component
{
    public int    $studentId;
    public string $date;
    public string $attendanceStatus  = 'present';
    public string $interactionLevel  = 'engaged';
    public bool   $homeworkSubmitted = false;
    public ?float $score             = null;
    public string $comment           = '';
    public bool   $saved             = false;
    public ?int   $existingId        = null;

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

    public function mount(int $studentId): void
    {
        $this->studentId = $studentId;
        $this->date      = Carbon::today()->toDateString();
        $this->loadExisting();
    }

    public function updatedDate(): void
    {
        $this->saved = false;
        $this->loadExisting();
    }

    private function loadExisting(): void
    {
        $teacher  = auth()->user()->teacher;
        $existing = DailyProgress::where('student_id', $this->studentId)
            ->where('subject_id', $teacher?->subject_id)
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
        $teacher = auth()->user()->teacher;

        DailyProgress::updateOrCreate(
            [
                'student_id' => $this->studentId,
                'subject_id' => $teacher->subject_id,
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
            'subject_id' => $teacher->subject_id,
        ])->whereDate('date', $this->date)->value('id');

        $this->notifyAdminIfAllLogged($teacher);
        $this->dispatch('progress-saved', studentId: $this->studentId);
    }

    private function notifyAdminIfAllLogged($teacher): void
    {
        $assignedCount = $teacher->students()->count();
        $loggedCount   = DailyProgress::where('teacher_id', $teacher->id)
            ->whereDate('date', $this->date)->count();

        if ($loggedCount >= $assignedCount && $assignedCount > 0) {
            foreach (User::role('admin')->get() as $admin) {
                $admin->notify(new AllProgressLogged($teacher, $this->date));
            }
        }
    }

    public function render()
    {
        $student = Student::with('gradeLevel')->find($this->studentId);
        return view('livewire.teacher.daily-progress-log', compact('student'));
    }
}
