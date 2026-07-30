<?php

namespace App\Filament\Admin\Pages;

use App\Settings\GeneralSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManageSystemSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = null;

    protected static string $settings = GeneralSettings::class;

    protected static bool $shouldRegisterNavigation = false;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('General Settings')
                    ->description('Configure your company and system defaults')
                    ->schema([
                        Forms\Components\TextInput::make('company_name')
                            ->label('Company Name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('company_address')
                            ->label('Company Address')
                            ->maxLength(500)
                            ->columnSpanFull(),
                        Forms\Components\Select::make('timezone')
                            ->label('Timezone')
                            ->options(fn () => collect(timezone_identifiers_list())->mapWithKeys(fn ($tz) => [$tz => $tz]))
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('currency')
                            ->label('Default Currency')
                            ->options([
                                'INR' => '₹ INR (Indian Rupee)',
                                'USD' => '$ USD (US Dollar)',
                                'EUR' => '€ EUR (Euro)',
                                'GBP' => '£ GBP (British Pound)',
                                'AED' => 'د.إ AED (Dirham)',
                            ])
                            ->searchable()
                            ->required(),
                        Forms\Components\TextInput::make('financial_year_start')
                            ->label('Financial Year Start (e.g., 2024-04-01)')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Tax Settings')
                    ->description('Configure tax defaults for the system')
                    ->schema([
                        Forms\Components\Select::make('tax_type')
                            ->label('Tax Type')
                            ->options([
                                'gst' => 'GST',
                                'vat' => 'VAT',
                                'sales_tax' => 'Sales Tax',
                                'none' => 'No Tax',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('default_tax_rate')
                            ->label('Default Tax Rate (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->required(),
                        Forms\Components\TextInput::make('tax_number')
                            ->label('Tax Registration Number (GST/VAT)')
                            ->maxLength(50),
                        Forms\Components\Toggle::make('is_tax_inclusive')
                            ->label('Prices are Tax Inclusive')
                            ->helperText('If enabled, product prices will include tax by default.'),
                    ])->columns(2),

                Forms\Components\Section::make('Inventory Settings')
                    ->description('Configure inventory and stock management')
                    ->schema([
                        Forms\Components\Select::make('valuation_method')
                            ->label('Stock Valuation Method')
                            ->options([
                                'fifo' => 'FIFO (First In, First Out)',
                                'average' => 'Average Cost',
                                'lifo' => 'LIFO (Last In, First Out)',
                            ])
                            ->required(),
                        Forms\Components\Toggle::make('enable_warehouses')
                            ->label('Enable Warehouse Management'),
                        Forms\Components\Toggle::make('enable_stock_alerts')
                            ->label('Enable Low Stock Alerts'),
                        Forms\Components\TextInput::make('low_stock_threshold')
                            ->label('Low Stock Threshold')
                            ->numeric()
                            ->minValue(0)
                            ->default(10)
                            ->required(),
                    ])->columns(2),
            ]);
    }
}
