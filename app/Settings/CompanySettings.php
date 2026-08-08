<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class CompanySettings extends Settings
{
    // Company Identity
    public string $company_name = 'My SaaS Company';
    public ?string $company_legal_name = null;
    public ?string $company_address = null;
    public ?string $company_city = null;
    public ?string $company_state = null;
    public ?string $company_zip = null;
    public ?string $company_country = null;
    public ?string $company_phone = null;
    public ?string $company_email = null;
    public ?string $company_website = null;

    // Registration
    public ?string $tax_number = null;
    public ?string $registration_number = null;
    public ?string $gstin = null;
    public ?string $pan_number = null;

    // Branding
    public ?string $company_logo_path = null;
    public ?string $company_favicon_path = null;
    public ?string $company_primary_color = '#f59e0b';

    public static function group(): string
    {
        return 'company';
    }
}

