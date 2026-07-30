<?php

namespace App\Filament\Admin\Resources;

use App\Exports\ExpensesExport;
use App\Filament\Admin\Resources\ExpenseResource\Pages;
use App\Models\Account;
use App\Models\Expense;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?int $navigationSort = 3;

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
                    ->description(__('Record the expense details, vendor and payment information'))
                    ->icon('heroicon-o-receipt-percent')
                    ->schema([
                        Forms\Components\Select::make('vendor_id')
                            ->relationship('vendor', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->label(__('Vendor / Payee'))
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label(__('Vendor Name'))
                                    ->required(),
                                Forms\Components\TextInput::make('email')
                                    ->label(__('Email'))
                                    ->email(),
                            ]),

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
                            ->required()
                            ->minValue(0)
                            ->helperText(__('Enter the expense amount')),

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
                    ->date('M d, Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('vendor.name')
                    ->label(__('Vendor'))
                    ->placeholder(__('N/A'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('expenseAccount.name')
                    ->label(__('Category'))
                    ->searchable()
                    ->badge()
                    ->color('warning'),

                Tables\Columns\TextColumn::make('amount')
                    ->label(__('Amount'))
                    ->money('INR')
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label(__('Payment Method'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'bank_transfer' => __('Bank Transfer'),
                        'cash' => __('Cash'),
                        'upi' => __('UPI / GPay'),
                        'cheque' => __('Cheque'),
                        'card' => __('Card'),
                        default => ucfirst($state),
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created'))
                    ->date('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('payment_method')
                    ->label(__('Payment Method'))
                    ->options([
                        'bank_transfer' => __('Bank Transfer'),
                        'cash' => __('Cash'),
                        'upi' => __('UPI / GPay'),
                        'cheque' => __('Cheque'),
                        'card' => __('Card'),
                    ]),
                Tables\Filters\Filter::make('date_range')
                    ->label(__('Date Range'))
                    ->form([
                        Forms\Components\DatePicker::make('date_from')
                            ->label(__('From')),
                        Forms\Components\DatePicker::make('date_to')
                            ->label(__('To')),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['date_from'], fn ($q, $d) => $q->whereDate('expense_date', '>=', $d))
                        ->when($data['date_to'], fn ($q, $d) => $q->whereDate('expense_date', '<=', $d))
                    ),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->form(static::form()),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('exportSelected')
                        ->label(__('Export Selected'))
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function ($records) {
                            $ids = $records->pluck('id')->toArray();
                            return Excel::download(new ExpensesExport(['ids' => $ids]), 'selected-expenses.xlsx');
                        }),
                ]),
            ])
            ->headerActions([
                ActionGroup::make([
                    Action::make('export')
                        ->label(__('Export All'))
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->action(function () {
                            $filters = array_filter(request()->only(['payment_method', 'date_from', 'date_to']));
                            return Excel::download(new ExpensesExport($filters), 'expenses-export.xlsx');
                        }),
                    Action::make('exportCsv')
                        ->label(__('Export as CSV'))
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('gray')
                        ->action(function () {
                            $filters = array_filter(request()->only(['payment_method', 'date_from', 'date_to']));
                            return Excel::download(new ExpensesExport($filters), 'expenses-export.csv', \Maatwebsite\Excel\Excel::CSV);
                        }),
                ]),
            ])
            ->defaultSort('expense_date', 'desc');
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['vendor', 'expenseAccount', 'paymentAccount']);
    }
}
