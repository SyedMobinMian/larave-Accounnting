<?php

namespace App\Filament\Admin\Resources\StockMovementResource\Pages;

use App\Exports\StockMovementsExport;
use App\Filament\Admin\Resources\Concerns\HasExportImportActions;
use App\Filament\Admin\Resources\StockMovementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStockMovements extends ListRecords
{
    use HasExportImportActions;

    protected static string $resource = StockMovementResource::class;

    protected function getExportClass(): ?string
    {
        return StockMovementsExport::class;
    }

    protected function getImportClass(): ?string
    {
        return null;
    }

    protected function getExportFilename(): string
    {
        return 'stock-movements-export.xlsx';
    }

    protected function getImportLabel(): string
    {
        return __('Import Stock Movements');
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

