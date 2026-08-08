<?php

namespace App\Imports;

use App\Models\Estimate;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\Importable;

class EstimatesImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;

    public function model(array $row)
    {
        return new Estimate([
            'client_id' => $row['client_id'] ?? null,
            'estimate_number' => $row['estimate_number'] ?? ('EST-' . strtoupper(uniqid())),
            'estimate_date' => $row['estimate_date'] ?? now(),
            'expiry_date' => $row['expiry_date'] ?? null,
            'status' => $row['status'] ?? 'draft',
            'subtotal' => (float) ($row['subtotal'] ?? 0),
            'tax_amount' => (float) ($row['tax_amount'] ?? 0),
            'total_amount' => (float) ($row['total_amount'] ?? 0),
        ]);
    }

    public function rules(): array
    {
        return [
            'client_id' => 'nullable|integer|exists:clients,id',
            'estimate_number' => 'nullable|string|max:255',
            'estimate_date' => 'nullable|date',
        ];
    }
}
