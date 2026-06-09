<?php
namespace App\Models\Finance;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetDepreciation extends Model
{
    protected $fillable = [
        'fixed_asset_id', 'period_date', 'depreciation_amount',
        'accumulated_before', 'accumulated_after',
        'book_value_before', 'book_value_after',
        'is_reversal', 'journal_entry_id', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'period_date' => 'date',
            'depreciation_amount' => 'decimal:2',
            'accumulated_before' => 'decimal:2',
            'accumulated_after' => 'decimal:2',
            'book_value_before' => 'decimal:2',
            'book_value_after' => 'decimal:2',
            'is_reversal' => 'boolean',
        ];
    }

    public function fixedAsset(): BelongsTo
    {
        return $this->belongsTo(FixedAsset::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
