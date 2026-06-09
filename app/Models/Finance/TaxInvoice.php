<?php
namespace App\Models\Finance;

use App\Models\Master\Company;
use App\Models\Master\Branch;
use App\Models\Master\Customer;
use App\Models\Master\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class TaxInvoice extends Model
{
    use LogsActivity, SoftDeletes;

    protected $fillable = [
        'company_id', 'branch_id', 'tax_invoice_number', 'tax_invoice_date',
        'transaction_type', 'reference_id', 'reference_type',
        'customer_id', 'supplier_id',
        'taxpayer_name', 'taxpayer_npwp', 'taxpayer_address',
        'dpp', 'ppn_amount', 'ppnbm_amount', 'status', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tax_invoice_date' => 'date',
            'dpp' => 'decimal:2',
            'ppn_amount' => 'decimal:2',
            'ppnbm_amount' => 'decimal:2',
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

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reference(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty();
    }
}
