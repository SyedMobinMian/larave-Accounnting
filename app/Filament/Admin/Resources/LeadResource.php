<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\LeadResource\Pages;
use App\Models\Lead;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Sales & CRM';
    protected static ?string $navigationLabel = 'Leads';
    protected static ?string $modelLabel = 'Lead';
    protected static ?string $pluralModelLabel = 'Leads';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Lead Details'))
                    ->description(__('Contact and qualification information'))
                    ->icon('heroicon-o-user-group')
                    ->schema([
                        Forms\Components\TextInput::make('contact_name')
                            ->label(__('Contact Name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('company_name')
                            ->label(__('Company Name'))
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label(__('Email'))
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label(__('Phone'))
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('website')
                            ->label(__('Website'))
                            ->url()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('estimated_value')
                            ->label(__('Estimated Value'))
                            ->numeric()
                            ->prefix('₹')
                            ->default(0.00),
                        Forms\Components\Select::make('status')
                            ->label(__('Status'))
                            ->options([
                                'new' => __('New'),
                                'contacted' => __('Contacted'),
                                'qualified' => __('Qualified'),
                                'proposal' => __('Proposal'),
                                'negotiation' => __('Negotiation'),
                                'won' => __('Won'),
                                'lost' => __('Lost'),
                            ])
                            ->default('new')
                            ->required(),
                        Forms\Components\Select::make('source')
                            ->label(__('Source'))
                            ->options([
                                'website' => __('Website'),
                                'referral' => __('Referral'),
                                'social_media' => __('Social Media'),
                                'email_campaign' => __('Email Campaign'),
                                'phone_call' => __('Phone Call'),
                                'walk_in' => __('Walk-in'),
                                'other' => __('Other'),
                            ])
                            ->default('other'),
                        Forms\Components\Select::make('assigned_to')
                            ->label(__('Assigned To'))
                            ->relationship('assignedTo', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        Forms\Components\Textarea::make('notes')
                            ->label(__('Notes'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('contact_name')
                    ->label(__('Contact'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('company_name')
                    ->label(__('Company'))
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('Email'))
                    ->searchable()
                    ->icon('heroicon-o-envelope'),
                Tables\Columns\TextColumn::make('phone')
                    ->label(__('Phone'))
                    ->searchable()
                    ->icon('heroicon-o-phone'),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->colors([
                        'gray' => 'new',
                        'info' => 'contacted',
                        'warning' => 'qualified',
                        'primary' => 'proposal',
                        'info' => 'negotiation',
                        'success' => 'won',
                        'danger' => 'lost',
                    ]),
                Tables\Columns\TextColumn::make('source')
                    ->label(__('Source'))
                    ->badge()
                    ->color('gray')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('estimated_value')
                    ->label(__('Value'))
                    ->money('INR')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options([
                        'new' => __('New'),
                        'contacted' => __('Contacted'),
                        'qualified' => __('Qualified'),
                        'proposal' => __('Proposal'),
                        'negotiation' => __('Negotiation'),
                        'won' => __('Won'),
                        'lost' => __('Lost'),
                    ]),
                Tables\Filters\SelectFilter::make('source')
                    ->label(__('Source'))
                    ->options([
                        'website' => __('Website'),
                        'referral' => __('Referral'),
                        'social_media' => __('Social Media'),
                        'email_campaign' => __('Email Campaign'),
                        'phone_call' => __('Phone Call'),
                        'walk_in' => __('Walk-in'),
                        'other' => __('Other'),
                    ]),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeads::route('/'),
            'create' => Pages\CreateLead::route('/create'),
            'edit' => Pages\EditLead::route('/{record}/edit'),
        ];
    }
}
