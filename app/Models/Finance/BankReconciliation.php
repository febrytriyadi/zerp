<?php
namespace App\Models\Finance;

use App\Models\Master\BankAccount;
use App\Models\Master\Company;
use App\Models\Master\Branch;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BankReconciliation extends Model
{
    protected $fillable = [
        'company_id', 'branch_id', 'bank_account_id', 'reconciliation_date',
        'statement_balance', 'book_balance', 'difference', 'status', 'notes',
        'created_by', 'posted_by',
    ];

    protected function casts(): array
    {
        return [
            'reconciliation_date' => 'date',
            'statement_balance' => 'decimal:2',
            'book_balance' => 'decimal:2',
            'difference' => 'decimal:2',
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

    public function lines(): HasMany
    {
        return $this->hasMany(BankReconciliationLine::class);
    }
}
