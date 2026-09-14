<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->restrictOnDelete();
            $table->foreignId('member_id')->constrained()->restrictOnDelete();
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->text('reason');
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestampTz('changed_at')->useCurrent();
            $table->timestamps();

            $table->index(['organization_id', 'member_id', 'changed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_status_histories');
    }
};
