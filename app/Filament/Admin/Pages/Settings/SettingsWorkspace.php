<?php

namespace App\Filament\Admin\Pages\Settings;

use App\Settings\CompanySettings;
use App\Settings\GeneralSettings;
use App\Settings\LocalizationSettings;
use App\Settings\InventorySettings;
use App\Settings\EmailSettings;
use App\Settings\PaymentSettings;
use App\Settings\SecuritySettings;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;

class SettingsWorkspace extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Settings';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $slug = 'settings';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.settings-workspace';

    public string $activeCategory = 'general';
    public string $activeTab = 'system-defaults';
    public ?array $data = [];

    protected string $currentSettingsClass = GeneralSettings::class;

    public static function getCategories(): array
    {
        return [
            'general' => ['label' => 'General', 'icon' => 'heroicon-o-adjustments-horizontal', 'group' => 'Core', 'tabs' => ['system-defaults' => ['label' => 'System Defaults', 'icon' => 'heroicon-o-server']]],
            'company' => ['label' => 'Company', 'icon' => 'heroicon-o-building-office', 'group' => 'Core', 'tabs' => ['company-info' => ['label' => 'Company Info', 'icon' => 'heroicon-o-information-circle'], 'branding' => ['label' => 'Branding', 'icon' => 'heroicon-o-photo']]],
            'localization' => ['label' => 'Localization', 'icon' => 'heroicon-o-language', 'group' => 'Core', 'tabs' => ['currency' => ['label' => 'Currency', 'icon' => 'heroicon-o-currency-dollar'], 'language' => ['label' => 'Language', 'icon' => 'heroicon-o-language'], 'country' => ['label' => 'Country', 'icon' => 'heroicon-o-globe-alt'], 'timezone' => ['label' => 'Timezone', 'icon' => 'heroicon-o-clock'], 'tax-rules' => ['label' => 'Tax Rules', 'icon' => 'heroicon-o-receipt-percent'], 'date-format' => ['label' => 'Date Format', 'icon' => 'heroicon-o-calendar']]],
            'finance' => ['label' => 'Finance', 'icon' => 'heroicon-o-calculator', 'group' => 'Business', 'tabs' => ['chart-of-accounts' => ['label' => 'Chart of Accounts', 'icon' => 'heroicon-o-rectangle-stack'], 'fiscal-year' => ['label' => 'Fiscal Year', 'icon' => 'heroicon-o-calendar-days'], 'tax-settings' => ['label' => 'Tax Settings', 'icon' => 'heroicon-o-receipt-percent'], 'payment-gateways' => ['label' => 'Payment Gateways', 'icon' => 'heroicon-o-credit-card'], 'bank-transfer' => ['label' => 'Bank Transfer', 'icon' => 'heroicon-o-building-library'], 'upi' => ['label' => 'UPI', 'icon' => 'heroicon-o-qr-code']]],
            'sales' => ['label' => 'Sales', 'icon' => 'heroicon-o-arrow-trending-up', 'group' => 'Business', 'tabs' => ['invoice-defaults' => ['label' => 'Invoice Defaults', 'icon' => 'heroicon-o-document-text'], 'estimate-defaults' => ['label' => 'Estimate Defaults', 'icon' => 'heroicon-o-document-duplicate']]],
            'procurement' => ['label' => 'Procurement', 'icon' => 'heroicon-o-shopping-bag', 'group' => 'Business', 'tabs' => ['purchase-settings' => ['label' => 'Purchase Settings', 'icon' => 'heroicon-o-cog'], 'vendor-defaults' => ['label' => 'Vendor Defaults', 'icon' => 'heroicon-o-truck']]],
            'inventory' => ['label' => 'Inventory', 'icon' => 'heroicon-o-cube', 'group' => 'Business', 'tabs' => ['warehouses' => ['label' => 'Warehouses', 'icon' => 'heroicon-o-building-storefront'], 'units-of-measure' => ['label' => 'Units of Measure', 'icon' => 'heroicon-o-scale'], 'categories' => ['label' => 'Categories', 'icon' => 'heroicon-o-tag'], 'stock-rules' => ['label' => 'Stock Rules', 'icon' => 'heroicon-o-exclamation-triangle']]],
            'appearance' => ['label' => 'Appearance & Branding', 'icon' => 'heroicon-o-paint-brush', 'group' => 'Appearance', 'tabs' => ['theme' => ['label' => 'Theme', 'icon' => 'heroicon-o-swatch'], 'layout' => ['label' => 'Layout', 'icon' => 'heroicon-o-table-cells'], 'invoice-designer' => ['label' => 'Invoice Designer', 'icon' => 'heroicon-o-document-chart-bar']]],
            'access-management' => ['label' => 'Access Management', 'icon' => 'heroicon-o-shield-check', 'group' => 'Administration', 'tabs' => ['users' => ['label' => 'All Users', 'icon' => 'heroicon-o-users'], 'roles-permissions' => ['label' => 'Roles & Permissions', 'icon' => 'heroicon-o-shield-check']]],
            'notifications' => ['label' => 'Notifications', 'icon' => 'heroicon-o-bell', 'group' => 'Administration', 'tabs' => ['email-settings' => ['label' => 'Email (SMTP)', 'icon' => 'heroicon-o-envelope'], 'notification-preferences' => ['label' => 'Notifications', 'icon' => 'heroicon-o-bell-alert']]],
            'security' => ['label' => 'Security', 'icon' => 'heroicon-o-lock-closed', 'group' => 'Administration', 'tabs' => ['password-policy' => ['label' => 'Password Policy', 'icon' => 'heroicon-o-key'], 'session' => ['label' => 'Session', 'icon' => 'heroicon-o-clock'], 'audit-log' => ['label' => 'Audit Log', 'icon' => 'heroicon-o-document-text']]],
            'integrations' => ['label' => 'Integrations', 'icon' => 'heroicon-o-puzzle-piece', 'group' => 'Platform', 'tabs' => ['payment-gateways' => ['label' => 'Payment Gateways', 'icon' => 'heroicon-o-credit-card'], 'api-keys' => ['label' => 'API Keys', 'icon' => 'heroicon-o-key']]],
            'ai' => ['label' => 'AI', 'icon' => 'heroicon-o-sparkles', 'group' => 'Platform', 'tabs' => ['ai-settings' => ['label' => 'AI Configuration', 'icon' => 'heroicon-o-cpu-chip']]],
            'system' => ['label' => 'System', 'icon' => 'heroicon-o-server-stack', 'group' => 'Platform', 'tabs' => ['system-info' => ['label' => 'System Information', 'icon' => 'heroicon-o-information-circle'], 'logs' => ['label' => 'System Logs', 'icon' => 'heroicon-o-document-text'], 'maintenance' => ['label' => 'Maintenance', 'icon' => 'heroicon-o-wrench-screwdriver']]],
        ];
    }

    public function mount(): void
    {
        $this->activeCategory = request()->query('category', 'general');
        $this->activeTab = request()->query('tab', $this->getDefaultTab());
        $this->loadFormData();
    }

    public function getCategoriesProperty(): array
    {
        return static::getCategories();
    }

    public function getTabsProperty(): array
    {
        $categories = static::getCategories();
        return $categories[$this->activeCategory]['tabs'] ?? [];
    }

    public function getTitle(): string
    {
        $categories = static::getCategories();
        $categoryLabel = $categories[$this->activeCategory]['label'] ?? 'Settings';
        return $categoryLabel . ' Settings';
    }

    public function updatedActiveCategory(): void
    {
        $this->activeTab = $this->getDefaultTab();
        $this->resetCachedForms();
        $this->loadFormData();
    }

    public function updatedActiveTab(): void
    {
        $this->resetCachedForms();
        $this->loadFormData();
    }

    protected function resetCachedForms(): void
    {
        // Filament caches the built form (schema) after the first render.
        // When switching categories/tabs, the schema must be rebuilt from
        // the newly selected tab, otherwise the stale "System Defaults"
        // form would keep rendering until a full page refresh.
        $this->cachedForms = null;
        $this->hasCachedForms = false;
    }

    protected function getDefaultTab(): string
    {
        $tabs = $this->getTabsProperty();
        return array_key_first($tabs) ?? 'system-defaults';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema($this->getActiveFormSchema())
            ->statePath('data');
    }

    protected function getActiveFormSchema(): array
    {
        $tabKey = $this->activeCategory . '.' . $this->activeTab;

        return match ($tabKey) {
            'general.system-defaults' => $this->getGeneralSystemDefaultsSchema(),
            'company.company-info' => $this->getCompanyInfoSchema(),
            'company.branding' => $this->getBrandingSchema(),
            'localization.currency' => $this->getCurrencySchema(),
            'localization.language' => $this->getLanguageSchema(),
            'localization.country' => $this->getCountrySchema(),
            'localization.timezone' => $this->getTimezoneSchema(),
            'localization.tax-rules' => $this->getTaxRulesSchema(),
            'localization.date-format' => $this->getDateFormatSchema(),
            'finance.chart-of-accounts' => $this->getChartOfAccountsSchema(),
            'finance.fiscal-year' => $this->getFiscalYearSchema(),
            'finance.tax-settings' => $this->getTaxSettingsSchema(),
            'finance.payment-gateways' => $this->getPaymentGatewaysSchema(),
            'finance.bank-transfer' => $this->getBankTransferSchema(),
            'finance.upi' => $this->getUpiSchema(),
            'sales.invoice-defaults' => $this->getInvoiceDefaultsSchema(),
            'sales.estimate-defaults' => $this->getEstimateDefaultsSchema(),
            'procurement.purchase-settings' => $this->getPurchaseSettingsSchema(),
            'procurement.vendor-defaults' => $this->getVendorDefaultsSchema(),
            'inventory.warehouses' => $this->getWarehousesSchema(),
            'inventory.units-of-measure' => $this->getUnitsOfMeasureSchema(),
            'inventory.categories' => $this->getCategoriesSchema(),
            'inventory.stock-rules' => $this->getStockRulesSchema(),
            'appearance.theme' => $this->getThemeSchema(),
            'appearance.layout' => $this->getLayoutSchema(),
            'appearance.invoice-designer' => $this->getInvoiceDesignerSchema(),
            'access-management.users' => $this->getUsersSchema(),
            'access-management.roles-permissions' => $this->getRolesPermissionsSchema(),
            'notifications.email-settings' => $this->getSmtpSettingsSchema(),
            'notifications.notification-preferences' => $this->getEmailNotificationsSchema(),
            'security.password-policy' => $this->getPasswordPolicySchema(),
            'security.session' => $this->getSessionSchema(),
            'security.audit-log' => $this->getAuditLogSchema(),
            'integrations.payment-gateways' => $this->getPaymentGatewaysSchema(),
            'integrations.api-keys' => $this->getApiKeysSchema(),
            'ai.ai-settings' => $this->getAiSettingsSchema(),
            'system.system-info' => $this->getSystemInfoSchema(),
            'system.logs' => $this->getSystemLogsSchema(),
            'system.maintenance' => $this->getMaintenanceSchema(),
            default => $this->getGeneralSystemDefaultsSchema(),
        };
    }

    protected function loadFormData(): void
    {
        $this->currentSettingsClass = $this->resolveSettingsClass();
        $settings = app($this->currentSettingsClass);
        $this->form->fill($settings->toArray());
    }

    protected function resolveSettingsClass(): string
    {
        $tabKey = $this->activeCategory . '.' . $this->activeTab;

        return match ($tabKey) {
            'general.system-defaults' => GeneralSettings::class,
            'company.company-info', 'company.branding' => CompanySettings::class,
            'localization.currency', 'localization.language', 'localization.country',
            'localization.timezone', 'localization.tax-rules', 'localization.date-format' => LocalizationSettings::class,
            'finance.chart-of-accounts', 'finance.fiscal-year', 'finance.tax-settings' => GeneralSettings::class,
            'finance.payment-gateways', 'finance.bank-transfer', 'finance.upi' => PaymentSettings::class,
            'inventory.stock-rules', 'inventory.warehouses', 'inventory.units-of-measure', 'inventory.categories' => InventorySettings::class,
            'notifications.email-settings', 'notifications.notification-preferences' => EmailSettings::class,
            'security.password-policy', 'security.session', 'security.audit-log' => SecuritySettings::class,
            default => GeneralSettings::class,
        };
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();
            $settingsClass = $this->currentSettingsClass;
            $settings = app($settingsClass);

            foreach ($data as $key => $value) {
                if (property_exists($settings, $key)) {
                    $settings->$key = $value;
                }
            }

            $settings->save();

            Notification::make()
                ->title('Settings saved successfully')
                ->success()
                ->send();
        } catch (Halt $e) {
            Notification::make()
                ->title('Failed to save settings')
                ->danger()
                ->send();
        }
    }

    // Form Schemas

    protected function getGeneralSystemDefaultsSchema(): array
    {
        return [Section::make('System Defaults')->description('Configure general system behavior')->schema([Toggle::make('maintenance_mode')->label('Maintenance Mode')->helperText('When enabled, only admins can access the system.'), Toggle::make('debug_mode')->label('Debug Mode')->helperText('Enable detailed error messages (disable in production).'), Toggle::make('enable_registration')->label('Enable User Registration')->default(true), Select::make('default_user_role')->label('Default Role for New Users')->options(fn() => \Spatie\Permission\Models\Role::pluck('name', 'name'))->default('panel_user'), TextInput::make('pagination_per_page')->label('Items Per Page')->numeric()->default(15)->minValue(5)->maxValue(100), Select::make('application_theme')->label('Application Theme')->options(['default' => 'Default', 'dark' => 'Dark', 'light' => 'Light'])->default('default')])->columns(2)];
    }

    protected function getCompanyInfoSchema(): array
    {
        return [Section::make('Company Information')->description('Manage your company details')->schema([TextInput::make('company_name')->label('Company Name')->required()->maxLength(255), TextInput::make('company_legal_name')->label('Legal Name')->maxLength(255), Textarea::make('company_address')->label('Address')->rows(3)->columnSpanFull(), TextInput::make('company_city')->label('City'), TextInput::make('company_state')->label('State'), TextInput::make('company_zip')->label('ZIP / Postal Code'), Select::make('company_country')->label('Country')->options(['IN' => 'India', 'US' => 'United States', 'GB' => 'United Kingdom', 'AE' => 'United Arab Emirates', 'SG' => 'Singapore'])->searchable(), TextInput::make('company_phone')->label('Phone')->tel(), TextInput::make('company_email')->label('Email')->email(), TextInput::make('company_website')->label('Website')->url()])->columns(2), Section::make('Registration Details')->schema([TextInput::make('tax_number')->label('Tax Registration Number'), TextInput::make('registration_number')->label('Company Registration Number'), TextInput::make('gstin')->label('GSTIN'), TextInput::make('pan_number')->label('PAN Number')])->columns(2)];
    }

    protected function getBrandingSchema(): array
    {
        return [Section::make('Branding')->description('Customize your company branding')->schema([TextInput::make('company_logo_path')->label('Logo URL / Path'), TextInput::make('company_favicon_path')->label('Favicon URL / Path'), TextInput::make('company_primary_color')->label('Primary Color (Hex)')->placeholder('#f59e0b')])->columns(2)];
    }

    protected function getCurrencySchema(): array
    {
        return [Section::make('Currency Settings')->schema([Select::make('currency')->label('Default Currency')->options(['INR' => 'INR (Indian Rupee)', 'USD' => 'USD (US Dollar)', 'EUR' => 'EUR (Euro)', 'GBP' => 'GBP (British Pound)', 'AED' => 'AED (Dirham)', 'SGD' => 'SGD (Singapore Dollar)'])->searchable()->required(), TextInput::make('currency_symbol')->label('Currency Symbol')->placeholder(''), Select::make('currency_precision')->label('Decimal Precision')->options([0 => '0 (No decimals)', 2 => '2 (Standard)', 3 => '3 (3 decimals)', 4 => '4 (4 decimals)'])->default(2)])->columns(2)];
    }

    protected function getLanguageSchema(): array
    {
        return [Section::make('Language Settings')->schema([Select::make('language')->label('Default Language')->options(['en' => 'English', 'hi' => 'Hindi', 'de' => 'German', 'es' => 'Spanish'])->required(), TextInput::make('language_locale')->label('Locale')->placeholder('en_US')->helperText('Full locale code for date/number formatting.')])->columns(2)];
    }

    protected function getCountrySchema(): array
    {
        return [Section::make('Country Settings')->schema([Select::make('country')->label('Default Country')->options(['IN' => 'India', 'US' => 'United States', 'GB' => 'United Kingdom', 'AE' => 'United Arab Emirates', 'SG' => 'Singapore'])->searchable()->required(), TextInput::make('country_code')->label('Country Code (ISO)')->placeholder('IN')->maxLength(2)])->columns(2)];
    }

    protected function getTimezoneSchema(): array
    {
        return [Section::make('Timezone Settings')->schema([Select::make('timezone')->label('Default Timezone')->options(fn() => collect(timezone_identifiers_list())->mapWithKeys(fn($tz) => [$tz => $tz]))->searchable()->required()])];
    }

    protected function getTaxRulesSchema(): array
    {
        return [Section::make('Tax Rules')->description('Configure tax calculation rules')->schema([Select::make('tax_type')->label('Tax Type')->options(['gst' => 'GST (Goods & Services Tax)', 'vat' => 'VAT (Value Added Tax)', 'sales_tax' => 'Sales Tax', 'none' => 'No Tax'])->required(), TextInput::make('default_tax_rate')->label('Default Tax Rate (%)')->numeric()->suffix('%')->minValue(0)->maxValue(100)->required(), TextInput::make('tax_number')->label('Tax Registration Number (GST/VAT)')->maxLength(50), Toggle::make('is_tax_inclusive')->label('Prices are Tax Inclusive')->helperText('If enabled, product prices will include tax by default.')])->columns(2)];
    }

    protected function getDateFormatSchema(): array
    {
        return [Section::make('Date & Time Format')->schema([Select::make('date_format')->label('Date Format')->options(['Y-m-d' => '2024-01-15 (Y-m-d)', 'd/m/Y' => '15/01/2024 (d/m/Y)', 'm/d/Y' => '01/15/2024 (m/d/Y)', 'd-m-Y' => '15-01-2024 (d-m-Y)', 'M d, Y' => 'Jan 15, 2024', 'd M Y' => '15 Jan 2024'])->required(), Select::make('time_format')->label('Time Format')->options(['H:i' => '14:30 (24-hour)', 'h:i A' => '02:30 PM (12-hour)'])->required(), TextInput::make('financial_year_start')->label('Financial Year Start')->placeholder('04-01')->helperText('Format: MM-DD (e.g., 04-01 for April 1st)')->required()])->columns(2)];
    }

    protected function getChartOfAccountsSchema(): array
    {
        return [Section::make('Chart of Accounts')->description('Configure default account settings')->schema([TextInput::make('default_revenue_account')->label('Default Revenue Account'), TextInput::make('default_expense_account')->label('Default Expense Account'), TextInput::make('default_asset_account')->label('Default Asset Account'), TextInput::make('default_liability_account')->label('Default Liability Account'), Toggle::make('auto_create_accounts')->label('Auto-create accounts on new module setup')->default(true)])->columns(2)];
    }

    protected function getFiscalYearSchema(): array
    {
        return [Section::make('Fiscal Year')->schema([TextInput::make('financial_year_start')->label('Financial Year Start (MM-DD)')->placeholder('04-01')->helperText('First month-day of your financial year')->required()])];
    }

    protected function getTaxSettingsSchema(): array
    {
        return $this->getTaxRulesSchema();
    }

    protected function getStockRulesSchema(): array
    {
        return [Section::make('Stock Valuation')->schema([Select::make('valuation_method')->label('Stock Valuation Method')->options(['fifo' => 'FIFO (First In, First Out)', 'average' => 'Average Cost', 'lifo' => 'LIFO (Last In, Last Out)'])->required()]), Section::make('Stock Alerts')->schema([Toggle::make('enable_warehouses')->label('Enable Warehouse Management')->helperText('Track inventory across multiple warehouses.'), Toggle::make('enable_stock_alerts')->label('Enable Low Stock Alerts'), TextInput::make('low_stock_threshold')->label('Low Stock Threshold')->numeric()->minValue(0)->default(10)->required(), Toggle::make('allow_negative_stock')->label('Allow Negative Stock')->helperText('Allow stock to go below zero.'), Toggle::make('auto_generate_sku')->label('Auto-Generate SKU')->helperText('Automatically generate SKU for new products.'), TextInput::make('sku_prefix')->label('SKU Prefix')->placeholder('SKU-')])->columns(2)];
    }

    protected function getPurchaseSettingsSchema(): array
    {
        return [Section::make('Purchase Order Settings')->schema([Select::make('default_purchase_status')->label('Default PO Status')->options(['draft' => 'Draft', 'pending' => 'Pending Approval', 'approved' => 'Approved'])->default('draft'), Toggle::make('require_purchase_approval')->label('Require Approval for Purchase Orders')])->columns(2)];
    }

    protected function getVendorDefaultsSchema(): array
    {
        return [Section::make('Vendor Defaults')->schema([TextInput::make('default_payment_terms')->label('Default Payment Terms (Days)')->numeric()->default(30)->suffix('days'), Select::make('default_vendor_currency')->label('Default Vendor Currency')->options(['INR' => 'INR ()', 'USD' => 'USD ($)', 'EUR' => 'EUR ()'])->default('INR')])->columns(2)];
    }

    protected function getPasswordPolicySchema(): array
    {
        return [Section::make('Password Policy')->schema([TextInput::make('password_min_length')->label('Minimum Password Length')->numeric()->default(8)->minValue(6)->maxValue(128), Toggle::make('password_require_special')->label('Require Special Characters')->default(true), Toggle::make('password_require_numbers')->label('Require Numbers')->default(true), Toggle::make('password_require_mixed_case')->label('Require Mixed Case')->default(true), TextInput::make('password_expiry_days')->label('Password Expiry (Days)')->numeric()->default(90)->helperText('Set to 0 for no expiry.'), TextInput::make('max_login_attempts')->label('Max Login Attempts')->numeric()->default(5), TextInput::make('lockout_duration_minutes')->label('Lockout Duration (Minutes)')->numeric()->default(30)])->columns(2)];
    }

    protected function getSessionSchema(): array
    {
        return [Section::make('Session Configuration')->schema([TextInput::make('session_lifetime')->label('Session Lifetime (Minutes)')->numeric()->default(120)->helperText('How long before a session expires.'), Toggle::make('single_session_per_user')->label('Single Session Per User')->helperText('Force logout from other devices when logging in.'), Toggle::make('enable_2fa')->label('Enable Two-Factor Authentication'), Toggle::make('enforce_2fa_for_all')->label('Enforce 2FA for All Users')->helperText('Require 2FA setup for all users.')])->columns(2)];
    }

    protected function getAuditLogSchema(): array
    {
        return [Section::make('Audit Log')->schema([Toggle::make('audit_log_enabled')->label('Enable Audit Logging')->helperText('Track all significant system changes.'), TextInput::make('audit_log_retention_days')->label('Retention Period (Days)')->numeric()->default(365)->helperText('How long to keep audit logs before automatic cleanup.')])->columns(2)];
    }

    protected function getSmtpSettingsSchema(): array
    {
        return [Section::make('SMTP Configuration')->description('Configure your email server settings')->schema([TextInput::make('smtp_host')->label('SMTP Host')->placeholder('smtp.gmail.com')->required(), TextInput::make('smtp_port')->label('SMTP Port')->numeric()->default(587)->required(), TextInput::make('smtp_username')->label('SMTP Username'), TextInput::make('smtp_password')->label('SMTP Password')->password(), Select::make('smtp_encryption')->label('Encryption')->options(['tls' => 'TLS', 'ssl' => 'SSL', 'none' => 'None'])->default('tls'), TextInput::make('mail_from_address')->label('From Address')->email()->placeholder('noreply@example.com')->required(), TextInput::make('mail_from_name')->label('From Name')->placeholder('My Company')])->columns(2)];
    }

    protected function getEmailNotificationsSchema(): array
    {
        return [Section::make('Email Notifications')->schema([Toggle::make('enable_email_notifications')->label('Enable Email Notifications')->default(true), Toggle::make('notify_on_invoice_creation')->label('Notify on Invoice Creation'), Toggle::make('notify_on_payment_received')->label('Notify on Payment Received'), Toggle::make('notify_on_low_stock')->label('Notify on Low Stock')])->columns(2)];
    }

    protected function getPaymentGatewaysSchema(): array
    {
        return [Section::make('Default Payment Gateway')->schema([Select::make('default_payment_gateway')->label('Default Gateway')->options(['stripe' => 'Stripe', 'paypal' => 'PayPal', 'razorpay' => 'Razorpay', 'bank_transfer' => 'Bank Transfer', 'cod' => 'Cash on Delivery'])->default('bank_transfer')]), Section::make('Stripe')->schema([TextInput::make('stripe_key')->label('Publishable Key'), TextInput::make('stripe_secret')->label('Secret Key')->password(), TextInput::make('stripe_webhook_secret')->label('Webhook Secret')->password()])->columns(2), Section::make('PayPal')->schema([TextInput::make('paypal_client_id')->label('Client ID'), TextInput::make('paypal_secret')->label('Secret')->password(), Toggle::make('paypal_sandbox_mode')->label('Sandbox Mode')])->columns(2), Section::make('Razorpay')->schema([TextInput::make('razorpay_key')->label('Key ID'), TextInput::make('razorpay_secret')->label('Key Secret')->password()])->columns(2)];
    }

    protected function getBankTransferSchema(): array
    {
        return [Section::make('Bank Transfer')->schema([Toggle::make('enable_bank_transfer')->label('Enable Bank Transfer')->default(true), Textarea::make('bank_instructions')->label('Bank Transfer Instructions')->rows(4)->helperText('Instructions shown to customers when they choose bank transfer.')])];
    }

    protected function getUpiSchema(): array
    {
        return [Section::make('UPI Settings')->schema([Toggle::make('enable_upi')->label('Enable UPI Payments')->default(true), TextInput::make('upi_id')->label('UPI ID / VPA')->placeholder('company@upi')])->columns(2)];
    }

    protected function getThemeSchema(): array
    {
        return [Section::make('Theme Settings')->schema([Select::make('application_theme')->label('Theme')->options(['default' => 'Default (Amber)', 'blue' => 'Blue', 'green' => 'Green', 'purple' => 'Purple', 'red' => 'Red'])->default('default'), TextInput::make('company_primary_color')->label('Primary Color (Hex)')->placeholder('#f59e0b')])->columns(2)];
    }

    protected function getLayoutSchema(): array
    {
        return [Section::make('Layout Settings')->schema([Select::make('sidebar_collapsed')->label('Sidebar Default State')->options(['false' => 'Expanded', 'true' => 'Collapsed'])->default('false'), Select::make('content_width')->label('Content Width')->options(['full' => 'Full Width', 'xl' => 'Extra Large', 'lg' => 'Large', 'md' => 'Medium'])->default('full')])->columns(2)];
    }

    protected function getInvoiceDefaultsSchema(): array
    {
        return [Section::make('Invoice Defaults')->description('Configure default settings for new invoices')->schema([TextInput::make('invoice_prefix')->label('Invoice Number Prefix')->placeholder('INV-'), TextInput::make('invoice_start_number')->label('Starting Invoice Number')->numeric()->default(1), Select::make('default_invoice_status')->label('Default Status')->options(['draft' => 'Draft', 'unpaid' => 'Unpaid'])->default('draft'), TextInput::make('payment_terms_days')->label('Default Payment Terms (Days)')->numeric()->default(30)->suffix('days')])->columns(2)];
    }

    protected function getEstimateDefaultsSchema(): array
    {
        return [Section::make('Estimate Defaults')->description('Configure default settings for estimates/quotes')->schema([TextInput::make('estimate_prefix')->label('Estimate Number Prefix')->placeholder('EST-'), TextInput::make('estimate_validity_days')->label('Estimate Validity (Days)')->numeric()->default(30)->suffix('days'), Toggle::make('auto_convert_to_invoice')->label('Auto-convert accepted estimates to invoices')])->columns(2)];
    }

    protected function getUsersSchema(): array
    {
        return [Section::make('User Management')->description('Manage system users')->schema([Toggle::make('enable_user_registration')->label('Allow Self-Registration')->default(true), Select::make('default_user_role')->label('Default Role for New Users')->options(fn() => \Spatie\Permission\Models\Role::pluck('name', 'name'))->default('panel_user'), Toggle::make('require_email_verification')->label('Require Email Verification')])->columns(2)];
    }

    protected function getRolesPermissionsSchema(): array
    {
        return [Section::make('Roles & Permissions')->description('Configure access control roles and their permissions')->schema([Toggle::make('enable_role_hierarchy')->label('Enable Role Hierarchy')])];
    }

    protected function getInvoiceDesignerSchema(): array
    {
        return [Section::make('Invoice Designer')->description('Customize the look and feel of your invoices')->schema([Select::make('invoice_template')->label('Default Template')->options(['modern' => 'Modern', 'classic' => 'Classic', 'minimal' => 'Minimal', 'professional' => 'Professional'])->default('modern'), Select::make('invoice_page_size')->label('Page Size')->options(['a4' => 'A4', 'letter' => 'Letter', 'legal' => 'Legal'])->default('a4'), Toggle::make('show_logo_on_invoice')->label('Show Company Logo')->default(true), Toggle::make('show_qr_code')->label('Show QR Code (UPI)'), Toggle::make('show_barcode')->label('Show Barcode'), Toggle::make('show_bank_details')->label('Show Bank Transfer Details'), Toggle::make('show_terms')->label('Show Terms & Conditions'), Toggle::make('show_signature')->label('Show Signature Area')])->columns(2)];
    }

    protected function getApiKeysSchema(): array
    {
        return [Section::make('API Keys')->description('Manage API integrations')->schema([Toggle::make('enable_api_access')->label('Enable REST API Access'), TextInput::make('api_rate_limit')->label('API Rate Limit (per minute)')->numeric()->default(60), Toggle::make('enable_webhooks')->label('Enable Webhooks')])->columns(2)];
    }

    protected function getAiSettingsSchema(): array
    {
        return [Section::make('AI Configuration')->description('Configure AI-powered features')->schema([Toggle::make('enable_ai_assistant')->label('Enable AI Assistant'), Select::make('ai_provider')->label('AI Provider')->options(['openai' => 'OpenAI', 'gemini' => 'Google Gemini', 'claude' => 'Anthropic Claude'])->default('openai'), TextInput::make('ai_api_key')->label('API Key')->password(), Select::make('ai_model')->label('Model')->options(['gpt-4' => 'GPT-4', 'gpt-3.5-turbo' => 'GPT-3.5 Turbo'])->default('gpt-3.5-turbo')])->columns(2)];
    }

    protected function getSystemInfoSchema(): array
    {
        return [Section::make('System Information')->description('System environment details')->schema([TextInput::make('app_name')->label('Application Name')->default(config('app.name')), TextInput::make('app_version')->label('Version')->default('1.0.0'), TextInput::make('php_version')->label('PHP Version')->default(PHP_VERSION)->disabled(), TextInput::make('laravel_version')->label('Laravel Version')->default(app()->version())->disabled(), TextInput::make('database_connection')->label('Database')->default(config('database.default'))->disabled(), TextInput::make('environment')->label('Environment')->default(app()->environment())->disabled()])->columns(2)];
    }

    protected function getSystemLogsSchema(): array
    {
        return [Section::make('System Logs')->description('Configure logging preferences')->schema([Select::make('log_level')->label('Minimum Log Level')->options(['debug' => 'Debug', 'info' => 'Info', 'notice' => 'Notice', 'warning' => 'Warning', 'error' => 'Error', 'critical' => 'Critical', 'alert' => 'Alert', 'emergency' => 'Emergency'])->default('debug'), Select::make('log_channel')->label('Log Channel')->options(['stack' => 'Stack (Multiple Channels)', 'single' => 'Single File', 'daily' => 'Daily File', 'syslog' => 'Syslog', 'errorlog' => 'Error Log'])->default('stack'), TextInput::make('log_retention_days')->label('Log Retention (Days)')->numeric()->default(30)])->columns(2)];
    }

    protected function getMaintenanceSchema(): array
    {
        return [Section::make('Maintenance Mode')->description('Control system maintenance')->schema([Toggle::make('maintenance_mode')->label('Enable Maintenance Mode')->helperText('Only admins can access the system when enabled.'), Textarea::make('maintenance_message')->label('Maintenance Message')->rows(3)->helperText('Message displayed to users during maintenance.')]), Section::make('Cache & Optimization')->schema([Toggle::make('enable_cache')->label('Enable Cache')->default(true), TextInput::make('cache_ttl_seconds')->label('Cache Duration (Seconds)')->numeric()->default(3600)])->columns(2)];
    }

    protected function getWarehousesSchema(): array
    {
        return [Section::make('Warehouse Management')->description('Configure warehouse settings')->schema([Toggle::make('enable_warehouses')->label('Enable Warehouse Management')->helperText('Track inventory across multiple physical locations.')])];
    }

    protected function getUnitsOfMeasureSchema(): array
    {
        return [Section::make('Units of Measure')->description('Configure default measurement units')->schema([Select::make('default_unit')->label('Default Unit')->options(['pcs' => 'Pieces (pcs)', 'kg' => 'Kilogram (kg)', 'g' => 'Gram (g)', 'm' => 'Meter (m)', 'box' => 'Box', 'hrs' => 'Hours (hrs)'])->default('pcs')])];
    }

    protected function getCategoriesSchema(): array
    {
        return [Section::make('Category Settings')->description('Configure product category defaults')->schema([Toggle::make('enable_categories')->label('Enable Product Categories')->default(true), Toggle::make('enforce_single_category')->label('Enforce Single Category per Product')->helperText('If disabled, products can belong to multiple categories.')])->columns(2)];
    }

    public function renderActiveTabContent(): string
    {
        return '';
    }
}
