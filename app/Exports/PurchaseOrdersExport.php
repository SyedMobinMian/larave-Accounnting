<?php

namespace App\Exports;

use App\Models\PurchaseOrder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PurchaseOrdersExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = PurchaseOrder::query()->with('vendor');

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('vendor', fn($v) => $v->where('name', 'like', "%{$search}%"));
            });
        }

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            __('ID'),
            __('Order Number'),
            __('Vendor'),
            __('Order Date'),
            __('Expected Delivery Date'),
            __('Subtotal'),
            __('Tax'),
            __('Total'),
            __('Status'),
            __('Created At'),
        ];
    }

    public function map($po): array
    {
        return [
            $po->id,
            $po->order_number,
            $po->vendor?->name ?? __('N/A'),
            $po->order_date?->format('Y-m-d'),
            $po->expected_delivery_date?->format('Y-m-d'),
            number_format((float) $po->subtotal, 2),
            number_format((float) $po->tax_amount, 2),
            number_format((float) $po->total_amount, 2),
            ucfirst(str_replace('_', ' ', $po->status)),
            $po->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
