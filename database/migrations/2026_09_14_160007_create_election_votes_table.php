<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('election_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ballot_id')->constrained('election_ballots')->restrictOnDelete();
            $table->foreignId('election_position_id')->constrained()->restrictOnDelete();
            $table->foreignId('candidate_member_id')->constrained('members')->restrictOnDelete();
            $table->unsignedInteger('selection_order')->nullable();
            $table->timestamps();

            $table->unique(['ballot_id', 'election_position_id', 'candidate_member_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('election_votes');
    }
};