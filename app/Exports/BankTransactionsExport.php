<?php

namespace App\Exports;

use App\Models\BankTransaction;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BankTransactionsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = BankTransaction::query()->with('bankAccount');

        if (!empty($this->filters['bank_account_id'])) {
            $query->where('bank_account_id', $this->filters['bank_account_id']);
        }

        if (!empty($this->filters['date_from'])) {
            $query->whereDate('transaction_date', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->whereDate('transaction_date', '<=', $this->filters['date_to']);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            __('ID'),
            __('Bank Account'),
            __('Transaction Date'),
            __('Description'),
            __('Debit'),
            __('Credit'),
            __('Balance'),
            __('Created At'),
        ];
    }

    public function map($transaction): array
    {
        return [
            $transaction->id,
            $transaction->bankAccount?->name ?? __('N/A'),
            $transaction->transaction_date?->format('Y-m-d'),
            $transaction->description,
            number_format((float) $transaction->debit, 2),
            number_format((float) $transaction->credit, 2),
            number_format((float) $transaction->balance, 2),
            $transaction->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
