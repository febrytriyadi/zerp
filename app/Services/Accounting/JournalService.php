<?php
namespace App\Services\Accounting;

use App\Data\CreateJournalData;
use App\Exceptions\JournalNotBalancedException;
use App\Models\Finance\JournalEntry;
use App\Models\Finance\JournalEntryLine;
use App\Services\NumberingService;
use Illuminate\Support\Facades\DB;

class JournalService
{
    public function __construct(
        protected FiscalPeriodService $fiscalPeriodService,
        protected LedgerService $ledgerService,
        protected NumberingService $numberingService,
    ) {}

    public function createAndPost(CreateJournalData $data): JournalEntry
    {
        return DB::transaction(function () use ($data) {
            $this->fiscalPeriodService->assertDateIsOpen($data->companyId, $data->branchId, $data->transactionDate);

            $totalDebit = array_sum(array_map(fn($l) => $l->debit, $data->lines));
            $totalCredit = array_sum(array_map(fn($l) => $l->credit, $data->lines));

            if (abs($totalDebit - $totalCredit) > 0.001) {
                throw new JournalNotBalancedException(
                    "Journal is not balanced. Total debit: {$totalDebit}, Total credit: {$totalCredit}."
                );
            }

            $fiscalPeriodId = $this->fiscalPeriodService->getPeriodId($data->companyId, $data->branchId, $data->transactionDate);

            $journalNumber = $this->numberingService->generate(
                'journal_entry',
                $data->companyId,
                $data->branchId,
                $data->transactionDate
            );

            $journalEntry = JournalEntry::create([
                'company_id' => $data->companyId,
                'branch_id' => $data->branchId,
                'fiscal_period_id' => $fiscalPeriodId,
                'journal_number' => $journalNumber,
                'transaction_date' => $data->transactionDate,
                'description' => $data->description,
                'reference_type' => $data->referenceType,
                'reference_id' => $data->referenceId,
                'currency_id' => $data->currencyId,
                'exchange_rate' => $data->exchangeRate ?? 1,
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'is_voided' => false,
                'created_by' => auth()->id(),
            ]);

            foreach ($data->lines as $lineData) {
                $line = JournalEntryLine::create([
                    'journal_entry_id' => $journalEntry->id,
                    'chart_of_account_id' => $lineData->chartOfAccountId,
                    'debit' => $lineData->debit,
                    'credit' => $lineData->credit,
                    'description' => $lineData->description,
                    'currency_id' => $lineData->currencyId,
                    'exchange_rate' => $lineData->exchangeRate,
                ]);

                $this->ledgerService->post($line);
            }

            return $journalEntry->fresh(['lines']);
        });
    }

    public function void(JournalEntry $journalEntry): JournalEntry
    {
        return DB::transaction(function () use ($journalEntry) {
            $journalEntry->load('lines');

            $reversalLines = $journalEntry->lines->map(function (JournalEntryLine $line) {
                return new \App\Data\JournalLineData(
                    chartOfAccountId: $line->chart_of_account_id,
                    debit: $line->credit,
                    credit: $line->debit,
                    description: "Reversal: {$line->description}",
                    currencyId: $line->currency_id,
                    exchangeRate: $line->exchange_rate,
                );
            })->toArray();

            $reversalData = new \App\Data\CreateJournalData(
                companyId: $journalEntry->company_id,
                branchId: $journalEntry->branch_id,
                transactionDate: now()->toDateString(),
                description: "Reversal of journal {$journalEntry->journal_number}",
                referenceType: $journalEntry->reference_type,
                referenceId: $journalEntry->reference_id,
                lines: $reversalLines,
                currencyId: $journalEntry->currency_id,
                exchangeRate: $journalEntry->exchange_rate,
            );

            $this->createAndPost($reversalData);

            $journalEntry->is_voided = true;
            $journalEntry->voided_at = now();
            $journalEntry->voided_by = auth()->id();
            $journalEntry->save();

            return $journalEntry->fresh();
        });
    }
}
