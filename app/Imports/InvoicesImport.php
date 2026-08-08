<?php

namespace App\Imports;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\Importable;

class InvoicesImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;

    public function model(array $row)
    {
        return new Invoice([
            'client_id' => $row['client_id'] ?? null,
            'invoice_number' => $row['invoice_number'] ?? ('INV-' . strtoupper(uniqid())),
            'issue_date' => $row['issue_date'] ?? now(),
            'due_date' => $row['due_date'] ?? null,
            'status' => $row['status'] ?? 'unpaid',
            'subtotal' => (float) ($row['subtotal'] ?? 0),
            'tax_amount' => (float) ($row['tax_amount'] ?? 0),
            'total_amount' => (float) ($row['total_amount'] ?? 0),
        ]);
    }

    public function rules(): array
    {
        return [
            'client_id' => 'nullable|integer|exists:clients,id',
            'invoice_number' => 'nullable|string|max:255',
            'issue_date' => 'nullable|date',
            'due_date' => 'nullable|date',
        ];
    }
}
