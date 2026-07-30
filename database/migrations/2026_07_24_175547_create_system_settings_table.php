<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $settings = [
            ['group' => 'general', 'name' => 'company_name', 'payload' => json_encode('My SaaS Company')],
            ['group' => 'general', 'name' => 'company_address', 'payload' => json_encode('123 Business St.')],
            ['group' => 'general', 'name' => 'timezone', 'payload' => json_encode('Asia/Kolkata')],
            ['group' => 'general', 'name' => 'currency', 'payload' => json_encode('INR')],
            ['group' => 'general', 'name' => 'financial_year_start', 'payload' => json_encode('04-01')],

            ['group' => 'general', 'name' => 'tax_type', 'payload' => json_encode('GST')],
            ['group' => 'general', 'name' => 'default_tax_rate', 'payload' => json_encode(18.00)],
            ['group' => 'general', 'name' => 'tax_number', 'payload' => json_encode('22AAAAA0000A1Z5')],
            ['group' => 'general', 'name' => 'is_tax_inclusive', 'payload' => json_encode(false)],

            ['group' => 'general', 'name' => 'valuation_method', 'payload' => json_encode('FIFO')],
            ['group' => 'general', 'name' => 'enable_warehouses', 'payload' => json_encode(true)],
            ['group' => 'general', 'name' => 'enable_stock_alerts', 'payload' => json_encode(true)],
            ['group' => 'general', 'name' => 'low_stock_threshold', 'payload' => json_encode(10)],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['group' => $setting['group'], 'name' => $setting['name']],
                ['payload' => $setting['payload'], 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }

    public function down(): void
    {
        DB::table('settings')->where('group', 'general')->delete();
    }
};