<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model {
    protected $fillable = ['grade_level_id', 'subject_id', 'teacher_id', 'day_of_week', 'start_time', 'end_time'];

    protected $casts = ['start_time' => 'datetime:H:i', 'end_time' => 'datetime:H:i'];

    public function gradeLevel(): BelongsTo { return $this->belongsTo(GradeLevel::class); }
    public function subject(): BelongsTo { return $this->belongsTo(Subject::class); }
    public function teacher(): BelongsTo { return $this->belongsTo(Teacher::class); }

    public function getDayNameAttribute(): string {
        $days = ['الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'];
        return $days[$this->day_of_week] ?? '';
    }
}
