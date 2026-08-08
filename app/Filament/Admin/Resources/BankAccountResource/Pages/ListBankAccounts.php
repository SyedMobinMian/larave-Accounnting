<?php

namespace App\Filament\Admin\Resources\BankAccountResource\Pages;

use App\Exports\BankAccountsExport;
use App\Filament\Admin\Resources\BankAccountResource;
use App\Filament\Admin\Resources\Concerns\HasExportImportActions;
use App\Imports\BankAccountsImport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBankAccounts extends ListRecords
{
    use HasExportImportActions;

    protected static string $resource = BankAccountResource::class;

    protected function getExportClass(): ?string
    {
        return BankAccountsExport::class;
    }

    protected function getImportClass(): ?string
    {
        return BankAccountsImport::class;
    }

    protected function getExportFilename(): string
    {
        return 'bank-accounts-export.xlsx';
    }

    protected function getImportLabel(): string
    {
        return __('Import Bank Accounts');
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

