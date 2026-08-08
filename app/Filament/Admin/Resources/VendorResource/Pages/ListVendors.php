<?php

namespace App\Filament\Admin\Resources\VendorResource\Pages;

use App\Exports\VendorsExport;
use App\Filament\Admin\Resources\Concerns\HasExportImportActions;
use App\Filament\Admin\Resources\VendorResource;
use App\Imports\VendorsImport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVendors extends ListRecords
{
    use HasExportImportActions;

    protected static string $resource = VendorResource::class;

    protected function getExportClass(): ?string
    {
        return VendorsExport::class;
    }

    protected function getImportClass(): ?string
    {
        return VendorsImport::class;
    }

    protected function getExportFilename(): string
    {
        return 'vendors-export.xlsx';
    }

    protected function getImportLabel(): string
    {
        return __('Import Vendors');
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
