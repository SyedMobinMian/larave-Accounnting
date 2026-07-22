<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EstimateResource\Pages;
use App\Models\Estimate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EstimateResource extends Resource
{
    protected static ?string $model = Estimate::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('client_id')
                    ->relationship('client', 'name')
                    ->required()
                    ->searchable(),

                Forms\Components\TextInput::make('estimate_number')
                    ->required()
                    ->default('EST-' . strtoupper(uniqid())),

                Forms\Components\DatePicker::make('estimate_date')
                    ->required()
                    ->default(now()),

                Forms\Components\DatePicker::make('expiry_date'),

                Forms\Components\Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'sent' => 'Sent',
                        'accepted' => 'Accepted',
                        'declined' => 'Declined',
                    ])
                    ->default('draft')
                    ->required(),

                Forms\Components\TextInput::make('subtotal')
                    ->numeric()
                    ->prefix('₹'),

                Forms\Components\TextInput::make('tax_total')
                    ->numeric()
                    ->prefix('₹')
                    ->default(0),

                Forms\Components\TextInput::make('total')
                    ->numeric()
                    ->prefix('₹')
                    ->required(),

                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('terms')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('estimate_number')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('client.name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('estimate_date')
                    ->date()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'gray' => 'draft',
                        'info' => 'sent',
                        'success' => 'accepted',
                        'danger' => 'declined',
                    ]),

                Tables\Columns\TextColumn::make('total')
                    ->money('INR')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('download_pdf')
                    ->label(__('Download PDF'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('info')
                    ->url(fn (Estimate $record) => route('estimates.pdf', $record))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEstimates::route('/'),
            'create' => Pages\CreateEstimate::route('/create'),
            'edit' => Pages\EditEstimate::route('/{record}/edit'),
        ];
    }
}