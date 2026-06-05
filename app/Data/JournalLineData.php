<?php
namespace App\Data;

class JournalLineData
{
    public function __construct(
        public int $chartOfAccountId,
        public float $debit = 0,
        public float $credit = 0,
        public ?string $description = null,
        public ?int $currencyId = null,
        public ?float $exchangeRate = null,
    ) {}
}
