<?php
namespace App\Models;

use App\Enums\AttendanceStatus;
use App\Enums\InteractionLevel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyProgress extends Model {
    protected $table = 'daily_progress';
    protected $fillable = ['student_id', 'subject_id', 'teacher_id', 'schedule_id', 'date',
        'attendance_status', 'interaction_level', 'homework_submitted', 'score', 'comment'];

    protected $casts = [
        'date' => 'date',
        'homework_submitted' => 'boolean',
        'score' => 'decimal:2',
    ];

    public function attendanceStatusEnum(): ?AttendanceStatus
    {
        return AttendanceStatus::tryFrom($this->attendance_status instanceof \BackedEnum ? $this->attendance_status->value : (string) $this->attendance_status);
    }

    public function interactionLevelEnum(): ?InteractionLevel
    {
        return InteractionLevel::tryFrom($this->interaction_level instanceof \BackedEnum ? $this->interaction_level->value : (string) $this->interaction_level);
    }

    public function student(): BelongsTo { return $this->belongsTo(Student::class); }
    public function subject(): BelongsTo { return $this->belongsTo(Subject::class); }
    public function teacher(): BelongsTo { return $this->belongsTo(Teacher::class); }
    public function schedule(): BelongsTo { return $this->belongsTo(Schedule::class); }
}
