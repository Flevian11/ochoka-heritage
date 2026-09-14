<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contribution_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->restrictOnDelete();
            $table->foreignId('member_id')->constrained()->restrictOnDelete();
            $table->decimal('amount', 12, 2);
            $table->dateTime('received_at');
            $table->string('method');
            $table->string('reference')->nullable();
            $table->string('status')->default('verified');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['organization_id', 'received_at']);
            $table->index(['member_id', 'received_at']);
            $table->index(['member_id', 'status']);
            $table->unique(['organization_id', 'reference']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contribution_payments');
    }
};
