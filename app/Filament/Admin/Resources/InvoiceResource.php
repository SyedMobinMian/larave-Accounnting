<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\InvoiceResource\Pages;
use App\Models\Invoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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

    public static function getModelLabel(): string
    {
        return __('Invoice');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Invoices');
    }

public static function form(Form $form): Form
    {
        // Read defaults from settings so user preferences take effect.
        $invoicePrefix = 'INV-';
        $defaultTaxRate = 0.00;
        $startNumber = 1;
        $paymentTerms = 30;
        try {
            $general = app(\App\Settings\GeneralSettings::class);
            $defaultTaxRate = (float) ($general->default_tax_rate ?? 0);
            $invoicePrefix = $general->invoice_prefix ?? 'INV-';
            $startNumber = (int) ($general->invoice_start_number ?? 1);
            $paymentTerms = (int) ($general->payment_terms_days ?? 30);
        } catch (\Throwable $e) {
            // settings not available yet
        }

        $nextNumber = $startNumber;
        try {
            $nextNumber = (int) (\App\Models\Invoice::max('id') ?? 0) + $startNumber;
        } catch (\Throwable $e) {
            // ignore
        }

        return $form->schema([
            Forms\Components\Grid::make()
                ->columns(3)
                ->schema([
                    // ═══════════ Bill To / Client ═══════════
                    Forms\Components\Group::make()
                        ->schema([
                            Forms\Components\Section::make(__('Bill To'))
                                ->icon('heroicon-o-building-office-2')
                                ->schema([
                                    Forms\Components\Select::make('client_id')
                                        ->label(__('Client'))
                                        ->relationship('client', 'company')
                                        ->searchable()
                                        ->preload()
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
                                            Forms\Components\TextInput::make('address')
                                                ->label(__('Address')),
                                            Forms\Components\TextInput::make('city')
                                                ->label(__('City')),
                                            Forms\Components\TextInput::make('state')
                                                ->label(__('State')),
                                            Forms\Components\TextInput::make('zip')
                                                ->label(__('ZIP')),
                                            Forms\Components\TextInput::make('country')
                                                ->label(__('Country')),
                                        ])
                                        ->required(),
                                ]),
                        ])
                        ->columnSpan(1),

                    // ═══════════ Invoice Details ═══════════
                    Forms\Components\Group::make()
                        ->schema([
                            Forms\Components\Section::make(__('Invoice Details'))
                                ->icon('heroicon-o-document-text')
                                ->schema([
                                    Forms\Components\TextInput::make('invoice_number')
                                        ->label(__('Invoice Number'))
                                        ->required()
                                        ->maxLength(255)
                                        ->default($invoicePrefix . $nextNumber),
                                    Forms\Components\DatePicker::make('issue_date')
                                        ->label(__('Issue Date'))
                                        ->required()
                                        ->default(now()),
                                    Forms\Components\DatePicker::make('due_date')
                                        ->label(__('Due Date'))
                                        ->default(now()->addDays($paymentTerms)),
                                    Forms\Components\Select::make('status')
                                        ->label(__('Status'))
                                        ->options([
                                            'draft' => __('Draft'),
                                            'unpaid' => __('Unpaid'),
                                            'paid' => __('Paid'),
                                            'partially_paid' => __('Partially Paid'),
                                            'cancelled' => __('Cancelled'),
                                        ])
                                        ->default('draft')
                                        ->required(),
                                ])
                                ->columns(2),
                        ])
                        ->columnSpan(2),
                ]),

            // ═══════════ Invoice Items (inline product addition) ═══════════
            Forms\Components\Section::make(__('Invoice Items'))
                ->description(__('Add products or line items to this invoice. The totals update automatically.'))
                ->icon('heroicon-o-shopping-cart')
                ->collapsible()
                ->schema([
                    Forms\Components\Repeater::make('items')
                        ->label(__('Line Items'))
                        ->relationship('items')
                        ->schema([
                            Forms\Components\Select::make('product_id')
                                ->label(__('Product'))
                                ->options(fn () => \App\Models\Product::query()->where('is_active', true)->pluck('name', 'id'))
                                ->searchable()
                                ->preload()
                                ->reactive()
                                ->afterStateUpdated(function ($state, Forms\Set $set) {
                                    $product = \App\Models\Product::find($state);
                                    if ($product) {
                                        $set('description', $product->name);
                                        $set('unit_price', (float) ($product->selling_price ?? $product->sales_price ?? 0));
                                    }
                                })
                                ->columnSpan(4),
                            Forms\Components\TextInput::make('description')
                                ->label(__('Description'))
                                ->columnSpan(3),
                            Forms\Components\TextInput::make('quantity')
                                ->label(__('Qty'))
                                ->numeric()
                                ->default(1)
                                ->reactive()
                                ->afterStateUpdated(function ($state, Forms\Set $set, $get) {
                                    $set('total_price', (float) $state * (float) $get('unit_price'));
                                })
                                ->columnSpan(1),
                            Forms\Components\TextInput::make('unit_price')
                                ->label(__('Rate'))
                                ->numeric()
                                ->prefix('₹')
                                ->reactive()
                                ->afterStateUpdated(function ($state, Forms\Set $set, $get) {
                                    $set('total_price', (float) $get('quantity') * (float) $state);
                                })
                                ->columnSpan(2),
                            Forms\Components\TextInput::make('total_price')
                                ->label(__('Line Total'))
                                ->numeric()
                                ->prefix('₹')
                                ->readOnly()
                                ->columnSpan(2),
                        ])
                        ->columns(12)
                        ->defaultItems(1)
                        ->reorderable()
                        ->collapsible()
                        ->addActionLabel(__('Add Product / Line Item')),
                ]),

            Forms\Components\Grid::make()
                ->columns(3)
                ->schema([
                    // ═══════════ Notes ═══════════
                    Forms\Components\Group::make()
                        ->schema([
                            Forms\Components\Section::make(__('Notes'))
                                ->icon('heroicon-o-chat-bubble-left-right')
                                ->schema([
                                    Forms\Components\Textarea::make('notes')
                                        ->label(__('Notes'))
                                        ->rows(4)
                                        ->placeholder(__('Thank you for your business!')),
                                ]),
                        ])
                        ->columnSpan(2),

                    // ═══════════ Totals ═══════════
                    Forms\Components\Group::make()
                        ->schema([
                            Forms\Components\Section::make(__('Totals'))
                                ->icon('heroicon-o-calculator')
                                ->schema([
                                    Forms\Components\TextInput::make('subtotal')
                                        ->label(__('Subtotal'))
                                        ->numeric()
                                        ->prefix('₹')
                                        ->readOnly()
                                        ->default(0.00),
                                    Forms\Components\TextInput::make('tax_amount')
                                        ->label(__('Tax (' . $defaultTaxRate . '%)'))
                                        ->numeric()
                                        ->prefix('₹')
                                        ->readOnly()
                                        ->default(0.00),
                                    Forms\Components\TextInput::make('total_amount')
                                        ->label(__('Total'))
                                        ->numeric()
                                        ->prefix('₹')
                                        ->readOnly()
                                        ->default(0.00),
                                ]),
                        ])
                        ->columnSpan(1),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label(__('Invoice Number'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('client.company')
                    ->label(__('Client'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('issue_date')
                    ->label(__('Date'))
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label(__('Total'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('Status')),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

public static function getRelations(): array
    {
        // Items are edited inline via the form's Repeater, so no
        // separate relation manager is registered here.
        return [
            //
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
}

