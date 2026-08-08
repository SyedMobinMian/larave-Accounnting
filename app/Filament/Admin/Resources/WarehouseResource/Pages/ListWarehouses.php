<?php

namespace App\Filament\Admin\Resources\WarehouseResource\Pages;

use App\Exports\WarehousesExport;
use App\Filament\Admin\Resources\Concerns\HasExportImportActions;
use App\Filament\Admin\Resources\WarehouseResource;
use App\Imports\WarehousesImport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWarehouses extends ListRecords
{
    use HasExportImportActions;

    protected static string $resource = WarehouseResource::class;

    protected function getExportClass(): ?string
    {
        return WarehousesExport::class;
    }

    protected function getImportClass(): ?string
    {
        return WarehousesImport::class;
    }

    protected function getExportFilename(): string
    {
        return 'warehouses-export.xlsx';
    }

    protected function getImportLabel(): string
    {
        return __('Import Warehouses');
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

