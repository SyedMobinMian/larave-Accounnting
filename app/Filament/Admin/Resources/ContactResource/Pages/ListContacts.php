<?php

namespace App\Filament\Admin\Resources\ContactResource\Pages;

use App\Exports\ContactsExport;
use App\Filament\Admin\Resources\Concerns\HasExportImportActions;
use App\Filament\Admin\Resources\ContactResource;
use App\Imports\ContactsImport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListContacts extends ListRecords
{
    use HasExportImportActions;

    protected static string $resource = ContactResource::class;

    protected function getExportClass(): ?string
    {
        return ContactsExport::class;
    }

    protected function getImportClass(): ?string
    {
        return ContactsImport::class;
    }

    protected function getExportFilename(): string
    {
        return 'contacts-export.xlsx';
    }

    protected function getImportLabel(): string
    {
        return __('Import Contacts');
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
