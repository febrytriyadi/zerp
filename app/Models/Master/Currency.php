<?php
namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = ['code', 'name', 'symbol', 'is_base', 'exchange_rate', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_base' => 'boolean',
            'exchange_rate' => 'decimal:4',
            'is_active' => 'boolean',
        ];
    }
}
