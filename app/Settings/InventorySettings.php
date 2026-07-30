<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class InventorySettings extends Settings
{
    // Valuation
    public string $valuation_method;

    // Warehouses
    public bool $enable_warehouses;
    public bool $enable_stock_alerts;
    public int $low_stock_threshold;

    // Barcode
    public bool $enable_barcode;
    public string $barcode_symbology;

    // Stock Rules
    public bool $allow_negative_stock;
    public bool $auto_generate_sku;
    public string $sku_prefix;

    public static function group(): string
    {
        return 'inventory';
    }
}

