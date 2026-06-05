<?php
namespace App\Models\Purchasing;

use App\Models\Master\Branch;
use App\Models\Master\Company;
use App\Models\Master\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BiddingSheet extends Model
{
    protected $fillable = ['company_id', 'branch_id', 'sheet_number', 'quotation_request_id', 'supplier_id', 'description', 'status', 'created_by'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function quotationRequest(): BelongsTo
    {
        return $this->belongsTo(QuotationRequest::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
