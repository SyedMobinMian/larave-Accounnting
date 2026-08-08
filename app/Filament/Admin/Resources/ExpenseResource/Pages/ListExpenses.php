<?php

namespace App\Filament\Admin\Resources\ExpenseResource\Pages;

use App\Exports\ExpensesExport;
use App\Filament\Admin\Resources\Concerns\HasExportImportActions;
use App\Filament\Admin\Resources\ExpenseResource;
use App\Imports\ExpensesImport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListExpenses extends ListRecords
{
    use HasExportImportActions;

    protected static string $resource = ExpenseResource::class;

    protected function getExportClass(): ?string
    {
        return ExpensesExport::class;
    }

    protected function getImportClass(): ?string
    {
        return ExpensesImport::class;
    }

    protected function getExportFilename(): string
    {
        return 'expenses-export.xlsx';
    }

    protected function getImportLabel(): string
    {
        return __('Import Expenses');
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
