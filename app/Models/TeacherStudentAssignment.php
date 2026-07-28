<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherStudentAssignment extends Model {
    protected $fillable = ['teacher_id', 'student_id', 'subject_id'];

    public function teacher(): BelongsTo { return $this->belongsTo(Teacher::class); }
    public function student(): BelongsTo { return $this->belongsTo(Student::class); }
    public function subject(): BelongsTo { return $this->belongsTo(Subject::class); }
}
