<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('issued_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_request_id')->unique()->constrained('document_requests')->cascadeOnDelete();
            $table->string('serial_number')->unique();
            $table->string('qr_token')->unique();
            $table->string('pdf_path');
            $table->timestamp('issued_at')->nullable();
            $table->boolean('is_valid')->default(true);
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('qr_token');
            $table->index('serial_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('issued_documents');
    }
};
