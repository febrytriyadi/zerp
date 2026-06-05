<?php
namespace App\Models\Sales;

use App\Models\Master\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesOrderItem extends Model
{
    protected $fillable = ['sales_order_id', 'product_id', 'quantity', 'delivered_quantity', 'invoiced_quantity', 'unit_price', 'discount_percent', 'discount_amount', 'total'];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'delivered_quantity' => 'decimal:2',
            'invoiced_quantity' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'discount_percent' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
