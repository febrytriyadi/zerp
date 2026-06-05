<?php
namespace App\Models\Production;

use App\Models\Finance\JournalEntry;
use App\Models\Master\Branch;
use App\Models\Master\Company;
use App\Models\Master\Product;
use App\Models\Master\Warehouse;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Assembly extends Model
{
    protected $fillable = [
        'company_id', 'branch_id', 'assembly_number', 'assembly_date',
        'product_id', 'quantity', 'warehouse_id', 'total_cost',
        'description', 'status', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'assembly_date' => 'date',
            'quantity' => 'decimal:2',
            'total_cost' => 'decimal:2',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(AssemblyItem::class);
    }

    public function journalEntry(): MorphOne
    {
        return $this->morphOne(JournalEntry::class, 'reference');
    }
}
