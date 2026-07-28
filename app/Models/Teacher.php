<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Teacher extends Model {
    protected $fillable = ['user_id', 'subject_id', 'full_name', 'phone'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function subject(): BelongsTo { return $this->belongsTo(Subject::class); }
    public function gradeLevels(): BelongsToMany { return $this->belongsToMany(GradeLevel::class, 'teacher_grade_levels'); }
    public function assignments(): HasMany { return $this->hasMany(TeacherStudentAssignment::class); }
    public function students(): BelongsToMany {
        return $this->belongsToMany(Student::class, 'teacher_student_assignments')->withPivot('subject_id')->withTimestamps();
    }
    public function dailyProgress(): HasMany { return $this->hasMany(DailyProgress::class); }
    public function schedules(): HasMany { return $this->hasMany(Schedule::class); }
}
