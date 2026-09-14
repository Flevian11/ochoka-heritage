<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('welfare_contributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('welfare_case_id')->constrained('welfare_cases')->restrictOnDelete();
            $table->foreignId('member_id')->constrained('members')->restrictOnDelete();
            $table->decimal('amount', 14, 2);
            $table->string('source_type');
            $table->string('payment_method')->nullable();
            $table->string('reference')->nullable();
            $table->date('paid_on');
            $table->string('status')->default('pending');
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('reversed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reversed_at')->nullable();
            $table->text('reversal_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['welfare_case_id', 'status']);
            $table->index(['member_id', 'paid_on']);
            $table->index(['organization_id', 'source_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('welfare_contributions');
    }
};
