<?php

namespace App\Exports\Master;

use App\Models\Master\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CustomerExport implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize
{
    public function collection()
    {
        return Customer::select('code', 'name', 'contact_person', 'phone', 'email', 'address', 'tax_id', 'is_active')
            ->get();
    }

    public function headings(): array
    {
        return ['Code', 'Name', 'Contact Person', 'Phone', 'Email', 'Address', 'Tax ID', 'Active'];
    }

    public function title(): string
    {
        return 'Customers';
    }
}
