<?php
namespace App\Models\Finance;

use App\Models\Master\Supplier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentRunItem extends Model
{
    protected $fillable = [
        'payment_run_id', 'supplier_id', 'invoice_type', 'invoice_id',
        'invoice_number', 'due_date', 'original_amount', 'outstanding_amount',
        'discount_percent', 'discount_amount', 'payment_amount',
        'withholding_tax_amount', 'net_payment', 'status',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'original_amount' => 'decimal:2',
            'outstanding_amount' => 'decimal:2',
            'discount_percent' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'payment_amount' => 'decimal:2',
            'withholding_tax_amount' => 'decimal:2',
            'net_payment' => 'decimal:2',
        ];
    }

    public function paymentRun(): BelongsTo
    {
        return $this->belongsTo(PaymentRun::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
