<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class LocalizationSettings extends Settings
{
    // Currency
    public string $currency;
    public string $currency_symbol;
    public int $currency_precision;

    // Language
    public string $language;
    public string $language_locale;

    // Country
    public string $country;
    public string $country_code;

    // Timezone
    public string $timezone;

    // Tax Rules
    public string $tax_type;
    public float $default_tax_rate;
    public ?string $tax_number;
    public bool $is_tax_inclusive;

    // Date Format
    public string $date_format;
    public string $time_format;
    public string $financial_year_start;

    public static function group(): string
    {
        return 'localization';
    }
}

