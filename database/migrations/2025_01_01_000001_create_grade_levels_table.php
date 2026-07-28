<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('grade_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name');           // KG1, KG2, Grade 1 ... Grade 11
            $table->unsignedTinyInteger('order')->default(0);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('grade_levels'); }
};
