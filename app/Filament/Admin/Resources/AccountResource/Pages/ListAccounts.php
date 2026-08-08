<?php

namespace App\Filament\Admin\Resources\AccountResource\Pages;

use App\Exports\AccountsExport;
use App\Filament\Admin\Resources\AccountResource;
use App\Filament\Admin\Resources\Concerns\HasExportImportActions;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAccounts extends ListRecords
{
    use HasExportImportActions;

    protected static string $resource = AccountResource::class;

    protected function getExportClass(): ?string
    {
        return AccountsExport::class;
    }

    protected function getImportClass(): ?string
    {
        return null;
    }

    protected function getExportFilename(): string
    {
        return 'accounts-export.xlsx';
    }

    protected function getImportLabel(): string
    {
        return __('Import Accounts');
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
