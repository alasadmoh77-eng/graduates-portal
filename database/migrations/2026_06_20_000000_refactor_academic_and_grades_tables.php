<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Drop total_points from graduate_academic_levels
        Schema::table('graduate_academic_levels', function (Blueprint $table) {
            $table->dropColumn('total_points');
        });

        // 2. Create grades_certificates table
        Schema::create('grades_certificates', function (Blueprint $table) {
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

        // 3. Create grades_certificate_levels table
        Schema::create('grades_certificate_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grades_certificate_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->string('name', 64);
            $table->string('academic_year', 64)->nullable();
            $table->string('level_avg', 32)->nullable();
            $table->string('final_result', 64)->nullable();
            $table->timestamps();
        });

        // 4. Create grades_certificate_semesters table
        Schema::create('grades_certificate_semesters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grades_certificate_level_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // 5. Create grades_certificate_subjects table
        Schema::create('grades_certificate_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grades_certificate_semester_id')->constrained('grades_certificate_semesters')->cascadeOnDelete();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->string('catalog_key', 64)->nullable();
            $table->string('name', 512);
            $table->string('credit_hours', 16)->nullable();
            $table->string('score', 32)->nullable();
            $table->string('rating', 64)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grades_certificate_subjects');
        Schema::dropIfExists('grades_certificate_semesters');
        Schema::dropIfExists('grades_certificate_levels');
        Schema::dropIfExists('grades_certificates');

        Schema::table('graduate_academic_levels', function (Blueprint $table) {
            $table->string('total_points', 32)->nullable();
        });
    }
};
