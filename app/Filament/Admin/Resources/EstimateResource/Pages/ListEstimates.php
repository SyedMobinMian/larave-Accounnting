<?php

namespace App\Filament\Admin\Resources\EstimateResource\Pages;

use App\Exports\EstimatesExport;
use App\Filament\Admin\Resources\Concerns\HasExportImportActions;
use App\Filament\Admin\Resources\EstimateResource;
use App\Imports\EstimatesImport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEstimates extends ListRecords
{
    use HasExportImportActions;

    protected static string $resource = EstimateResource::class;

    protected function getExportClass(): ?string
    {
        return EstimatesExport::class;
    }

    protected function getImportClass(): ?string
    {
        return EstimatesImport::class;
    }

    protected function getExportFilename(): string
    {
        return 'estimates-export.xlsx';
    }

    protected function getImportLabel(): string
    {
        return __('Import Estimates');
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
