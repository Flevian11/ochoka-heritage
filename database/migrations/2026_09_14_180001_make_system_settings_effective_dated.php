<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->date('effective_from')->nullable()->after('is_public');
            $table->date('effective_until')->nullable()->after('effective_from');
        });

        DB::table('system_settings')->orderBy('id')->each(function ($setting) {
            DB::table('system_settings')->where('id', $setting->id)->update([
                'effective_from' => substr($setting->created_at, 0, 10),
            ]);
        });

        DB::statement('ALTER TABLE system_settings ALTER COLUMN effective_from SET NOT NULL');
        DB::statement('ALTER TABLE system_settings DROP CONSTRAINT IF EXISTS system_settings_organization_id_key_unique');

        Schema::table('system_settings', function (Blueprint $table) {
            $table->unique(['organization_id', 'key', 'effective_from']);
            $table->index(['organization_id', 'key', 'effective_from', 'effective_until']);
        });
    }

    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropUnique('system_settings_organization_id_key_effective_from_unique');
            $table->dropIndex('system_settings_organization_id_key_effective_from_effective_until_index');
            $table->dropColumn(['effective_from', 'effective_until']);
        });
        Schema::table('system_settings', function (Blueprint $table) {
            $table->unique(['organization_id', 'key']);
        });
    }
};
