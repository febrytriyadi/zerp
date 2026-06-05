<?php
namespace App\Models\Inventory;

use App\Models\Master\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOpnameItem extends Model
{
    protected $fillable = ['stock_opname_id', 'product_id', 'system_quantity', 'physical_quantity', 'difference', 'unit_cost', 'difference_value', 'description'];

    protected function casts(): array
    {
        return [
            'system_quantity' => 'decimal:2',
            'physical_quantity' => 'decimal:2',
            'difference' => 'decimal:2',
            'unit_cost' => 'decimal:2',
            'difference_value' => 'decimal:2',
        ];
    }

    public function stockOpname(): BelongsTo
    {
        return $this->belongsTo(StockOpname::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
