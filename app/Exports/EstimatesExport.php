<?php

namespace App\Exports;

use App\Models\Estimate;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EstimatesExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Estimate::query()->with('client');

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('estimate_number', 'like', "%{$search}%")
                  ->orWhereHas('client', fn($c) => $c->where('company', 'like', "%{$search}%"));
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
            __('Estimate Number'),
            __('Client'),
            __('Estimate Date'),
            __('Expiry Date'),
            __('Subtotal'),
            __('Tax'),
            __('Total'),
            __('Status'),
            __('Created At'),
        ];
    }

    public function map($estimate): array
    {
        return [
            $estimate->id,
            $estimate->estimate_number,
            $estimate->client?->company ?? __('N/A'),
            $estimate->estimate_date?->format('Y-m-d'),
            $estimate->expiry_date?->format('Y-m-d'),
            number_format((float) $estimate->subtotal, 2),
            number_format((float) $estimate->tax_amount, 2),
            number_format((float) $estimate->total_amount, 2),
            ucfirst(str_replace('_', ' ', $estimate->status)),
            $estimate->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
