<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add fields to portal_jobs table
        Schema::table('portal_jobs', function (Blueprint $table) {
            $table->boolean('is_filled')->default(false)->after('status');
            $table->timestamp('filled_at')->nullable()->after('is_filled');
        });

        // 2. Safe Backfill: Mark jobs that already have 'hired' applications as filled
        try {
            $hiredJobs = DB::table('job_applications')
                ->where('status', 'hired')
                ->select('job_id', DB::raw('MAX(updated_at) as hired_at'))
                ->groupBy('job_id')
                ->get();

            foreach ($hiredJobs as $hiredJob) {
                DB::table('portal_jobs')
                    ->where('id', $hiredJob->job_id)
                    ->update([
                        'is_filled' => true,
                        'filled_at' => $hiredJob->hired_at ?? now(),
                    ]);
            }
        } catch (\Exception $e) {
            // Log warning, but do not block the migration from running
            Log::warning('Failed to backfill filled jobs: ' . $e->getMessage());
        }
    }

    public function down(): void
    {
        Schema::table('portal_jobs', function (Blueprint $table) {
            $table->dropColumn(['is_filled', 'filled_at']);
        });
    }
};
