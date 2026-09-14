<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meeting_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->constrained()->restrictOnDelete();
            $table->string('role')->nullable();
            $table->boolean('is_required')->default(false);
            $table->timestamps();

            $table->unique(['meeting_id', 'member_id']);
            $table->index(['meeting_id', 'is_required']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_participants');
    }
};
