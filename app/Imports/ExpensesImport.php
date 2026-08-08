<?php

namespace App\Imports;

use App\Models\Expense;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\Importable;

class ExpensesImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;

    public function model(array $row)
    {
        return new Expense([
            'vendor_id' => $row['vendor_id'] ?? null,
            'expense_account_id' => $row['expense_account_id'] ?? null,
            'payment_account_id' => $row['payment_account_id'] ?? null,
            'amount' => (float) ($row['amount'] ?? 0),
            'expense_date' => $row['expense_date'] ?? now(),
            'payment_method' => $row['payment_method'] ?? 'bank_transfer',
            'reference_number' => $row['reference_number'] ?? null,
            'description' => $row['description'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'nullable|date',
        ];
    }
}
