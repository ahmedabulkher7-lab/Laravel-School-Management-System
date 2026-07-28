<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeeklyReport extends Model {
    protected $fillable = ['student_id', 'week_start_date', 'week_end_date', 'file_path', 'generated_by', 'generated_at'];

    protected $casts = ['week_start_date' => 'date', 'week_end_date' => 'date', 'generated_at' => 'datetime'];

    public function student(): BelongsTo { return $this->belongsTo(Student::class); }
    public function generatedBy(): BelongsTo { return $this->belongsTo(User::class, 'generated_by'); }
}
