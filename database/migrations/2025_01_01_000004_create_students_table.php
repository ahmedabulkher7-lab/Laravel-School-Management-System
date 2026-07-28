<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('grade_level_id')->constrained();
            $table->string('full_name');
            $table->date('date_of_birth');
            $table->string('guardian_name');
            $table->string('guardian_phone', 20);
            $table->string('phone', 20)->nullable();
            $table->date('enrollment_date')->default(now());
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('students'); }
};
