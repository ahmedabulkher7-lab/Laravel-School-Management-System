<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;

class Student extends Model {
    protected $fillable = ['user_id', 'grade_level_id', 'full_name', 'date_of_birth',
        'guardian_name', 'guardian_phone', 'phone', 'enrollment_date'];

    protected $casts = ['date_of_birth' => 'date', 'enrollment_date' => 'date'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function gradeLevel(): BelongsTo { return $this->belongsTo(GradeLevel::class); }
    public function dailyProgress(): HasMany { return $this->hasMany(DailyProgress::class); }
    public function weeklyReports(): HasMany { return $this->hasMany(WeeklyReport::class); }
    public function assignments(): HasMany { return $this->hasMany(TeacherStudentAssignment::class); }
    public function teachers(): BelongsToMany {
        return $this->belongsToMany(Teacher::class, 'teacher_student_assignments')->withPivot('subject_id')->withTimestamps();
    }

    // Computed age accessor
    public function getAgeAttribute(): int {
        return Carbon::parse($this->date_of_birth)->age;
    }
}
