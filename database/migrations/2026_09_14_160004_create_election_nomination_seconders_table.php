<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('election_nomination_seconders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nomination_id')->constrained('election_nominations')->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('members')->restrictOnDelete();
            $table->timestamps();

            $table->unique(['nomination_id', 'member_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('election_nomination_seconders');
    }
};