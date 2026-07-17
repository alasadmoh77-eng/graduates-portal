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
        Schema::table('document_requests', function (Blueprint $table) {
            $table->boolean('payment_required')->default(true)->after('payment_rejection_reason');
        });

        // Sync existing requests with their document types payment status
        $types = Illuminate\Support\Facades\DB::table('document_types')->get();
        foreach ($types as $type) {
            Illuminate\Support\Facades\DB::table('document_requests')
                ->where('document_type_id', $type->id)
                ->update(['payment_required' => $type->payment_required]);
        }
    }

    public function down(): void
    {
        Schema::table('document_requests', function (Blueprint $table) {
            $table->dropColumn('payment_required');
        });
    }
};
