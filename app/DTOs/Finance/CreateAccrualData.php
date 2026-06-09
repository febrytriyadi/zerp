<?php
namespace App\DTOs\Finance;

class CreateAccrualData
{
    public function __construct(
        public int $companyId,
        public int $branchId,
        public string $accrualType,
        public string $category,
        public string $description,
        public float $totalAmount,
        public string $startDate,
        public string $endDate,
        public int $totalPeriods,
        public float $amountPerPeriod,
        public int $debitAccountId,
        public int $creditAccountId,
        public ?string $notes = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            companyId: (int) $data['company_id'],
            branchId: (int) $data['branch_id'],
            accrualType: $data['accrual_type'],
            category: $data['category'],
            description: $data['description'],
            totalAmount: (float) $data['total_amount'],
            startDate: $data['start_date'],
            endDate: $data['end_date'],
            totalPeriods: (int) $data['total_periods'],
            amountPerPeriod: (float) $data['amount_per_period'],
            debitAccountId: (int) $data['debit_account_id'],
            creditAccountId: (int) $data['credit_account_id'],
            notes: $data['notes'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'company_id' => $this->companyId,
            'branch_id' => $this->branchId,
            'accrual_type' => $this->accrualType,
            'category' => $this->category,
            'description' => $this->description,
            'total_amount' => $this->totalAmount,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'total_periods' => $this->totalPeriods,
            'amount_per_period' => $this->amountPerPeriod,
            'debit_account_id' => $this->debitAccountId,
            'credit_account_id' => $this->creditAccountId,
            'notes' => $this->notes,
        ];
    }
}
