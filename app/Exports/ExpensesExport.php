<?php

namespace App\Exports;

use App\Models\Expense;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExpensesExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Expense::query()
            ->with(['vendor', 'expenseAccount', 'paymentAccount']);

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%");
            });
        }

        if (!empty($this->filters['payment_method'])) {
            $query->where('payment_method', $this->filters['payment_method']);
        }

        if (!empty($this->filters['date_from'])) {
            $query->whereDate('expense_date', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->whereDate('expense_date', '<=', $this->filters['date_to']);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Expense Date',
            'Vendor',
            'Category',
            'Paid From',
            'Amount',
            'Payment Method',
            'Reference #',
            'Description',
            'Created At',
        ];
    }

    public function map($expense): array
    {
        return [
            $expense->id,
            $expense->expense_date?->format('Y-m-d'),
            $expense->vendor?->name ?? 'N/A',
            $expense->expenseAccount?->name ?? 'N/A',
            $expense->paymentAccount?->name ?? 'N/A',
            number_format($expense->amount, 2),
            ucfirst(str_replace('_', ' ', $expense->payment_method)),
            $expense->reference_number ?? 'N/A',
            $expense->description,
            $expense->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}

