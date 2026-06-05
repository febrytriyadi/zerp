<?php
namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CustomerPaymentInvoice extends Pivot
{
    protected $fillable = ['customer_payment_id', 'sales_invoice_id', 'amount'];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2'];
    }
}
