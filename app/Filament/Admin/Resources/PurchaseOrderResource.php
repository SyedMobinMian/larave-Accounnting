<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PurchaseOrderResource\Pages;
use App\Models\PurchaseOrder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PurchaseOrderResource extends Resource
{
    protected static ?string $model = PurchaseOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Procurement';
    protected static ?string $navigationLabel = 'Purchase Orders';
    protected static ?string $modelLabel = 'Purchase Order';
    protected static ?string $pluralModelLabel = 'Purchase Orders';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Purchase Order Details'))
                    ->description(__('Create purchase orders for vendors'))
                    ->icon('heroicon-o-shopping-cart')
                    ->schema([
                        Forms\Components\Select::make('vendor_id')
                            ->label(__('Vendor'))
                            ->relationship('vendor', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('order_number')
                            ->label(__('Order Number'))
                            ->required()
                            ->maxLength(255)
                            ->default('PO-' . strtoupper(uniqid())),
                        Forms\Components\DatePicker::make('order_date')
                            ->label(__('Order Date'))
                            ->required()
                            ->default(now()),
                        Forms\Components\DatePicker::make('expected_delivery_date')
                            ->label(__('Expected Delivery Date')),
                        Forms\Components\Select::make('status')
                            ->label(__('Status'))
                            ->options([
                                'draft' => __('Draft'),
                                'sent' => __('Sent'),
                                'confirmed' => __('Confirmed'),
                                'received' => __('Received'),
                                'cancelled' => __('Cancelled'),
                            ])
                            ->default('draft')
                            ->required(),
                        Forms\Components\TextInput::make('subtotal')
                            ->label(__('Subtotal'))
                            ->numeric()
                            ->prefix('₹')
                            ->default(0.00),
                        Forms\Components\TextInput::make('tax_amount')
                            ->label(__('Tax'))
                            ->numeric()
                            ->prefix('₹')
                            ->default(0.00),
                        Forms\Components\TextInput::make('total_amount')
                            ->label(__('Total'))
                            ->numeric()
                            ->prefix('₹')
                            ->required()
                            ->default(0.00),
                        Forms\Components\Textarea::make('notes')
                            ->label(__('Notes'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label(__('Order #'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('vendor.name')
                    ->label(__('Vendor'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('order_date')
                    ->label(__('Order Date'))
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('expected_delivery_date')
                    ->label(__('Delivery'))
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->colors([
                        'gray' => 'draft',
                        'info' => 'sent',
                        'warning' => 'confirmed',
                        'success' => 'received',
                        'danger' => 'cancelled',
                    ]),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label(__('Total'))
                    ->money('INR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options([
                        'draft' => __('Draft'),
                        'sent' => __('Sent'),
                        'confirmed' => __('Confirmed'),
                        'received' => __('Received'),
                        'cancelled' => __('Cancelled'),
                    ]),
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
            ->defaultSort('created_at', 'desc');
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
