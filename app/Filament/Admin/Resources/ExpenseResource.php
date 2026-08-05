<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ExpenseResource\Pages;
use App\Models\Expense;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-down';
    protected static ?string $navigationGroup = 'Financials';
    protected static ?string $navigationLabel = 'Expenses';
    protected static ?string $modelLabel = 'Expense';
    protected static ?string $pluralModelLabel = 'Expenses';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Expense Details'))
                    ->description(__('Record business expenses'))
                    ->icon('heroicon-o-arrow-trending-down')
                    ->schema([
                        Forms\Components\Select::make('vendor_id')
                            ->label(__('Vendor'))
                            ->relationship('vendor', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        Forms\Components\Select::make('expense_account_id')
                            ->label(__('Expense Account (Debit)'))
                            ->relationship('expenseAccount', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('payment_account_id')
                            ->label(__('Payment Account (Credit)'))
                            ->relationship('paymentAccount', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('amount')
                            ->label(__('Amount'))
                            ->required()
                            ->numeric()
                            ->prefix('₹'),
                        Forms\Components\DatePicker::make('expense_date')
                            ->label(__('Expense Date'))
                            ->required()
                            ->default(now()),
                        Forms\Components\Select::make('payment_method')
                            ->label(__('Payment Method'))
                            ->options([
                                'cash' => __('Cash'),
                                'bank_transfer' => __('Bank Transfer'),
                                'check' => __('Cheque'),
                                'credit_card' => __('Credit Card'),
                                'upi' => __('UPI'),
                                'other' => __('Other'),
                            ])
                            ->default('bank_transfer'),
                        Forms\Components\TextInput::make('reference_number')
                            ->label(__('Reference Number'))
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label(__('Description'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('expense_date')
                    ->label(__('Date'))
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('vendor.name')
                    ->label(__('Vendor'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expenseAccount.name')
                    ->label(__('Account'))
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label(__('Amount'))
                    ->money('INR')
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label(__('Method'))
                    ->badge()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('reference_number')
                    ->label(__('Reference'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('payment_method')
                    ->label(__('Payment Method'))
                    ->options([
                        'cash' => __('Cash'),
                        'bank_transfer' => __('Bank Transfer'),
                        'check' => __('Cheque'),
                        'credit_card' => __('Credit Card'),
                        'upi' => __('UPI'),
                        'other' => __('Other'),
                    ]),
                Tables\Filters\Filter::make('expense_date')
                    ->form([
                        Forms\Components\DatePicker::make('date_from')
                            ->label(__('From')),
                        Forms\Components\DatePicker::make('date_to')
                            ->label(__('To')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('expense_date', '>=', $date),
                            )
                            ->when(
                                $data['date_to'],
                                fn (Builder $query, $date): Builder => $query->whereDate('expense_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('expense_date', 'desc');
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
            'index' => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpense::route('/create'),
            'edit' => Pages\EditExpense::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['vendor', 'expenseAccount', 'paymentAccount']);
    }
}
