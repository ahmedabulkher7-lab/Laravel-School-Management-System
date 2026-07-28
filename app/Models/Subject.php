<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Subject extends Model {
    protected $fillable = ['name', 'name_ar', 'color'];

    public function gradeLevels(): BelongsToMany { return $this->belongsToMany(GradeLevel::class, 'grade_level_subject'); }
    public function teachers(): HasMany { return $this->hasMany(Teacher::class); }
    public function dailyProgress(): HasMany { return $this->hasMany(DailyProgress::class); }
    public function schedules(): HasMany { return $this->hasMany(Schedule::class); }
}
