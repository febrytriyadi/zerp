<?php
namespace App\Models\Inventory;

use App\Models\Master\Branch;
use App\Models\Master\Company;
use App\Models\Master\Product;
use App\Models\Master\Warehouse;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class InventoryMovement extends Model
{
    protected $fillable = [
        'company_id', 'branch_id', 'product_id', 'warehouse_id',
        'source_warehouse_id', 'destination_warehouse_id', 'transaction_type',
        'quantity_in', 'quantity_out', 'unit_cost', 'total_cost',
        'reference_type', 'reference_id', 'transaction_date', 'description', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'quantity_in' => 'decimal:2',
            'quantity_out' => 'decimal:2',
            'unit_cost' => 'decimal:2',
            'total_cost' => 'decimal:2',
            'transaction_date' => 'date',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function sourceWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'source_warehouse_id');
    }

    public function destinationWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'destination_warehouse_id');
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }
}
