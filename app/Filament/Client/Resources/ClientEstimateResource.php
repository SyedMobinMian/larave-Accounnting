<?php

namespace App\Filament\Client\Resources;

use App\Models\Estimate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;

class ClientEstimateResource extends Resource
{
    protected static ?string $model = Estimate::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    public static function getNavigationLabel(): string
    {
        return __('My Estimates');
    }

    public static function getModelLabel(): string
    {
        return __('Estimate');
    }

    public static function getPluralModelLabel(): string
    {
        return __('My Estimates');
    }

    public static function canCreate(): bool { return false; }
    public static function canDelete($record): bool { return false; }
    public static function canEdit($record): bool { return false; }

    // 🔒 CLIENT SCOPING    
    /**
     * getEloquentQuery
     *
     * @return Builder
     */
    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();

        return parent::getEloquentQuery()
            ->where('client_id', $user->client_id ?? $user->id);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('estimate_number')
                    ->label(__('Estimate #'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('date')
                    ->label(__('Date'))
                    ->date(),
                Tables\Columns\TextColumn::make('expiry_date')
                    ->label(__('Expiry Date'))
                    ->date(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'accepted' => 'success',
                        'declined' => 'danger',
                        'sent' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('total')
                    ->label(__('Total'))
                    ->money('INR'),
            ])
            ->actions([
                // ✅ Accept Action
                Action::make('accept')
                    ->label(__('Accept Quote'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Estimate $record) => in_array($record->status, ['sent', 'draft']))
                    ->action(function (Estimate $record) {
                        $record->update(['status' => 'accepted']);
                        Notification::make()
                            ->title(__('Estimate Accepted Successfully!'))
                            ->success()
                            ->send();
                    }),

                // ❌ Reject Action
                Action::make('decline')
                    ->label(__('Decline Quote'))
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Estimate $record) => in_array($record->status, ['sent', 'draft']))
                    ->action(function (Estimate $record) {
                        $record->update(['status' => 'declined']);
                        Notification::make()
                            ->title(__('Estimate Declined'))
                            ->warning()
                            ->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ClientEstimateResource\Pages\ListClientEstimates::route('/'),
        ];
    }
}