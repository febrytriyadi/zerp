<?php
namespace App\Data;

class CreateAssetData
{
    public function __construct(
        public int $companyId,
        public int $branchId,
        public string $assetName,
        public string $assetCategory,
        public string $purchaseDate,
        public float $purchaseCost,
        public float $salvageValue,
        public int $usefulLifeYears,
        public string $depreciationMethod,
        public ?string $location = null,
        public ?string $description = null,
        public ?int $chartOfAccountId = null,
        public ?int $accumulatedDeprAccountId = null,
        public ?int $depreciationExpenseAccountId = null,
    ) {}
}
