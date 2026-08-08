<?php

namespace App\Exports;

use App\Models\BankAccount;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BankAccountsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        return BankAccount::query();
    }

    public function headings(): array
    {
        return [
            __('ID'),
            __('Account Name'),
            __('Bank Name'),
            __('Account Number'),
            __('Currency'),
            __('Opening Balance'),
            __('Active'),
            __('Default'),
            __('Created At'),
        ];
    }

    public function map($account): array
    {
        return [
            $account->id,
            $account->name,
            $account->bank_name,
            $account->account_number,
            $account->currency,
            number_format((float) $account->opening_balance, 2),
            $account->is_active ? __('Yes') : __('No'),
            $account->is_default ? __('Yes') : __('No'),
            $account->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
