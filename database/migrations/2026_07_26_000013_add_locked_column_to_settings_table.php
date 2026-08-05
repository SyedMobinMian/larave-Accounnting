<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add the `locked` column required by spatie/laravel-settings v3.9+.
     *
     * The original 2026_07_24_173906_create_settings_table migration was created
     * without the `locked` boolean column, which the package's
     * DatabaseSettingsRepository::getLockedProperties() query depends on
     * (select `name` from `settings` where `group` = ? and `locked` = 1).
     */
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (!Schema::hasColumn('settings', 'locked')) {
                $table->boolean('locked')->default(false)->after('name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (Schema::hasColumn('settings', 'locked')) {
                $table->dropColumn('locked');
            }
        });
    }
};

