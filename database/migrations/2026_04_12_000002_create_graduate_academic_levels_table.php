<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('graduate_academic_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('graduate_academic_record_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->string('name', 64);
            $table->string('academic_year', 64)->nullable();
            $table->string('level_avg', 32)->nullable();
            $table->string('total_points', 32)->nullable();
            $table->string('final_result', 64)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('graduate_academic_levels');
    }
};
