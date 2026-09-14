<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('election_ballots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('election_id')->constrained()->restrictOnDelete();
            $table->foreignId('voter_member_id')->constrained('members')->restrictOnDelete();
            $table->string('ballot_token')->unique();
            $table->string('status')->default('cast');
            $table->dateTime('cast_at');
            $table->dateTime('sealed_at')->nullable();
            $table->timestamps();

            $table->unique(['election_id', 'voter_member_id']);
            $table->index(['election_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('election_ballots');
    }
};