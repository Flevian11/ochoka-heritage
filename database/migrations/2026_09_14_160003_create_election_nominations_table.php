<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('election_nominations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('election_position_id')->constrained()->cascadeOnDelete();
            $table->foreignId('candidate_member_id')->constrained('members')->restrictOnDelete();
            $table->foreignId('nominated_by')->nullable()->constrained('members')->nullOnDelete();
            $table->string('status')->default('pending');
            $table->text('statement')->nullable();
            $table->jsonb('eligibility_snapshot')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('verified_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->unique(['election_position_id', 'candidate_member_id']);
            $table->index(['election_position_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('election_nominations');
    }
};