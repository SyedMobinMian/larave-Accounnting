<?php

namespace App\Exports;

use App\Models\Contact;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ContactsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        return Contact::query()->with('client');
    }

    public function headings(): array
    {
        return [
            __('ID'),
            __('Client'),
            __('First Name'),
            __('Last Name'),
            __('Email'),
            __('Phone'),
            __('Title'),
            __('Primary'),
            __('Active'),
            __('Created At'),
        ];
    }

    public function map($contact): array
    {
        return [
            $contact->id,
            $contact->client?->company ?? __('N/A'),
            $contact->firstname,
            $contact->lastname,
            $contact->email,
            $contact->phonenumber,
            $contact->title,
            $contact->is_primary ? __('Yes') : __('No'),
            $contact->active ? __('Yes') : __('No'),
            $contact->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
