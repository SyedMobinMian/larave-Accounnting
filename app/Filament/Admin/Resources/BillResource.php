<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\BillResource\Pages;
use App\Models\Bill;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BillResource extends Resource
{
    protected static ?string $model = Bill::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-arrow-down';
    protected static ?string $navigationGroup = 'Procurement';
    protected static ?string $navigationLabel = 'Bills';
    protected static ?string $modelLabel = 'Bill';
    protected static ?string $pluralModelLabel = 'Bills';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Bill Header'))
                    ->description(__('Vendor and bill identification details'))
                    ->icon('heroicon-o-receipt-percent')
                    ->schema([
                        Forms\Components\TextInput::make('bill_number')
                            ->label(__('Bill Number'))
                            ->default('BILL-' . strtoupper(uniqid()))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('vendor_id')
                            ->label(__('Vendor'))
                            ->relationship('vendor', 'company_name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\DatePicker::make('bill_date')
                            ->label(__('Bill Date'))
                            ->default(now())
                            ->required(),
                        Forms\Components\DatePicker::make('due_date')
                            ->label(__('Due Date')),
                        Forms\Components\Select::make('status')
                            ->label(__('Status'))
                            ->options([
                                'draft' => __('Draft'),
                                'unpaid' => __('Unpaid'),
                                'partially_paid' => __('Partially Paid'),
                                'paid' => __('Paid'),
                                'overdue' => __('Overdue'),
                                'cancelled' => __('Cancelled'),
                            ])
                            ->default('unpaid')
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
                            ->label(__('Tax Amount'))
                            ->numeric()
                            ->prefix('₹')
                            ->default(0),
                        Forms\Components\TextInput::make('total_amount')
                            ->label(__('Total Amount'))
                            ->numeric()
                            ->prefix('₹')
                            ->default(0),
                        Forms\Components\TextInput::make('paid_amount')
                            ->label(__('Paid Amount'))
                            ->numeric()
                            ->prefix('₹')
                            ->default(0),
                    ])->columns(2),

                Forms\Components\Section::make(__('Notes'))
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label(__('Notes'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('bill_number')
                    ->label(__('Bill #'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('vendor.company_name')
                    ->label(__('Vendor'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bill_date')
                    ->label(__('Bill Date'))
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->label(__('Due Date'))
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->colors([
                        'gray' => 'draft',
                        'warning' => 'unpaid',
                        'info' => 'partially_paid',
                        'success' => 'paid',
                        'danger' => 'overdue',
                        'gray' => 'cancelled',
                    ]),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label(__('Total'))
                    ->money('INR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('paid_amount')
                    ->label(__('Paid'))
                    ->money('INR')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('balance')
                    ->label(__('Balance'))
                    ->money('INR')
                    ->getStateUsing(fn (Bill $record): float => $record->total_amount - $record->paid_amount)
                    ->color(fn (Bill $record): string => $record->total_amount - $record->paid_amount > 0 ? 'danger' : 'success'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options([
                        'draft' => __('Draft'),
                        'unpaid' => __('Unpaid'),
                        'partially_paid' => __('Partially Paid'),
                        'paid' => __('Paid'),
                        'overdue' => __('Overdue'),
                        'cancelled' => __('Cancelled'),
                    ]),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\Action::make('record_payment')
                        ->label(__('Record Payment'))
                        ->icon('heroicon-o-banknotes')
                        ->color('success')
                        ->form([
                            Forms\Components\DatePicker::make('payment_date')
                                ->label(__('Payment Date'))
                                ->default(now())
                                ->required(),
                            Forms\Components\TextInput::make('amount')
                                ->label(__('Amount'))
                                ->numeric()
                                ->prefix('₹')
                                ->required()
                                ->maxValue(fn (Bill $record): float => $record->total_amount - $record->paid_amount),
                            Forms\Components\Select::make('payment_method')
                                ->label(__('Payment Method'))
                                ->options([
                                    'bank_transfer' => __('Bank Transfer'),
                                    'cash' => __('Cash'),
                                    'cheque' => __('Cheque'),
                                    'upi' => __('UPI'),
                                    'card' => __('Card'),
                                ]),
                            Forms\Components\TextInput::make('reference')
                                ->label(__('Reference / Transaction ID')),
                        ])
                        ->action(function (Bill $record, array $data) {
                            $record->payments()->create($data);
                            $record->increment('paid_amount', $data['amount']);
                            if ($record->paid_amount >= $record->total_amount) {
                                $record->update(['status' => 'paid']);
                            } else {
                                $record->update(['status' => 'partially_paid']);
                            }
                            Notification::make()
                                ->title(__('Payment recorded successfully!'))
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('bill_date', 'desc');
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
            'index' => Pages\ListBills::route('/'),
            'create' => Pages\CreateBill::route('/create'),
            'edit' => Pages\EditBill::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['vendor', 'payments']);
    }
}

