<?php
namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    // General / Company Identity
    public string $company_name;
    public ?string $company_address;
    public string $timezone;
    public string $currency;
    public string $financial_year_start;

    // Tax Settings
    public string $tax_type;
    public ?string $tax_number;
    public float $default_tax_rate;
    public bool $is_tax_inclusive;

    // Inventory Settings
    public string $valuation_method;
    public bool $enable_warehouses;
    public bool $enable_stock_alerts;
    public int $low_stock_threshold;

    // System
    public bool $maintenance_mode = false;
    public bool $debug_mode = false;
    public bool $enable_registration = true;
    public string $default_user_role = 'panel_user';
    public int $pagination_per_page = 15;
    public string $application_theme = 'default';

    // Sales / Invoice Defaults
    public ?string $invoice_prefix = 'INV-';
    public int $invoice_start_number = 1;
    public string $default_invoice_status = 'draft';
    public int $payment_terms_days = 30;

    // Sales / Estimate Defaults
    public ?string $estimate_prefix = 'EST-';
    public int $estimate_validity_days = 30;
    public bool $auto_convert_to_invoice = false;

    // Finance / Chart of Accounts
    public ?string $default_revenue_account = null;
    public ?string $default_expense_account = null;
    public ?string $default_asset_account = null;
    public ?string $default_liability_account = null;
    public bool $auto_create_accounts = true;

    // Procurement
    public string $default_purchase_status = 'draft';
    public bool $require_purchase_approval = false;

    // Vendor Defaults
    public int $default_payment_terms = 30;
    public string $default_vendor_currency = 'INR';

// Appearance / Theme
    public ?string $company_primary_color = '#6366f1';
    public ?string $theme_accent_color = '#a855f7';
    public ?string $theme_sidebar_color = '#ffffff';
    public bool $sidebar_collapsed = false;
    public string $content_width = 'full';

    // Invoice Designer
    public string $invoice_template = 'modern';
    public string $invoice_page_size = 'a4';
    public bool $show_logo_on_invoice = true;
    public bool $show_qr_code = false;
    public bool $show_barcode = false;
    public bool $show_bank_details = true;
    public bool $show_terms = true;
    public bool $show_signature = false;

    // Access Management
    public bool $enable_user_registration = true;
    public bool $require_email_verification = false;
    public bool $enable_role_hierarchy = false;

    // Integrations / API
    public bool $enable_api_access = false;
    public int $api_rate_limit = 60;
    public bool $enable_webhooks = false;

    // AI
    public bool $enable_ai_assistant = false;
    public string $ai_provider = 'openai';
    public ?string $ai_api_key = null;
    public string $ai_model = 'gpt-3.5-turbo';

    // System
    public ?string $app_name = 'Laravel Accounting';
    public string $log_level = 'debug';
    public string $log_channel = 'stack';
    public int $log_retention_days = 30;
    public ?string $maintenance_message = null;
    public bool $enable_cache = true;
    public int $cache_ttl_seconds = 3600;

    public static function group(): string
    {
        return 'general';
    }
}


