<?php

namespace App\Filament\Admin\Resources\InvoiceResource\Pages;

use App\Filament\Admin\Resources\InvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

protected function afterSave(): void
    {
        $invoice = $this->getRecord();

        // Recalculate totals from the saved line items.
        $subtotal = (float) $invoice->items()->sum('total_price');

        // Pull the configured tax rate from settings (default 10%).
        $taxRate = 10.0;
        try {
            $taxRate = (float) (app(\App\Settings\GeneralSettings::class)->default_tax_rate ?? 10.0);
        } catch (\Throwable $e) {
            // fall back to default
        }

        $tax = round($subtotal * ($taxRate / 100), 2);

        $invoice->update([
            'subtotal' => $subtotal,
            'tax_amount' => $tax,
            'total_amount' => round($subtotal + $tax, 2),
        ]);
    }
}
