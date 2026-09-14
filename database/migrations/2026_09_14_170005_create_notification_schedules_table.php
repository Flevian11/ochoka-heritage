<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('notification_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('communication_channel_id')->constrained()->restrictOnDelete();
            $table->foreignId('notification_template_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->string('frequency'); // once, hourly, daily, weekly, monthly, cron
            $table->string('cron_expression')->nullable();
            $table->jsonb('audience_criteria')->nullable();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at')->nullable();
            $table->dateTime('last_run_at')->nullable();
            $table->dateTime('next_run_at')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
            $table->index(['organization_id','is_enabled','next_run_at']);
        });
    }
    public function down(): void { Schema::dropIfExists('notification_schedules'); }
};