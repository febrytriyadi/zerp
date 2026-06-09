<?php
namespace App\Models\Finance;

use App\Models\Master\Customer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DunningRunItem extends Model
{
    protected $fillable = [
        'dunning_run_id', 'customer_id', 'invoice_type', 'invoice_id',
        'invoice_number', 'due_date', 'days_overdue', 'original_amount',
        'outstanding_amount', 'dunning_charge', 'total_due', 'status',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'original_amount' => 'decimal:2',
            'outstanding_amount' => 'decimal:2',
            'dunning_charge' => 'decimal:2',
            'total_due' => 'decimal:2',
        ];
    }

    public function dunningRun(): BelongsTo
    {
        return $this->belongsTo(DunningRun::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
