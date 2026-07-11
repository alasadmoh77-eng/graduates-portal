<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('document_type_id')->constrained('document_types');
            $table->string('tracking_code')->unique();
            $table->enum('language', ['AR', 'EN']);
            $table->string('purpose', 255);
            $table->enum('delivery_type', ['DIGITAL_PDF', 'PICKUP']);
            $table->enum('status', [
                'SUBMITTED', 'UNDER_REVIEW', 'APPROVED', 'REJECTED', 'READY', 'ISSUED'
            ])->default('SUBMITTED');
            $table->text('admin_note')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['status', 'created_at']);
            $table->index(['user_id', 'document_type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_requests');
    }
};
