<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Call all necessary seeders in the correct order
        $this->call([
            // Core setup
            UserSeeder::class,

            // System Settings
            SettingsSeeder::class,

            // Demo/Sample data
            DemoDataSeeder::class,
        ]);
    }
}
