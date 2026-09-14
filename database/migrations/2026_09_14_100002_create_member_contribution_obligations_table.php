<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_contribution_obligations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->restrictOnDelete();
            $table->foreignId('member_id')->constrained()->restrictOnDelete();
            $table->foreignId('contribution_rule_id')->constrained()->restrictOnDelete();
            $table->date('period');
            $table->decimal('amount_due', 12, 2);
            $table->string('status')->default('unpaid');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['member_id', 'contribution_rule_id', 'period']);
            $table->index(['organization_id', 'period']);
            $table->index(['member_id', 'period']);
            $table->index(['member_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_contribution_obligations');
    }
};
