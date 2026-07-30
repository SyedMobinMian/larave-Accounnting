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
        
        $subtotal = $invoice->items()->sum('total_price');
        $tax = $subtotal * 0.10; // 10% tax example
        
        $invoice->update([
            'subtotal' => $subtotal,
            'tax_amount' => $tax,
            'total_amount' => $subtotal + $tax,
        ]);
    }
}
