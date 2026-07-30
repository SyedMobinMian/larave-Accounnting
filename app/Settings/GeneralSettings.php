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
    public bool $maintenance_mode;
    public bool $debug_mode;
    public bool $enable_registration;
    public string $default_user_role;
    public int $pagination_per_page;
    public string $application_theme;

    public static function group(): string
    {
        return 'general';
    }
}

