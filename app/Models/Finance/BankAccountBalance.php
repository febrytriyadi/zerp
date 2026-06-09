<?php
namespace App\Models\Finance;

use App\Models\Master\BankAccount;
use App\Models\Master\Currency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankAccountBalance extends Model
{
    protected $fillable = [
        'bank_account_id', 'balance_date', 'opening_balance', 'total_debit',
        'total_credit', 'ending_balance', 'currency_id', 'exchange_rate',
    ];

    protected function casts(): array
    {
        return [
            'balance_date' => 'date',
            'opening_balance' => 'decimal:2',
            'total_debit' => 'decimal:2',
            'total_credit' => 'decimal:2',
            'ending_balance' => 'decimal:2',
            'exchange_rate' => 'decimal:4',
        ];
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
}
