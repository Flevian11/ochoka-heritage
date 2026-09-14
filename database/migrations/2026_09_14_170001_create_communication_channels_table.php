<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('communication_channels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code');
            $table->string('channel_type'); // email, sms, whatsapp, in_app
            $table->string('provider')->nullable();
            $table->jsonb('configuration')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['organization_id','code']);
            $table->index(['organization_id','channel_type','is_active']);
        });
    }
    public function down(): void { Schema::dropIfExists('communication_channels'); }
};