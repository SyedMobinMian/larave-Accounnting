<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\BankAccountResource\Pages;
use App\Models\BankAccount;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BankAccountResource extends Resource
{
    protected static ?string $model = BankAccount::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';
    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return __('Banking');
    }

    public static function getNavigationLabel(): string
    {
        return __('Bank Accounts');
    }

    public static function getModelLabel(): string
    {
        return __('Bank Account');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Bank Accounts');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Account Details'))
                    ->description(__('Bank account information'))
                    ->icon('heroicon-o-building-library')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('Account Name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('bank_name')
                            ->label(__('Bank Name'))
                            ->maxLength(255),
                        Forms\Components\TextInput::make('account_number')
                            ->label(__('Account Number'))
                            ->maxLength(255),
                        Forms\Components\TextInput::make('ifsc_code')
                            ->label(__('IFSC Code'))
                            ->maxLength(255),
                        Forms\Components\TextInput::make('branch')
                            ->label(__('Branch'))
                            ->maxLength(255),
                        Forms\Components\TextInput::make('upi_id')
                            ->label(__('UPI ID'))
                            ->maxLength(255),
                        Forms\Components\TextInput::make('currency')
                            ->label(__('Currency'))
                            ->default('INR')
                            ->maxLength(3),
                        Forms\Components\TextInput::make('opening_balance')
                            ->label(__('Opening Balance'))
                            ->numeric()
                            ->prefix('₹')
                            ->default(0.00),
                        Forms\Components\Toggle::make('is_active')
                            ->label(__('Active'))
                            ->default(true),
                        Forms\Components\Toggle::make('is_default')
                            ->label(__('Default Account'))
                            ->default(false),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Account Name'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('bank_name')
                    ->label(__('Bank'))
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('account_number')
                    ->label(__('Account Number'))
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('opening_balance')
                    ->label(__('Opening Balance'))
                    ->money('INR')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('Active'))
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_default')
                    ->label(__('Default'))
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('Status')),
                Tables\Filters\TernaryFilter::make('is_default')
                    ->label(__('Default')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
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
