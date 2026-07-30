<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        // Upsert settings to avoid duplicates on re-run
        $settings = [
// General Settings (System)
            ['group' => 'general', 'name' => 'maintenance_mode', 'payload' => json_encode(false)],
            ['group' => 'general', 'name' => 'debug_mode', 'payload' => json_encode(false)],
            ['group' => 'general', 'name' => 'enable_registration', 'payload' => json_encode(true)],
            ['group' => 'general', 'name' => 'default_user_role', 'payload' => json_encode('client')],
            ['group' => 'general', 'name' => 'pagination_per_page', 'payload' => json_encode(15)],
            ['group' => 'general', 'name' => 'application_theme', 'payload' => json_encode('default')],

            // General Settings (Company Identity - used by ManageSystemSettings)
            ['group' => 'general', 'name' => 'company_name', 'payload' => json_encode('My Company')],
            ['group' => 'general', 'name' => 'company_address', 'payload' => json_encode(null)],
            ['group' => 'general', 'name' => 'timezone', 'payload' => json_encode('Asia/Kolkata')],
            ['group' => 'general', 'name' => 'currency', 'payload' => json_encode('INR')],
            ['group' => 'general', 'name' => 'financial_year_start', 'payload' => json_encode('2024-04-01')],

            // General Settings (Tax - used by ManageSystemSettings)
            ['group' => 'general', 'name' => 'tax_type', 'payload' => json_encode('gst')],
            ['group' => 'general', 'name' => 'tax_number', 'payload' => json_encode(null)],
            ['group' => 'general', 'name' => 'default_tax_rate', 'payload' => json_encode(18.0)],
            ['group' => 'general', 'name' => 'is_tax_inclusive', 'payload' => json_encode(false)],

            // General Settings (Inventory - used by ManageSystemSettings)
            ['group' => 'general', 'name' => 'valuation_method', 'payload' => json_encode('average')],
            ['group' => 'general', 'name' => 'enable_warehouses', 'payload' => json_encode(false)],
            ['group' => 'general', 'name' => 'enable_stock_alerts', 'payload' => json_encode(true)],
            ['group' => 'general', 'name' => 'low_stock_threshold', 'payload' => json_encode(10)],

            // Company Settings
            ['group' => 'company', 'name' => 'company_name', 'payload' => json_encode('My Company')],
            ['group' => 'company', 'name' => 'company_legal_name', 'payload' => json_encode(null)],
            ['group' => 'company', 'name' => 'company_address', 'payload' => json_encode(null)],
            ['group' => 'company', 'name' => 'company_city', 'payload' => json_encode(null)],
            ['group' => 'company', 'name' => 'company_state', 'payload' => json_encode(null)],
            ['group' => 'company', 'name' => 'company_zip', 'payload' => json_encode(null)],
            ['group' => 'company', 'name' => 'company_country', 'payload' => json_encode(null)],
            ['group' => 'company', 'name' => 'company_phone', 'payload' => json_encode(null)],
            ['group' => 'company', 'name' => 'company_email', 'payload' => json_encode(null)],
            ['group' => 'company', 'name' => 'company_website', 'payload' => json_encode(null)],
            ['group' => 'company', 'name' => 'tax_number', 'payload' => json_encode(null)],
            ['group' => 'company', 'name' => 'registration_number', 'payload' => json_encode(null)],
            ['group' => 'company', 'name' => 'gstin', 'payload' => json_encode(null)],
            ['group' => 'company', 'name' => 'pan_number', 'payload' => json_encode(null)],
            ['group' => 'company', 'name' => 'company_logo_path', 'payload' => json_encode(null)],
            ['group' => 'company', 'name' => 'company_favicon_path', 'payload' => json_encode(null)],
            ['group' => 'company', 'name' => 'company_primary_color', 'payload' => json_encode(null)],

            // Email Settings
            ['group' => 'email', 'name' => 'smtp_host', 'payload' => json_encode('localhost')],
            ['group' => 'email', 'name' => 'smtp_port', 'payload' => json_encode(587)],
            ['group' => 'email', 'name' => 'smtp_username', 'payload' => json_encode(null)],
            ['group' => 'email', 'name' => 'smtp_password', 'payload' => json_encode(null)],
            ['group' => 'email', 'name' => 'smtp_encryption', 'payload' => json_encode('tls')],
            ['group' => 'email', 'name' => 'mail_from_address', 'payload' => json_encode('noreply@example.com')],
            ['group' => 'email', 'name' => 'mail_from_name', 'payload' => json_encode('My Company')],
            ['group' => 'email', 'name' => 'enable_email_notifications', 'payload' => json_encode(true)],
            ['group' => 'email', 'name' => 'notify_on_invoice_creation', 'payload' => json_encode(true)],
            ['group' => 'email', 'name' => 'notify_on_payment_received', 'payload' => json_encode(true)],
            ['group' => 'email', 'name' => 'notify_on_low_stock', 'payload' => json_encode(false)],

            // Inventory Settings
            ['group' => 'inventory', 'name' => 'valuation_method', 'payload' => json_encode('average')],
            ['group' => 'inventory', 'name' => 'enable_warehouses', 'payload' => json_encode(false)],
            ['group' => 'inventory', 'name' => 'enable_stock_alerts', 'payload' => json_encode(true)],
            ['group' => 'inventory', 'name' => 'low_stock_threshold', 'payload' => json_encode(10)],
            ['group' => 'inventory', 'name' => 'enable_barcode', 'payload' => json_encode(false)],
            ['group' => 'inventory', 'name' => 'barcode_symbology', 'payload' => json_encode('CODE128')],
            ['group' => 'inventory', 'name' => 'allow_negative_stock', 'payload' => json_encode(false)],
            ['group' => 'inventory', 'name' => 'auto_generate_sku', 'payload' => json_encode(true)],
            ['group' => 'inventory', 'name' => 'sku_prefix', 'payload' => json_encode('SKU')],

            // Localization Settings
            ['group' => 'localization', 'name' => 'currency', 'payload' => json_encode('INR')],
            ['group' => 'localization', 'name' => 'currency_symbol', 'payload' => json_encode('₹')],
            ['group' => 'localization', 'name' => 'currency_precision', 'payload' => json_encode(2)],
            ['group' => 'localization', 'name' => 'language', 'payload' => json_encode('en')],
            ['group' => 'localization', 'name' => 'language_locale', 'payload' => json_encode('en_US')],
            ['group' => 'localization', 'name' => 'country', 'payload' => json_encode('India')],
            ['group' => 'localization', 'name' => 'country_code', 'payload' => json_encode('IN')],
            ['group' => 'localization', 'name' => 'timezone', 'payload' => json_encode('Asia/Kolkata')],
            ['group' => 'localization', 'name' => 'tax_type', 'payload' => json_encode('gst')],
            ['group' => 'localization', 'name' => 'default_tax_rate', 'payload' => json_encode(18.0)],
            ['group' => 'localization', 'name' => 'tax_number', 'payload' => json_encode(null)],
            ['group' => 'localization', 'name' => 'is_tax_inclusive', 'payload' => json_encode(false)],
            ['group' => 'localization', 'name' => 'date_format', 'payload' => json_encode('d/m/Y')],
            ['group' => 'localization', 'name' => 'time_format', 'payload' => json_encode('H:i')],
            ['group' => 'localization', 'name' => 'financial_year_start', 'payload' => json_encode('2024-04-01')],

            // Payment Settings
            ['group' => 'payment', 'name' => 'default_payment_gateway', 'payload' => json_encode('cod')],
            ['group' => 'payment', 'name' => 'stripe_key', 'payload' => json_encode(null)],
            ['group' => 'payment', 'name' => 'stripe_secret', 'payload' => json_encode(null)],
            ['group' => 'payment', 'name' => 'stripe_webhook_secret', 'payload' => json_encode(null)],
            ['group' => 'payment', 'name' => 'paypal_client_id', 'payload' => json_encode(null)],
            ['group' => 'payment', 'name' => 'paypal_secret', 'payload' => json_encode(null)],
            ['group' => 'payment', 'name' => 'paypal_sandbox_mode', 'payload' => json_encode(true)],
            ['group' => 'payment', 'name' => 'razorpay_key', 'payload' => json_encode(null)],
            ['group' => 'payment', 'name' => 'razorpay_secret', 'payload' => json_encode(null)],
            ['group' => 'payment', 'name' => 'enable_cod', 'payload' => json_encode(true)],
            ['group' => 'payment', 'name' => 'cod_max_amount', 'payload' => json_encode(5000.0)],
            ['group' => 'payment', 'name' => 'enable_bank_transfer', 'payload' => json_encode(true)],
            ['group' => 'payment', 'name' => 'bank_instructions', 'payload' => json_encode('Please transfer to our bank account.')],
            ['group' => 'payment', 'name' => 'enable_upi', 'payload' => json_encode(false)],
            ['group' => 'payment', 'name' => 'upi_id', 'payload' => json_encode(null)],

            // Security Settings
            ['group' => 'security', 'name' => 'password_min_length', 'payload' => json_encode(8)],
            ['group' => 'security', 'name' => 'password_require_special', 'payload' => json_encode(true)],
            ['group' => 'security', 'name' => 'password_require_numbers', 'payload' => json_encode(true)],
            ['group' => 'security', 'name' => 'password_require_mixed_case', 'payload' => json_encode(true)],
            ['group' => 'security', 'name' => 'password_expiry_days', 'payload' => json_encode(90)],
            ['group' => 'security', 'name' => 'enable_2fa', 'payload' => json_encode(false)],
            ['group' => 'security', 'name' => 'enforce_2fa_for_all', 'payload' => json_encode(false)],
            ['group' => 'security', 'name' => 'session_lifetime', 'payload' => json_encode(120)],
            ['group' => 'security', 'name' => 'single_session_per_user', 'payload' => json_encode(false)],
            ['group' => 'security', 'name' => 'audit_log_enabled', 'payload' => json_encode(true)],
            ['group' => 'security', 'name' => 'audit_log_retention_days', 'payload' => json_encode(365)],
            ['group' => 'security', 'name' => 'max_login_attempts', 'payload' => json_encode(5)],
            ['group' => 'security', 'name' => 'lockout_duration_minutes', 'payload' => json_encode(15)],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['group' => $setting['group'], 'name' => $setting['name']],
                ['payload' => $setting['payload'], 'updated_at' => now()]
            );
        }

        $this->command->info('Settings seeded successfully!');
    }
}

