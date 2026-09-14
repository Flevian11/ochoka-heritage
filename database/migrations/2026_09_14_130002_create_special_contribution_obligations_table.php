<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('special_contribution_obligations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('special_contribution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->constrained()->restrictOnDelete();
            $table->decimal('amount', 14, 2);
            $table->decimal('paid_amount', 14, 2)->default(0);
            $table->string('status')->default('pending');
            $table->timestamp('waived_at')->nullable();
            $table->foreignId('waived_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('waiver_reason')->nullable();
            $table->timestamps();

            $table->unique(['special_contribution_id', 'member_id']);
            $table->index(['member_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('special_contribution_obligations');
    }
};
