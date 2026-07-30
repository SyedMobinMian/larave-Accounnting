<?php

namespace App\Filament\Admin\Resources;

use App\Exports\ProductsExport;
use App\Filament\Admin\Resources\ProductResource\Pages;
use App\Imports\ProductsImport;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?int $navigationSort = 1;

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
                    ->description(__('Basic product details and identification'))
                    ->icon('heroicon-o-tag')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('Product Name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('sku')
                            ->label(__('SKU / Item Code'))
                            ->default('SKU-' . strtoupper(uniqid()))
                            ->required()
                            ->unique(ignoreRecord: true),
                        Forms\Components\Textarea::make('description')
                            ->label(__('Description'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make(__('Pricing & Inventory'))
                    ->description(__('Cost, selling price and stock management'))
                    ->icon('heroicon-o-currency-rupee')
                    ->schema([
                        Forms\Components\TextInput::make('cost_price')
                            ->label(__('Cost Price (Purchase)'))
                            ->numeric()
                            ->prefix('₹')
                            ->default(0.00)
                            ->required()
                            ->helperText(__('The price at which you purchase this product')),
                        Forms\Components\TextInput::make('selling_price')
                            ->label(__('Selling Price (Sales)'))
                            ->numeric()
                            ->prefix('₹')
                            ->default(0.00)
                            ->required()
                            ->helperText(__('The price at which you sell this product to customers')),
                        Forms\Components\TextInput::make('stock_quantity')
                            ->label(__('In-Stock Quantity'))
                            ->numeric()
                            ->default(0)
                            ->required()
                            ->minValue(0),
                        Forms\Components\TextInput::make('min_stock_alert')
                            ->label(__('Low Stock Threshold'))
                            ->numeric()
                            ->default(5)
                            ->required()
                            ->minValue(0)
                            ->helperText(__('You will be alerted when stock falls below this level')),
                        Forms\Components\TextInput::make('unit')
                            ->label(__('Measurement Unit'))
                            ->default('pcs')
                            ->placeholder('pcs, kg, meters')
                            ->required(),
                        Forms\Components\Toggle::make('is_active')
                            ->label(__('Active Status'))
                            ->default(true)
                            ->inline(false),
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
                    ->sortable()
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Product Name'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (Product $record): ?string => $record->description ? substr($record->description, 0, 50) . '...' : null),
                Tables\Columns\TextColumn::make('cost_price')
                    ->label(__('Cost Price'))
                    ->money('INR')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('selling_price')
                    ->label(__('Selling Price'))
                    ->money('INR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label(__('Stock'))
                    ->badge()
                    ->color(fn (Product $record) => $record->stock_quantity <= $record->min_stock_alert ? 'danger' : 'success')
                    ->sortable()
                    ->icon(fn (Product $record) => $record->stock_quantity <= $record->min_stock_alert ? 'heroicon-o-exclamation-triangle' : 'heroicon-o-check-circle'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('Active'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),
            ])
            ->filters([
                Tables\Filters\Filter::make('low_stock')
                    ->label(__('Low Stock Alert'))
                    ->query(fn ($query) => $query->whereColumn('stock_quantity', '<=', 'min_stock_alert')),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('Status'))
                    ->placeholder(__('All Products'))
                    ->trueLabel(__('Active'))
                    ->falseLabel(__('Inactive')),
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
                            return Excel::download(new ProductsExport(['ids' => $ids]), 'selected-products.xlsx');
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
                            return Excel::download(new ProductsExport(), 'products-export.xlsx');
                        }),
                    Action::make('exportCsv')
                        ->label(__('Export as CSV'))
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('gray')
                        ->action(function () {
                            return Excel::download(new ProductsExport(), 'products-export.csv', \Maatwebsite\Excel\Excel::CSV);
                        }),
                    Action::make('import')
                        ->label(__('Import Products'))
                        ->icon('heroicon-o-arrow-up-tray')
                        ->color('warning')
                        ->form([
                            Forms\Components\FileUpload::make('file')
                                ->label(__('Upload Excel/CSV File'))
                                ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'text/csv', 'application/vnd.ms-excel'])
                                ->required()
                                ->maxSize(5120),
                        ])
                        ->action(function (array $data) {
                            try {
                                $import = new ProductsImport();
                                Excel::import($import, $data['file']);
                                $failures = $import->failures();

                                Notification::make()
                                    ->title(__('Import completed!'))
                                    ->body(count($failures) > 0
                                        ? count($failures) . ' ' . __('rows failed validation.')
                                        : __('All records imported successfully.'))
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title(__('Import failed'))
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with('unit');
    }
}
