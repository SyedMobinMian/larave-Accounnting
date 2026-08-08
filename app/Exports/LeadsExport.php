<?php

namespace App\Exports;

use App\Models\Lead;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LeadsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Lead::query()->with('assignedTo');

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('contact_name', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            __('ID'),
            __('Contact Name'),
            __('Company Name'),
            __('Email'),
            __('Phone'),
            __('Website'),
            __('Estimated Value'),
            __('Status'),
            __('Source'),
            __('Assigned To'),
            __('Created At'),
        ];
    }

    public function map($lead): array
    {
        return [
            $lead->id,
            $lead->contact_name,
            $lead->company_name,
            $lead->email,
            $lead->phone,
            $lead->website,
            number_format((float) $lead->estimated_value, 2),
            ucfirst(str_replace('_', ' ', $lead->status)),
            ucfirst(str_replace('_', ' ', $lead->source)),
            $lead->assignedTo?->name ?? __('N/A'),
            $lead->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
