<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoryAndUnitSeeder extends Seeder
{
    public function run(): void
    {
        // Units of Measure
        $units = [
            ['name' => 'Piece', 'short_name' => 'pcs', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kilogram', 'short_name' => 'kg', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Hour', 'short_name' => 'hrs', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Box', 'short_name' => 'box', 'created_at' => now(), 'updated_at' => now()],
        ];
        
        foreach ($units as $unit) {
            DB::table('units_of_measure')->updateOrInsert(['name' => $unit['name']], $unit);
        }

        // Categories
        $categories = [
            ['name' => 'Hardware & Electronics', 'slug' => 'hardware-electronics', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Software & Services', 'slug' => 'software-services', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Office Supplies', 'slug' => 'office-supplies', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Consulting', 'slug' => 'consulting', 'created_at' => now(), 'updated_at' => now()],
        ];

        foreach ($categories as $category) {
            DB::table('categories')->updateOrInsert(['name' => $category['name']], $category);
        }
    }
}
