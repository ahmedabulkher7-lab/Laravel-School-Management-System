<?php
namespace App\Models;

use App\Enums\StudyTrack;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class Student extends Model {
    protected $fillable = ['user_id', 'grade_level_id', 'full_name', 'date_of_birth',
        'guardian_name', 'guardian_phone', 'phone', 'enrollment_date', 'track'];

    protected $casts = ['date_of_birth' => 'date', 'enrollment_date' => 'date', 'track' => StudyTrack::class];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function gradeLevel(): BelongsTo { return $this->belongsTo(GradeLevel::class); }
    public function dailyProgress(): HasMany { return $this->hasMany(DailyProgress::class); }
    public function weeklyReports(): HasMany { return $this->hasMany(WeeklyReport::class); }

    public function getSubjectsAttribute(): Collection
    {
        return $this->gradeLevel?->subjects ?? collect();
    }

    // Computed age accessor
    public function getAgeAttribute(): int {
        return Carbon::parse($this->date_of_birth)->age;
    }
}
