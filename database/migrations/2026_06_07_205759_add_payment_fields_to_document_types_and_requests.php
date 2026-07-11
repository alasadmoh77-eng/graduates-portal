<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_types', function (Blueprint $table) {
            $table->decimal('fee_amount', 10, 2)->nullable()->after('fee_mock');
            $table->string('currency', 3)->default('YER')->after('fee_amount');
            $table->boolean('payment_required')->default(false)->after('currency');
        });

        Schema::table('document_requests', function (Blueprint $table) {
            $table->string('payment_status', 20)->default('not_required')->after('admin_note');
            $table->string('payment_proof_path')->nullable()->after('payment_status');
            $table->foreignId('payment_reviewed_by')->nullable()->constrained('users')->nullOnDelete()->after('payment_proof_path');
            $table->timestamp('payment_reviewed_at')->nullable()->after('payment_reviewed_by');
            $table->text('payment_rejection_reason')->nullable()->after('payment_reviewed_at');
            $table->decimal('fee_amount', 10, 2)->nullable()->after('payment_rejection_reason');
            $table->string('currency', 3)->default('YER')->after('fee_amount');

            $table->index('payment_status');
        });

        // Set default fees for existing document types
        DB::table('document_types')->where('code', 'ACADEMIC_RECORD')->update([
            'fee_amount' => 2000,
            'currency' => 'YER',
            'payment_required' => true,
        ]);

        DB::table('document_types')->where('code', 'GRADES_CERTIFICATE')->update([
            'fee_amount' => 3000,
            'currency' => 'YER',
            'payment_required' => true,
        ]);

        DB::table('document_types')->whereNotIn('code', ['ACADEMIC_RECORD', 'GRADES_CERTIFICATE'])->update([
            'fee_amount' => 0,
            'currency' => 'YER',
            'payment_required' => false,
        ]);
    }

    public function down(): void
    {
        Schema::table('document_requests', function (Blueprint $table) {
            $table->dropIndex(['payment_status']);
            $table->dropColumn(['payment_status', 'payment_proof_path', 'payment_reviewed_by', 'payment_reviewed_at', 'payment_rejection_reason', 'fee_amount', 'currency']);
        });

        Schema::table('document_types', function (Blueprint $table) {
            $table->dropColumn(['fee_amount', 'currency', 'payment_required']);
        });
    }
};
