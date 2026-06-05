<?php
namespace App\Models\Sales;

use App\Models\Finance\JournalEntry;
use App\Models\Master\BankAccount;
use App\Models\Master\Branch;
use App\Models\Master\CashAccount;
use App\Models\Master\Company;
use App\Models\Master\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class SalesDownPayment extends Model
{
    protected $fillable = [
        'company_id', 'branch_id', 'down_payment_number', 'transaction_date',
        'sales_order_id', 'customer_id', 'payment_method', 'cash_account_id',
        'bank_account_id', 'giro_transaction_id', 'amount', 'description', 'status', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'transaction_date' => 'date',
            'amount' => 'decimal:2',
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

    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function journalEntry(): MorphOne
    {
        return $this->morphOne(JournalEntry::class, 'reference');
    }
}
