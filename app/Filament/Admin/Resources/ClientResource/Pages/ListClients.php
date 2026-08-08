<?php

namespace App\Filament\Admin\Resources\ClientResource\Pages;

use App\Exports\ClientsExport;
use App\Filament\Admin\Resources\ClientResource;
use App\Filament\Admin\Resources\Concerns\HasExportImportActions;
use App\Imports\ClientsImport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClients extends ListRecords
{
    use HasExportImportActions;

    protected static string $resource = ClientResource::class;

    protected function getExportClass(): ?string
    {
        return ClientsExport::class;
    }

    protected function getImportClass(): ?string
    {
        return ClientsImport::class;
    }

    protected function getExportFilename(): string
    {
        return 'clients-export.xlsx';
    }

    protected function getImportLabel(): string
    {
        return __('Import Clients');
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
