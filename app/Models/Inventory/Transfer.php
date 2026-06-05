<?php
namespace App\Models\Inventory;

use App\Models\Master\Branch;
use App\Models\Master\Company;
use App\Models\Master\Warehouse;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transfer extends Model
{
    protected $fillable = [
        'company_id', 'branch_id', 'transfer_number', 'transfer_date',
        'source_warehouse_id', 'destination_warehouse_id', 'description',
        'status', 'created_by', 'approved_by',
    ];

    protected function casts(): array
    {
        return ['transfer_date' => 'date'];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function sourceWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'source_warehouse_id');
    }

    public function destinationWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'destination_warehouse_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(TransferItem::class);
    }
}
