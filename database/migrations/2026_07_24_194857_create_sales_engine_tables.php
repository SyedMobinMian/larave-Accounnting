<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // The estimates table was already created by 2026_07_25_000001_create_estimates_table
        // (which includes notes + terms columns). This migration only ensures the
        // estimate_items table and the invoices.estimate_id link exist.

        // Create estimate_items table if it doesn't exist
        if (!Schema::hasTable('estimate_items')) {
            Schema::create('estimate_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('estimate_id')->constrained()->cascadeOnDelete();
                $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
                $table->string('description');
                $table->decimal('quantity', 15, 2)->default(1.00);
                $table->decimal('unit_price', 15, 2)->default(0.00);
                $table->decimal('total_price', 15, 2)->default(0.00);
                $table->timestamps();
            });
        }

        // Ensure estimates table has expected columns if it somehow predates the new schema
        if (Schema::hasTable('estimates')) {
            Schema::table('estimates', function (Blueprint $table) {
                if (!Schema::hasColumn('estimates', 'notes')) {
                    $table->text('notes')->nullable()->after('total_amount');
                }
                if (!Schema::hasColumn('estimates', 'terms')) {
                    $table->text('terms')->nullable()->after('notes');
                }
                if (!Schema::hasColumn('estimates', 'expiry_date')) {
                    $table->date('expiry_date')->nullable()->after('estimate_date');
                }
            });
        }

        // Add estimate_id link to invoices table if missing
        if (Schema::hasTable('invoices') && !Schema::hasColumn('invoices', 'estimate_id')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->foreignId('estimate_id')->nullable()->after('client_id')->constrained()->nullOnDelete();
            });
        }
    }

    public function down(): void {
        Schema::dropIfExists('estimate_items');
        if (Schema::hasColumn('invoices', 'estimate_id')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->dropForeign(['estimate_id']);
                $table->dropColumn('estimate_id');
            });
        }
    }
};

