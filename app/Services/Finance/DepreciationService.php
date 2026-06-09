<?php
namespace App\Services\Finance;

use App\Models\Finance\AssetDepreciation;
use App\Models\Finance\FixedAsset;
use App\Services\Accounting\FiscalPeriodService;
use Carbon\Carbon;

class DepreciationService
{
    public function __construct(
        protected FiscalPeriodService $fiscalPeriodService,
    ) {}

    public function calculateMonthlyDepreciation(FixedAsset $asset, string $periodDate): float
    {
        $cost = $asset->purchase_cost;
        $salvage = $asset->salvage_value;
        $years = $asset->useful_life_years;
        $months = $years * 12;

        if ($asset->depreciation_method === 'declining_balance') {
            $rate = 2 / $years;
            $bookValue = $asset->book_value;
            $monthly = ($bookValue * $rate) / 12;
            $maxDepreciation = $bookValue - $salvage;
            return round(min($monthly, $maxDepreciation), 2);
        }

        // straight_line
        $depreciableAmount = $cost - $salvage;
        if ($months <= 0) return 0;
        return round($depreciableAmount / $months, 2);
    }

    public function generateDepreciation(FixedAsset $asset, string $periodDate): AssetDepreciation
    {
        $this->fiscalPeriodService->assertDateIsOpen($asset->company_id, $asset->branch_id, $periodDate);

        $amount = $this->calculateMonthlyDepreciation($asset, $periodDate);
        $accumBefore = $asset->accumulated_depreciation;
        $accumAfter = $accumBefore + $amount;
        $bookBefore = $asset->book_value;
        $bookAfter = max($bookBefore - $amount, $asset->salvage_value);

        $actualAmount = $bookBefore - $bookAfter;

        $depreciation = AssetDepreciation::create([
            'fixed_asset_id' => $asset->id,
            'period_date' => $periodDate,
            'depreciation_amount' => $actualAmount,
            'accumulated_before' => $accumBefore,
            'accumulated_after' => $accumAfter,
            'book_value_before' => $bookBefore,
            'book_value_after' => $bookAfter,
            'created_by' => auth()->id(),
        ]);

        $asset->accumulated_depreciation = $accumAfter;
        $asset->book_value = $bookAfter;
        $asset->last_depreciation_date = $periodDate;

        if ($bookAfter <= $asset->salvage_value) {
            $asset->status = 'fully_depreciated';
        } else {
            $asset->status = 'depreciating';
        }

        $asset->save();

        return $depreciation->fresh();
    }

    public function generateBulkDepreciation(int $companyId, int $branchId, string $periodDate): array
    {
        $assets = FixedAsset::byCompany($companyId)
            ->where('branch_id', $branchId)
            ->whereIn('status', ['active', 'depreciating'])
            ->get();

        $results = [];

        foreach ($assets as $asset) {
            if ($asset->last_depreciation_date) {
                $lastDate = Carbon::parse($asset->last_depreciation_date);
                $period = Carbon::parse($periodDate);
                if ($lastDate->format('Y-m') === $period->format('Y-m')) {
                    continue;
                }
            }

            $results[] = $this->generateDepreciation($asset, $periodDate);
        }

        return $results;
    }
}
