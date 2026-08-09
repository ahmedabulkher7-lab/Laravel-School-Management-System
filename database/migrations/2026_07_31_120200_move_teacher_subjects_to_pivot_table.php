<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        DB::table('teachers')
            ->select(['id', 'subject_id'])
            ->whereNotNull('subject_id')
            ->orderBy('id')
            ->each(function (object $teacher): void {
                DB::table('subject_teacher')->insertOrIgnore([
                    'subject_id' => $teacher->subject_id,
                    'teacher_id' => $teacher->id,
                ]);
            });

        Schema::table('teachers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('subject_id');
        });
    }

    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->foreignId('subject_id')->nullable()->constrained();
        });

        DB::table('teachers')->orderBy('id')->each(function (object $teacher): void {
            $subjectId = DB::table('subject_teacher')
                ->where('teacher_id', $teacher->id)
                ->orderBy('subject_id')
                ->value('subject_id');

            DB::table('teachers')->where('id', $teacher->id)->update(['subject_id' => $subjectId]);
        });
    }
};
