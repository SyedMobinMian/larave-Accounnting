<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('phone')->nullable();
            $table->string('manager_name')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        // Add warehouse_id column to products table (if not already present)
        if (Schema::hasTable('products') && !Schema::hasColumn('products', 'warehouse_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->foreignId('warehouse_id')->nullable()->after('category_id')->constrained()->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};

