<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ContactResource\Pages;
// use App\Filament\Admin\Resources\ContactResource\RelationManagers;
use App\Models\Contact;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
// use Illuminate\Database\Eloquent\Builder;
// use Illuminate\Database\Eloquent\SoftDeletingScope;

class ContactResource extends Resource
{
    protected static ?string $model = Contact::class;

    protected static ?string $navigationIcon = 'heroicon-o-phone';

    public static function getNavigationGroup(): ?string
    {
        return __('Sales & CRM');
    }

    public static function getModelLabel(): string
    {
        return __('Contact');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Contacts');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('client_id')
                    ->label(__('Client'))
                    ->relationship('client', 'company')
                    ->searchable()
                    ->preload(),
                Forms\Components\Toggle::make('is_primary')
                    ->label(__('Primary Contact'))
                    ->required(),
                Forms\Components\TextInput::make('firstname')
                    ->label(__('First Name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('lastname')
                    ->label(__('Last Name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label(__('Email'))
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phonenumber')
                    ->label(__('Phone'))
                    ->tel()
                    ->maxLength(255),
                Forms\Components\TextInput::make('title')
                    ->label(__('Title'))
                    ->maxLength(255),
                Forms\Components\TextInput::make('password')
                    ->label(__('Password'))
                    ->password()
                    ->maxLength(255),
                Forms\Components\Toggle::make('active')
                    ->label(__('Active'))
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client.company')
                    ->label(__('Client'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_primary')
                    ->label(__('Primary'))
                    ->boolean(),
                Tables\Columns\TextColumn::make('firstname')
                    ->label(__('First Name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('lastname')
                    ->label(__('Last Name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('Email'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('phonenumber')
                    ->label(__('Phone'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->label(__('Title'))
                    ->searchable(),
                Tables\Columns\IconColumn::make('active')
                    ->label(__('Active'))
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('Updated At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('active')
                    ->label(__('Status')),
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
            'index' => Pages\ListContacts::route('/'),
            'create' => Pages\CreateContact::route('/create'),
            'edit' => Pages\EditContact::route('/{record}/edit'),
        ];
    }
}

