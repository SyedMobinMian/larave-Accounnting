<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Product::query()->with('unit');

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if (isset($this->filters['is_active'])) {
            $query->where('is_active', $this->filters['is_active']);
        }

        if (!empty($this->filters['low_stock'])) {
            $query->whereColumn('stock_quantity', '<=', 'min_stock_alert');
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            __('ID'),
            __('SKU'),
            __('Product Name'),
            __('Description'),
            __('Cost Price'),
            __('Selling Price'),
            __('Stock Quantity'),
            __('Min Stock Alert'),
            __('Unit'),
            __('Active'),
            __('Created At'),
        ];
    }

    public function map($product): array
    {
        return [
            $product->id,
            $product->sku,
            $product->name,
            $product->description,
            number_format($product->cost_price, 2),
            number_format($product->selling_price, 2),
            $product->stock_quantity,
            $product->min_stock_alert,
            $product->unit?->short_name ?? 'pcs',
            $product->is_active ? __('Yes') : __('No'),
            $product->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
