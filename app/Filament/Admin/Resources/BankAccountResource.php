<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\BankAccountResource\Pages;
use App\Models\BankAccount;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BankAccountResource extends Resource
{
    protected static ?string $model = BankAccount::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';

    public static function getNavigationGroup(): ?string
    {
        return __('Banking');
    }

    public static function getNavigationLabel(): string
    {
        return __('Bank & Cash Accounts');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Account Details'))
                    ->schema([
                        Forms\Components\TextInput::make('account_name')
                            ->label(__('Account Display Name'))
                            ->placeholder('e.g. HDFC Main Business Account')
                            ->required(),

                        Forms\Components\TextInput::make('bank_name')
                            ->label(__('Bank Name'))
                            ->placeholder('e.g. HDFC Bank')
                            ->required(),

                        Forms\Components\TextInput::make('account_number')
                            ->label(__('Account Number'))
                            ->required(),

                        Forms\Components\TextInput::make('ifsc_code')
                            ->label(__('IFSC Code'))
                            ->placeholder('e.g. HDFC0001234')
                            ->required(),

                        Forms\Components\TextInput::make('branch')
                            ->label(__('Branch Name')),

                        Forms\Components\TextInput::make('upi_id')
                            ->label(__('UPI ID / VPA (For QR Code)'))
                            ->placeholder('e.g. company@hdfcbank')
                            ->required(),

                        Forms\Components\TextInput::make('opening_balance')
                            ->label(__('Opening Balance'))
                            ->numeric()
                            ->prefix('₹')
                            ->default(0.00)
                            ->required(),

                        Forms\Components\Toggle::make('is_default')
                            ->label(__('Default Bank for Invoices'))
                            ->default(false),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('account_name')
                    ->label(__('Account Name'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('bank_name')
                    ->label(__('Bank'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('account_number')
                    ->label(__('Account #'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('ifsc_code')
                    ->label(__('IFSC Code')),

                Tables\Columns\TextColumn::make('upi_id')
                    ->label(__('UPI ID'))
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('opening_balance')
                    ->label(__('Balance'))
                    ->money('INR')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_default')
                    ->label(__('Default'))
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBankAccounts::route('/'),
            'create' => Pages\CreateBankAccount::route('/create'),
            'edit' => Pages\EditBankAccount::route('/{record}/edit'),
        ];
    }
}