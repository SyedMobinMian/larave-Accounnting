<?php

namespace Database\Seeders;

use App\Models\AccountType;
use Illuminate\Database\Seeder;

class AccountTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            // Assets
            ['name' => 'Bank', 'base_type' => 'ASSET'],
            ['name' => 'Cash', 'base_type' => 'ASSET'],
            ['name' => 'Accounts Receivable', 'base_type' => 'ASSET'],
            ['name' => 'Inventory', 'base_type' => 'ASSET'],
            ['name' => 'Fixed Asset', 'base_type' => 'ASSET'],
            // Liabilities
            ['name' => 'Accounts Payable', 'base_type' => 'LIABILITY'],
            ['name' => 'Credit Card', 'base_type' => 'LIABILITY'],
            ['name' => 'Loan', 'base_type' => 'LIABILITY'],
            // Equity
            ['name' => 'Equity', 'base_type' => 'EQUITY'],
            // Revenue
            ['name' => 'Sales', 'base_type' => 'REVENUE'],
            ['name' => 'Other Income', 'base_type' => 'REVENUE'],
            // Expense
            ['name' => 'Cost of Goods Sold', 'base_type' => 'EXPENSE'],
            ['name' => 'Expense', 'base_type' => 'EXPENSE'],
        ];

        foreach ($types as $type) {
            AccountType::create($type);
        }
    }
}
