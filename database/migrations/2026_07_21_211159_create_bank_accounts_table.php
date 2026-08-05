<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The bank_accounts table already exists via 2024_07_29_100001_create_bank_accounts_table
        // (columns: name, bank_name, account_number, currency, opening_balance, is_active).
        // This was previously a duplicate CREATE that would fail.
        // Instead, add the optional banking fields used by the app.

        if (Schema::hasTable('bank_accounts')) {
            Schema::table('bank_accounts', function (Blueprint $table) {
                if (!Schema::hasColumn('bank_accounts', 'ifsc_code')) {
                    $table->string('ifsc_code')->nullable()->after('account_number');
                }
                if (!Schema::hasColumn('bank_accounts', 'branch')) {
                    $table->string('branch')->nullable()->after('ifsc_code');
                }
                if (!Schema::hasColumn('bank_accounts', 'upi_id')) {
                    $table->string('upi_id')->nullable()->after('branch');
                }
                if (!Schema::hasColumn('bank_accounts', 'is_default')) {
                    $table->boolean('is_default')->default(false)->after('is_active');
                }
            });
        }
    }

    public function down(): void
    {
        // Intentionally left no-op: dropping columns would be destructive and this
        // migration only synchronizes schema for the existing table.
    }
};

