<?php

namespace App\Exports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ClientsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Client::query();

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('company', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phonenumber', 'like', "%{$search}%");
            });
        }

        if (isset($this->filters['active'])) {
            $query->where('active', $this->filters['active']);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Company',
            'VAT Number',
            'Phone',
            'Email',
            'Website',
            'Address',
            'City',
            'State',
            'ZIP',
            'Country',
            'Active',
            'Billing Street',
            'Billing City',
            'Billing State',
            'Billing ZIP',
            'Billing Country',
            'Shipping Street',
            'Shipping City',
            'Shipping State',
            'Shipping ZIP',
            'Shipping Country',
            'Created At',
        ];
    }

    public function map($client): array
    {
        return [
            $client->id,
            $client->company,
            $client->vat,
            $client->phonenumber,
            $client->email,
            $client->website,
            $client->address,
            $client->city,
            $client->state,
            $client->zip,
            $client->country,
            $client->active ? 'Yes' : 'No',
            $client->billing_street,
            $client->billing_city,
            $client->billing_state,
            $client->billing_zip,
            $client->billing_country,
            $client->shipping_street,
            $client->shipping_city,
            $client->shipping_state,
            $client->shipping_zip,
            $client->shipping_country,
            $client->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}

