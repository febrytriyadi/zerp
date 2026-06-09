<?php
namespace App\Models\Finance;

use App\Models\Master\Branch;
use App\Models\Master\ChartOfAccount;
use App\Models\Master\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Accrual extends Model
{
    use LogsActivity, SoftDeletes;

    protected $fillable = [
        'company_id', 'branch_id', 'accrual_number', 'accrual_type', 'category',
        'description', 'total_amount', 'recognized_amount', 'remaining_amount',
        'start_date', 'end_date', 'total_periods', 'recognized_periods',
        'amount_per_period', 'debit_account_id', 'credit_account_id',
        'status', 'notes', 'created_by', 'voided_by', 'voided_at',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'recognized_amount' => 'decimal:2',
            'remaining_amount' => 'decimal:2',
            'amount_per_period' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
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

    public function debitAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'debit_account_id');
    }

    public function creditAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'credit_account_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function voidedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'voided_by');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll()->logOnlyDirty();
    }
}
