<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('executive_appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('member_id')
                ->constrained()
                ->restrictOnDelete();
            $table->foreignId('executive_position_id')
                ->constrained()
                ->restrictOnDelete();

            $table->date('starts_at');
            $table->date('ends_at')->nullable();
            $table->string('status')->default('active');

            $table->foreignId('appointed_by')->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('appointed_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->foreignId('revoked_by')->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->text('reason')->nullable();

            $table->timestamps();

            $table->index(['organization_id', 'status']);
            $table->index(['member_id', 'status']);
            $table->index(['executive_position_id', 'status']);
            $table->index(['starts_at', 'ends_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('executive_appointments');
    }
};
