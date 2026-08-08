<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Seed all settings properties that are declared on the Settings classes
 * but are missing from the settings table.
 *
 * spatie/laravel-settings throws MissingSettings on save() for any property
 * that is not already stored in the DB (properties that were only loaded via
 * their PHP default value are still considered "missing" when persisting).
 *
 * This migration ensures every property from GeneralSettings, CompanySettings,
 * LocalizationSettings, InventorySettings, PaymentSettings, SecuritySettings
 * and EmailSettings has a row so saving any tab works.
 */
return new class extends Migration
{
    public function up(): void
    {
        $settings = [
            // ───────────────────────── General ─────────────────────────
            ['group' => 'general', 'name' => 'company_name', 'payload' => 'My SaaS Company'],
            ['group' => 'general', 'name' => 'company_address', 'payload' => null],
            ['group' => 'general', 'name' => 'timezone', 'payload' => 'Asia/Kolkata'],
            ['group' => 'general', 'name' => 'currency', 'payload' => 'INR'],
            ['group' => 'general', 'name' => 'financial_year_start', 'payload' => '04-01'],
            ['group' => 'general', 'name' => 'tax_type', 'payload' => 'gst'],
            ['group' => 'general', 'name' => 'tax_number', 'payload' => null],
            ['group' => 'general', 'name' => 'default_tax_rate', 'payload' => 18.0],
            ['group' => 'general', 'name' => 'is_tax_inclusive', 'payload' => false],
            ['group' => 'general', 'name' => 'valuation_method', 'payload' => 'average'],
            ['group' => 'general', 'name' => 'enable_warehouses', 'payload' => false],
            ['group' => 'general', 'name' => 'enable_stock_alerts', 'payload' => true],
            ['group' => 'general', 'name' => 'low_stock_threshold', 'payload' => 10],
            ['group' => 'general', 'name' => 'maintenance_mode', 'payload' => false],
            ['group' => 'general', 'name' => 'debug_mode', 'payload' => false],
            ['group' => 'general', 'name' => 'enable_registration', 'payload' => true],
            ['group' => 'general', 'name' => 'default_user_role', 'payload' => 'panel_user'],
            ['group' => 'general', 'name' => 'pagination_per_page', 'payload' => 15],
            ['group' => 'general', 'name' => 'application_theme', 'payload' => 'default'],

            // Sales / Invoice Defaults
            ['group' => 'general', 'name' => 'invoice_prefix', 'payload' => 'INV-'],
            ['group' => 'general', 'name' => 'invoice_start_number', 'payload' => 1],
            ['group' => 'general', 'name' => 'default_invoice_status', 'payload' => 'draft'],
            ['group' => 'general', 'name' => 'payment_terms_days', 'payload' => 30],

            // Sales / Estimate Defaults
            ['group' => 'general', 'name' => 'estimate_prefix', 'payload' => 'EST-'],
            ['group' => 'general', 'name' => 'estimate_validity_days', 'payload' => 30],
            ['group' => 'general', 'name' => 'auto_convert_to_invoice', 'payload' => false],

            // Finance / Chart of Accounts
            ['group' => 'general', 'name' => 'default_revenue_account', 'payload' => null],
            ['group' => 'general', 'name' => 'default_expense_account', 'payload' => null],
            ['group' => 'general', 'name' => 'default_asset_account', 'payload' => null],
            ['group' => 'general', 'name' => 'default_liability_account', 'payload' => null],
            ['group' => 'general', 'name' => 'auto_create_accounts', 'payload' => true],

            // Procurement
            ['group' => 'general', 'name' => 'default_purchase_status', 'payload' => 'draft'],
            ['group' => 'general', 'name' => 'require_purchase_approval', 'payload' => false],

            // Vendor Defaults
            ['group' => 'general', 'name' => 'default_payment_terms', 'payload' => 30],
            ['group' => 'general', 'name' => 'default_vendor_currency', 'payload' => 'INR'],

            // Appearance / Theme
            ['group' => 'general', 'name' => 'company_primary_color', 'payload' => '#6366f1'],
            ['group' => 'general', 'name' => 'theme_accent_color', 'payload' => '#a855f7'],
            ['group' => 'general', 'name' => 'theme_sidebar_color', 'payload' => '#ffffff'],
            ['group' => 'general', 'name' => 'sidebar_collapsed', 'payload' => false],
            ['group' => 'general', 'name' => 'content_width', 'payload' => 'full'],

            // Invoice Designer
            ['group' => 'general', 'name' => 'invoice_template', 'payload' => 'modern'],
            ['group' => 'general', 'name' => 'invoice_page_size', 'payload' => 'a4'],
            ['group' => 'general', 'name' => 'show_logo_on_invoice', 'payload' => true],
            ['group' => 'general', 'name' => 'show_qr_code', 'payload' => false],
            ['group' => 'general', 'name' => 'show_barcode', 'payload' => false],
            ['group' => 'general', 'name' => 'show_bank_details', 'payload' => true],
            ['group' => 'general', 'name' => 'show_terms', 'payload' => true],
            ['group' => 'general', 'name' => 'show_signature', 'payload' => false],

            // Access Management
            ['group' => 'general', 'name' => 'enable_user_registration', 'payload' => true],
            ['group' => 'general', 'name' => 'require_email_verification', 'payload' => false],
            ['group' => 'general', 'name' => 'enable_role_hierarchy', 'payload' => false],

            // Integrations / API
            ['group' => 'general', 'name' => 'enable_api_access', 'payload' => false],
            ['group' => 'general', 'name' => 'api_rate_limit', 'payload' => 60],
            ['group' => 'general', 'name' => 'enable_webhooks', 'payload' => false],

            // AI
            ['group' => 'general', 'name' => 'enable_ai_assistant', 'payload' => false],
            ['group' => 'general', 'name' => 'ai_provider', 'payload' => 'openai'],
            ['group' => 'general', 'name' => 'ai_api_key', 'payload' => null],
            ['group' => 'general', 'name' => 'ai_model', 'payload' => 'gpt-3.5-turbo'],

            // System
            ['group' => 'general', 'name' => 'app_name', 'payload' => 'Laravel Accounting'],
            ['group' => 'general', 'name' => 'log_level', 'payload' => 'debug'],
            ['group' => 'general', 'name' => 'log_channel', 'payload' => 'stack'],
            ['group' => 'general', 'name' => 'log_retention_days', 'payload' => 30],
            ['group' => 'general', 'name' => 'maintenance_message', 'payload' => null],
            ['group' => 'general', 'name' => 'enable_cache', 'payload' => true],
            ['group' => 'general', 'name' => 'cache_ttl_seconds', 'payload' => 3600],

            // ───────────────────────── Company ─────────────────────────
            ['group' => 'company', 'name' => 'company_name', 'payload' => 'My SaaS Company'],
            ['group' => 'company', 'name' => 'company_legal_name', 'payload' => null],
            ['group' => 'company', 'name' => 'company_address', 'payload' => null],
            ['group' => 'company', 'name' => 'company_city', 'payload' => null],
            ['group' => 'company', 'name' => 'company_state', 'payload' => null],
            ['group' => 'company', 'name' => 'company_zip', 'payload' => null],
            ['group' => 'company', 'name' => 'company_country', 'payload' => null],
            ['group' => 'company', 'name' => 'company_phone', 'payload' => null],
            ['group' => 'company', 'name' => 'company_email', 'payload' => null],
            ['group' => 'company', 'name' => 'company_website', 'payload' => null],
            ['group' => 'company', 'name' => 'tax_number', 'payload' => null],
            ['group' => 'company', 'name' => 'registration_number', 'payload' => null],
            ['group' => 'company', 'name' => 'gstin', 'payload' => null],
            ['group' => 'company', 'name' => 'pan_number', 'payload' => null],
            ['group' => 'company', 'name' => 'company_logo_path', 'payload' => null],
            ['group' => 'company', 'name' => 'company_favicon_path', 'payload' => null],
            ['group' => 'company', 'name' => 'company_primary_color', 'payload' => '#f59e0b'],

            // ───────────────────────── Localization ─────────────────────────
            ['group' => 'localization', 'name' => 'currency', 'payload' => 'INR'],
            ['group' => 'localization', 'name' => 'currency_symbol', 'payload' => '₹'],
            ['group' => 'localization', 'name' => 'currency_precision', 'payload' => 2],
            ['group' => 'localization', 'name' => 'language', 'payload' => 'en'],
            ['group' => 'localization', 'name' => 'language_locale', 'payload' => 'en_US'],
            ['group' => 'localization', 'name' => 'country', 'payload' => 'IN'],
            ['group' => 'localization', 'name' => 'country_code', 'payload' => 'IN'],
            ['group' => 'localization', 'name' => 'timezone', 'payload' => 'Asia/Kolkata'],
            ['group' => 'localization', 'name' => 'tax_type', 'payload' => 'gst'],
            ['group' => 'localization', 'name' => 'default_tax_rate', 'payload' => 18.0],
            ['group' => 'localization', 'name' => 'tax_number', 'payload' => null],
            ['group' => 'localization', 'name' => 'is_tax_inclusive', 'payload' => false],
            ['group' => 'localization', 'name' => 'date_format', 'payload' => 'Y-m-d'],
            ['group' => 'localization', 'name' => 'time_format', 'payload' => 'H:i'],
            ['group' => 'localization', 'name' => 'financial_year_start', 'payload' => '04-01'],

            // ───────────────────────── Inventory ─────────────────────────
            ['group' => 'inventory', 'name' => 'valuation_method', 'payload' => 'average'],
            ['group' => 'inventory', 'name' => 'enable_warehouses', 'payload' => false],
            ['group' => 'inventory', 'name' => 'enable_stock_alerts', 'payload' => true],
            ['group' => 'inventory', 'name' => 'low_stock_threshold', 'payload' => 10],
            ['group' => 'inventory', 'name' => 'enable_barcode', 'payload' => false],
            ['group' => 'inventory', 'name' => 'barcode_symbology', 'payload' => 'code128'],
            ['group' => 'inventory', 'name' => 'allow_negative_stock', 'payload' => false],
            ['group' => 'inventory', 'name' => 'auto_generate_sku', 'payload' => true],
            ['group' => 'inventory', 'name' => 'sku_prefix', 'payload' => 'SKU-'],
            ['group' => 'inventory', 'name' => 'default_unit', 'payload' => 'pcs'],
            ['group' => 'inventory', 'name' => 'enable_categories', 'payload' => true],
            ['group' => 'inventory', 'name' => 'enforce_single_category', 'payload' => true],

            // ───────────────────────── Payment ─────────────────────────
            ['group' => 'payment', 'name' => 'default_payment_gateway', 'payload' => 'bank_transfer'],
            ['group' => 'payment', 'name' => 'stripe_key', 'payload' => null],
            ['group' => 'payment', 'name' => 'stripe_secret', 'payload' => null],
            ['group' => 'payment', 'name' => 'stripe_webhook_secret', 'payload' => null],
            ['group' => 'payment', 'name' => 'paypal_client_id', 'payload' => null],
            ['group' => 'payment', 'name' => 'paypal_secret', 'payload' => null],
            ['group' => 'payment', 'name' => 'paypal_sandbox_mode', 'payload' => true],
            ['group' => 'payment', 'name' => 'razorpay_key', 'payload' => null],
            ['group' => 'payment', 'name' => 'razorpay_secret', 'payload' => null],
            ['group' => 'payment', 'name' => 'enable_cod', 'payload' => false],
            ['group' => 'payment', 'name' => 'cod_max_amount', 'payload' => 50000],
            ['group' => 'payment', 'name' => 'enable_bank_transfer', 'payload' => true],
            ['group' => 'payment', 'name' => 'bank_instructions', 'payload' => null],
            ['group' => 'payment', 'name' => 'enable_upi', 'payload' => true],
            ['group' => 'payment', 'name' => 'upi_id', 'payload' => null],

            // ───────────────────────── Security ─────────────────────────
            ['group' => 'security', 'name' => 'password_min_length', 'payload' => 8],
            ['group' => 'security', 'name' => 'password_require_special', 'payload' => true],
            ['group' => 'security', 'name' => 'password_require_numbers', 'payload' => true],
            ['group' => 'security', 'name' => 'password_require_mixed_case', 'payload' => true],
            ['group' => 'security', 'name' => 'password_expiry_days', 'payload' => 90],
            ['group' => 'security', 'name' => 'enable_2fa', 'payload' => false],
            ['group' => 'security', 'name' => 'enforce_2fa_for_all', 'payload' => false],
            ['group' => 'security', 'name' => 'session_lifetime', 'payload' => 120],
            ['group' => 'security', 'name' => 'single_session_per_user', 'payload' => false],
            ['group' => 'security', 'name' => 'audit_log_enabled', 'payload' => true],
            ['group' => 'security', 'name' => 'audit_log_retention_days', 'payload' => 365],
            ['group' => 'security', 'name' => 'max_login_attempts', 'payload' => 5],
            ['group' => 'security', 'name' => 'lockout_duration_minutes', 'payload' => 30],

            // ───────────────────────── Email ─────────────────────────
            ['group' => 'email', 'name' => 'smtp_host', 'payload' => 'smtp.gmail.com'],
            ['group' => 'email', 'name' => 'smtp_port', 'payload' => 587],
            ['group' => 'email', 'name' => 'smtp_username', 'payload' => null],
            ['group' => 'email', 'name' => 'smtp_password', 'payload' => null],
            ['group' => 'email', 'name' => 'smtp_encryption', 'payload' => 'tls'],
            ['group' => 'email', 'name' => 'mail_from_address', 'payload' => 'noreply@example.com'],
            ['group' => 'email', 'name' => 'mail_from_name', 'payload' => 'My Company'],
            ['group' => 'email', 'name' => 'enable_email_notifications', 'payload' => true],
            ['group' => 'email', 'name' => 'notify_on_invoice_creation', 'payload' => true],
            ['group' => 'email', 'name' => 'notify_on_payment_received', 'payload' => true],
            ['group' => 'email', 'name' => 'notify_on_low_stock', 'payload' => true],
            ['group' => 'email', 'name' => 'notification_preferences', 'payload' => false],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['group' => $setting['group'], 'name' => $setting['name']],
                [
                    'payload' => json_encode($setting['payload']),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    public function down(): void
    {
        // Intentionally no-op: we don't want to delete existing settings values.
    }
};

