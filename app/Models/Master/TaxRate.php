<?php
namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class TaxRate extends Model
{
    protected $fillable = ['name', 'category', 'rate', 'withholding_rate', 'tax_code', 'is_active'];

    protected function casts(): array
    {
        return [
            'rate' => 'decimal:2',
            'withholding_rate' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function scopePpn($query)
    {
        return $query->where('category', 'ppn');
    }

    public function scopePph($query)
    {
        return $query->whereIn('category', ['pph21', 'pph23', 'pph42', 'pph_final']);
    }

    public function isPpn(): bool
    {
        return $this->category === 'ppn';
    }

    public function isPph(): bool
    {
        return in_array($this->category, ['pph21', 'pph23', 'pph42', 'pph_final', 'pph15']);
    }
}
