<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\EstimateResource\Pages;
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
    protected static ?string $navigationGroup = 'Sales & CRM';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Estimate Details'))
                    ->schema([
                        Forms\Components\Select::make('client_id')
                            ->label(__('Client'))
                            ->relationship('client', 'company')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('estimate_number')
                            ->label(__('Estimate Number'))
                            ->required()
                            ->default('EST-' . strtoupper(uniqid())),
                        Forms\Components\DatePicker::make('estimate_date')
                            ->label(__('Estimate Date'))
                            ->required()
                            ->default(now()),
                        Forms\Components\DatePicker::make('expiry_date')
                            ->label(__('Expiry Date')),
                        Forms\Components\Select::make('status')
                            ->label(__('Status'))
                            ->options([
                                'draft' => 'Draft',
                                'sent' => 'Sent',
                                'accepted' => 'Accepted',
                                'declined' => 'Declined',
                            ])
                            ->default('draft')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make(__('Totals'))
                    ->schema([
                        Forms\Components\TextInput::make('subtotal')
                            ->label(__('Subtotal'))
                            ->numeric()
                            ->prefix('₹')
                            ->default(0),
                        Forms\Components\TextInput::make('tax_amount')
                            ->label(__('Tax'))
                            ->numeric()
                            ->prefix('₹')
                            ->default(0),
                        Forms\Components\TextInput::make('total_amount')
                            ->label(__('Total'))
                            ->numeric()
                            ->prefix('₹')
                            ->required(),
                    ])->columns(3),

                Forms\Components\Section::make(__('Notes & Terms'))
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label(__('Notes'))
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('terms')
                            ->label(__('Terms'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('estimate_number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('client.company')
                    ->label(__('Client'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('estimate_date')
                    ->label(__('Date'))
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expiry_date')
                    ->label(__('Expiry'))
                    ->date()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'gray' => 'draft',
                        'info' => 'sent',
                        'success' => 'accepted',
                        'danger' => 'declined',
                    ]),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label(__('Total'))
                    ->money('INR')
                    ->sortable(),
            ])
            ->filters([])
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
