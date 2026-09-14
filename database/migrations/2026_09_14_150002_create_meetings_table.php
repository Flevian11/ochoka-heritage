<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('meeting_type_id')->constrained()->restrictOnDelete();
            $table->foreignId('organizer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at')->nullable();
            $table->string('location')->nullable();
            $table->string('format')->default('physical');
            $table->string('google_meet_url')->nullable();
            $table->text('agenda')->nullable();
            $table->string('status')->default('scheduled');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'starts_at']);
            $table->index(['organization_id', 'status']);
            $table->index(['meeting_type_id', 'starts_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meetings');
    }
};
