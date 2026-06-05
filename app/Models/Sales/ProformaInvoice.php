<?php
namespace App\Models\Sales;

use App\Models\Master\Branch;
use App\Models\Master\Company;
use App\Models\Master\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProformaInvoice extends Model
{
    protected $fillable = [
        'company_id', 'branch_id', 'proforma_number', 'proforma_date',
        'sales_order_id', 'customer_id', 'subtotal', 'tax_amount', 'total',
        'status', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'proforma_date' => 'date',
            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
