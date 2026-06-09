<?php
namespace App\Models\Finance;

use App\Models\Master\Branch;
use App\Models\Master\Company;
use App\Models\Master\FiscalPeriod;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ClosingJournal extends Model
{
    use LogsActivity, SoftDeletes;

    protected $fillable = [
        'company_id', 'branch_id', 'closing_number', 'closing_type',
        'fiscal_period_id', 'description', 'total_debit', 'total_credit',
        'status', 'journal_entry_id', 'items',
        'posted_at', 'posted_by', 'voided_at', 'voided_by', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'total_debit' => 'decimal:2',
            'total_credit' => 'decimal:2',
            'items' => 'array',
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

    public function fiscalPeriod(): BelongsTo
    {
        return $this->belongsTo(FiscalPeriod::class);
    }

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function postedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function voidedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'voided_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll()->logOnlyDirty();
    }
}
