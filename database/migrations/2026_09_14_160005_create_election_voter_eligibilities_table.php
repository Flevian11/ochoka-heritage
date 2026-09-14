<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('election_voter_eligibilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('election_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('members')->restrictOnDelete();
            $table->boolean('eligible')->default(true);
            $table->jsonb('criteria_snapshot')->nullable();
            $table->dateTime('snapshotted_at');
            $table->timestamps();

            $table->unique(['election_id', 'member_id']);
            $table->index(['election_id', 'eligible']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('election_voter_eligibilities');
    }
};