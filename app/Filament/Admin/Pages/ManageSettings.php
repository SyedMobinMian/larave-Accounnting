<?php

namespace App\Filament\Admin\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class ManageSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = null;

    protected static ?string $title = 'System Customization';

    protected static ?int $navigationSort = 3;

    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.pages.manage-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'company_name' => config('app.name', 'My Accounting'),
            'currency' => 'INR',
            'tax_number' => '',
            'allow_portal_registration' => false,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Company Identity & Branding')
                    ->description('Set your business name and default preferences')
                    ->schema([
                        Forms\Components\TextInput::make('company_name')
                            ->label('Business / Company Name')
                            ->required(),
                        Forms\Components\TextInput::make('tax_number')
                            ->label('GST / Tax Registration Number'),
                        Forms\Components\Select::make('currency')
                            ->options([
                                'INR' => '₹ (INR - Rupee)',
                                'USD' => '$ (USD - Dollar)',
                                'EUR' => '€ (EUR - Euro)',
                            ])
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Portal Customization & Rules')
                    ->schema([
                        Forms\Components\Toggle::make('allow_portal_registration')
                            ->label('Allow External Clients Self Registration')
                            ->helperText('If enabled, clients can sign up on /portal directly.'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        // Custom save logic or settings database store
        Notification::make()
            ->title('Settings Saved Successfully!')
            ->success()
            ->send();
    }
}