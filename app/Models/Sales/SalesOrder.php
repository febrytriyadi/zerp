<?php
namespace App\Models\Sales;

use App\Models\Master\Branch;
use App\Models\Master\Company;
use App\Models\Master\Currency;
use App\Models\Master\Customer;
use App\Models\Master\PaymentTerm;
use App\Models\Master\TaxRate;
use App\Models\Master\Warehouse;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesOrder extends Model
{
    protected $fillable = [
        'company_id', 'branch_id', 'order_number', 'order_date', 'customer_id',
        'customer_address', 'payment_term_id', 'currency_id', 'exchange_rate', 'tax_rate_id',
        'subtotal', 'tax_amount', 'total', 'down_payment_amount', 'down_payment_balance',
        'outstanding_amount', 'description', 'status', 'sales_quotation_id', 'warehouse_id',
        'approved_by', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'order_date' => 'date',
            'exchange_rate' => 'decimal:4',
            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total' => 'decimal:2',
            'down_payment_amount' => 'decimal:2',
            'down_payment_balance' => 'decimal:2',
            'outstanding_amount' => 'decimal:2',
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
        return $this->hasMany(SalesOrderItem::class);
    }

    public function salesQuotation(): BelongsTo
    {
        return $this->belongsTo(SalesQuotation::class);
    }

    public function downPayments(): HasMany
    {
        return $this->hasMany(SalesDownPayment::class);
    }

    public function deliveryOrders(): HasMany
    {
        return $this->hasMany(DeliveryOrder::class);
    }

    public function salesInvoices(): HasMany
    {
        return $this->hasMany(SalesInvoice::class);
    }

    public function packingLists(): HasMany
    {
        return $this->hasMany(PackingList::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }
}
