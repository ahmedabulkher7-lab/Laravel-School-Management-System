<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('daily_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->enum('attendance_status', ['present', 'absent', 'late']);
            $table->enum('interaction_level', ['engaged', 'not_engaged']);
            $table->boolean('homework_submitted')->default(false);
            $table->decimal('score', 4, 2)->nullable(); // 0.00 - 10.00
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->unique(['student_id', 'subject_id', 'date']); // prevent duplicates
        });
    }
    public function down(): void { Schema::dropIfExists('daily_progress'); }
};
