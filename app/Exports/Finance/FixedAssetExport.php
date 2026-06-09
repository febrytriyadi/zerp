<?php

namespace App\Exports\Finance;

use App\Models\Finance\FixedAsset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class FixedAssetExport implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize
{
    public function collection()
    {
        return FixedAsset::select(
            'asset_number', 'asset_name', 'asset_category', 'purchase_date',
            'purchase_cost', 'salvage_value', 'useful_life_years', 'depreciation_method',
            'accumulated_depreciation', 'book_value', 'location', 'status'
        )->get();
    }

    public function headings(): array
    {
        return [
            'Asset Number', 'Asset Name', 'Category', 'Purchase Date',
            'Purchase Cost', 'Salvage Value', 'Useful Life (Years)', 'Depreciation Method',
            'Accumulated Depreciation', 'Book Value', 'Location', 'Status',
        ];
    }

    public function title(): string
    {
        return 'Fixed Assets';
    }
}
