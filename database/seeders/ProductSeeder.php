<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categoryId = DB::table('categories')->value('id');
        $unitId = DB::table('units_of_measure')->value('id');

        $products = [
            [
                'name' => 'Dell XPS 15 Laptop',
                'type' => 'product',
                'sku' => 'PROD-DELL-001',
                'barcode' => '8901234567890',
                'description' => '15-inch laptop with Intel i7, 16GB RAM, 512GB SSD.',
                'category_id' => $categoryId,
                'unit_of_measure_id' => $unitId,
                'purchase_price' => 1100.00,
                'sales_price' => 1499.99,
                'track_inventory' => true,
                'stock_on_hand' => 25.00,
                'reserved_stock' => 2.00,
                'low_stock_threshold' => 5.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Web Development Consulting',
                'type' => 'service',
                'sku' => 'SERV-WEB-001',
                'barcode' => null,
                'description' => 'Custom Laravel development and architectural consulting (per hour).',
                'category_id' => $categoryId,
                'unit_of_measure_id' => $unitId,
                'purchase_price' => 0.00,
                'sales_price' => 85.00,
                'track_inventory' => false,
                'stock_on_hand' => 0.00,
                'reserved_stock' => 0.00,
                'low_stock_threshold' => 0.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ergonomic Office Chair',
                'type' => 'product',
                'sku' => 'PROD-CHAIR-002',
                'barcode' => '8909876543210',
                'description' => 'High-back mesh chair with adjustable armrests and lumbar support.',
                'category_id' => $categoryId,
                'unit_of_measure_id' => $unitId,
                'purchase_price' => 120.00,
                'sales_price' => 249.50,
                'track_inventory' => true,
                'stock_on_hand' => 14.00,
                'reserved_stock' => 0.00,
                'low_stock_threshold' => 3.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($products as $product) {
            DB::table('products')->updateOrInsert(['sku' => $product['sku']], $product);
        }
    }
}