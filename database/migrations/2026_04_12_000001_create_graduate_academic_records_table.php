<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('graduate_academic_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('student_name_ar')->nullable();
            $table->string('student_name_en')->nullable();
            $table->string('university_number', 64)->nullable();
            $table->string('degree_ar')->nullable();
            $table->string('degree_en')->nullable();
            $table->string('total_marks', 32)->nullable();
            $table->string('gpa', 32)->nullable();
            $table->string('overall_rating', 64)->nullable();
            $table->string('honors_rank', 128)->nullable();
            $table->string('graduation_year_label', 64)->nullable();
            $table->string('enrollment_year_label', 64)->nullable();
            $table->string('exam_session', 64)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('graduate_academic_records');
    }
};
