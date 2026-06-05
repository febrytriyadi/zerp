<?php
namespace App\Models\Purchasing;

use App\Models\Master\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReceivedGoodsItem extends Model
{
    protected $fillable = ['received_goods_id', 'product_id', 'quantity', 'accepted_quantity', 'rejected_quantity', 'unit_price', 'total'];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'accepted_quantity' => 'decimal:2',
            'rejected_quantity' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function receivedGoods(): BelongsTo
    {
        return $this->belongsTo(ReceivedGoods::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
