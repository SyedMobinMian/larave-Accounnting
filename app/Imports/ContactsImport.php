<?php

namespace App\Imports;

use App\Models\Contact;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\Importable;

class ContactsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;

    public function model(array $row)
    {
        return new Contact([
            'client_id' => $row['client_id'] ?? null,
            'is_primary' => in_array(strtolower($row['is_primary'] ?? 'no'), ['yes', '1', 'true', 'active']),
            'firstname' => $row['firstname'] ?? $row['first_name'] ?? null,
            'lastname' => $row['lastname'] ?? $row['last_name'] ?? null,
            'email' => $row['email'] ?? null,
            'phonenumber' => $row['phonenumber'] ?? $row['phone'] ?? null,
            'title' => $row['title'] ?? null,
            'active' => in_array(strtolower($row['active'] ?? 'yes'), ['yes', '1', 'true', 'active']),
        ]);
    }

    public function rules(): array
    {
        return [
            'firstname' => 'required_without:first_name|string|max:255',
            'first_name' => 'required_without:firstname|string|max:255',
            'email' => 'nullable|email|max:255',
            'phonenumber' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
        ];
    }
}
