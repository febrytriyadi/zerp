<?php
namespace App\Data;

class DepreciationData
{
    public function __construct(
        public int $fixedAssetId,
        public string $periodDate,
        public float $depreciationAmount,
        public float $accumulatedBefore,
        public float $accumulatedAfter,
        public float $bookValueBefore,
        public float $bookValueAfter,
        public ?int $journalEntryId = null,
    ) {}
}
