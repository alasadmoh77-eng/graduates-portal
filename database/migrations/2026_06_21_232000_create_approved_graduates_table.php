<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('approved_graduates', function (Blueprint $table) {
            $table->id();
            $table->string('university_id')->unique();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('major');
            $table->integer('graduation_year');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approved_graduates');
    }
};
