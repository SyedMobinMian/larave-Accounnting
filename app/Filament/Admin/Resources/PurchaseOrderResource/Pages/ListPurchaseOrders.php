<?php

namespace App\Filament\Admin\Resources\PurchaseOrderResource\Pages;

use App\Exports\PurchaseOrdersExport;
use App\Filament\Admin\Resources\Concerns\HasExportImportActions;
use App\Filament\Admin\Resources\PurchaseOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPurchaseOrders extends ListRecords
{
    use HasExportImportActions;

    protected static string $resource = PurchaseOrderResource::class;

    protected function getExportClass(): ?string
    {
        return PurchaseOrdersExport::class;
    }

    protected function getImportClass(): ?string
    {
        return null;
    }

    protected function getExportFilename(): string
    {
        return 'purchase-orders-export.xlsx';
    }

    protected function getImportLabel(): string
    {
        return __('Import Purchase Orders');
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
