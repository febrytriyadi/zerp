<?php
namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NumberingFormat extends Model
{
    protected $fillable = ['company_id', 'branch_id', 'transaction_type', 'format', 'prefix', 'last_number', 'next_number', 'last_year', 'last_month', 'reset_period'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
