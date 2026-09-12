<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // MySQL may choose the old composite unique index to support the
        // student foreign key. Add a dedicated index before removing it.
        Schema::table('daily_progress', function (Blueprint $table) {
            $table->index('student_id', 'daily_progress_student_id_index');
        });

        if (!Schema::hasColumn('daily_progress', 'schedule_id')) {
            Schema::table('daily_progress', function (Blueprint $table) {
                $table->foreignId('schedule_id')->nullable()->after('teacher_id')
                    ->constrained('schedules')->nullOnDelete();
                $table->index(['schedule_id', 'date']);
            });
        }

        Schema::table('daily_progress', function (Blueprint $table) {
            $table->dropUnique('daily_progress_student_id_subject_id_date_unique');
            $table->unique(['student_id', 'schedule_id', 'date'], 'daily_progress_student_schedule_date_unique');
        });
    }

    public function down(): void
    {
        Schema::table('daily_progress', function (Blueprint $table) {
            $table->dropUnique('daily_progress_student_schedule_date_unique');
            $table->dropIndex(['schedule_id', 'date']);
            $table->dropConstrainedForeignId('schedule_id');
            $table->unique(['student_id', 'subject_id', 'date']);
            $table->dropIndex('daily_progress_student_id_index');
        });
    }
};
