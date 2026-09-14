<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('communication_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->restrictOnDelete();
            $table->foreignId('communication_channel_id')->constrained()->restrictOnDelete();
            $table->foreignId('notification_template_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('campaign_type')->default('announcement');
            $table->string('status')->default('draft');
            $table->string('subject')->nullable();
            $table->text('body')->nullable();
            $table->jsonb('audience_criteria')->nullable();
            $table->dateTime('scheduled_at')->nullable();
            $table->dateTime('sent_at')->nullable();
            $table->timestamps();
            $table->index(['organization_id','status']);
            $table->index(['organization_id','scheduled_at']);
        });
    }
    public function down(): void { Schema::dropIfExists('communication_campaigns'); }
};