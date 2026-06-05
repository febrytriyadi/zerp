<?php
namespace App\Models\Sales;

use App\Models\Finance\JournalEntry;
use App\Models\Master\Branch;
use App\Models\Master\Company;
use App\Models\Master\Currency;
use App\Models\Master\Customer;
use App\Models\Master\PaymentTerm;
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

class SalesInvoice extends Model
{
    use LogsActivity, SoftDeletes;
    protected $fillable = [
        'company_id', 'branch_id', 'invoice_number', 'invoice_date', 'due_date',
        'customer_id', 'sales_order_id', 'delivery_order_id', 'payment_term_id',
        'currency_id', 'exchange_rate', 'tax_rate_id', 'subtotal',
        'down_payment_deduction', 'tax_amount', 'total', 'outstanding_amount',
        'description', 'status', 'posted_at', 'posted_by', 'created_by',
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

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SalesInvoiceItem::class);
    }

    public function journalEntry(): MorphOne
    {
        return $this->morphOne(JournalEntry::class, 'reference');
    }

    public function customerPayments(): BelongsToMany
    {
        return $this->belongsToMany(CustomerPayment::class, 'customer_payment_invoices')
            ->withPivot('amount');
    }

    public function returns(): HasMany
    {
        return $this->hasMany(SalesReturn::class);
    }

    public function isEditable(): bool
    {
        return $this->status === 'draft';
    }

    public function isPosted(): bool
    {
        return $this->status === 'posted';
    }

    public function isVoidable(): bool
    {
        return $this->status === 'posted' && $this->outstanding_amount == 0;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty();
    }
}
