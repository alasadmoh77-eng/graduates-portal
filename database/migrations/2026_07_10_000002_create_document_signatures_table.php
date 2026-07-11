<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('issued_document_id')->constrained('issued_documents')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('role_title');
            $table->timestamp('signed_at');
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->unique(['issued_document_id', 'role_title']);
            $table->index(['issued_document_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_signatures');
    }
};
