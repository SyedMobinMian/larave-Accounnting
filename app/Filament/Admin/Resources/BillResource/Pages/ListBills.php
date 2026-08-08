<?php

namespace App\Filament\Admin\Resources\BillResource\Pages;

use App\Exports\BillsExport;
use App\Filament\Admin\Resources\BillResource;
use App\Filament\Admin\Resources\Concerns\HasExportImportActions;
use App\Imports\BillsImport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBills extends ListRecords
{
    use HasExportImportActions;

    protected static string $resource = BillResource::class;

    protected function getExportClass(): ?string
    {
        return BillsExport::class;
    }

    protected function getImportClass(): ?string
    {
        return BillsImport::class;
    }

    protected function getExportFilename(): string
    {
        return 'bills-export.xlsx';
    }

    protected function getImportLabel(): string
    {
        return __('Import Bills');
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

