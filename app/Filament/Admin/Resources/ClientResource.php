<?php

namespace App\Filament\Admin\Resources;

use App\Exports\ClientsExport;
use App\Filament\Admin\Resources\ClientResource\Pages;
use App\Imports\ClientsImport;
use App\Models\Client;
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
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationGroup = 'Sales & CRM';
    protected static ?string $navigationLabel = 'Clients';
    protected static ?string $modelLabel = 'Client';
    protected static ?string $pluralModelLabel = 'Clients';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Company Information')
                    ->description('Basic company details and contact information')
                    ->icon('heroicon-o-building-office')
                    ->schema([
                        Forms\Components\TextInput::make('company')
                            ->label('Company Name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),
                        Forms\Components\TextInput::make('vat')
                            ->label('VAT / Tax Number')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phonenumber')
                            ->label('Phone Number')
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('website')
                            ->label('Website')
                            ->url()
                            ->maxLength(255)
                            ->prefix('https://'),
                        Forms\Components\Toggle::make('active')
                            ->label('Active')
                            ->default(true)
                            ->inline(false),
                    ])->columns(2),

                Forms\Components\Section::make('Primary Address')
                    ->description('Main business address')
                    ->icon('heroicon-o-map-pin')
                    ->schema([
                        Forms\Components\TextInput::make('address')
                            ->label('Street Address')
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('city')
                            ->label('City')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('state')
                            ->label('State / Province')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('zip')
                            ->label('ZIP / Postal Code')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('country')
                            ->label('Country')
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Tabs::make('Billing & Shipping')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Billing Address')
                            ->icon('heroicon-o-credit-card')
                            ->schema([
                                Forms\Components\TextInput::make('billing_street')
                                    ->label('Street')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('billing_city')
                                    ->label('City')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('billing_state')
                                    ->label('State')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('billing_zip')
                                    ->label('ZIP')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('billing_country')
                                    ->label('Country')
                                    ->maxLength(255),
                            ])->columns(2),
                        Forms\Components\Tabs\Tab::make('Shipping Address')
                            ->icon('heroicon-o-truck')
                            ->schema([
                                Forms\Components\TextInput::make('shipping_street')
                                    ->label('Street')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('shipping_city')
                                    ->label('City')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('shipping_state')
                                    ->label('State')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('shipping_zip')
                                    ->label('ZIP')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('shipping_country')
                                    ->label('Country')
                                    ->maxLength(255),
                            ])->columns(2),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company')
                    ->label('Company')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (Client $record): ?string => $record->email),
                Tables\Columns\TextColumn::make('phonenumber')
                    ->label('Phone')
                    ->searchable()
                    ->icon('heroicon-o-phone'),
                Tables\Columns\TextColumn::make('city')
                    ->label('City')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('country')
                    ->label('Country')
                    ->searchable()
                    ->badge()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->date('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('active')
                    ->label('Active Status')
                    ->placeholder('All Clients')
                    ->trueLabel('Active Clients')
                    ->falseLabel('Inactive Clients'),
                Tables\Filters\SelectFilter::make('country')
                    ->label('Country')
                    ->options(fn () => Client::query()->whereNotNull('country')->pluck('country', 'country')->toArray())
                    ->searchable(),
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
                        ->label('Export Selected')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function ($records) {
                            $ids = $records->pluck('id')->toArray();
                            $filters = array_filter(request()->only(['search', 'active', 'country']));
                            $filters['ids'] = $ids;
                            return Excel::download(new ClientsExport($filters), 'selected-clients.xlsx');
                        }),
                ]),
            ])
            ->headerActions([
                ActionGroup::make([
                    Action::make('export')
                        ->label('Export All')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->action(function () {
                            $filters = array_filter(request()->only(['search', 'active', 'country']));
                            return Excel::download(new ClientsExport($filters), 'clients-export.xlsx');
                        }),
                    Action::make('exportCsv')
                        ->label('Export as CSV')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('gray')
                        ->action(function () {
                            $filters = array_filter(request()->only(['search', 'active', 'country']));
                            return Excel::download(new ClientsExport($filters), 'clients-export.csv', \Maatwebsite\Excel\Excel::CSV);
                        }),
                    Action::make('import')
                        ->label('Import Clients')
                        ->icon('heroicon-o-arrow-up-tray')
                        ->color('warning')
                        ->form([
                            Forms\Components\FileUpload::make('file')
                                ->label('Upload Excel/CSV File')
                                ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'text/csv', 'application/vnd.ms-excel'])
                                ->required()
                                ->maxSize(5120),
                        ])
                        ->action(function (array $data) {
                            try {
                                $import = new ClientsImport();
                                Excel::import($import, $data['file']);
                                $successCount = $import->getRowCount();
                                $failures = $import->failures();

                                Notification::make()
                                    ->title('Import completed!')
                                    ->body(count($failures) > 0
                                        ? count($failures) . ' rows failed validation.'
                                        : 'All records imported successfully.')
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Import failed')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),
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
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withCount('invoices');
    }
}
