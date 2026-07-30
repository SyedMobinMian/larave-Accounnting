<?php

namespace App\Imports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\Importable;

class ClientsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;

    public function model(array $row)
    {
        return new Client([
            'company' => $row['company'] ?? null,
            'vat' => $row['vat_number'] ?? $row['vat'] ?? null,
            'phonenumber' => $row['phone'] ?? $row['phonenumber'] ?? null,
            'email' => $row['email'] ?? null,
            'website' => $row['website'] ?? null,
            'address' => $row['address'] ?? null,
            'city' => $row['city'] ?? null,
            'state' => $row['state'] ?? null,
            'zip' => $row['zip'] ?? $row['zip'] ?? null,
            'country' => $row['country'] ?? null,
            'active' => in_array(strtolower($row['active'] ?? 'yes'), ['yes', '1', 'true', 'active']),
            'billing_street' => $row['billing_street'] ?? null,
            'billing_city' => $row['billing_city'] ?? null,
            'billing_state' => $row['billing_state'] ?? null,
            'billing_zip' => $row['billing_zip'] ?? null,
            'billing_country' => $row['billing_country'] ?? null,
            'shipping_street' => $row['shipping_street'] ?? null,
            'shipping_city' => $row['shipping_city'] ?? null,
            'shipping_state' => $row['shipping_state'] ?? null,
            'shipping_zip' => $row['shipping_zip'] ?? null,
            'shipping_country' => $row['shipping_country'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'company' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
        ];
    }
}

