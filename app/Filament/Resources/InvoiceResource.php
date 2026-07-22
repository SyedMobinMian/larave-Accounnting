<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Models\Invoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function getNavigationGroup(): ?string
    {
        return __('Financials');
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
                    ->schema([
                        Forms\Components\Select::make('client_id')
                            ->label(__('Client'))
                            ->relationship('client', 'company')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('invoice_number')
                            ->label(__('Invoice Number'))
                            ->default('INV-' . strtoupper(uniqid()))
                            ->required(),
                        Forms\Components\DatePicker::make('date')
                            ->label(__('Date'))
                            ->default(now())
                            ->required(),
                        Forms\Components\DatePicker::make('duedate')
                            ->label(__('Due Date')),
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
                    ->schema([
                        Forms\Components\TextInput::make('subtotal')
                            ->label(__('Subtotal'))
                            ->numeric()
                            ->prefix('₹')
                            ->default(0.00)
                            ->readOnly()
                            ->dehydrated(),
                        Forms\Components\TextInput::make('total_tax')
                            ->label(__('Total Tax'))
                            ->numeric()
                            ->prefix('₹')
                            ->default(0.00)
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Get $get, Set $set) => static::updateTotals($get, $set)),
                        Forms\Components\TextInput::make('total')
                            ->label(__('Total'))
                            ->numeric()
                            ->prefix('₹')
                            ->default(0.00)
                            ->readOnly()
                            ->dehydrated(),
                    ])->columns(3),
            ]);
    }

    // Single item calculation (qty * rate) - FIXED Float type
    public static function updateItemAmount(Get $get, Set $set): void
    {
        $qty = (float) ($get('qty') ?? 0);
        $rate = (float) ($get('rate') ?? 0);
        $set('amount', round($qty * $rate, 2));
    }

    // Global total calculation
    public static function updateTotals(Get $get, Set $set): void
    {
        $items = $get('items') ?? [];
        $subtotal = 0;

        foreach ($items as $item) {
            $qty = (float) ($item['qty'] ?? 0);
            $rate = (float) ($item['rate'] ?? 0);
            $subtotal += ($qty * $rate);
        }

        $tax = (float) ($get('total_tax') ?? 0);

        $set('subtotal', round($subtotal, 2));
        $set('total', round($subtotal + $tax, 2));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client.company')
                    ->label(__('Client'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label(__('Invoice #'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('date')
                    ->label(__('Date'))
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('duedate')
                    ->label(__('Due Date'))
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subtotal')
                    ->label(__('Subtotal'))
                    ->money('INR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_tax')
                    ->label(__('Tax'))
                    ->money('INR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->label(__('Total'))
                    ->money('INR')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label(__('Status'))
                    ->colors([
                        'danger' => 'unpaid',
                        'warning' => 'partially_paid',
                        'success' => 'paid',
                        'gray' => 'cancelled',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                // Record Payment Modal Action (With Auto Status Update)
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
                            ->default(fn (Invoice $record) => $record->total - $record->payments()->sum('amount'))
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
                        // 1. Create payment entry
                        $record->payments()->create($data);

                        // 2. Recalculate total payments and auto update status
                        $totalPaid = $record->payments()->sum('amount');

                        if ($totalPaid >= $record->total) {
                            $record->update(['status' => 'paid']);
                        } elseif ($totalPaid > 0) {
                            $record->update(['status' => 'partially_paid']);
                        }
                    }),

                // PDF Download Action Button
                Tables\Actions\Action::make('downloadPdf')
                    ->label(__('PDF'))
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->url(fn (Invoice $record): string => route('invoices.pdf', $record))
                    ->openUrlInNewTab(),
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
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}