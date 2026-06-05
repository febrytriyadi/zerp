<?php
namespace App\Models\Purchasing;

use App\Models\Finance\JournalEntry;
use App\Models\Master\Branch;
use App\Models\Master\Company;
use App\Models\Master\Currency;
use App\Models\Master\PaymentTerm;
use App\Models\Master\Supplier;
use App\Models\Master\TaxRate;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PurchaseInvoice extends Model
{
    use LogsActivity, SoftDeletes;
    protected $fillable = [
        'company_id', 'branch_id', 'invoice_number', 'invoice_date', 'due_date',
        'supplier_id', 'purchase_order_id', 'received_goods_id', 'payment_term_id',
        'currency_id', 'exchange_rate', 'tax_rate_id', 'subtotal',
        'down_payment_deduction', 'tax_amount', 'total', 'outstanding_amount',
        'description', 'status', 'approved_by', 'posted_at', 'posted_by', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'invoice_date' => 'date',
            'due_date' => 'date',
            'exchange_rate' => 'decimal:4',
            'subtotal' => 'decimal:2',
            'down_payment_deduction' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total' => 'decimal:2',
            'outstanding_amount' => 'decimal:2',
            'posted_at' => 'datetime',
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

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseInvoiceItem::class);
    }

    public function journalEntry(): MorphOne
    {
        return $this->morphOne(JournalEntry::class, 'reference');
    }

    public function supplierPayments(): BelongsToMany
    {
        return $this->belongsToMany(SupplierPayment::class, 'supplier_payment_invoices')
            ->withPivot('amount');
    }

    public function returns(): HasMany
    {
        return $this->hasMany(PurchaseReturn::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty();
    }
}
