<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->string('invoice_number')->unique();
            $table->date('date');
            $table->date('duedate')->nullable();
            $table->decimal('subtotal', 15, 2)->default(0.00);
            $table->decimal('total_tax', 15, 2)->default(0.00);
            $table->decimal('discount_total', 15, 2)->default(0.00);
            $table->decimal('total', 15, 2)->default(0.00);
            $table->string('status')->default('unpaid'); // unpaid, paid, partially_paid, cancelled
            $table->text('clientnote')->nullable();
            $table->text('adminnote')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
