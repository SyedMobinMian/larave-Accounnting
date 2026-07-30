<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('goods_receipt_notes', function (Blueprint $table) {
            $table->id();
            $table->string('grn_number')->unique();
            $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
            $table->date('received_date');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('goods_receipt_note_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('goods_receipt_note_id')->constrained('goods_receipt_notes')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->decimal('received_quantity', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('goods_receipt_note_items');
        Schema::dropIfExists('goods_receipt_notes');
    }
};
