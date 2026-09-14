<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meeting_decisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('sequence')->default(1);
            $table->text('description');
            $table->text('resolution')->nullable();
            $table->timestamps();

            $table->unique(['meeting_id', 'sequence']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_decisions');
    }
};
