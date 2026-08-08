<?php

namespace App\Filament\Admin\Resources\InvoiceResource\Pages;

use App\Exports\InvoicesExport;
use App\Filament\Admin\Resources\Concerns\HasExportImportActions;
use App\Filament\Admin\Resources\InvoiceResource;
use App\Imports\InvoicesImport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInvoices extends ListRecords
{
    use HasExportImportActions;

    protected static string $resource = InvoiceResource::class;

    protected function getExportClass(): ?string
    {
        return InvoicesExport::class;
    }

    protected function getImportClass(): ?string
    {
        return InvoicesImport::class;
    }

    protected function getExportFilename(): string
    {
        return 'invoices-export.xlsx';
    }

    protected function getImportLabel(): string
    {
        return __('Import Invoices');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ActionGroup::make($this->getExportActions())
                ->label(__('Export / Import'))
                ->icon('heroicon-o-arrow-up-tray')
                ->color('gray'),
            Actions\CreateAction::make(),
        ];
    }
}
