<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PurchaseOrderResource\Pages;
use App\Models\GoodsReceiptNote;
use App\Models\Product;
use App\Models\PurchaseOrder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PurchaseOrderResource extends Resource
{
    protected static ?string $model = PurchaseOrder::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Purchase Engine';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('PO Header')
                    ->schema([
                        Forms\Components\TextInput::make('po_number')
                            ->default('PO-' . strtoupper(uniqid()))
                            ->required()
                            ->readOnly(),

                        Forms\Components\Select::make('vendor_id')
                            ->relationship('vendor', 'company_name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\DatePicker::make('order_date')
                            ->default(now())
                            ->required(),

                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'issued' => 'Issued',
                                'partially_received' => 'Partially Received',
                                'received' => 'Received',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('draft')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Line Items')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship('items')
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->relationship('product', 'name')
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        $product = Product::find($state);
                                        if ($product) {
                                            $set('unit_price', $product->purchase_price);
                                        }
                                    })
                                    ->required()
                                    ->columnSpan(4),

                                Forms\Components\TextInput::make('quantity')
                                    ->numeric()
                                    ->default(1)
                                    ->reactive()
                                    ->afterStateUpdated(fn ($state, Forms\Get $get, Forms\Set $set) => 
                                        $set('subtotal', (float)$state * (float)$get('unit_price'))
                                    )
                                    ->required()
                                    ->columnSpan(2),

                                Forms\Components\TextInput::make('unit_price')
                                    ->numeric()
                                    ->prefix('₹')
                                    ->reactive()
                                    ->afterStateUpdated(fn ($state, Forms\Get $get, Forms\Set $set) => 
                                        $set('subtotal', (float)$state * (float)$get('quantity'))
                                    )
                                    ->required()
                                    ->columnSpan(2),

                                Forms\Components\TextInput::make('subtotal')
                                    ->numeric()
                                    ->prefix('₹')
                                    ->readOnly()
                                    ->columnSpan(4),
                            ])
                            ->columns(12)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('po_number')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('vendor.company_name')->sortable(),
                Tables\Columns\TextColumn::make('order_date')->date(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'draft',
                        'info' => 'issued',
                        'success' => 'received',
                        'danger' => 'cancelled',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('receive_goods')
                    ->label('Receive Goods (GRN)')
                    ->icon('heroicon-o-truck')
                    ->color('success')
                    ->visible(fn (PurchaseOrder $record) => in_array($record->status, ['issued', 'partially_received']))
                    ->action(function (PurchaseOrder $record) {
                        $grn = GoodsReceiptNote::create([
                            'grn_number' => 'GRN-' . strtoupper(uniqid()),
                            'purchase_order_id' => $record->id,
                            'received_date' => now(),
                        ]);

                        foreach ($record->items as $item) {
                            $grn->items()->create([
                                'product_id' => $item->product_id,
                                'received_quantity' => $item->quantity,
                            ]);

                            $product = Product::find($item->product_id);
                            if ($product && $product->track_inventory) {
                                $product->increment('stock_on_hand', $item->quantity);
                            }
                        }

                        $record->update(['status' => 'received']);

                        Notification::make()
                            ->title('Goods received! Inventory stock updated.')
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