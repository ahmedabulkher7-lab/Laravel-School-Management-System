<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('weekly_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained()->cascadeOnDelete();
            $table->foreignId('grade_level_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->date('week_start');
            $table->text('class_work')->nullable();
            $table->text('homework')->nullable();
            $table->text('online_games')->nullable();
            $table->timestamps();

            $table->unique(['teacher_id', 'grade_level_id', 'subject_id', 'week_start'], 'weekly_plans_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weekly_plans');
    }
};
