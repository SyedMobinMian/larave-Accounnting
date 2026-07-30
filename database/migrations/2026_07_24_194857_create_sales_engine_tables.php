<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Create estimates table if it doesn't exist
        if (!Schema::hasTable('estimates')) {
            Schema::create('estimates', function (Blueprint $table) {
                $table->id();
                $table->string('estimate_number');
                $table->foreignId('client_id')->constrained()->cascadeOnDelete();
                $table->date('estimate_date');
                $table->date('expiry_date');
                $table->enum('status', ['draft', 'sent', 'accepted', 'declined', 'expired'])->default('draft');
                $table->decimal('subtotal', 15, 2)->default(0.00);
                $table->decimal('tax_amount', 15, 2)->default(0.00);
                $table->decimal('total_amount', 15, 2)->default(0.00);
                $table->timestamps();
            });
        }

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

        // Add estimate_id link to invoices table if missing
        if (Schema::hasTable('invoices') && !Schema::hasColumn('invoices', 'estimate_id')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->foreignId('estimate_id')->nullable()->after('client_id')->constrained()->nullOnDelete();
            });
        }
    }

    public function down(): void {
        Schema::dropIfExists('estimate_items');
        Schema::dropIfExists('estimates');
        if (Schema::hasColumn('invoices', 'estimate_id')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->dropForeign(['estimate_id']);
                $table->dropColumn('estimate_id');
            });
        }
    }
};
