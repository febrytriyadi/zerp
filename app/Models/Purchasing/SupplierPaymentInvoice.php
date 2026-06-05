<?php
namespace App\Models\Purchasing;

use Illuminate\Database\Eloquent\Relations\Pivot;

class SupplierPaymentInvoice extends Pivot
{
    protected $fillable = ['supplier_payment_id', 'purchase_invoice_id', 'amount'];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2'];
    }
}
