<?php

namespace App\Imports;

use App\Models\Bill;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\Importable;

class BillsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;

    public function model(array $row)
    {
        return new Bill([
            'vendor_id' => $row['vendor_id'] ?? null,
            'bill_number' => $row['bill_number'] ?? ('BILL-' . strtoupper(uniqid())),
            'bill_date' => $row['bill_date'] ?? now(),
            'due_date' => $row['due_date'] ?? null,
            'status' => $row['status'] ?? 'unpaid',
            'subtotal' => (float) ($row['subtotal'] ?? 0),
            'tax_amount' => (float) ($row['tax_amount'] ?? 0),
            'total_amount' => (float) ($row['total_amount'] ?? 0),
            'paid_amount' => (float) ($row['paid_amount'] ?? 0),
        ]);
    }

    public function rules(): array
    {
        return [
            'vendor_id' => 'nullable|integer|exists:vendors,id',
            'bill_number' => 'nullable|string|max:255',
            'bill_date' => 'nullable|date',
            'due_date' => 'nullable|date',
        ];
    }
}
