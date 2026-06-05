<?php
namespace App\Models\Purchasing;

use App\Models\Master\Branch;
use App\Models\Master\Company;
use App\Models\Master\Supplier;
use App\Models\Master\Warehouse;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReceivedGoods extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'company_id', 'branch_id', 'receive_number', 'receive_date',
        'purchase_order_id', 'supplier_id', 'warehouse_id', 'description',
        'status', 'created_by',
    ];

    protected function casts(): array
    {
        return ['receive_date' => 'date'];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ReceivedGoodsItem::class);
    }
}
