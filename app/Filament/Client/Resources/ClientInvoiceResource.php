<?php

namespace App\Filament\Client\Resources;

use App\Models\Invoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\Action;
use Razorpay\Api\Api;
use Filament\Notifications\Notification;

class ClientInvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'My Invoices';
    protected static ?string $modelLabel = 'Invoice';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        return parent::getEloquentQuery()
            ->where('client_id', $user->client_id ?? $user->id);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Invoice Overview')
                    ->schema([
                        Forms\Components\TextInput::make('invoice_number')->disabled(),
                        Forms\Components\DatePicker::make('date')->disabled(),
                        Forms\Components\DatePicker::make('due_date')->disabled(),
                        Forms\Components\TextInput::make('status')->disabled(),
                        Forms\Components\TextInput::make('total')->prefix('₹')->disabled(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label('Invoice #')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'partially_paid' => 'warning',
                        'unpaid' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('total')
                    ->money('INR')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),

                // 💳 Razorpay Pay Now Action (With Partial Payment Support)
                Action::make('pay_now')
                    ->label('Pay Now')
                    ->icon('heroicon-o-credit-card')
                    ->color('success')
                    ->hidden(fn (Invoice $record): bool => $record->status === 'paid')
                    ->form([
                        Forms\Components\TextInput::make('amount_to_pay')
                            ->label('Amount to Pay (₹)')
                            ->numeric()
                            ->required()
                            ->default(fn (Invoice $record) => $record->remaining_balance)
                            ->maxValue(fn (Invoice $record) => $record->remaining_balance)
                            ->prefix('₹')
                            ->helperText(fn (Invoice $record) => "Total: ₹{$record->total} | Remaining: ₹{$record->remaining_balance}"),
                    ])
                    ->action(function (Invoice $record, array $data) {
                        try {
                            $api = new Api(
                                config('services.razorpay.key_id'),
                                config('services.razorpay.key_secret')
                            );

                            $payAmount = (float) $data['amount_to_pay'];

                            // 1. Create Razorpay Payment Link for partial/full amount
                            $paymentLink = $api->paymentLink->create([
                                'amount' => (int) round($payAmount * 100), // Amount in paise
                                'currency' => 'INR',
                                'accept_partial' => false,
                                'description' => "Payment for Invoice #{$record->invoice_number}",
                                'customer' => [
                                    'name' => auth()->user()->name,
                                    'email' => auth()->user()->email,
                                ],
                                'notify' => [
                                    'sms' => false,
                                    'email' => true,
                                ],
                                'reminder_enable' => true,
                                'notes' => [
                                    'invoice_id' => $record->id,
                                    'invoice_number' => $record->invoice_number,
                                ],
                                'callback_url' => route('razorpay.callback'),
                                'callback_method' => 'get',
                            ]);

                            // 2. Redirect client to Razorpay checkout page
                            return redirect()->away($paymentLink->short_url);

                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Payment Initialization Failed')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                // 📄 PDF Download Action
                Action::make('download_pdf')
                    ->label('Download PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('primary')
                    ->url(fn (Invoice $record): string => route('invoices.pdf', $record))
                    ->openUrlInNewTab(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ClientInvoiceResource\Pages\ListClientInvoices::route('/'),
            'view' => ClientInvoiceResource\Pages\ViewClientInvoice::route('/{record}'),
        ];
    }
}