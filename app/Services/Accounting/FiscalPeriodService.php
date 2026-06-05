<?php
namespace App\Services\Accounting;

use App\Exceptions\FiscalPeriodClosedException;
use App\Models\Master\FiscalPeriod;
use Illuminate\Support\Facades\Auth;

class FiscalPeriodService
{
    public function isOpen(int $companyId, int $branchId, string $date): bool
    {
        $period = FiscalPeriod::where('company_id', $companyId)
            ->where('branch_id', $branchId)
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->first();

        return $period && $period->is_open;
    }

    public function assertDateIsOpen(int $companyId, int $branchId, string $date): void
    {
        if (!$this->isOpen($companyId, $branchId, $date)) {
            throw new FiscalPeriodClosedException("Fiscal period is closed for date {$date}.");
        }
    }

    public function getPeriodId(int $companyId, int $branchId, string $date): int
    {
        $period = FiscalPeriod::where('company_id', $companyId)
            ->where('branch_id', $branchId)
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->firstOrFail();

        return $period->id;
    }

    public function closePeriod(FiscalPeriod $fiscalPeriod): void
    {
        $fiscalPeriod->is_open = false;
        $fiscalPeriod->is_closed = true;
        $fiscalPeriod->closed_at = now();
        $fiscalPeriod->closed_by = Auth::id();
        $fiscalPeriod->save();
    }

    public function openPeriod(FiscalPeriod $fiscalPeriod): void
    {
        $fiscalPeriod->is_open = true;
        $fiscalPeriod->is_closed = false;
        $fiscalPeriod->closed_at = null;
        $fiscalPeriod->closed_by = null;
        $fiscalPeriod->save();
    }
}
