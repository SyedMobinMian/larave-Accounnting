<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['product', 'service'])->default('product');
            $table->string('sku')->nullable()->unique();
            $table->string('barcode')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('unit_of_measure_id')->nullable()->constrained('units_of_measure')->nullOnDelete();
            $table->decimal('purchase_price', 15, 2)->default(0.00);
            $table->decimal('sales_price', 15, 2)->default(0.00);
            $table->boolean('track_inventory')->default(true);
            $table->decimal('stock_on_hand', 15, 2)->default(0.00);
            $table->decimal('reserved_stock', 15, 2)->default(0.00);
            $table->decimal('low_stock_threshold', 15, 2)->default(10.00);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('products');
    }
};
