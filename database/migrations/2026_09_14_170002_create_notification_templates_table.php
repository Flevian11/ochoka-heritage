<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('communication_channel_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->string('key');
            $table->string('subject')->nullable();
            $table->text('body');
            $table->jsonb('variables')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['organization_id','key','communication_channel_id']);
            $table->index(['organization_id','is_active']);
        });
    }
    public function down(): void { Schema::dropIfExists('notification_templates'); }
};