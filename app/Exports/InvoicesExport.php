<?php

namespace App\Exports;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InvoicesExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Invoice::query()
            ->with(['client', 'items'])
            ->select('invoices.*');

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('client', fn($c) => $c->where('company', 'like', "%{$search}%"));
            });
        }

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['date_from'])) {
            $query->whereDate('issue_date', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->whereDate('issue_date', '<=', $this->filters['date_to']);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Invoice #',
            'Client',
            'Issue Date',
            'Due Date',
            'Subtotal',
            'Tax Amount',
            'Total Amount',
            'Status',
            'Created At',
        ];
    }

    public function map($invoice): array
    {
        return [
            $invoice->id,
            $invoice->invoice_number,
            $invoice->client?->company ?? 'N/A',
            $invoice->issue_date?->format('Y-m-d'),
            $invoice->due_date?->format('Y-m-d'),
            number_format($invoice->subtotal, 2),
            number_format($invoice->tax_amount, 2),
            number_format($invoice->total_amount, 2),
            ucfirst(str_replace('_', ' ', $invoice->status)),
            $invoice->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}

