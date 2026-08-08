<?php

namespace App\Imports;

use App\Models\Warehouse;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\Importable;

class WarehousesImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;

    public function model(array $row)
    {
        return new Warehouse([
            'name' => $row['name'] ?? null,
            'code' => $row['code'] ?? null,
            'address' => $row['address'] ?? null,
            'city' => $row['city'] ?? null,
            'state' => $row['state'] ?? null,
            'country' => $row['country'] ?? null,
            'phone' => $row['phone'] ?? null,
            'manager_name' => $row['manager_name'] ?? null,
            'is_active' => in_array(strtolower($row['active'] ?? 'yes'), ['yes', '1', 'true', 'active']),
            'is_default' => in_array(strtolower($row['default'] ?? 'no'), ['yes', '1', 'true', 'active']),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
        ];
    }
}
