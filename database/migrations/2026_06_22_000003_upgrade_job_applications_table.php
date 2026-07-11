<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            // Extended pipeline: new → shortlisted → interviewed → hired / rejected
            // status column already exists as string('new'), we just add new columns
            $table->text('employer_notes')->nullable()->after('status');
            $table->datetime('interview_date')->nullable()->after('employer_notes');
            $table->text('interview_notes')->nullable()->after('interview_date');
        });
    }

    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropColumn(['employer_notes', 'interview_date', 'interview_notes']);
        });
    }
};
