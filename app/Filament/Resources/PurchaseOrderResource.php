<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseOrderResource\Pages;
use App\Models\Product;
use App\Models\PurchaseOrder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PurchaseOrderResource extends Resource
{
    protected static ?string $model = PurchaseOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    public static function getNavigationGroup(): ?string
    {
        return __('Procurement & Inventory');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Purchase Order Details'))
                    ->schema([
                        Forms\Components\Select::make('vendor_id')
                            ->label(__('Vendor / Supplier'))
                            ->relationship('vendor', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\TextInput::make('po_number')
                            ->label(__('PO Number'))
                            ->default('PO-' . strtoupper(uniqid()))
                            ->required(),

                        Forms\Components\DatePicker::make('order_date')
                            ->label(__('Order Date'))
                            ->default(now())
                            ->required(),

                        Forms\Components\DatePicker::make('expected_date')
                            ->label(__('Expected Delivery Date')),

                        Forms\Components\Select::make('status')
                            ->label(__('Status'))
                            ->options([
                                'draft' => __('Draft'),
                                'ordered' => __('Ordered'),
                                'received' => __('Received (Stock In)'),
                                'cancelled' => __('Cancelled'),
                            ])
                            ->default('draft')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make(__('Items To Purchase'))
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->label(__('Product'))
                                    ->options(Product::pluck('name', 'id'))
                                    ->searchable()
                                    ->live()
                                    ->afterStateUpdated(function ($state, Set $set) {
                                        if ($product = Product::find($state)) {
                                            $set('description', $product->name);
                                            $set('unit_cost', $product->cost_price);
                                        }
                                    })
                                    ->required(),

                                Forms\Components\TextInput::make('description')
                                    ->label(__('Description'))
                                    ->required(),

                                Forms\Components\TextInput::make('qty')
                                    ->label(__('Qty'))
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Get $get, Set $set) => static::updateItemAmount($get, $set)),

                                Forms\Components\TextInput::make('unit_cost')
                                    ->label(__('Unit Cost'))
                                    ->numeric()
                                    ->prefix('₹')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Get $get, Set $set) => static::updateItemAmount($get, $set)),

                                Forms\Components\TextInput::make('amount')
                                    ->label(__('Amount'))
                                    ->numeric()
                                    ->prefix('₹')
                                    ->readOnly(),
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
                            ->readOnly(),

                        Forms\Components\TextInput::make('total')
                            ->label(__('Total Amount'))
                            ->numeric()
                            ->prefix('₹')
                            ->readOnly(),
                    ])->columns(2),
            ]);
    }

    public static function updateItemAmount(Get $get, Set $set): void
    {
        $qty = (float) ($get('qty') ?? 0);
        $cost = (float) ($get('unit_cost') ?? 0);
        $set('amount', round($qty * $cost, 2));
    }

    public static function updateTotals(Get $get, Set $set): void
    {
        $items = $get('items') ?? [];
        $subtotal = 0;

        foreach ($items as $item) {
            $subtotal += ((float)($item['qty'] ?? 0) * (float)($item['unit_cost'] ?? 0));
        }

        $set('subtotal', round($subtotal, 2));
        $set('total', round($subtotal, 2));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('po_number')->label(__('PO #'))->searchable(),
                Tables\Columns\TextColumn::make('vendor.name')->label(__('Vendor'))->searchable(),
                Tables\Columns\TextColumn::make('order_date')->label(__('Date'))->date(),
                Tables\Columns\TextColumn::make('total')->label(__('Total'))->money('INR'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'gray' => 'draft',
                        'warning' => 'ordered',
                        'success' => 'received',
                        'danger' => 'cancelled',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                
                // Receive Order & Stock In Action
                Tables\Actions\Action::make('markAsReceived')
                    ->label(__('Receive Goods (Stock In)'))
                    ->icon('heroicon-o-archive-box-arrow-down')
                    ->color('success')
                    ->visible(fn (PurchaseOrder $record) => $record->status !== 'received')
                    ->requiresConfirmation()
                    ->action(function (PurchaseOrder $record) {
                        foreach ($record->items as $item) {
                            if ($item->product_id && $product = Product::find($item->product_id)) {
                                $product->increment('stock_quantity', $item->qty);
                            }
                        }

                        $record->update(['status' => 'received']);

                        Notification::make()
                            ->title(__('Goods Received & Inventory Updated!'))
                            ->success()
                            ->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPurchaseOrders::route('/'),
            'create' => Pages\CreatePurchaseOrder::route('/create'),
            'edit' => Pages\EditPurchaseOrder::route('/{record}/edit'),
        ];
    }
}