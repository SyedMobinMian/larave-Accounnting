<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\StockMovementResource\Pages;
use App\Models\Product;
use App\Models\StockMovement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StockMovementResource extends Resource
{
    protected static ?string $model = StockMovement::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';
    protected static ?string $navigationGroup = 'Inventory';
    protected static ?string $navigationLabel = 'Stock Movements';
    protected static ?string $modelLabel = 'Stock Movement';
    protected static ?string $pluralModelLabel = 'Stock Movements';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Stock Movement Details'))
                    ->description(__('Record stock movement in/out'))
                    ->icon('heroicon-o-arrow-path')
                    ->schema([
                        Forms\Components\Select::make('product_id')
                            ->label(__('Product'))
                            ->relationship('product', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('warehouse_id')
                            ->label(__('Warehouse'))
                            ->relationship('warehouse', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        Forms\Components\Select::make('type')
                            ->label(__('Movement Type'))
                            ->options([
                                'in' => __('Stock In'),
                                'out' => __('Stock Out'),
                                'transfer_in' => __('Transfer In'),
                                'transfer_out' => __('Transfer Out'),
                                'adjustment' => __('Adjustment'),
                                'opening' => __('Opening Stock'),
                            ])
                            ->default('in')
                            ->required(),
                        Forms\Components\TextInput::make('quantity')
                            ->label(__('Quantity'))
                            ->numeric()
                            ->required()
                            ->default(0),
                        Forms\Components\TextInput::make('unit_cost')
                            ->label(__('Unit Cost'))
                            ->numeric()
                            ->prefix('₹')
                            ->default(0),
                        Forms\Components\Textarea::make('notes')
                            ->label(__('Notes'))
                            ->rows(2)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label(__('Product'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('warehouse.name')
                    ->label(__('Warehouse'))
                    ->placeholder(__('—'))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('type')
                    ->label(__('Type'))
                    ->badge()
                    ->colors([
                        'success' => 'in',
                        'danger' => 'out',
                        'info' => 'transfer_in',
                        'warning' => 'transfer_out',
                        'gray' => 'adjustment',
                        'primary' => 'opening',
                    ]),
                Tables\Columns\TextColumn::make('quantity')
                    ->label(__('Quantity'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('balance_after')
                    ->label(__('Balance After'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Date'))
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('createdBy.name')
                    ->label(__('By'))
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label(__('Type'))
                    ->options([
                        'in' => __('Stock In'),
                        'out' => __('Stock Out'),
                        'transfer_in' => __('Transfer In'),
                        'transfer_out' => __('Transfer Out'),
                        'adjustment' => __('Adjustment'),
                        'opening' => __('Opening Stock'),
                    ]),
                Tables\Filters\SelectFilter::make('warehouse_id')
                    ->label(__('Warehouse'))
                    ->relationship('warehouse', 'name'),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\Action::make('adjust_stock')
                        ->label(__('Adjust Stock'))
                        ->icon('heroicon-o-adjustments-horizontal')
                        ->color('warning')
                        ->form([
                            Forms\Components\TextInput::make('new_quantity')
                                ->label(__('New Quantity'))
                                ->numeric()
                                ->required(),
                            Forms\Components\Textarea::make('reason')
                                ->label(__('Reason'))
                                ->rows(2)
                                ->required(),
                        ])
                        ->action(function (StockMovement $record, array $data) {
                            $product = $record->product;
                            $diff = $data['new_quantity'] - $product->stock_quantity;
                            $product->update(['stock_quantity' => $data['new_quantity']]);

                            StockMovement::create([
                                'product_id' => $product->id,
                                'warehouse_id' => $record->warehouse_id,
                                'type' => 'adjustment',
                                'quantity' => abs($diff),
                                'balance_before' => $data['new_quantity'] - $diff,
                                'balance_after' => $data['new_quantity'],
                                'notes' => 'Manual adjustment: ' . $data['reason'],
                                'created_by' => auth()->id(),
                            ]);

                            Notification::make()
                                ->title(__('Stock adjusted successfully!'))
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
            'index' => Pages\ListStockMovements::route('/'),
            'create' => Pages\CreateStockMovement::route('/create'),
            'edit' => Pages\EditStockMovement::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['product', 'warehouse', 'createdBy']);
    }
}

