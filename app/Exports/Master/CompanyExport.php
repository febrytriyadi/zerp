<?php

namespace App\Exports\Master;

use App\Models\Master\Company;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CompanyExport implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize
{
    public function collection()
    {
        return Company::select('code', 'name', 'phone', 'email', 'tax_id', 'address', 'is_active')->get();
    }

    public function headings(): array
    {
        return ['Code', 'Name', 'Phone', 'Email', 'Tax ID', 'Address', 'Active'];
    }

    public function title(): string
    {
        return 'Companies';
    }
}
