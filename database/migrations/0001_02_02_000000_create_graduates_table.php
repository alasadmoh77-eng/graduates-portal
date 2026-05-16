<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('graduates', function (Blueprint $table) {
            $table->foreignId('user_id')->primary()->constrained('users')->cascadeOnDelete();
            $table->string('university_id')->unique();
            $table->string('phone')->nullable();
            $table->foreignId('major_id')->constrained('majors');
            $table->year('graduation_year');
            $table->string('photo')->nullable();
            $table->string('cv_path')->nullable();
            $table->timestamps();
            
            $table->index('university_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('graduates');
    }
};
