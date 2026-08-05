<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\InvoiceResource\RelationManagers\ItemsRelationManager;
use App\Filament\Admin\Resources\InvoiceResource\Pages;
use App\Models\Invoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('Sales & CRM');
    }

    public static function getNavigationLabel(): string
    {
        return __('Invoices');
    }

    public static function getModelLabel(): string
    {
        return __('Invoice');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Invoices');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('client_id')
                ->label(__('Client'))
                ->relationship('client', 'company')
                ->searchable()
                ->preload()
                ->required(),
            Forms\Components\TextInput::make('invoice_number')
                ->label(__('Invoice Number'))
                ->required()
                ->maxLength(255),
            Forms\Components\DatePicker::make('issue_date')
                ->label(__('Date'))
                ->required(),
            Forms\Components\DatePicker::make('due_date')
                ->label(__('Due Date')),
            Forms\Components\Select::make('status')
                ->options([
                    'unpaid' => __('Unpaid'),
                    'paid' => __('Paid'),
                    'cancelled' => __('Cancelled'),
                ])
                ->required(),
            Forms\Components\TextInput::make('subtotal')
                ->label(__('Subtotal'))
                ->required()
                ->numeric()
                ->default(0.00),
            Forms\Components\TextInput::make('tax_amount')
                ->label(__('Total Tax'))
                ->required()
                ->numeric()
                ->default(0.00),
            Forms\Components\TextInput::make('total_amount')
                ->label(__('Total'))
                ->required()
                ->numeric()
                ->default(0.00),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label(__('Invoice Number'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('client.company')
                    ->label(__('Client'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('issue_date')
                    ->label(__('Date'))
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label(__('Total'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('Status')),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}

