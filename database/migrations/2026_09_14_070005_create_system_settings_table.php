<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('organization_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('key');
            $table->text('value')->nullable();

            $table->string('type')->default('string');

            $table->string('group')->default('general');
            $table->text('description')->nullable();

            $table->boolean('is_public')->default(false);

            $table->timestamps();

            $table->unique(['organization_id', 'key']);
            $table->index(['organization_id', 'group']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};