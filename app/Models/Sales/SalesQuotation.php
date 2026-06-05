<?php
namespace App\Models\Sales;

use App\Models\Master\Branch;
use App\Models\Master\Company;
use App\Models\Master\Currency;
use App\Models\Master\Customer;
use App\Models\Master\PaymentTerm;
use App\Models\Master\TaxRate;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesQuotation extends Model
{
    protected $fillable = [
        'company_id', 'branch_id', 'quotation_number', 'quotation_date', 'customer_id',
        'customer_address', 'payment_term_id', 'currency_id', 'exchange_rate', 'tax_rate_id',
        'subtotal', 'tax_amount', 'total', 'description', 'status', 'approved_by',
        'converted_to_so_id', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'quotation_date' => 'date',
            'exchange_rate' => 'decimal:4',
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

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SalesQuotationItem::class);
    }

    public function paymentTerm(): BelongsTo
    {
        return $this->belongsTo(PaymentTerm::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function taxRate(): BelongsTo
    {
        return $this->belongsTo(TaxRate::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
