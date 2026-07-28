<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class GradeLevel extends Model {
    protected $fillable = ['name', 'order'];

    public function students(): HasMany { return $this->hasMany(Student::class); }
    public function subjects(): BelongsToMany { return $this->belongsToMany(Subject::class, 'grade_level_subject'); }
    public function teachers(): BelongsToMany { return $this->belongsToMany(Teacher::class, 'teacher_grade_levels'); }
    public function schedules(): HasMany { return $this->hasMany(Schedule::class); }
}
