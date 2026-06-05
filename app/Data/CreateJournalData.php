<?php
namespace App\Data;

class CreateJournalData
{
    /** @param JournalLineData[] $lines */
    public function __construct(
        public int $companyId,
        public int $branchId,
        public string $transactionDate,
        public string $description,
        public ?string $referenceType = null,
        public ?int $referenceId = null,
        public array $lines = [],
        public ?int $currencyId = null,
        public ?float $exchangeRate = null,
    ) {}
}
