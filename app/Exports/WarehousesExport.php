<?php

namespace App\Exports;

use App\Models\Warehouse;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class WarehousesExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        return Warehouse::query();
    }

    public function headings(): array
    {
        return [
            __('ID'),
            __('Warehouse Name'),
            __('Warehouse Code'),
            __('Address'),
            __('City'),
            __('State'),
            __('Country'),
            __('Phone'),
            __('Manager Name'),
            __('Active'),
            __('Default'),
            __('Created At'),
        ];
    }

    public function map($warehouse): array
    {
        return [
            $warehouse->id,
            $warehouse->name,
            $warehouse->code,
            $warehouse->address,
            $warehouse->city,
            $warehouse->state,
            $warehouse->country,
            $warehouse->phone,
            $warehouse->manager_name,
            $warehouse->is_active ? __('Yes') : __('No'),
            $warehouse->is_default ? __('Yes') : __('No'),
            $warehouse->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
