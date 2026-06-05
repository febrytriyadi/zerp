<?php
namespace App\Models\Finance;

use App\Models\Master\Company;
use App\Models\Master\Branch;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecurringTransaction extends Model
{
    protected $fillable = [
        'company_id', 'branch_id', 'name', 'description', 'frequency',
        'interval_count', 'next_run_date', 'last_run_date', 'total_runs',
        'max_runs', 'is_active', 'journal_template', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'next_run_date' => 'date',
            'last_run_date' => 'date',
            'is_active' => 'boolean',
            'journal_template' => 'array',
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
}
