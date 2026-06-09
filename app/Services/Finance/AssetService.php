<?php
namespace App\Services\Finance;

use App\Data\CreateAssetData;
use App\Models\Finance\AssetTransaction;
use App\Models\Finance\FixedAsset;
use App\Services\Accounting\FiscalPeriodService;
use App\Services\Accounting\JournalService;
use App\Services\NumberingService;
use Illuminate\Support\Facades\DB;

class AssetService
{
    public function __construct(
        protected FiscalPeriodService $fiscalPeriodService,
        protected JournalService $journalService,
        protected NumberingService $numberingService,
        protected DepreciationService $depreciationService,
    ) {}

    public function create(CreateAssetData $data): FixedAsset
    {
        return DB::transaction(function () use ($data) {
            $this->fiscalPeriodService->assertDateIsOpen($data->companyId, $data->branchId, $data->purchaseDate);

            $assetNumber = $this->numberingService->generate(
                'fixed_asset',
                $data->companyId,
                $data->branchId,
                $data->purchaseDate
            );

            $bookValue = $data->purchaseCost;

            $asset = FixedAsset::create([
                'company_id' => $data->companyId,
                'branch_id' => $data->branchId,
                'asset_number' => $assetNumber,
                'asset_name' => $data->assetName,
                'asset_category' => $data->assetCategory,
                'purchase_date' => $data->purchaseDate,
                'purchase_cost' => $data->purchaseCost,
                'salvage_value' => $data->salvageValue,
                'useful_life_years' => $data->usefulLifeYears,
                'depreciation_method' => $data->depreciationMethod,
                'accumulated_depreciation' => 0,
                'book_value' => $bookValue,
                'location' => $data->location,
                'description' => $data->description,
                'chart_of_account_id' => $data->chartOfAccountId,
                'accumulated_depr_account_id' => $data->accumulatedDeprAccountId,
                'depreciation_expense_account_id' => $data->depreciationExpenseAccountId,
                'status' => 'active',
                'created_by' => auth()->id(),
            ]);

            AssetTransaction::create([
                'fixed_asset_id' => $asset->id,
                'transaction_type' => 'acquisition',
                'transaction_date' => $data->purchaseDate,
                'amount' => $data->purchaseCost,
                'description' => "Perolehan {$data->assetName}",
                'created_by' => auth()->id(),
            ]);

            return $asset->fresh();
        });
    }

    public function sell(FixedAsset $asset, string $saleDate, float $saleAmount, ?string $description = null): FixedAsset
    {
        return DB::transaction(function () use ($asset, $saleDate, $saleAmount, $description) {
            $this->fiscalPeriodService->assertDateIsOpen($asset->company_id, $asset->branch_id, $saleDate);

            $bookValue = $asset->book_value;
            $gainLoss = $saleAmount - $bookValue;

            $asset->status = 'sold';
            $asset->save();

            AssetTransaction::create([
                'fixed_asset_id' => $asset->id,
                'transaction_type' => 'sale',
                'transaction_date' => $saleDate,
                'amount' => $saleAmount,
                'description' => $description ?? "Penjualan {$asset->asset_name}",
                'created_by' => auth()->id(),
            ]);

            return $asset->fresh();
        });
    }

    public function retire(FixedAsset $asset, string $retireDate, ?string $description = null): FixedAsset
    {
        return DB::transaction(function () use ($asset, $retireDate, $description) {
            $this->fiscalPeriodService->assertDateIsOpen($asset->company_id, $asset->branch_id, $retireDate);

            $remainingBookValue = $asset->book_value - $asset->accumulated_depreciation;

            $asset->status = 'retired';
            $asset->save();

            AssetTransaction::create([
                'fixed_asset_id' => $asset->id,
                'transaction_type' => 'retirement',
                'transaction_date' => $retireDate,
                'amount' => $asset->purchase_cost,
                'description' => $description ?? "Retiremen {$asset->asset_name}",
                'created_by' => auth()->id(),
            ]);

            return $asset->fresh();
        });
    }

    public function revalue(FixedAsset $asset, float $newValue, string $revalueDate, ?string $description = null): FixedAsset
    {
        return DB::transaction(function () use ($asset, $newValue, $revalueDate, $description) {
            $this->fiscalPeriodService->assertDateIsOpen($asset->company_id, $asset->branch_id, $revalueDate);

            $diff = $newValue - $asset->book_value;

            $asset->purchase_cost = $newValue;
            $asset->book_value = $newValue - $asset->accumulated_depreciation;
            $asset->save();

            AssetTransaction::create([
                'fixed_asset_id' => $asset->id,
                'transaction_type' => 'revaluation',
                'transaction_date' => $revalueDate,
                'amount' => $diff,
                'description' => $description ?? "Revaluasi {$asset->asset_name}",
                'created_by' => auth()->id(),
            ]);

            return $asset->fresh();
        });
    }

    protected function getDefaultAssetAccount(string $category): int
    {
        $map = [
            'land' => '1.2.1',
            'building' => '1.2.2',
            'machinery' => '1.2.3',
            'vehicle' => '1.2.4',
            'furniture' => '1.2.5',
            'computer' => '1.2.6',
            'other' => '1.2.7',
        ];

        $code = $map[$category] ?? '1.2.7';
        $account = \App\Models\Master\ChartOfAccount::where('code', $code)->first();

        return $account?->id ?? 1;
    }
}
