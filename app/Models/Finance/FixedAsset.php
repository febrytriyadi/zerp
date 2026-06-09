<?php
namespace App\Models\Finance;

use App\Models\Master\Company;
use App\Models\Master\Branch;
use App\Models\Master\ChartOfAccount;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class FixedAsset extends Model
{
    use LogsActivity, SoftDeletes;

    protected $fillable = [
        'company_id', 'branch_id', 'asset_number', 'asset_name', 'asset_category',
        'purchase_date', 'purchase_cost', 'salvage_value', 'useful_life_years',
        'depreciation_method', 'accumulated_depreciation', 'book_value',
        'location', 'description', 'chart_of_account_id',
        'accumulated_depr_account_id', 'depreciation_expense_account_id',
        'status', 'last_depreciation_date', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
            'purchase_cost' => 'decimal:2',
            'salvage_value' => 'decimal:2',
            'accumulated_depreciation' => 'decimal:2',
            'book_value' => 'decimal:2',
            'last_depreciation_date' => 'date',
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

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function chartOfAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'chart_of_account_id');
    }

    public function accumulatedDeprAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'accumulated_depr_account_id');
    }

    public function depreciationExpenseAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'depreciation_expense_account_id');
    }

    public function depreciations(): HasMany
    {
        return $this->hasMany(AssetDepreciation::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(AssetTransaction::class);
    }

    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function isActive(): bool
    {
        return in_array($this->status, ['active', 'depreciating']);
    }

    public function isFullyDepreciated(): bool
    {
        return $this->status === 'fully_depreciated' || $this->book_value <= $this->salvage_value;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty();
    }
}
