<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Filament\Resources\ClientResource\RelationManagers;
use App\Models\Client;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('company')
                    ->maxLength(255),
                Forms\Components\TextInput::make('vat')
                    ->maxLength(255),
                Forms\Components\TextInput::make('phonenumber')
                    ->tel()
                    ->maxLength(255),
                Forms\Components\TextInput::make('country')
                    ->maxLength(255),
                Forms\Components\TextInput::make('city')
                    ->maxLength(255),
                Forms\Components\TextInput::make('zip')
                    ->maxLength(255),
                Forms\Components\TextInput::make('state')
                    ->maxLength(255),
                Forms\Components\TextInput::make('address')
                    ->maxLength(255),
                Forms\Components\TextInput::make('website')
                    ->maxLength(255),
                Forms\Components\Toggle::make('active')
                    ->required(),
                Forms\Components\TextInput::make('billing_street')
                    ->maxLength(255),
                Forms\Components\TextInput::make('billing_city')
                    ->maxLength(255),
                Forms\Components\TextInput::make('billing_state')
                    ->maxLength(255),
                Forms\Components\TextInput::make('billing_zip')
                    ->maxLength(255),
                Forms\Components\TextInput::make('billing_country')
                    ->maxLength(255),
                Forms\Components\TextInput::make('shipping_street')
                    ->maxLength(255),
                Forms\Components\TextInput::make('shipping_city')
                    ->maxLength(255),
                Forms\Components\TextInput::make('shipping_state')
                    ->maxLength(255),
                Forms\Components\TextInput::make('shipping_zip')
                    ->maxLength(255),
                Forms\Components\TextInput::make('shipping_country')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company')
                    ->searchable(),
                Tables\Columns\TextColumn::make('vat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phonenumber')
                    ->searchable(),
                Tables\Columns\TextColumn::make('country')
                    ->searchable(),
                Tables\Columns\TextColumn::make('city')
                    ->searchable(),
                Tables\Columns\TextColumn::make('zip')
                    ->searchable(),
                Tables\Columns\TextColumn::make('state')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('website')
                    ->searchable(),
                Tables\Columns\IconColumn::make('active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('billing_street')
                    ->searchable(),
                Tables\Columns\TextColumn::make('billing_city')
                    ->searchable(),
                Tables\Columns\TextColumn::make('billing_state')
                    ->searchable(),
                Tables\Columns\TextColumn::make('billing_zip')
                    ->searchable(),
                Tables\Columns\TextColumn::make('billing_country')
                    ->searchable(),
                Tables\Columns\TextColumn::make('shipping_street')
                    ->searchable(),
                Tables\Columns\TextColumn::make('shipping_city')
                    ->searchable(),
                Tables\Columns\TextColumn::make('shipping_state')
                    ->searchable(),
                Tables\Columns\TextColumn::make('shipping_zip')
                    ->searchable(),
                Tables\Columns\TextColumn::make('shipping_country')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}
