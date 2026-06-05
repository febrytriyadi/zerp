<?php
namespace App\Models\Purchasing;

use App\Models\Master\Branch;
use App\Models\Master\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuotationRequest extends Model
{
    protected $fillable = ['company_id', 'branch_id', 'request_number', 'request_date', 'description', 'status', 'created_by'];

    protected function casts(): array
    {
        return ['request_date' => 'date'];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
