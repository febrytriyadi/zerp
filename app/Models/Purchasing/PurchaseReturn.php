<?php
namespace App\Models\Purchasing;

use App\Models\Finance\JournalEntry;
use App\Models\Master\Branch;
use App\Models\Master\Company;
use App\Models\Master\Supplier;
use App\Models\Master\Warehouse;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class PurchaseReturn extends Model
{
    protected $fillable = [
        'company_id', 'branch_id', 'return_number', 'return_date',
        'purchase_invoice_id', 'supplier_id', 'warehouse_id', 'return_type',
        'description', 'status', 'created_by',
    ];

    protected function casts(): array
    {
        return ['return_date' => 'date'];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function purchaseInvoice(): BelongsTo
    {
        return $this->belongsTo(PurchaseInvoice::class);
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
        return $this->hasMany(PurchaseReturnItem::class);
    }

    public function journalEntry(): MorphOne
    {
        return $this->morphOne(JournalEntry::class, 'reference');
    }
}
