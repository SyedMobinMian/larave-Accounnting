<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class CompanySettings extends Settings
{
    // Company Identity
    public string $company_name;
    public ?string $company_legal_name;
    public ?string $company_address;
    public ?string $company_city;
    public ?string $company_state;
    public ?string $company_zip;
    public ?string $company_country;
    public ?string $company_phone;
    public ?string $company_email;
    public ?string $company_website;

    // Registration
    public ?string $tax_number;
    public ?string $registration_number;
    public ?string $gstin;
    public ?string $pan_number;

    // Branding
    public ?string $company_logo_path;
    public ?string $company_favicon_path;
    public ?string $company_primary_color;

    public static function group(): string
    {
        return 'company';
    }
}

