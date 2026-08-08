<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class LocalizationSettings extends Settings
{
    // Currency
    public string $currency = 'INR';
    public string $currency_symbol = '₹';
    public int $currency_precision = 2;

    // Language
    public string $language = 'en';
    public string $language_locale = 'en_US';

    // Country
    public string $country = 'IN';
    public string $country_code = 'IN';

    // Timezone
    public string $timezone = 'Asia/Kolkata';

    // Tax Rules
    public string $tax_type = 'gst';
    public float $default_tax_rate = 18.0;
    public ?string $tax_number = null;
    public bool $is_tax_inclusive = false;

    // Date Format
    public string $date_format = 'Y-m-d';
    public string $time_format = 'H:i';
    public string $financial_year_start = '04-01';

    public static function group(): string
    {
        return 'localization';
    }
}

