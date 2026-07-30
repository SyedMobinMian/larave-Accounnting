<?php
namespace App\Filament\Pages;

use App\Settings\GeneralSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

/**
 * @deprecated This page is deprecated. Use App\Filament\Admin\Pages\Settings\SettingsWorkspace instead.
 *             All settings are now managed through the centralized Settings Workspace.
 *             This page is kept for backward compatibility but hidden from navigation.
 */
class ManageSystemSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = null;
    protected static ?string $navigationLabel = 'Configuration Center';
    protected static ?string $title = 'System Configuration Center';

    protected static string $settings = GeneralSettings::class;

    protected static bool $shouldRegisterNavigation = false;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Configuration')
                    ->tabs([
                        // Tab 1: General Information
                        Forms\Components\Tabs\Tab::make('General')
                            ->icon('heroicon-o-building-office')
                            ->schema([
                                Forms\Components\TextInput::make('company_name')
                                    ->required(),
                                Forms\Components\Textarea::make('company_address')
                                    ->rows(3),
                                Forms\Components\Select::make('timezone')
                                    ->options([
                                        'Asia/Kolkata' => 'Asia/Kolkata (IST)',
                                        'UTC' => 'UTC',
                                        'America/New_York' => 'America/New_York (EST)',
                                    ])
                                    ->required(),
                                Forms\Components\Select::make('currency')
                                    ->options([
                                        'INR' => 'INR (₹)',
                                        'USD' => 'USD ($)',
                                        'EUR' => 'EUR (€)',
                                    ])
                                    ->required(),
                                Forms\Components\TextInput::make('financial_year_start')
                                    ->label('Financial Year Start (MM-DD)')
                                    ->placeholder('04-01')
                                    ->required(),
                            ])->columns(2),

                        // Tab 2: Tax (GST/VAT)
                        Forms\Components\Tabs\Tab::make('Tax (GST/VAT)')
                            ->icon('heroicon-o-receipt-percent')
                            ->schema([
                                Forms\Components\Select::make('tax_type')
                                    ->options([
                                        'GST' => 'GST (Goods & Services Tax)',
                                        'VAT' => 'VAT (Value Added Tax)',
                                        'NONE' => 'No Tax Applicable',
                                    ])
                                    ->required(),
                                Forms\Components\TextInput::make('tax_number')
                                    ->label('Tax / GSTIN Number'),
                                Forms\Components\TextInput::make('default_tax_rate')
                                    ->numeric()
                                    ->suffix('%')
                                    ->required(),
                                Forms\Components\Toggle::make('is_tax_inclusive')
                                    ->label('Prices are Tax Inclusive')
                                    ->helperText('If enabled, prices in Quotations and Invoices will include tax by default.'),
                            ])->columns(2),

                        // Tab 3: Inventory Settings
                        Forms\Components\Tabs\Tab::make('Inventory')
                            ->icon('heroicon-o-cube')
                            ->schema([
                                Forms\Components\Select::make('valuation_method')
                                    ->label('Valuation Method')
                                    ->options([
                                        'FIFO' => 'FIFO (First In, First Out)',
                                        'LIFO' => 'LIFO (Last In, First Out)',
                                        'WAC' => 'WAC (Weighted Average Cost)',
                                    ])
                                    ->required(),
                                Forms\Components\TextInput::make('low_stock_threshold')
                                    ->numeric()
                                    ->label('Default Low Stock Alert Threshold'),
                                Forms\Components\Toggle::make('enable_warehouses')
                                    ->label('Enable Multi-Warehouse Management'),
                                Forms\Components\Toggle::make('enable_stock_alerts')
                                    ->label('Enable Low Stock Notifications'),
                            ])->columns(2),
                    ])->columnSpanFull(),
            ]);
    }
}