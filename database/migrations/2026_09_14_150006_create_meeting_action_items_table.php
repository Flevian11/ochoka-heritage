<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meeting_action_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_id')->constrained()->cascadeOnDelete();
            $table->foreignId('responsible_member_id')->nullable()->constrained('members')->nullOnDelete();
            $table->text('description');
            $table->string('status')->default('open');
            $table->date('due_date')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();

            $table->index(['meeting_id', 'status']);
            $table->index(['responsible_member_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_action_items');
    }
};
