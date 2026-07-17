<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_signature_approved')->default(false)->after('signature_image');
            $table->timestamp('signature_approved_at')->nullable()->after('is_signature_approved');
            $table->foreignId('signature_approved_by')->nullable()->after('signature_approved_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('signature_approved_by');
            $table->dropColumn('is_signature_approved');
            $table->dropColumn('signature_approved_at');
        });
    }
};
