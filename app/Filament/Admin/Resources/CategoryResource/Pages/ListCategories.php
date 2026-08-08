<?php

namespace App\Filament\Admin\Resources\CategoryResource\Pages;

use App\Exports\CategoriesExport;
use App\Filament\Admin\Resources\CategoryResource;
use App\Filament\Admin\Resources\Concerns\HasExportImportActions;
use App\Imports\CategoriesImport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCategories extends ListRecords
{
    use HasExportImportActions;

    protected static string $resource = CategoryResource::class;

    protected function getExportClass(): ?string
    {
        return CategoriesExport::class;
    }

    protected function getImportClass(): ?string
    {
        return CategoriesImport::class;
    }

    protected function getExportFilename(): string
    {
        return 'categories-export.xlsx';
    }

    protected function getImportLabel(): string
    {
        return __('Import Categories');
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

