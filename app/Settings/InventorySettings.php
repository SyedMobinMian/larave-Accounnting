<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class InventorySettings extends Settings
{
    // Valuation
    public string $valuation_method = 'average';

    // Warehouses
    public bool $enable_warehouses = false;
    public bool $enable_stock_alerts = true;
    public int $low_stock_threshold = 10;

    // Barcode
    public bool $enable_barcode = false;
    public string $barcode_symbology = 'code128';

    // Stock Rules
    public bool $allow_negative_stock = false;
    public bool $auto_generate_sku = true;
    public string $sku_prefix = 'SKU-';

    // Units of Measure
    public string $default_unit = 'pcs';

    // Categories
    public bool $enable_categories = true;
    public bool $enforce_single_category = true;

    public static function group(): string
    {
        return 'inventory';
    }
}

