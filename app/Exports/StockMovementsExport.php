<?php

namespace App\Exports;

use App\Models\StockMovement;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StockMovementsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = StockMovement::query()->with(['product', 'warehouse', 'createdBy']);

        if (!empty($this->filters['type'])) {
            $query->where('type', $this->filters['type']);
        }

        if (!empty($this->filters['warehouse_id'])) {
            $query->where('warehouse_id', $this->filters['warehouse_id']);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            __('ID'),
            __('Product'),
            __('Warehouse'),
            __('Type'),
            __('Quantity'),
            __('Unit Cost'),
            __('Balance Before'),
            __('Balance After'),
            __('Date'),
            __('By'),
        ];
    }

    public function map($movement): array
    {
        return [
            $movement->id,
            $movement->product?->name ?? __('N/A'),
            $movement->warehouse?->name ?? __('N/A'),
            ucfirst(str_replace('_', ' ', $movement->type)),
            number_format((float) $movement->quantity, 2),
            number_format((float) $movement->unit_cost, 2),
            number_format((float) $movement->balance_before, 2),
            number_format((float) $movement->balance_after, 2),
            $movement->created_at?->format('Y-m-d H:i:s'),
            $movement->createdBy?->name ?? __('N/A'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
