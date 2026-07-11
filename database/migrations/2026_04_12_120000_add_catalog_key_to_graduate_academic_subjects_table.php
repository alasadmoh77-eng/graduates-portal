<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('graduate_academic_subjects', function (Blueprint $table) {
            $table->string('catalog_key', 64)->nullable()->after('sort_order');
        });
    }

    public function down(): void
    {
        Schema::table('graduate_academic_subjects', function (Blueprint $table) {
            $table->dropColumn('catalog_key');
        });
    }
};
