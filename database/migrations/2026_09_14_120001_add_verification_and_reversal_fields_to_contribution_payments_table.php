<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contribution_payments', function (Blueprint $table) {
            $table->foreignId('verified_by')->nullable()->after('status')->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable()->after('verified_by');
            $table->foreignId('reversed_by')->nullable()->after('verified_at')->constrained('users')->nullOnDelete();
            $table->timestamp('reversed_at')->nullable()->after('reversed_by');
            $table->text('reversal_reason')->nullable()->after('reversed_at');

            $table->index(['organization_id', 'status', 'received_at']);
        });
    }

    public function down(): void
    {
        Schema::table('contribution_payments', function (Blueprint $table) {
            $table->dropIndex(['organization_id', 'status', 'received_at']);
            $table->dropConstrainedForeignId('verified_by');
            $table->dropColumn('verified_at');
            $table->dropConstrainedForeignId('reversed_by');
            $table->dropColumn(['reversed_at', 'reversal_reason']);
        });
    }
};
