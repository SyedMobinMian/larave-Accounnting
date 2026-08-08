<?php

namespace App\Exports;

use App\Models\Account;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AccountsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        return Account::query();
    }

    public function headings(): array
    {
        return [
            __('ID'),
            __('Code'),
            __('Name'),
            __('Type'),
            __('Balance'),
            __('Active'),
            __('Created At'),
        ];
    }

    public function map($account): array
    {
        return [
            $account->id,
            $account->code,
            $account->name,
            ucfirst(str_replace('_', ' ', $account->type)),
            number_format((float) $account->balance, 2),
            $account->active ? __('Yes') : __('No'),
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
