<?php
namespace App\Models\Sales;

use App\Models\Master\Branch;
use App\Models\Master\Company;
use App\Models\Master\Warehouse;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackingList extends Model
{
    protected $fillable = [
        'company_id', 'branch_id', 'packing_number', 'packing_date',
        'sales_order_id', 'delivery_order_id', 'warehouse_id', 'status', 'created_by',
    ];

    protected function casts(): array
    {
        return ['packing_date' => 'date'];
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

    public function deliveryOrder(): BelongsTo
    {
        return $this->belongsTo(DeliveryOrder::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }
}
