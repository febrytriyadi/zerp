<?php
namespace App\Models\Finance;

use App\Models\Master\Branch;
use App\Models\Master\BankAccount;
use App\Models\Master\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PaymentRun extends Model
{
    use LogsActivity, SoftDeletes;

    protected $fillable = [
        'company_id', 'branch_id', 'run_number', 'run_date', 'payment_method',
        'bank_account_id', 'total_suppliers', 'total_invoices', 'total_amount',
        'status', 'notes', 'created_by', 'posted_by', 'posted_at',
    ];

    protected function casts(): array
    {
        return [
            'run_date' => 'date',
            'total_amount' => 'decimal:2',
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

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PaymentRunItem::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function postedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty();
    }
}
