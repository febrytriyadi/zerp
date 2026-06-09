<?php
namespace App\DTOs\Finance;

class CreateClosingJournalData
{
    /** @param array<int, array{account_id: int, debit: float, credit: float}> $items */
    public function __construct(
        public int $companyId,
        public int $branchId,
        public string $closingType,
        public int $fiscalPeriodId,
        public string $description,
        public array $items = [],
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            companyId: (int) $data['company_id'],
            branchId: (int) $data['branch_id'],
            closingType: $data['closing_type'],
            fiscalPeriodId: (int) $data['fiscal_period_id'],
            description: $data['description'],
            items: $data['items'] ?? [],
        );
    }

    public function toArray(): array
    {
        return [
            'company_id' => $this->companyId,
            'branch_id' => $this->branchId,
            'closing_type' => $this->closingType,
            'fiscal_period_id' => $this->fiscalPeriodId,
            'description' => $this->description,
            'items' => $this->items,
        ];
    }
}
