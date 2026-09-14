<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contribution_payment_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contribution_payment_id')->constrained('contribution_payments')->restrictOnDelete();
            $table->foreignId('member_contribution_obligation_id')->constrained('member_contribution_obligations')->restrictOnDelete();
            $table->decimal('amount', 12, 2);
            $table->timestamps();

            $table->unique(['contribution_payment_id', 'member_contribution_obligation_id']);
            $table->index('member_contribution_obligation_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contribution_payment_allocations');
    }
};
