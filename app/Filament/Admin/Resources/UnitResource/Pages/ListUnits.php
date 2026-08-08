<?php

namespace App\Filament\Admin\Resources\UnitResource\Pages;

use App\Exports\UnitsExport;
use App\Filament\Admin\Resources\Concerns\HasExportImportActions;
use App\Filament\Admin\Resources\UnitResource;
use App\Imports\UnitsImport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUnits extends ListRecords
{
    use HasExportImportActions;

    protected static string $resource = UnitResource::class;

    protected function getExportClass(): ?string
    {
        return UnitsExport::class;
    }

    protected function getImportClass(): ?string
    {
        return UnitsImport::class;
    }

    protected function getExportFilename(): string
    {
        return 'units-export.xlsx';
    }

    protected function getImportLabel(): string
    {
        return __('Import Units');
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
