<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('graduate_academic_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('graduate_academic_semester_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->string('name', 512);
            $table->string('credit_hours', 16)->nullable();
            $table->string('score', 32)->nullable();
            $table->string('rating', 64)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('graduate_academic_subjects');
    }
};
