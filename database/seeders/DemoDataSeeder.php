<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Comprehensive demo data seeder.
 *
 * Populates every module with related, realistic data so the ERP can be
 * explored end-to-end: Clients, Contacts, Products, Units, Categories,
 * Warehouses, Vendors, Invoices (+items/payments), Estimates, Leads,
 * Expenses, Purchase Orders, Bills, Accounts, Bank Accounts,
 * Bank Transactions, Stock Movements and Journal Entries.
 *
 * Safe to re-run: uses updateOrInsert / guarded checks.
 */
class DemoDataSeeder extends Seeder
{
    protected array $accountIds = [];

    public function run(): void
    {
        $this->seedUnits();
        $this->seedCategories();
        $this->seedWarehouses();
        $this->seedProducts();
        $this->seedAccounts();
        $this->seedBankAccounts();
        $this->seedClientsAndContacts();
        $this->seedVendors();
        $this->seedLeads();
        $this->seedInvoices();
        $this->seedEstimates();
        $this->seedExpenses();
        $this->seedPurchaseOrders();
        $this->seedBills();
        $this->seedJournalEntries();

        $this->command?->info('Demo data seeded successfully!');
    }

    /* ─────────────────────── Units ─────────────────────── */
    protected function seedUnits(): void
    {
        $units = [
            ['name' => 'Piece', 'short_name' => 'pcs', 'is_active' => true],
            ['name' => 'Kilogram', 'short_name' => 'kg', 'is_active' => true],
            ['name' => 'Meter', 'short_name' => 'm', 'is_active' => true],
            ['name' => 'Hour', 'short_name' => 'hrs', 'is_active' => true],
            ['name' => 'Box', 'short_name' => 'box', 'is_active' => true],
            ['name' => 'Dozen', 'short_name' => 'doz', 'is_active' => true],
        ];

        foreach ($units as $unit) {
            foreach (['units', 'units_of_measure'] as $table) {
                DB::table($table)->updateOrInsert(
                    ['name' => $unit['name']],
                    [...$unit, 'created_at' => now(), 'updated_at' => now()]
                );
            }
        }
    }

    /* ─────────────────────── Categories ─────────────────────── */
    protected function seedCategories(): void
    {
        $categories = [
            ['name' => 'Hardware & Electronics', 'slug' => 'hardware-electronics', 'description' => 'Computers, peripherals and electronics'],
            ['name' => 'Software & Services', 'slug' => 'software-services', 'description' => 'Software licenses and IT services'],
            ['name' => 'Office Supplies', 'slug' => 'office-supplies', 'description' => 'Stationery and everyday office items'],
            ['name' => 'Furniture', 'slug' => 'furniture', 'description' => 'Office furniture and fixtures'],
            ['name' => 'Consulting', 'slug' => 'consulting', 'description' => 'Professional consulting services'],
        ];

        foreach ($categories as $category) {
            DB::table('categories')->updateOrInsert(
                ['slug' => $category['slug']],
                [...$category, 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }

    /* ─────────────────────── Warehouses ─────────────────────── */
    protected function seedWarehouses(): void
    {
        $warehouses = [
            ['name' => 'Main Warehouse', 'code' => 'WH-MAIN', 'address' => '12 Industrial Park, Mumbai', 'city' => 'Mumbai', 'state' => 'Maharashtra', 'country' => 'India', 'phone' => '+91 22 4000 1234', 'manager_name' => 'Rahul Sharma', 'is_active' => true, 'is_default' => true],
            ['name' => 'Delhi Distribution Center', 'code' => 'WH-DEL', 'address' => '45 Transport Nagar, Delhi', 'city' => 'New Delhi', 'state' => 'Delhi', 'country' => 'India', 'phone' => '+91 11 4500 5678', 'manager_name' => 'Priya Verma', 'is_active' => true, 'is_default' => false],
        ];

        foreach ($warehouses as $warehouse) {
            DB::table('warehouses')->updateOrInsert(
                ['code' => $warehouse['code']],
                [...$warehouse, 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }

    /* ─────────────────────── Products ─────────────────────── */
    protected function seedProducts(): void
    {
        $catElectronics = DB::table('categories')->where('slug', 'hardware-electronics')->value('id');
        $catServices = DB::table('categories')->where('slug', 'software-services')->value('id');
        $catOffice = DB::table('categories')->where('slug', 'office-supplies')->value('id');
        $catFurniture = DB::table('categories')->where('slug', 'furniture')->value('id');

        $unitPcs = DB::table('units')->where('short_name', 'pcs')->value('id');
        $unitHrs = DB::table('units')->where('short_name', 'hrs')->value('id');
        $unitBox = DB::table('units')->where('short_name', 'box')->value('id');

        $warehouseMain = DB::table('warehouses')->where('code', 'WH-MAIN')->value('id');
        $warehouseDel = DB::table('warehouses')->where('code', 'WH-DEL')->value('id');

        $products = [
            ['name' => 'Dell XPS 15 Laptop', 'type' => 'product', 'sku' => 'PROD-DELL-XPS15', 'barcode' => '8901234567890', 'description' => '15-inch laptop, Intel i7, 16GB RAM, 512GB SSD', 'category_id' => $catElectronics, 'unit_id' => $unitPcs, 'unit_of_measure_id' => $unitPcs, 'cost_price' => 1100.00, 'selling_price' => 1499.99, 'stock_quantity' => 25, 'min_stock_alert' => 5, 'purchase_price' => 1100.00, 'sales_price' => 1499.99, 'track_inventory' => true, 'stock_on_hand' => 25, 'low_stock_threshold' => 5, 'warehouse_id' => $warehouseMain, 'is_active' => true],
            ['name' => 'iPhone 15 Pro 256GB', 'type' => 'product', 'sku' => 'PROD-IPH-15PRO', 'barcode' => '8901234567891', 'description' => 'Apple iPhone 15 Pro, 256GB, Titanium', 'category_id' => $catElectronics, 'unit_id' => $unitPcs, 'unit_of_measure_id' => $unitPcs, 'cost_price' => 950.00, 'selling_price' => 1199.00, 'stock_quantity' => 14, 'min_stock_alert' => 3, 'purchase_price' => 950.00, 'sales_price' => 1199.00, 'track_inventory' => true, 'stock_on_hand' => 14, 'low_stock_threshold' => 3, 'warehouse_id' => $warehouseMain, 'is_active' => true],
            ['name' => 'HP LaserJet Pro Printer', 'type' => 'product', 'sku' => 'PROD-HP-LJPRO', 'barcode' => '8901234567892', 'description' => 'Monochrome laser printer for offices', 'category_id' => $catElectronics, 'unit_id' => $unitPcs, 'unit_of_measure_id' => $unitPcs, 'cost_price' => 180.00, 'selling_price' => 279.00, 'stock_quantity' => 9, 'min_stock_alert' => 2, 'purchase_price' => 180.00, 'sales_price' => 279.00, 'track_inventory' => true, 'stock_on_hand' => 9, 'low_stock_threshold' => 2, 'warehouse_id' => $warehouseDel, 'is_active' => true],
            ['name' => 'Ergonomic Office Chair', 'type' => 'product', 'sku' => 'PROD-CHAIR-ERG', 'barcode' => '8901234567893', 'description' => 'High-back mesh ergonomic chair', 'category_id' => $catFurniture, 'unit_id' => $unitPcs, 'unit_of_measure_id' => $unitPcs, 'cost_price' => 120.00, 'selling_price' => 249.50, 'stock_quantity' => 18, 'min_stock_alert' => 4, 'purchase_price' => 120.00, 'sales_price' => 249.50, 'track_inventory' => true, 'stock_on_hand' => 18, 'low_stock_threshold' => 4, 'warehouse_id' => $warehouseMain, 'is_active' => true],
            ['name' => 'A4 Paper Ream (500 sheets)', 'type' => 'product', 'sku' => 'PROD-APER-A4', 'barcode' => '8901234567894', 'description' => 'Premium A4 copier paper, 80gsm', 'category_id' => $catOffice, 'unit_id' => $unitBox, 'unit_of_measure_id' => $unitBox, 'cost_price' => 3.50, 'selling_price' => 6.99, 'stock_quantity' => 300, 'min_stock_alert' => 50, 'purchase_price' => 3.50, 'sales_price' => 6.99, 'track_inventory' => true, 'stock_on_hand' => 300, 'low_stock_threshold' => 50, 'warehouse_id' => $warehouseDel, 'is_active' => true],
            ['name' => 'GST Invoicing Software License', 'type' => 'service', 'sku' => 'SERV-GST-LIC', 'description' => 'Annual license - cloud GST invoicing software', 'category_id' => $catServices, 'unit_id' => $unitHrs, 'unit_of_measure_id' => $unitHrs, 'cost_price' => 0.00, 'selling_price' => 299.00, 'stock_quantity' => 0, 'min_stock_alert' => 0, 'purchase_price' => 0.00, 'sales_price' => 299.00, 'track_inventory' => false, 'stock_on_hand' => 0, 'low_stock_threshold' => 0, 'warehouse_id' => null, 'is_active' => true],
            ['name' => 'Web Development Consulting', 'type' => 'service', 'sku' => 'SERV-WEB-CONS', 'description' => 'Custom Laravel development (per hour)', 'category_id' => $catServices, 'unit_id' => $unitHrs, 'unit_of_measure_id' => $unitHrs, 'cost_price' => 0.00, 'selling_price' => 85.00, 'stock_quantity' => 0, 'min_stock_alert' => 0, 'purchase_price' => 0.00, 'sales_price' => 85.00, 'track_inventory' => false, 'stock_on_hand' => 0, 'low_stock_threshold' => 0, 'warehouse_id' => null, 'is_active' => true],
            ['name' => 'Premium Steel Desk', 'type' => 'product', 'sku' => 'PROD-DESK-STL', 'barcode' => '8901234567895', 'description' => '3-foot steel office desk', 'category_id' => $catFurniture, 'unit_id' => $unitPcs, 'unit_of_measure_id' => $unitPcs, 'cost_price' => 200.00, 'selling_price' => 349.00, 'stock_quantity' => 7, 'min_stock_alert' => 2, 'purchase_price' => 200.00, 'sales_price' => 349.00, 'track_inventory' => true, 'stock_on_hand' => 7, 'low_stock_threshold' => 2, 'warehouse_id' => $warehouseMain, 'is_active' => true],
        ];

        foreach ($products as $product) {
            DB::table('products')->updateOrInsert(
                ['sku' => $product['sku']],
                [...$product, 'created_at' => now(), 'updated_at' => now()]
            );

            $productId = DB::table('products')->where('sku', $product['sku'])->value('id');
            $hasMovement = DB::table('stock_movements')
                ->where('product_id', $productId)
                ->where('reference_type', 'seed')
                ->exists();

            if (($product['track_inventory'] ?? false) && $productId && !$hasMovement) {
                DB::table('stock_movements')->insert([
                    'product_id' => $productId,
                    'warehouse_id' => $product['warehouse_id'],
                    'type' => 'in',
                    'quantity' => $product['stock_quantity'],
                    'unit_cost' => $product['cost_price'],
                    'total_cost' => $product['cost_price'] * $product['stock_quantity'],
                    'balance_before' => 0,
                    'balance_after' => $product['stock_quantity'],
                    'reference_type' => 'seed',
                    'reference_id' => $productId,
                    'notes' => 'Initial demo stock',
                    'created_by' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /* ─────────────────────── Accounts ─────────────────────── */
    protected function seedAccounts(): void
    {
        // Account types first
        $types = [
            ['name' => 'Bank', 'base_type' => 'ASSET'],
            ['name' => 'Cash', 'base_type' => 'ASSET'],
            ['name' => 'Accounts Receivable', 'base_type' => 'ASSET'],
            ['name' => 'Inventory', 'base_type' => 'ASSET'],
            ['name' => 'Fixed Asset', 'base_type' => 'ASSET'],
            ['name' => 'Accounts Payable', 'base_type' => 'LIABILITY'],
            ['name' => 'Credit Card', 'base_type' => 'LIABILITY'],
            ['name' => 'Loan', 'base_type' => 'LIABILITY'],
            ['name' => 'Equity', 'base_type' => 'EQUITY'],
            ['name' => 'Sales', 'base_type' => 'REVENUE'],
            ['name' => 'Other Income', 'base_type' => 'REVENUE'],
            ['name' => 'Cost of Goods Sold', 'base_type' => 'EXPENSE'],
            ['name' => 'Expense', 'base_type' => 'EXPENSE'],
        ];

        if (\Schema::hasTable('account_types')) {
            foreach ($types as $type) {
                DB::table('account_types')->updateOrInsert(
                    ['name' => $type['name']],
                    [...$type, 'created_at' => now(), 'updated_at' => now()]
                );
            }
        }

        $accounts = [
            ['code' => '1010', 'name' => 'Cash', 'type' => 'asset', 'balance' => 25000.00],
            ['code' => '1020', 'name' => 'Main Bank Account', 'type' => 'asset', 'balance' => 85000.00],
            ['code' => '1100', 'name' => 'Accounts Receivable', 'type' => 'asset', 'balance' => 45250.00],
            ['code' => '1200', 'name' => 'Inventory', 'type' => 'asset', 'balance' => 65000.00],
            ['code' => '1500', 'name' => 'Equipment & Furniture', 'type' => 'asset', 'balance' => 40000.00],
            ['code' => '2010', 'name' => 'Accounts Payable', 'type' => 'liability', 'balance' => 32500.00],
            ['code' => '2050', 'name' => 'Sales Tax Payable', 'type' => 'liability', 'balance' => 5820.00],
            ['code' => '3000', 'name' => "Owner's Equity", 'type' => 'equity', 'balance' => 150000.00],
            ['code' => '4000', 'name' => 'Sales Revenue', 'type' => 'revenue', 'balance' => 120000.00],
            ['code' => '4100', 'name' => 'Service Revenue', 'type' => 'revenue', 'balance' => 30000.00],
            ['code' => '5000', 'name' => 'Cost of Goods Sold', 'type' => 'expense', 'balance' => 52000.00],
            ['code' => '6000', 'name' => 'Office Rent', 'type' => 'expense', 'balance' => 12000.00],
            ['code' => '6010', 'name' => 'Utilities', 'type' => 'expense', 'balance' => 4500.00],
            ['code' => '6020', 'name' => 'Salaries & Wages', 'type' => 'expense', 'balance' => 48000.00],
            ['code' => '6030', 'name' => 'Marketing & Advertising', 'type' => 'expense', 'balance' => 8000.00],
            ['code' => '6040', 'name' => 'General Expenses', 'type' => 'expense', 'balance' => 3000.00],
        ];

        foreach ($accounts as $account) {
            DB::table('accounts')->updateOrInsert(
                ['code' => $account['code']],
                [...$account, 'active' => true, 'created_at' => now(), 'updated_at' => now()]
            );
            $this->accountIds[$account['code']] = DB::table('accounts')->where('code', $account['code'])->value('id');
        }
    }

    /* ─────────────────────── Bank Accounts ─────────────────────── */
    protected function seedBankAccounts(): void
    {
        $bankAccounts = [
            ['name' => 'HDFC Current Account', 'bank_name' => 'HDFC Bank', 'account_number' => '50100234567890', 'currency' => 'INR', 'opening_balance' => 50000.00, 'is_active' => true, 'is_default' => true],
            ['name' => 'SBI Business Account', 'bank_name' => 'State Bank of India', 'account_number' => '35678901234', 'currency' => 'INR', 'opening_balance' => 35000.00, 'is_active' => true, 'is_default' => false],
        ];

        foreach ($bankAccounts as $bankAccount) {
            $encrypted = Crypt::encryptString($bankAccount['account_number']);
            unset($bankAccount['account_number']);
            $bankAccount['account_number'] = $encrypted;

            DB::table('bank_accounts')->updateOrInsert(
                ['name' => $bankAccount['name']],
                [...$bankAccount, 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }

    /* ─────────────────────── Clients + Contacts ─────────────────────── */
    protected function seedClientsAndContacts(): void
    {
        $clients = [
            ['company' => 'Acme Corporation', 'vat' => 'US987654321', 'phonenumber' => '+1 (555) 019-2834', 'country' => 'USA', 'city' => 'Austin', 'zip' => '78701', 'state' => 'TX', 'address' => '123 Innovation Way, Tech Park', 'website' => 'https://acme.corp'],
            ['company' => 'Nexus Global Solutions', 'vat' => 'GB123456789', 'phonenumber' => '+44 20 7946 0912', 'country' => 'UK', 'city' => 'London', 'zip' => 'EC2N 2DB', 'state' => 'Greater London', 'address' => '45 Financial Center St', 'website' => 'https://nexusglobal.io'],
            ['company' => 'Tata Consultancy (Demo)', 'vat' => 'IN27ABCDE1234F1Z5', 'phonenumber' => '+91 22 6600 1234', 'country' => 'India', 'city' => 'Mumbai', 'zip' => '400001', 'state' => 'Maharashtra', 'address' => 'TCS House, Fort', 'website' => 'https://tcs.com'],
            ['company' => 'Infosys Retail Division', 'vat' => 'IN29EFGH5678K2T6', 'phonenumber' => '+91 80 4000 8888', 'country' => 'India', 'city' => 'Bengaluru', 'zip' => '560001', 'state' => 'Karnataka', 'address' => 'Electronic City, Phase 1', 'website' => 'https://infosys.com'],
            ['company' => 'Reliance Communications', 'vat' => 'IN24JKLM9012Q3R7', 'phonenumber' => '+91 22 6226 5555', 'country' => 'India', 'city' => 'Navi Mumbai', 'zip' => '400070', 'state' => 'Maharashtra', 'address' => 'Reliance Corporate Park', 'website' => 'https://ril.com'],
        ];

        $contactNames = [
            'Acme Corporation' => ['John', 'Doe', 'Accounts Payable Manager'],
            'Nexus Global Solutions' => ['Emma', 'Watson', 'Finance Director'],
            'Tata Consultancy (Demo)' => ['Raj', 'Patel', 'Procurement Head'],
            'Infosys Retail Division' => ['Ananya', 'Iyer', 'Operations Lead'],
            'Reliance Communications' => ['Vikram', 'Singh', 'Purchase Manager'],
        ];

        foreach ($clients as $clientData) {
            $clientId = DB::table('clients')->where('company', $clientData['company'])->value('id');
            if (!$clientId) {
                $clientId = DB::table('clients')->insertGetId([...$clientData, 'active' => true, 'created_at' => now(), 'updated_at' => now()]);
            }

            $names = $contactNames[$clientData['company']];
            $slug = Str::slug($clientData['company']);
            $baseEmail = $slug . '@mail.com';
            DB::table('contacts')->updateOrInsert(
                ['email' => $baseEmail],
                [
                    'client_id' => $clientId,
                    'is_primary' => true,
                    'firstname' => $names[0],
                    'lastname' => $names[1],
                    'phonenumber' => $clientData['phonenumber'],
                    'title' => $names[2],
                    'active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    /* ─────────────────────── Vendors ─────────────────────── */
    protected function seedVendors(): void
    {
        $vendors = [
            ['name' => 'Techify Solutions', 'company_name' => 'Techify Solutions Pvt Ltd', 'email' => 'sales@techify.com', 'phone' => '+91 99 0000 1111', 'address' => '56 Cyber City, Gurugram', 'tax_number' => '06AABCT1332L1ZK'],
            ['name' => 'GlobalOffice Supplies', 'company_name' => 'GlobalOffice Supplies Inc', 'email' => 'billing@globaloffice.io', 'phone' => '+1 (800) 555-0199', 'address' => '88 Commerce Ave, New York', 'tax_number' => 'US88-1234567'],
            ['name' => 'ByteHard Computers', 'company_name' => 'ByteHard Computers', 'email' => 'orders@bytehard.in', 'phone' => '+91 80 4567 8901', 'address' => '12 MG Road, Bengaluru', 'tax_number' => '29AABCB2345F1Z1'],
            ['name' => 'CloudWorks Pvt Ltd', 'company_name' => 'CloudWorks India', 'email' => 'invoices@cloudworks.in', 'phone' => '+91 44 2345 6789', 'address' => '7 IT Park, Chennai', 'tax_number' => '33AACCC3456K1Z2'],
        ];

        foreach ($vendors as $vendor) {
            DB::table('vendors')->updateOrInsert(
                ['name' => $vendor['name']],
                [...$vendor, 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }

    /* ─────────────────────── Leads ─────────────────────── */
    protected function seedLeads(): void
    {
        $leads = [
            ['company_name' => 'Velocity Startups', 'contact_name' => 'Alice Brown', 'email' => 'alice@velocity.io', 'phone' => '+1 555 111 2233', 'status' => 'new', 'source' => 'website', 'estimated_value' => 8500.00, 'website' => 'https://velocity.io', 'city' => 'San Francisco', 'country' => 'USA', 'notes' => 'Interested in ERP package for startup.'],
            ['company_name' => 'Sunrise Retail', 'contact_name' => 'David Kumar', 'email' => 'david@sunriseretail.in', 'phone' => '+91 98 7654 3210', 'status' => 'contacted', 'source' => 'referral', 'estimated_value' => 12000.00, 'city' => 'Chennai', 'country' => 'India', 'notes' => 'Looking for invoicing and inventory.'],
            ['company_name' => 'GreenEnergy Ltd', 'contact_name' => 'Sarah Johnson', 'email' => 'sarah@greenenergy.co', 'phone' => '+44 20 1234 5678', 'status' => 'qualified', 'source' => 'social_media', 'estimated_value' => 25000.00, 'city' => 'Manchester', 'country' => 'UK', 'notes' => 'Needs full suite including payroll.'],
            ['company_name' => 'Bright Future Edu', 'contact_name' => 'Rohan Gupta', 'email' => 'rohan@bfedu.in', 'phone' => '+91 99 8888 7766', 'status' => 'negotiation', 'source' => 'email_campaign', 'estimated_value' => 18500.00, 'city' => 'Pune', 'country' => 'India', 'notes' => 'Education sector, needs billing module.'],
            ['company_name' => 'NovaTech Manufacturing', 'contact_name' => 'Michael Chen', 'email' => 'michael@novatech.com', 'phone' => '+1 415 777 8899', 'status' => 'proposal', 'source' => 'phone_call', 'estimated_value' => 42000.00, 'city' => 'Detroit', 'country' => 'USA', 'notes' => 'Requires inventory + manufacturing tracking.'],
            ['company_name' => 'Zephyr Media Group', 'contact_name' => 'Laura Wilson', 'email' => 'laura@zephyrmg.com', 'phone' => '+55 11 5555 4444', 'status' => 'won', 'source' => 'website', 'estimated_value' => 15000.00, 'city' => 'Sao Paulo', 'country' => 'Brazil', 'notes' => 'Won - onboarding in progress.'],
        ];

        foreach ($leads as $lead) {
            DB::table('leads')->updateOrInsert(
                ['email' => $lead['email']],
                [...$lead, 'assigned_to' => 1, 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }

    /* ─────────────────────── Invoices ─────────────────────── */
    protected function seedInvoices(): void
    {
        $clients = DB::table('clients')->pluck('id')->all();
        $products = DB::table('products')->get();

        if (empty($clients) || $products->isEmpty()) {
            return;
        }

        $statuses = ['paid', 'unpaid', 'draft', 'cancelled'];

        foreach ($clients as $index => $clientId) {
            $clientOdd = ($index % 2 === 0);
            $productPool = $products->take($clientOdd ? 3 : 2);

            $subtotal = 0;
            $invoiceId = DB::table('invoices')->where('invoice_number', 'INV-2026-' . str_pad($clientId, 4, '0', STR_PAD_LEFT))->value('id');

            if (!$invoiceId) {
                $invoiceId = DB::table('invoices')->insertGetId([
                    'client_id' => $clientId,
                    'invoice_number' => 'INV-2026-' . str_pad($clientId, 4, '0', STR_PAD_LEFT),
                    'issue_date' => now()->subDays(15 - $index),
                    'due_date' => now()->addDays(15 + $index),
                    'status' => $statuses[$index % count($statuses)],
                    'subtotal' => 0,
                    'tax_amount' => 0,
                    'total_amount' => 0,
                    'notes' => 'Thank you for your business!',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Clear old items to allow re-run
            DB::table('invoice_items')->where('invoice_id', $invoiceId)->delete();

            foreach ($productPool as $i => $product) {
                $price = $product->selling_price ?? $product->sales_price ?? 100;
                $qty = ($i % 2 === 0) ? 2 : 5;
                $lineTotal = $price * $qty;
                $subtotal += $lineTotal;

                DB::table('invoice_items')->insert([
                    'invoice_id' => $invoiceId,
                    'product_id' => $product->id,
                    'description' => $product->name,
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'total_price' => $lineTotal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $taxAmount = round($subtotal * 0.10, 2);
            $totalAmount = round($subtotal + $taxAmount, 2);

            DB::table('invoices')->where('id', $invoiceId)->update([
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
            ]);

            // Payment for paid invoices
            $invoice = DB::table('invoices')->where('id', $invoiceId)->first();
            if ($invoice->status === 'paid') {
                $hasPayment = DB::table('invoice_payments')->where('invoice_id', $invoiceId)->exists();
                if (!$hasPayment) {
                    DB::table('invoice_payments')->insert([
                        'invoice_id' => $invoiceId,
                        'account_id' => $this->accountIds['1020'] ?? null,
                        'payment_number' => 'PAY-2026-' . str_pad($clientId, 4, '0', STR_PAD_LEFT),
                        'payment_date' => now()->subDays(2),
                        'amount' => $totalAmount,
                        'payment_method' => 'bank_transfer',
                        'reference' => 'TXN-' . Str::random(8),
                        'notes' => 'Payment received in full via Wire Transfer.',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    /* ─────────────────────── Estimates ─────────────────────── */
    protected function seedEstimates(): void
    {
        $clients = DB::table('clients')->pluck('id')->all();
        $products = DB::table('products')->get();

        if (empty($clients) || $products->isEmpty()) {
            return;
        }

        foreach (array_slice($clients, 0, 4) as $index => $clientId) {
            $estimateNumber = 'EST-2026-' . str_pad($clientId, 4, '0', STR_PAD_LEFT);
            $estimateId = DB::table('estimates')->where('estimate_number', $estimateNumber)->value('id');

            if (!$estimateId) {
                $estimateId = DB::table('estimates')->insertGetId([
                    'client_id' => $clientId,
                    'estimate_number' => $estimateNumber,
                    'estimate_date' => now()->subDays(5),
                    'expiry_date' => now()->addDays(25),
                    'status' => ['draft', 'sent', 'accepted', 'declined'][$index],
                    'subtotal' => 0,
                    'tax_amount' => 0,
                    'total_amount' => 0,
                    'notes' => 'Thank you for considering our services.',
                    'terms' => 'Payment due within 30 days.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('estimate_items')->where('estimate_id', $estimateId)->delete();

            $subtotal = 0;
            $product = $products->get(($index + 1) % max($products->count(), 1));

            if ($product) {
                $price = $product->selling_price ?? $product->sales_price ?? 100;
                $qty = 4;
                $lineTotal = $price * $qty;
                $subtotal += $lineTotal;

                DB::table('estimate_items')->insert([
                    'estimate_id' => $estimateId,
                    'product_id' => $product->id,
                    'description' => $product->name,
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'total_price' => $lineTotal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $taxAmount = round($subtotal * 0.10, 2);
            DB::table('estimates')->where('id', $estimateId)->update([
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total_amount' => round($subtotal + $taxAmount, 2),
            ]);
        }
    }

    /* ─────────────────────── Expenses ─────────────────────── */
    protected function seedExpenses(): void
    {
        $vendors = DB::table('vendors')->pluck('id')->all();
        $expenseAccountId = $this->accountIds['6040'] ?? null; // General Expenses
        $paymentAccountId = $this->accountIds['1020'] ?? null; // Main Bank

if (!$expenseAccountId || empty($vendors)) {
            return;
        }

        $expenses = [
            ['vendor_id' => $vendors[0] ?? null, 'amount' => 1500.00, 'description' => 'Office internet and hosting'],
            ['vendor_id' => $vendors[1] ?? null, 'amount' => 3200.00, 'description' => 'Stationery and office supplies'],
            ['vendor_id' => $vendors[2] ?? null, 'amount' => 2200.00, 'description' => 'IT equipment maintenance'],
            ['vendor_id' => $vendors[3] ?? null, 'amount' => 4100.00, 'description' => 'Cloud software subscription'],
        ];

        foreach ($expenses as $i => $expense) {
            DB::table('expenses')->insert([
                'vendor_id' => $expense['vendor_id'],
                'expense_account_id' => $expenseAccountId,
                'payment_account_id' => $paymentAccountId,
                'amount' => $expense['amount'],
                'expense_date' => now()->subDays($i + 2),
                'payment_method' => 'bank_transfer',
                'reference_number' => 'EXP-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'description' => $expense['description'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /* ─────────────────────── Purchase Orders ─────────────────────── */
    protected function seedPurchaseOrders(): void
    {
        $vendors = DB::table('vendors')->pluck('id')->all();
        $products = DB::table('products')->where('type', 'product')->get();

        if (empty($vendors) || $products->isEmpty()) {
            return;
        }

        foreach (array_slice($vendors, 0, 3) as $index => $vendorId) {
            $poNumber = 'PO-2026-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);
            $poId = DB::table('purchase_orders')->where('po_number', $poNumber)->value('id');

            if (!$poId) {
                $poId = DB::table('purchase_orders')->insertGetId([
                    'po_number' => $poNumber,
                    'vendor_id' => $vendorId,
                    'order_date' => now()->subDays(7),
                    'expected_date' => now()->addDays(7),
'status' => ['draft', 'ordered', 'received'][$index],
                    'subtotal' => 0,
                    'tax_total' => 0,
                    'total' => 0,
                    'notes' => 'Demo purchase order.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('purchase_order_items')->where('purchase_order_id', $poId)->delete();

            $subtotal = 0;
            $product = $products->get(($index * 2) % max($products->count(), 1));

            if ($product) {
                $cost = $product->cost_price ?? $product->purchase_price ?? 50;
                $qty = 10;
                $lineTotal = $cost * $qty;
                $subtotal += $lineTotal;

                DB::table('purchase_order_items')->insert([
                    'purchase_order_id' => $poId,
                    'product_id' => $product->id,
                    'description' => $product->name,
                    'qty' => $qty,
                    'unit_cost' => $cost,
                    'amount' => $lineTotal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $tax = round($subtotal * 0.10, 2);
            DB::table('purchase_orders')->where('id', $poId)->update([
                'subtotal' => $subtotal,
                'tax_total' => $tax,
                'total' => round($subtotal + $tax, 2),
            ]);
        }
    }

    /* ─────────────────────── Bills ─────────────────────── */
    protected function seedBills(): void
    {
        $vendors = DB::table('vendors')->pluck('id')->all();
        $products = DB::table('products')->get();

        if (empty($vendors) || $products->isEmpty()) {
            return;
        }

        foreach (array_slice($vendors, 0, 3) as $index => $vendorId) {
            $billNumber = 'BILL-2026-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);
            $billId = DB::table('bills')->where('bill_number', $billNumber)->value('id');

            if (!$billId) {
                $billId = DB::table('bills')->insertGetId([
                    'bill_number' => $billNumber,
                    'vendor_id' => $vendorId,
                    'bill_date' => now()->subDays(10),
                    'due_date' => now()->addDays(20),
                    'status' => ['unpaid', 'paid', 'overdue'][$index],
                    'subtotal' => 0,
                    'tax_amount' => 0,
                    'total_amount' => 0,
                    'paid_amount' => 0,
                    'notes' => 'Vendor bill.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('bill_items')->where('bill_id', $billId)->delete();

            $subtotal = 0;
            $product = $products->get($index % max($products->count(), 1));

            if ($product) {
                $price = $product->cost_price ?? $product->purchase_price ?? 50;
                $qty = 8;
                $lineTotal = $price * $qty;
                $subtotal += $lineTotal;

                DB::table('bill_items')->insert([
                    'bill_id' => $billId,
                    'product_id' => $product->id,
                    'description' => $product->name,
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'subtotal' => $lineTotal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $tax = round($subtotal * 0.10, 2);
            $total = round($subtotal + $tax, 2);
            $paid = ($index === 1) ? $total : 0;

            DB::table('bills')->where('id', $billId)->update([
                'subtotal' => $subtotal,
                'tax_amount' => $tax,
                'total_amount' => $total,
                'paid_amount' => $paid,
            ]);
        }
    }

    /* ─────────────────────── Journal Entries ─────────────────────── */
    protected function seedJournalEntries(): void
    {
        $salesRevenueId = $this->accountIds['4000'] ?? null;
        $arId = $this->accountIds['1100'] ?? null;
        $bankId = $this->accountIds['1020'] ?? null;
        $expenseId = $this->accountIds['6040'] ?? null;

        // Sales journal
        if ($salesRevenueId && $arId) {
            $jeId = DB::table('journal_entries')->where('reference', 'JV-2026-0001')->value('id');
            if (!$jeId) {
                $jeId = DB::table('journal_entries')->insertGetId([
                    'date' => now()->subDays(5),
                    'reference' => 'JV-2026-0001',
                    'description' => 'Record sales revenue for demo invoice',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            DB::table('journal_items')->where('journal_entry_id', $jeId)->delete();
            DB::table('journal_items')->insert([
                ['journal_entry_id' => $jeId, 'account_id' => $arId, 'debit' => 15000.00, 'credit' => 0.00, 'created_at' => now(), 'updated_at' => now()],
                ['journal_entry_id' => $jeId, 'account_id' => $salesRevenueId, 'debit' => 0.00, 'credit' => 15000.00, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        // Payment journal
        if ($bankId && $arId) {
            $jeId = DB::table('journal_entries')->where('reference', 'JV-2026-0002')->value('id');
            if (!$jeId) {
                $jeId = DB::table('journal_entries')->insertGetId([
                    'date' => now()->subDays(2),
                    'reference' => 'JV-2026-0002',
                    'description' => 'Received payment from client',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            DB::table('journal_items')->where('journal_entry_id', $jeId)->delete();
            DB::table('journal_items')->insert([
                ['journal_entry_id' => $jeId, 'account_id' => $bankId, 'debit' => 8000.00, 'credit' => 0.00, 'created_at' => now(), 'updated_at' => now()],
                ['journal_entry_id' => $jeId, 'account_id' => $arId, 'debit' => 0.00, 'credit' => 8000.00, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        // Expense journal
        if ($expenseId && $bankId) {
            $jeId = DB::table('journal_entries')->where('reference', 'JV-2026-0003')->value('id');
            if (!$jeId) {
                $jeId = DB::table('journal_entries')->insertGetId([
                    'date' => now()->subDays(3),
                    'reference' => 'JV-2026-0003',
                    'description' => 'Record office expense',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            DB::table('journal_items')->where('journal_entry_id', $jeId)->delete();
            DB::table('journal_items')->insert([
                ['journal_entry_id' => $jeId, 'account_id' => $expenseId, 'debit' => 2500.00, 'credit' => 0.00, 'created_at' => now(), 'updated_at' => now()],
                ['journal_entry_id' => $jeId, 'account_id' => $bankId, 'debit' => 0.00, 'credit' => 2500.00, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }
}

