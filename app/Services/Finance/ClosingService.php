<?php
namespace App\Services\Finance;

use App\DTOs\Finance\CreateAccrualData;
use App\DTOs\Finance\CreateClosingJournalData;
use App\Models\Finance\Accrual;
use App\Models\Finance\ClosingJournal;
use App\Models\Finance\JournalEntry;
use App\Models\Finance\JournalEntryLine;
use App\Models\Master\FiscalPeriod;
use App\Services\Accounting\FiscalPeriodService;
use App\Services\NumberingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ClosingService
{
    public function __construct(
        protected FiscalPeriodService $fiscalPeriodService,
        protected NumberingService $numberingService,
    ) {}

    public function createAccrual(CreateAccrualData $data): Accrual
    {
        return DB::transaction(function () use ($data) {
            $accrualNumber = 'ACR-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5));

            return Accrual::create([
                'company_id' => $data->companyId,
                'branch_id' => $data->branchId,
                'accrual_number' => $accrualNumber,
                'accrual_type' => $data->accrualType,
                'category' => $data->category,
                'description' => $data->description,
                'total_amount' => $data->totalAmount,
                'remaining_amount' => $data->totalAmount,
                'recognized_amount' => 0,
                'start_date' => $data->startDate,
                'end_date' => $data->endDate,
                'total_periods' => $data->totalPeriods,
                'recognized_periods' => 0,
                'amount_per_period' => $data->amountPerPeriod,
                'debit_account_id' => $data->debitAccountId,
                'credit_account_id' => $data->creditAccountId,
                'status' => 'active',
                'notes' => $data->notes,
                'created_by' => auth()->id(),
            ]);
        });
    }

    public function recognizeAccrual(Accrual $accrual): JournalEntry
    {
        return DB::transaction(function () use ($accrual) {
            $amount = $accrual->amount_per_period;

            $fiscalPeriodId = $this->fiscalPeriodService->getPeriodId(
                $accrual->company_id,
                $accrual->branch_id,
                now()->toDateString()
            );

            $journalNumber = $this->numberingService->generate(
                'journal_entry',
                $accrual->company_id,
                $accrual->branch_id,
                now()->toDateString()
            );

            $journalEntry = JournalEntry::create([
                'company_id' => $accrual->company_id,
                'branch_id' => $accrual->branch_id,
                'journal_number' => $journalNumber,
                'transaction_date' => now()->toDateString(),
                'description' => 'Accrual recognition: ' . $accrual->description,
                'reference_type' => Accrual::class,
                'reference_id' => $accrual->id,
                'fiscal_period_id' => $fiscalPeriodId,
                'total_debit' => $amount,
                'total_credit' => $amount,
                'status' => 'posted',
                'is_voided' => false,
                'posted_at' => now(),
                'posted_by' => auth()->id(),
                'created_by' => auth()->id(),
            ]);

            JournalEntryLine::create([
                'journal_entry_id' => $journalEntry->id,
                'chart_of_account_id' => $accrual->credit_account_id,
                'debit' => $amount,
                'credit' => 0,
                'description' => $accrual->description,
            ]);

            JournalEntryLine::create([
                'journal_entry_id' => $journalEntry->id,
                'chart_of_account_id' => $accrual->debit_account_id,
                'debit' => 0,
                'credit' => $amount,
                'description' => $accrual->description,
            ]);

            $accrual->increment('recognized_periods');
            $accrual->increment('recognized_amount', $amount);
            $accrual->decrement('remaining_amount', $amount);

            if ($accrual->recognized_periods >= $accrual->total_periods) {
                $accrual->status = 'fully_recognized';
            }

            $accrual->save();

            return $journalEntry->fresh(['lines']);
        });
    }

    public function createMonthEndClosing(CreateClosingJournalData $data): ClosingJournal
    {
        return DB::transaction(function () use ($data) {
            $period = FiscalPeriod::findOrFail($data->fiscalPeriodId);
            $year = date('Y', strtotime($period->start_date));
            $month = date('m', strtotime($period->start_date));

            $count = ClosingJournal::where('closing_type', 'month_end')
                ->where('company_id', $data->companyId)
                ->where('branch_id', $data->branchId)
                ->count();

            $closingNumber = 'CLM-' . $year . $month . '-' . str_pad($count + 1, 5, '0', STR_PAD_LEFT);

            return ClosingJournal::create([
                'company_id' => $data->companyId,
                'branch_id' => $data->branchId,
                'closing_number' => $closingNumber,
                'closing_type' => 'month_end',
                'fiscal_period_id' => $data->fiscalPeriodId,
                'description' => $data->description,
                'total_debit' => 0,
                'total_credit' => 0,
                'status' => 'draft',
                'items' => $data->items,
                'created_by' => auth()->id(),
            ]);
        });
    }

    public function createYearEndClosing(CreateClosingJournalData $data): ClosingJournal
    {
        return DB::transaction(function () use ($data) {
            $period = FiscalPeriod::findOrFail($data->fiscalPeriodId);
            $year = date('Y', strtotime($period->start_date));

            $count = ClosingJournal::where('closing_type', 'year_end')
                ->where('company_id', $data->companyId)
                ->where('branch_id', $data->branchId)
                ->count();

            $closingNumber = 'CLY-' . $year . '-' . str_pad($count + 1, 5, '0', STR_PAD_LEFT);

            return ClosingJournal::create([
                'company_id' => $data->companyId,
                'branch_id' => $data->branchId,
                'closing_number' => $closingNumber,
                'closing_type' => 'year_end',
                'fiscal_period_id' => $data->fiscalPeriodId,
                'description' => $data->description,
                'total_debit' => 0,
                'total_credit' => 0,
                'status' => 'draft',
                'items' => $data->items,
                'created_by' => auth()->id(),
            ]);
        });
    }

    public function postClosingJournal(ClosingJournal $closingJournal): ClosingJournal
    {
        return DB::transaction(function () use ($closingJournal) {
            $items = is_string($closingJournal->items)
                ? json_decode($closingJournal->items, true)
                : ($closingJournal->items ?? []);

            $totalDebit = array_sum(array_column($items, 'debit'));
            $totalCredit = array_sum(array_column($items, 'credit'));

            $fiscalPeriodId = $this->fiscalPeriodService->getPeriodId(
                $closingJournal->company_id,
                $closingJournal->branch_id,
                now()->toDateString()
            );

            $journalNumber = $this->numberingService->generate(
                'journal_entry',
                $closingJournal->company_id,
                $closingJournal->branch_id,
                now()->toDateString()
            );

            $journalEntry = JournalEntry::create([
                'company_id' => $closingJournal->company_id,
                'branch_id' => $closingJournal->branch_id,
                'journal_number' => $journalNumber,
                'transaction_date' => now()->toDateString(),
                'description' => $closingJournal->description,
                'reference_type' => ClosingJournal::class,
                'reference_id' => $closingJournal->id,
                'fiscal_period_id' => $fiscalPeriodId,
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'status' => 'posted',
                'is_voided' => false,
                'posted_at' => now(),
                'posted_by' => auth()->id(),
                'created_by' => auth()->id(),
            ]);

            foreach ($items as $item) {
                JournalEntryLine::create([
                    'journal_entry_id' => $journalEntry->id,
                    'chart_of_account_id' => $item['account_id'],
                    'debit' => $item['debit'],
                    'credit' => $item['credit'],
                    'description' => $closingJournal->description,
                ]);
            }

            $closingJournal->journal_entry_id = $journalEntry->id;
            $closingJournal->total_debit = $totalDebit;
            $closingJournal->total_credit = $totalCredit;
            $closingJournal->status = 'posted';
            $closingJournal->posted_at = now();
            $closingJournal->posted_by = auth()->id();
            $closingJournal->save();

            return $closingJournal->fresh();
        });
    }
}
