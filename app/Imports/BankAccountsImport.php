<?php

namespace App\Imports;

use App\Models\BankAccount;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\Importable;

class BankAccountsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;

    public function model(array $row)
    {
        return new BankAccount([
            'name' => $row['name'] ?? null,
            'bank_name' => $row['bank_name'] ?? null,
            'account_number' => $row['account_number'] ?? null,
            'currency' => $row['currency'] ?? 'INR',
            'opening_balance' => (float) ($row['opening_balance'] ?? 0),
            'is_active' => in_array(strtolower($row['active'] ?? 'yes'), ['yes', '1', 'true', 'active']),
            'is_default' => in_array(strtolower($row['default'] ?? 'no'), ['yes', '1', 'true', 'active']),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'opening_balance' => 'nullable|numeric',
        ];
    }
}
