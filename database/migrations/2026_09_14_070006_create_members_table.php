<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();

            $table->foreignId('organization_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->unique()
                ->constrained()
                ->nullOnDelete();

            $table->string('membership_number')->unique();

            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');

            $table->string('phone')->nullable();
            $table->string('alternate_phone')->nullable();
            $table->string('email')->nullable();

            $table->date('date_of_birth')->nullable();

            $table->string('national_id')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('county')->nullable();

            $table->date('joined_at')->nullable();

            $table->string('status')->default('active');

            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'status']);
            $table->index(['organization_id', 'last_name']);
            $table->index(['organization_id', 'phone']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};