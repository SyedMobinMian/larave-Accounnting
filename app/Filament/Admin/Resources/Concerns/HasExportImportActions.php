<?php

namespace App\Filament\Admin\Resources\Concerns;

use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Reusable export/import header actions for List pages.
 *
 * Usage:
 *   class ListRecords extends ListRecords
 *   {
 *       use HasExportImportActions;
 *   }
 *
 * Override `getExportClass()`, `getImportClass()`, `getExportFilename()`,
 * `getImportLabel()` to customize per module.
 */
trait HasExportImportActions
{
    protected function getExportClass(): ?string
    {
        return null;
    }

    protected function getImportClass(): ?string
    {
        return null;
    }

    protected function getExportFilename(): string
    {
        return 'export.xlsx';
    }

    protected function getImportLabel(): string
    {
        return __('Import');
    }

    protected function getExportActions(): array
    {
        $exportClass = $this->getExportClass();
        $importClass = $this->getImportClass();

        $actions = [];

        if ($exportClass) {
            $actions[] = Actions\Action::make('export')
                ->label(__('Export All'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () use ($exportClass) {
                    return Excel::download(new $exportClass(), $this->getExportFilename());
                });

            $actions[] = Actions\Action::make('exportCsv')
                ->label(__('Export as CSV'))
                ->icon('heroicon-o-document-arrow-down')
                ->color('gray')
                ->action(function () use ($exportClass) {
                    return Excel::download(new $exportClass(), str_replace('.xlsx', '.csv', $this->getExportFilename()), \Maatwebsite\Excel\Excel::CSV);
                });
        }

        if ($importClass) {
            $actions[] = Actions\Action::make('import')
                ->label($this->getImportLabel())
                ->icon('heroicon-o-arrow-up-tray')
                ->color('warning')
                ->form([
                    Forms\Components\FileUpload::make('file')
                        ->label(__('Upload Excel/CSV File'))
                        ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'text/csv', 'application/vnd.ms-excel'])
                        ->required()
                        ->maxSize(5120),
                ])
                ->action(function (array $data) use ($importClass) {
                    try {
                        $import = new $importClass();
                        Excel::import($import, $data['file']);
                        $failures = $import->failures();

                        Notification::make()
                            ->title(__('Import completed!'))
                            ->body(count($failures) > 0
                                ? count($failures) . ' ' . __('rows failed validation.')
                                : __('All records imported successfully.'))
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title(__('Import failed'))
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                });
        }

        return $actions;
    }
}
