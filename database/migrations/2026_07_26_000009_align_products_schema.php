<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Align the products table columns with the Product model & resources.
        // The original 2026_07_20_183012 migration used purchase_price/sales_price/stock_on_hand,
        // while the app uses cost_price/selling_price/stock_quantity/min_stock_alert.
        // This runs BEFORE 2026_07_26_000010_add_performance_indexes so the
        // products_stock_quantity_index can be created.

        Schema::table('products', function (Blueprint $table) {
            // Add unit_id alias to match the Unit relationship used by ProductResource
            if (!Schema::hasColumn('products', 'unit_id')) {
                $table->foreignId('unit_id')->nullable()->after('unit_of_measure_id');
            }

            // Add the model-expected price/stock columns if missing
            if (!Schema::hasColumn('products', 'cost_price')) {
                $table->decimal('cost_price', 15, 2)->default(0.00)->after('unit_id');
            }
            if (!Schema::hasColumn('products', 'selling_price')) {
                $table->decimal('selling_price', 15, 2)->default(0.00)->after('cost_price');
            }
            if (!Schema::hasColumn('products', 'stock_quantity')) {
                $table->decimal('stock_quantity', 15, 2)->default(0.00)->after('selling_price');
            }
            if (!Schema::hasColumn('products', 'min_stock_alert')) {
                $table->decimal('min_stock_alert', 15, 2)->default(10.00)->after('stock_quantity');
            }
        });

        // Backfill the new columns from the legacy columns so existing data is preserved.
        DB::table('products')->update([
            'cost_price' => DB::raw('purchase_price'),
            'selling_price' => DB::raw('sales_price'),
            'stock_quantity' => DB::raw('stock_on_hand'),
            'min_stock_alert' => DB::raw('low_stock_threshold'),
        ]);

        // Sync unit_id from unit_of_measure_id
        DB::table('products')->update([
            'unit_id' => DB::raw('unit_of_measure_id'),
        ]);
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $columns = ['unit_id', 'cost_price', 'selling_price', 'stock_quantity', 'min_stock_alert'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('products', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

