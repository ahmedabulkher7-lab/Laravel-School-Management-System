<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyProgress extends Model {
    protected $table = 'daily_progress';
    protected $fillable = ['student_id', 'subject_id', 'teacher_id', 'date',
        'attendance_status', 'interaction_level', 'homework_submitted', 'score', 'comment'];

    protected $casts = ['date' => 'date', 'homework_submitted' => 'boolean', 'score' => 'decimal:2'];

    public function student(): BelongsTo { return $this->belongsTo(Student::class); }
    public function subject(): BelongsTo { return $this->belongsTo(Subject::class); }
    public function teacher(): BelongsTo { return $this->belongsTo(Teacher::class); }
}
