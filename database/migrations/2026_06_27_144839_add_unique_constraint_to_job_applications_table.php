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
        // 1. تنظيف البيانات المكررة قبل إضافة قيد الفرادة (Deduplicate)
        // نقوم بالبحث عن التكرارات والاحتفاظ بالطلب الأقدم (الذي يمتلك أصغر ID) وحذف البقية
        $duplicates = \Illuminate\Support\Facades\DB::table('job_applications')
            ->select('job_id', 'graduate_id', \Illuminate\Support\Facades\DB::raw('MIN(id) as keep_id'))
            ->groupBy('job_id', 'graduate_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $duplicate) {
            \Illuminate\Support\Facades\DB::table('job_applications')
                ->where('job_id', $duplicate->job_id)
                ->where('graduate_id', $duplicate->graduate_id)
                ->where('id', '!=', $duplicate->keep_id)
                ->delete();
        }

        // 2. إضافة قيد الفرادة (Unique Constraint)
        Schema::table('job_applications', function (Blueprint $table) {
            $table->unique(['job_id', 'graduate_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropUnique(['job_id', 'graduate_id']);
        });
    }
};
