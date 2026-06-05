<?php

namespace App\Exports\Master;

use App\Models\Master\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ProductExport implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize
{
    public function collection()
    {
        return Product::select('code', 'name', 'purchase_price', 'selling_price', 'cost_method', 'average_cost', 'is_active')
            ->get();
    }

    public function headings(): array
    {
        return ['Code', 'Name', 'Purchase Price', 'Selling Price', 'Cost Method', 'Average Cost', 'Active'];
    }

    public function title(): string
    {
        return 'Products';
    }
}
