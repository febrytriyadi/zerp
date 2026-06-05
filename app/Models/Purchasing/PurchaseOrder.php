<?php
namespace App\Models\Purchasing;

use App\Models\Master\Branch;
use App\Models\Master\Company;
use App\Models\Master\Currency;
use App\Models\Master\PaymentTerm;
use App\Models\Master\Supplier;
use App\Models\Master\TaxRate;
use App\Models\Master\Warehouse;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'company_id', 'branch_id', 'order_number', 'order_date', 'supplier_id',
        'purchase_request_id', 'payment_term_id', 'currency_id', 'exchange_rate',
        'tax_rate_id', 'subtotal', 'tax_amount', 'total', 'down_payment_amount',
        'description', 'status', 'approved_by', 'created_by', 'warehouse_id',
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

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function purchaseRequest(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    public function downPayments(): HasMany
    {
        return $this->hasMany(PurchaseDownPayment::class);
    }

    public function receivedGoods(): HasMany
    {
        return $this->hasMany(ReceivedGoods::class);
    }

    public function purchaseInvoices(): HasMany
    {
        return $this->hasMany(PurchaseInvoice::class);
    }

    public function paymentTerm(): BelongsTo
    {
        return $this->belongsTo(PaymentTerm::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }
}
