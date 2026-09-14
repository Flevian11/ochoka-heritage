<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_expenditures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('financial_fund_id')->constrained('financial_funds')->restrictOnDelete();
            $table->foreignId('ledger_transaction_id')->nullable()->constrained('financial_transactions')->nullOnDelete();
            $table->string('category');
            $table->string('description');
            $table->decimal('amount', 14, 2);
            $table->date('spent_on');
            $table->string('payee')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('reference')->nullable();
            $table->string('status')->default('draft');
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('paid_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('reversed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reversed_at')->nullable();
            $table->text('reversal_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'status']);
            $table->index(['financial_fund_id', 'spent_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_expenditures');
    }
};
