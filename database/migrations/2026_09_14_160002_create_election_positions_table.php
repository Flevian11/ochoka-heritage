<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('election_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('election_id')->constrained()->cascadeOnDelete();
            $table->foreignId('executive_position_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('seats')->default(1);
            $table->unsignedInteger('max_votes_per_voter')->default(1);
            $table->unsignedInteger('nomination_limit')->nullable();
            $table->jsonb('eligibility_criteria')->nullable();
            $table->timestamps();

            $table->unique(['election_id', 'executive_position_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('election_positions');
    }
};