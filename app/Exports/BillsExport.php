<?php

namespace App\Exports;

use App\Models\Bill;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BillsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Bill::query()->with('vendor');

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('bill_number', 'like', "%{$search}%")
                  ->orWhereHas('vendor', fn($v) => $v->where('company_name', 'like', "%{$search}%"));
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
            __('Bill Number'),
            __('Vendor'),
            __('Bill Date'),
            __('Due Date'),
            __('Subtotal'),
            __('Tax Amount'),
            __('Total Amount'),
            __('Paid Amount'),
            __('Status'),
            __('Created At'),
        ];
    }

    public function map($bill): array
    {
        return [
            $bill->id,
            $bill->bill_number,
            $bill->vendor?->company_name ?? __('N/A'),
            $bill->bill_date?->format('Y-m-d'),
            $bill->due_date?->format('Y-m-d'),
            number_format((float) $bill->subtotal, 2),
            number_format((float) $bill->tax_amount, 2),
            number_format((float) $bill->total_amount, 2),
            number_format((float) $bill->paid_amount, 2),
            ucfirst(str_replace('_', ' ', $bill->status)),
            $bill->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
