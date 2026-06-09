<?php
namespace App\Models\Finance;

use App\Models\Master\Branch;
use App\Models\Master\ChartOfAccount;
use App\Models\Master\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class DunningLevel extends Model
{
    use LogsActivity, SoftDeletes;

    protected $fillable = [
        'company_id', 'branch_id', 'code', 'name', 'days_from', 'days_to',
        'charge_percent', 'charge_amount', 'charge_account_id', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'charge_percent' => 'decimal:2',
            'charge_amount' => 'decimal:2',
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

    public function chargeAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'charge_account_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty();
    }
}
