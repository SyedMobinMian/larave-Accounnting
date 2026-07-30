<?php

namespace App\Filament\Admin\Resources\InvoiceResource\RelationManagers;

use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->label('Product')
                    ->options(Product::pluck('name', 'id'))
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(function ($state, Set $set) {
                        $product = Product::find($state);
                        if ($product) {
                            $set('unit_price', $product->sales_price ?? 0);
                            $set('description', $product->name);
                        }
                    })
                    ->columnSpan(4),

                Forms\Components\TextInput::make('description')
                    ->columnSpan(8),

                Forms\Components\TextInput::make('quantity')
                    ->numeric()
                    ->default(1)
                    ->reactive()
                    ->afterStateUpdated(fn ($state, Set $set, $get) => 
                        $set('total_price', (float)$state * (float)$get('unit_price'))
                    )
                    ->columnSpan(4),

                Forms\Components\TextInput::make('unit_price')
                    ->numeric()
                    ->prefix('$')
                    ->reactive()
                    ->afterStateUpdated(fn ($state, Set $set, $get) => 
                        $set('total_price', (float)$state * (float)$get('quantity'))
                    )
                    ->columnSpan(4),

                Forms\Components\TextInput::make('total_price')
                    ->numeric()
                    ->prefix('$')
                    ->readOnly()
                    ->columnSpan(4),
            ])->columns(12);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                Tables\Columns\TextColumn::make('product.name')->label('Product'),
                Tables\Columns\TextColumn::make('description'),
                Tables\Columns\TextColumn::make('quantity'),
                Tables\Columns\TextColumn::make('unit_price')->money('USD'),
                Tables\Columns\TextColumn::make('total_price')->money('USD'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
    
}