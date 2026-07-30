<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;


class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    public static function getNavigationGroup(): ?string
    {
        return __('Procurement & Inventory');
    }

    public static function getNavigationLabel(): string
    {
        return __('Products & Stock');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Product Information'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('Product Name'))
                            ->required(),

                        Forms\Components\TextInput::make('sku')
                            ->label(__('SKU / Item Code'))
                            ->default('SKU-' . strtoupper(uniqid()))
                            ->required(),

                        Forms\Components\Textarea::make('description')
                            ->label(__('Description'))
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make(__('Pricing & Inventory'))
                    ->schema([
                        Forms\Components\TextInput::make('cost_price')
                            ->label(__('Cost Price (Purchase)'))
                            ->numeric()
                            ->prefix('₹')
                            ->default(0.00)
                            ->required(),

                        Forms\Components\TextInput::make('selling_price')
                            ->label(__('Selling Price (Sales)'))
                            ->numeric()
                            ->prefix('₹')
                            ->default(0.00)
                            ->required(),

                        Forms\Components\TextInput::make('stock_quantity')
                            ->label(__('In-Stock Quantity'))
                            ->numeric()
                            ->default(0)
                            ->required(),

                        Forms\Components\TextInput::make('min_stock_alert')
                            ->label(__('Low Stock Threshold Alert'))
                            ->numeric()
                            ->default(5)
                            ->required(),

                        Forms\Components\TextInput::make('unit')
                            ->label(__('Measurement Unit'))
                            ->default('pcs')
                            ->placeholder('pcs, kg, meters')
                            ->required(),

                        Forms\Components\Toggle::make('is_active')
                            ->label(__('Active Status'))
                            ->default(true),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sku')
                    ->label(__('SKU'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label(__('Product Name'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('cost_price')
                    ->label(__('Cost Price'))
                    ->money('INR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('selling_price')
                    ->label(__('Selling Price'))
                    ->money('INR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label(__('Stock'))
                    ->badge()
                    ->color(fn (Product $record) => $record->stock_quantity <= $record->min_stock_alert ? 'danger' : 'success')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('Active'))
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\Filter::make('low_stock')
                    ->label(__('Low Stock Alert'))
                    ->query(fn ($query) => $query->whereColumn('stock_quantity', '<=', 'min_stock_alert')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}