<?php
namespace App\Services\Finance;

use App\Data\CreateJournalData;
use App\Data\JournalLineData;
use App\Models\Finance\CashTransaction;
use App\Models\Master\ChartOfAccount;
use App\Services\Accounting\FiscalPeriodService;
use App\Services\Accounting\JournalService;
use App\Services\NumberingService;
use Illuminate\Support\Facades\DB;

class CashTransactionService
{
    public function __construct(
        protected FiscalPeriodService $fiscalPeriodService,
        protected JournalService $journalService,
        protected NumberingService $numberingService,
    ) {}

    public function create(array $data): CashTransaction
    {
        $this->fiscalPeriodService->assertDateIsOpen($data['company_id'], $data['branch_id'], $data['transaction_date']);

        $data['transaction_number'] = $this->numberingService->generate(
            'cash_transaction',
            $data['company_id'],
            $data['branch_id'],
            $data['transaction_date']
        );

        $data['status'] = $data['status'] ?? 'draft';

        return CashTransaction::create($data);
    }

    public function update(CashTransaction $transaction, array $data): void
    {
        if ($transaction->status !== 'draft') {
            throw new \RuntimeException('Only draft transactions can be updated.');
        }

        $transaction->update($data);
    }

    public function post(CashTransaction $transaction): void
    {
        if ($transaction->status !== 'approved') {
            throw new \RuntimeException('Only approved transactions can be posted.');
        }

        DB::transaction(function () use ($transaction) {
            $this->fiscalPeriodService->assertDateIsOpen(
                $transaction->company_id,
                $transaction->branch_id,
                $transaction->transaction_date->toDateString()
            );

            $lines = [];

            $contraAccountId = $transaction->chart_of_account_id
                ?? ChartOfAccount::where('company_id', $transaction->company_id)
                    ->where('code', '1-1050')
                    ->value('id');

            if ($transaction->type === 'receipt') {
                $lines[] = new JournalLineData(
                    chartOfAccountId: $transaction->cash_account_id,
                    debit: $transaction->amount,
                    credit: 0,
                );
                $lines[] = new JournalLineData(
                    chartOfAccountId: $contraAccountId,
                    debit: 0,
                    credit: $transaction->amount,
                );
            } else {
                $lines[] = new JournalLineData(
                    chartOfAccountId: $contraAccountId,
                    debit: $transaction->amount,
                    credit: 0,
                );
                $lines[] = new JournalLineData(
                    chartOfAccountId: $transaction->cash_account_id,
                    debit: 0,
                    credit: $transaction->amount,
                );
            }

            $journalData = new CreateJournalData(
                companyId: $transaction->company_id,
                branchId: $transaction->branch_id,
                transactionDate: $transaction->transaction_date->toDateString(),
                description: $transaction->description ?? 'Cash transaction',
                referenceType: 'cash_transaction',
                referenceId: $transaction->id,
                lines: $lines,
            );

            $this->journalService->createAndPost($journalData);

            $transaction->status = 'posted';
            $transaction->posted_at = now();
            $transaction->posted_by = auth()->id();
            $transaction->save();
        });
    }

    public function void(CashTransaction $transaction): void
    {
        DB::transaction(function () use ($transaction) {
            if ($transaction->journalEntry) {
                $this->journalService->void($transaction->journalEntry);
            }

            $transaction->status = 'voided';
            $transaction->voided_at = now();
            $transaction->voided_by = auth()->id();
            $transaction->save();
        });
    }
}
