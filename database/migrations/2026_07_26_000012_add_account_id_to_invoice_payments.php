<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('invoice_payments') && !Schema::hasColumn('invoice_payments', 'account_id')) {
            Schema::table('invoice_payments', function (Blueprint $table) {
                $table->foreignId('account_id')->nullable()->after('invoice_id')->constrained('accounts')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('invoice_payments') && Schema::hasColumn('invoice_payments', 'account_id')) {
            Schema::table('invoice_payments', function (Blueprint $table) {
                $table->dropConstrainedForeignId('account_id');
            });
        }
    }
};

