<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpenseResource\Pages;
use App\Models\Account;
use App\Models\Expense;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    // Dynamic Navigation Labels & Groups
    public static function getNavigationGroup(): ?string
    {
        return __('Financials');
    }

    public static function getNavigationLabel(): string
    {
        return __('Expenses');
    }

    public static function getModelLabel(): string
    {
        return __('Expense');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Expenses');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Expense Details'))
                    ->schema([
                        Forms\Components\Select::make('vendor_id')
                            ->relationship('vendor', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->label(__('Vendor / Payee')),

                        Forms\Components\DatePicker::make('expense_date')
                            ->label(__('Expense Date'))
                            ->default(now())
                            ->required(),

                        Forms\Components\Select::make('expense_account_id')
                            ->label(__('Expense Category / Account'))
                            ->options(
                                fn () => Account::query()
                                    ->where('type', 'expense')
                                    ->pluck('name', 'id')
                                    ->toArray()
                            )
                            ->searchable()
                            ->required(),

                        Forms\Components\Select::make('payment_account_id')
                            ->label(__('Paid From (Cash/Bank)'))
                            ->options(
                                fn () => Account::query()
                                    ->whereIn('type', ['asset'])
                                    ->pluck('name', 'id')
                                    ->toArray()
                            )
                            ->searchable()
                            ->required(),

                        Forms\Components\TextInput::make('amount')
                            ->label(__('Amount'))
                            ->numeric()
                            ->prefix('₹')
                            ->required(),

                        Forms\Components\Select::make('payment_method')
                            ->label(__('Payment Method'))
                            ->options([
                                'bank_transfer' => __('Bank Transfer'),
                                'cash' => __('Cash'),
                                'upi' => __('UPI / GPay'),
                                'cheque' => __('Cheque'),
                                'card' => __('Credit / Debit Card'),
                            ])
                            ->default('bank_transfer')
                            ->required(),

                        Forms\Components\TextInput::make('reference_number')
                            ->label(__('Transaction Ref / Receipt No')),

                        Forms\Components\Textarea::make('description')
                            ->label(__('Description'))
                            ->rows(2)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('expense_date')
                    ->label(__('Expense Date'))
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('vendor.name')
                    ->label(__('Vendor'))
                    ->placeholder(__('N/A'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('expenseAccount.name')
                    ->label(__('Category'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('paymentAccount.name')
                    ->label(__('Paid From'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label(__('Amount'))
                    ->money('INR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label(__('Payment Method'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'bank_transfer' => __('Bank Transfer'),
                        'cash' => __('Cash'),
                        'upi' => __('UPI / GPay'),
                        'cheque' => __('Cheque'),
                        'card' => __('Credit / Debit Card'),
                        default => ucfirst($state),
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpense::route('/create'),
            'edit' => Pages\EditExpense::route('/{record}/edit'),
        ];
    }
}