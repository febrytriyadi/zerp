<?php
namespace App\Models\Finance;

use App\Models\Master\BankAccount;
use App\Models\Master\ChartOfAccount;
use App\Models\Master\Company;
use App\Models\Master\Branch;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class GiroTransaction extends Model
{
    protected $fillable = [
        'company_id', 'branch_id', 'type', 'transaction_number', 'giro_number',
        'transaction_date', 'due_date', 'bank_account_id', 'amount', 'issuer_name',
        'chart_of_account_id', 'description', 'status', 'cleared_at', 'bounced_at',
        'posted_at', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'transaction_date' => 'date',
            'due_date' => 'date',
            'amount' => 'decimal:2',
            'cleared_at' => 'datetime',
            'bounced_at' => 'datetime',
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

    public function contraAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'chart_of_account_id');
    }

    public function journalEntry(): MorphOne
    {
        return $this->morphOne(JournalEntry::class, 'reference');
    }
}
