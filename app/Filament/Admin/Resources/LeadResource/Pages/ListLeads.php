<?php

namespace App\Filament\Admin\Resources\LeadResource\Pages;

use App\Exports\LeadsExport;
use App\Filament\Admin\Resources\Concerns\HasExportImportActions;
use App\Filament\Admin\Resources\LeadResource;
use App\Imports\LeadsImport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLeads extends ListRecords
{
    use HasExportImportActions;

    protected static string $resource = LeadResource::class;

    protected function getExportClass(): ?string
    {
        return LeadsExport::class;
    }

    protected function getImportClass(): ?string
    {
        return LeadsImport::class;
    }

    protected function getExportFilename(): string
    {
        return 'leads-export.xlsx';
    }

    protected function getImportLabel(): string
    {
        return __('Import Leads');
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

