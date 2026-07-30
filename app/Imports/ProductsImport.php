<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\Importable;

class ProductsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;

    public function model(array $row)
    {
        return new Product([
            'name' => $row['product_name'] ?? $row['name'] ?? null,
            'sku' => $row['sku'] ?? $row['sku_item_code'] ?? ('SKU-' . strtoupper(uniqid())),
            'description' => $row['description'] ?? null,
            'cost_price' => (float) ($row['cost_price'] ?? 0),
            'selling_price' => (float) ($row['selling_price'] ?? 0),
            'stock_quantity' => (int) ($row['stock_quantity'] ?? 0),
            'min_stock_alert' => (int) ($row['min_stock_alert'] ?? 5),
            'unit' => $row['unit'] ?? $row['measurement_unit'] ?? 'pcs',
            'is_active' => in_array(strtolower($row['active'] ?? 'yes'), ['yes', '1', 'true', 'active']),
        ]);
    }

    public function rules(): array
    {
        return [
            'product_name' => 'required_without:name|string|max:255',
            'name' => 'required_without:product_name|string|max:255',
            'sku' => 'nullable|string|max:255',
            'cost_price' => 'nullable|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
        ];
    }
}

