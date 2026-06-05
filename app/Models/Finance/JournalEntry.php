<?php
namespace App\Models\Finance;

use App\Models\Master\Company;
use App\Models\Master\Branch;
use App\Models\Master\FiscalPeriod;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class JournalEntry extends Model
{
    use LogsActivity;
    protected $fillable = [
        'company_id', 'branch_id', 'journal_number', 'transaction_date', 'description',
        'reference_type', 'reference_id', 'fiscal_period_id', 'total_debit', 'total_credit',
        'currency_id', 'exchange_rate', 'status', 'is_voided', 'posted_at', 'posted_by',
        'voided_at', 'voided_by', 'reversal_id', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'transaction_date' => 'date',
            'total_debit' => 'decimal:2',
            'total_credit' => 'decimal:2',
            'exchange_rate' => 'decimal:4',
            'is_voided' => 'boolean',
            'posted_at' => 'datetime',
            'voided_at' => 'datetime',
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

    public function lines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    public function fiscalPeriod(): BelongsTo
    {
        return $this->belongsTo(FiscalPeriod::class);
    }

    public function postedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reversal(): BelongsTo
    {
        return $this->belongsTo(self::class, 'reversal_id');
    }

    public function reference(): MorphTo
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
