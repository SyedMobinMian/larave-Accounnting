<?php

namespace App\Imports;

use App\Models\Lead;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\Importable;

class LeadsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;

    public function model(array $row)
    {
        return new Lead([
            'contact_name' => $row['contact_name'] ?? null,
            'company_name' => $row['company_name'] ?? null,
            'email' => $row['email'] ?? null,
            'phone' => $row['phone'] ?? null,
            'website' => $row['website'] ?? null,
            'estimated_value' => (float) ($row['estimated_value'] ?? 0),
            'status' => $row['status'] ?? 'new',
            'source' => $row['source'] ?? 'other',
            'assigned_to' => $row['assigned_to'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'contact_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'estimated_value' => 'nullable|numeric|min:0',
        ];
    }
}
