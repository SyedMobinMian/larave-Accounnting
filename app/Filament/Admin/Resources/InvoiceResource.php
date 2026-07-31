<?php

namespace App\Filament\Admin\Resources;

use App\Exports\InvoicesExport;
use App\Filament\Admin\Resources\InvoiceResource\RelationManagers\ItemsRelationManager;
use App\Filament\Admin\Resources\InvoiceResource\Pages;
use App\Models\Invoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('Sales & CRM');
    }

    public static function getNavigationLabel(): string
    {
        return __('Invoices');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Invoice Header'))
                    ->description(__('Client and invoice identification details'))
                    ->icon('heroicon-o-receipt-percent')
                    ->schema([
                        Forms\Components\Select::make('client_id')
                            ->label(__('Client'))
                            ->relationship('client', 'company')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('company')
                                    ->label(__('Company Name'))
                                    ->required(),
                                Forms\Components\TextInput::make('email')
                                    ->label(__('Email'))
                                    ->email(),
                                Forms\Components\TextInput::make('phonenumber')
                                    ->label(__('Phone'))
                                    ->tel(),
                            ]),
                        Forms\Components\TextInput::make('invoice_number')
                            ->label(__('Invoice Number'))
                            ->default('INV-' . strtoupper(uniqid()))
                            ->required(),
                        Forms\Components\DatePicker::make('issue_date')
                            ->label(__('Invoice Date'))
                            ->default(now())
                            ->required(),
                        Forms\Components\DatePicker::make('due_date')
                            ->label(__('Due Date'))
                            ->default(now()->addDays(30)),
                        Forms\Components\Select::make('status')
                            ->label(__('Status'))
                            ->options([
                                'unpaid' => __('Unpaid'),
                                'partially_paid' => __('Partially Paid'),
                                'paid' => __('Paid'),
                                'cancelled' => __('Cancelled'),
                            ])
                            ->default('unpaid')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make(__('Invoice Items'))
                    ->description(__('Add line items with quantities and rates'))
                    ->icon('heroicon-o-list-bullet')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('description')
                                    ->label(__('Description'))
                                    ->required()
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('qty')
                                    ->label(__('Qty'))
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Get $get, Set $set) => static::updateItemAmount($get, $set)),
                                Forms\Components\TextInput::make('rate')
                                    ->label(__('Rate'))
                                    ->numeric()
                                    ->prefix('₹')
                                    ->default(0.00)
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Get $get, Set $set) => static::updateItemAmount($get, $set)),
                                Forms\Components\TextInput::make('amount')
                                    ->label(__('Amount'))
                                    ->numeric()
                                    ->prefix('₹')
                                    ->default(0.00)
                                    ->readOnly()
                                    ->dehydrated(),
                            ])
                            ->columns(5)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Get $get, Set $set) => static::updateTotals($get, $set)),
                    ]),

                Forms\Components\Section::make(__('Totals'))
                    ->description(__('Invoice summary with tax'))
                    ->icon('heroicon-o-calculator')
                    ->schema([
                        Forms\Components\TextInput::make('subtotal')
                            ->label(__('Subtotal'))
                            ->numeric()
                            ->prefix('₹')
                            ->default(0.00)
                            ->readOnly()
                            ->dehydrated(),
                        Forms\Components\TextInput::make('tax_amount')
                            ->label(__('Tax'))
                            ->numeric()
                            ->prefix('₹')
                            ->default(0.00)
                            ->required()
                            ->helperText(__('Enter tax amount if applicable'))
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Get $get, Set $set) => static::updateTotals($get, $set)),
                        Forms\Components\TextInput::make('total_amount')
                            ->label(__('Total'))
                            ->numeric()
                            ->prefix('₹')
                            ->default(0.00)
                            ->readOnly()
                            ->dehydrated(),
                    ])->columns(3),
            ]);
    }

    public static function updateItemAmount(Get $get, Set $set): void
    {
        $qty = (float) ($get('qty') ?? 0);
        $rate = (float) ($get('rate') ?? 0);
        $set('amount', round($qty * $rate, 2));
    }

    public static function updateTotals(Get $get, Set $set): void
    {
        $items = $get('items') ?? [];
        $subtotal = 0;

        foreach ($items as $item) {
            $qty = (float) ($item['qty'] ?? 0);
            $rate = (float) ($item['rate'] ?? 0);
            $subtotal += ($qty * $rate);
        }

        $tax = (float) ($get('tax_amount') ?? 0);

        $set('subtotal', round($subtotal, 2));
        $set('total_amount', round($subtotal + $tax, 2));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label(__('Invoice #'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('client.company')
                    ->label(__('Client'))
                    ->searchable()
                    ->sortable()
                    ->description(fn (Invoice $record): ?string => $record->client?->email),
                Tables\Columns\TextColumn::make('issue_date')
                    ->label(__('Date'))
                    ->date('M d, Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->label(__('Due Date'))
                    ->date('M d, Y')
                    ->sortable()
                    ->color(fn (Invoice $record): string => $record->due_date && $record->due_date->isPast() && $record->status !== 'paid' ? 'danger' : 'gray'),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label(__('Total'))
                    ->money('INR')
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->colors([
                        'danger' => 'unpaid',
                        'warning' => 'partially_paid',
                        'success' => 'paid',
                        'gray' => 'cancelled',
                    ])
                    ->icons([
                        'heroicon-o-x-circle' => 'unpaid',
                        'heroicon-o-adjustments-horizontal' => 'partially_paid',
                        'heroicon-o-check-circle' => 'paid',
                        'heroicon-o-ban' => 'cancelled',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created'))
                    ->date('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options([
                        'unpaid' => __('Unpaid'),
                        'partially_paid' => __('Partially Paid'),
                        'paid' => __('Paid'),
                        'cancelled' => __('Cancelled'),
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
                        ->when($data['date_from'], fn ($q, $d) => $q->whereDate('issue_date', '>=', $d))
                        ->when($data['date_to'], fn ($q, $d) => $q->whereDate('issue_date', '<=', $d))
                    ),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->form(static::form()),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('recordPayment')
                        ->label(__('Record Payment'))
                        ->icon('heroicon-o-banknotes')
                        ->color('warning')
                        ->visible(fn (Invoice $record) => $record->status !== 'paid')
                        ->form([
                            Forms\Components\Select::make('account_id')
                                ->label(__('Deposit To Account'))
                                ->options(
                                    \App\Models\Account::whereIn('type', ['asset'])
                                        ->pluck('name', 'id')
                                )
                                ->required(),
                            Forms\Components\TextInput::make('amount')
                                ->label(__('Payment Amount'))
                                ->numeric()
                                ->prefix('₹')
                                ->default(fn (Invoice $record) => $record->total_amount - $record->payments()->sum('amount'))
                                ->required(),
                            Forms\Components\DatePicker::make('payment_date')
                                ->label(__('Payment Date'))
                                ->default(now())
                                ->required(),
                            Forms\Components\Select::make('payment_method')
                                ->label(__('Payment Method'))
                                ->options([
                                    'bank_transfer' => __('Bank Transfer'),
                                    'cash' => __('Cash'),
                                    'upi' => __('UPI / GPay'),
                                    'cheque' => __('Cheque'),
                                ])
                                ->default('bank_transfer')
                                ->required(),
                            Forms\Components\TextInput::make('reference_number')
                                ->label(__('Transaction Ref / Cheque No')),
                        ])
                        ->action(function (Invoice $record, array $data): void {
                            $record->payments()->create($data);

                            $totalPaid = $record->payments()->sum('amount');

                            if ($totalPaid >= $record->total_amount) {
                                $record->update(['status' => 'paid']);
                            } elseif ($totalPaid > 0) {
                                $record->update(['status' => 'partially_paid']);
                            }
                        }),
                    Tables\Actions\Action::make('downloadPdf')
                        ->label(__('PDF'))
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('success')
                        ->url(fn (Invoice $record): string => route('invoices.pdf', $record))
                        ->openUrlInNewTab(),
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
                            return Excel::download(new InvoicesExport(['ids' => $ids]), 'selected-invoices.xlsx');
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
                            $filters = array_filter(request()->only(['status', 'date_from', 'date_to']));
                            return Excel::download(new InvoicesExport($filters), 'invoices-export.xlsx');
                        }),
                    Action::make('exportCsv')
                        ->label(__('Export as CSV'))
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('gray')
                        ->action(function () {
                            $filters = array_filter(request()->only(['status', 'date_from', 'date_to']));
                            return Excel::download(new InvoicesExport($filters), 'invoices-export.csv', \Maatwebsite\Excel\Excel::CSV);
                        }),
                ]),
            ])
            ->defaultSort('issue_date', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['client', 'items']);
    }
}
