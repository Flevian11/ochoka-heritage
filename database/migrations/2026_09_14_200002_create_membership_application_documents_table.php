<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membership_application_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('membership_application_id')->constrained()->cascadeOnDelete();
            $table->foreignId('organization_id')->constrained()->restrictOnDelete();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('document_type');
            $table->string('original_name');
            $table->string('storage_disk')->default('local');
            $table->string('storage_path');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size_bytes')->nullable();
            $table->jsonb('metadata')->nullable();
            $table->timestamps();

            $table->index(['organization_id', 'document_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membership_application_documents');
    }
};
