<?php

namespace App\Filament\Admin\Resources\BankTransactionResource\Pages;

use App\Exports\BankTransactionsExport;
use App\Filament\Admin\Resources\BankTransactionResource;
use App\Filament\Admin\Resources\Concerns\HasExportImportActions;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBankTransactions extends ListRecords
{
    use HasExportImportActions;

    protected static string $resource = BankTransactionResource::class;

    protected function getExportClass(): ?string
    {
        return BankTransactionsExport::class;
    }

    protected function getImportClass(): ?string
    {
        return null;
    }

    protected function getExportFilename(): string
    {
        return 'bank-transactions-export.xlsx';
    }

    protected function getImportLabel(): string
    {
        return __('Import Bank Transactions');
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

