<?php
namespace App\Models\Inventory;

use App\Models\Finance\JournalEntry;
use App\Models\Master\Branch;
use App\Models\Master\Company;
use App\Models\Master\Warehouse;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class StockAdjustment extends Model
{
    protected $fillable = [
        'company_id', 'branch_id', 'adjustment_number', 'adjustment_date',
        'warehouse_id', 'stock_opname_id', 'adjustment_type', 'description',
        'status', 'created_by',
    ];

    protected function casts(): array
    {
        return ['adjustment_date' => 'date'];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function stockOpname(): BelongsTo
    {
        return $this->belongsTo(StockOpname::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockAdjustmentItem::class);
    }

    public function journalEntry(): MorphOne
    {
        return $this->morphOne(JournalEntry::class, 'reference');
    }
}
