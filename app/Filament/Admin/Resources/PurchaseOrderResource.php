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
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;

class PurchaseOrderResource extends Resource
{
    protected static ?string $model = PurchaseOrder::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Procurement & Inventory';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('PO Header')
                    ->description(__('Purchase order identification and vendor'))
                    ->icon('heroicon-o-receipt-percent')
                    ->schema([
                        Forms\Components\TextInput::make('po_number')
                            ->label(__('PO Number'))
                            ->default('PO-' . strtoupper(uniqid()))
                            ->required()
                            ->readOnly(),

                        Forms\Components\Select::make('vendor_id')
                            ->label(__('Vendor'))
                            ->relationship('vendor', 'company_name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\DatePicker::make('order_date')
                            ->label(__('Order Date'))
                            ->default(now())
                            ->required(),

                        Forms\Components\Select::make('status')
                            ->label(__('Status'))
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
                    ->description(__('Products and quantities to order'))
                    ->icon('heroicon-o-list-bullet')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship('items')
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->label(__('Product'))
                                    ->relationship('product', 'name')
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        $product = Product::find($state);
                                        if ($product) {
                                            $set('unit_price', $product->selling_price);
                                        }
                                    })
                                    ->required()
                                    ->columnSpan(4),

                                Forms\Components\TextInput::make('quantity')
                                    ->label(__('Quantity'))
                                    ->numeric()
                                    ->default(1)
                                    ->reactive()
                                    ->afterStateUpdated(fn ($state, Forms\Get $get, Forms\Set $set) => 
                                        $set('subtotal', (float)$state * (float)$get('unit_price'))
                                    )
                                    ->required()
                                    ->columnSpan(2),

                                Forms\Components\TextInput::make('unit_price')
                                    ->label(__('Unit Price'))
                                    ->numeric()
                                    ->prefix('₹')
                                    ->reactive()
                                    ->afterStateUpdated(fn ($state, Forms\Get $get, Forms\Set $set) => 
                                        $set('subtotal', (float)$state * (float)$get('quantity'))
                                    )
                                    ->required()
                                    ->columnSpan(2),

                                Forms\Components\TextInput::make('subtotal')
                                    ->label(__('Subtotal'))
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
                Tables\Columns\TextColumn::make('po_number')
                    ->label(__('PO #'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('vendor.company_name')
                    ->label(__('Vendor'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('order_date')
                    ->label(__('Order Date'))
                    ->date('M d, Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->colors([
                        'warning' => 'draft',
                        'info' => 'issued',
                        'success' => 'received',
                        'danger' => 'cancelled',
                    ])
                    ->icons([
                        'heroicon-o-pencil' => 'draft',
                        'heroicon-o-paper-airplane' => 'issued',
                        'heroicon-o-check-circle' => 'received',
                        'heroicon-o-ban' => 'cancelled',
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options([
                        'draft' => 'Draft',
                        'issued' => 'Issued',
                        'partially_received' => 'Partially Received',
                        'received' => 'Received',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->form(static::form()),
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
                                if ($product) {
                                    $product->increment('stock_quantity', $item->quantity);
                                }
                            }

                            $record->update(['status' => 'received']);

                            Notification::make()
                                ->title('Goods received! Inventory stock updated.')
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('order_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPurchaseOrders::route('/'),
            'create' => Pages\CreatePurchaseOrder::route('/create'),
            'edit' => Pages\EditPurchaseOrder::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['vendor', 'items.product']);
    }
}
