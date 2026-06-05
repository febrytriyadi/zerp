<?php
namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChartOfAccount extends Model
{
    protected $fillable = ['company_id', 'code', 'name', 'type', 'normal_balance', 'is_active', 'is_header', 'parent_id', 'level', 'balance'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_header' => 'boolean',
            'balance' => 'decimal:2',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function journalEntryLines(): HasMany
    {
        return $this->hasMany(\App\Models\Finance\JournalEntryLine::class);
    }
}
