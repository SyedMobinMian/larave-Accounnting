<?php

namespace App\Filament\Admin\Resources\JournalEntryResource\Pages;

use App\Exports\JournalEntriesExport;
use App\Filament\Admin\Resources\Concerns\HasExportImportActions;
use App\Filament\Admin\Resources\JournalEntryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJournalEntries extends ListRecords
{
    use HasExportImportActions;

    protected static string $resource = JournalEntryResource::class;

    protected function getExportClass(): ?string
    {
        return JournalEntriesExport::class;
    }

    protected function getImportClass(): ?string
    {
        return null;
    }

    protected function getExportFilename(): string
    {
        return 'journal-entries-export.xlsx';
    }

    protected function getImportLabel(): string
    {
        return __('Import Journal Entries');
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
