<?php
namespace App\Models;

use App\Enums\StudyTrack;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Teacher extends Model {
    protected $fillable = ['user_id', 'full_name', 'phone'];


    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function subjects(): BelongsToMany { return $this->belongsToMany(Subject::class, 'subject_teacher'); }
    public function gradeLevels(): BelongsToMany { return $this->belongsToMany(GradeLevel::class, 'teacher_grade_levels'); }
    public function dailyProgress(): HasMany { return $this->hasMany(DailyProgress::class); }
    public function schedules(): HasMany { return $this->hasMany(Schedule::class); }
    public function weeklyPlans(): HasMany { return $this->hasMany(WeeklyPlan::class); }
}
