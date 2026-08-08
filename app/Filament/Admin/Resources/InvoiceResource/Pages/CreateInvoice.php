<?php

namespace App\Filament\Admin\Resources\InvoiceResource\Pages;

use App\Filament\Admin\Resources\InvoiceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    /**
     * Recalculate subtotal / tax / total from the inline line items
     * before the invoice and its items are persisted.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $taxRate = 10.0;
        try {
            $taxRate = (float) (app(\App\Settings\GeneralSettings::class)->default_tax_rate ?? 10.0);
        } catch (\Throwable $e) {
            // fall back to default
        }

        $subtotal = 0.0;
        foreach (($data['items'] ?? []) as $item) {
            $subtotal += (float) ($item['total_price'] ?? 0);
        }

        $data['subtotal'] = round($subtotal, 2);
        $data['tax_amount'] = round($subtotal * ($taxRate / 100), 2);
        $data['total_amount'] = round($subtotal + $data['tax_amount'], 2);

        return $data;
    }
}
