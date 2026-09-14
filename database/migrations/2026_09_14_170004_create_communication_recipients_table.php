<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('communication_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('communication_campaign_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->nullable()->constrained('members')->nullOnDelete();
            $table->string('destination');
            $table->string('recipient_name')->nullable();
            $table->string('status')->default('pending');
            $table->string('provider_message_id')->nullable();
            $table->dateTime('queued_at')->nullable();
            $table->dateTime('sent_at')->nullable();
            $table->dateTime('delivered_at')->nullable();
            $table->dateTime('failed_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamps();
            $table->index(['communication_campaign_id','status']);
            $table->index(['member_id','status']);
            $table->unique(['communication_campaign_id','destination']);
        });
    }
    public function down(): void { Schema::dropIfExists('communication_recipients'); }
};