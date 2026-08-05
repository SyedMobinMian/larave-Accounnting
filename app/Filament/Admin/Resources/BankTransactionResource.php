<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\BankTransactionResource\Pages;
use App\Models\BankTransaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BankTransactionResource extends Resource
{
    protected static ?string $model = BankTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('Banking');
    }

    public static function getNavigationLabel(): string
    {
        return __('Transactions');
    }

    public static function getModelLabel(): string
    {
        return __('Transaction');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Transactions');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Transaction Details'))
                    ->description(__('Record bank transactions'))
                    ->icon('heroicon-o-banknotes')
                    ->schema([
                        Forms\Components\Select::make('bank_account_id')
                            ->label(__('Bank Account'))
                            ->relationship('bankAccount', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\DatePicker::make('transaction_date')
                            ->label(__('Transaction Date'))
                            ->default(now())
                            ->required(),
                        Forms\Components\TextInput::make('description')
                            ->label(__('Description'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('debit')
                            ->label(__('Debit (Money Out)'))
                            ->numeric()
                            ->prefix('₹')
                            ->default(0.00),
                        Forms\Components\TextInput::make('credit')
                            ->label(__('Credit (Money In)'))
                            ->numeric()
                            ->prefix('₹')
                            ->default(0.00),
                        Forms\Components\TextInput::make('balance')
                            ->label(__('Running Balance'))
                            ->numeric()
                            ->prefix('₹')
                            ->default(0.00),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('transaction_date')
                    ->label(__('Date'))
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('bankAccount.name')
                    ->label(__('Account'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label(__('Description'))
                    ->searchable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('debit')
                    ->label(__('Debit'))
                    ->money('INR')
                    ->color('danger')
                    ->sortable(),
                Tables\Columns\TextColumn::make('credit')
                    ->label(__('Credit'))
                    ->money('INR')
                    ->color('success')
                    ->sortable(),
                Tables\Columns\TextColumn::make('balance')
                    ->label(__('Balance'))
                    ->money('INR')
                    ->sortable()
                    ->weight('bold'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('bank_account_id')
                    ->label(__('Account'))
                    ->relationship('bankAccount', 'name'),
                Tables\Filters\Filter::make('transaction_date')
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
                                fn (Builder $query, $date): Builder => $query->whereDate('transaction_date', '>=', $date),
                            )
                            ->when(
                                $data['date_to'],
                                fn (Builder $query, $date): Builder => $query->whereDate('transaction_date', '<=', $date),
                            );
                    }),
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
            ->defaultSort('transaction_date', 'desc');
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
            'index' => Pages\ListBankTransactions::route('/'),
            'create' => Pages\CreateBankTransaction::route('/create'),
            'edit' => Pages\EditBankTransaction::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['bankAccount']);
    }
}

