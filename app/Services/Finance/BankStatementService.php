<?php
namespace App\Services\Finance;

use App\Models\Finance\BankAccountBalance;
use App\Models\Finance\BankStatement;
use App\Models\Finance\BankStatementLine;
use App\Models\Master\BankAccount;
use Illuminate\Support\Facades\DB;

class BankStatementService
{
    public function import(array $data, $file = null): BankStatement
    {
        $data['company_id'] ??= auth()->user()->company_id ?? 1;
        $data['branch_id'] ??= auth()->user()->branch_id ?? 1;
        $data['created_by'] ??= auth()->id();
        $data['status'] ??= 'draft';

        $totalDeposits = 0;
        $totalWithdrawals = 0;

        if ($file) {
            // Mock: file import placeholder — parse CSV/XLS in production
            $totalDeposits = $data['total_deposits'] ?? 0;
            $totalWithdrawals = $data['total_withdrawals'] ?? 0;
        }

        $data['total_deposits'] = $totalDeposits;
        $data['total_withdrawals'] = $totalWithdrawals;
        $data['ending_balance'] = ($data['beginning_balance'] ?? 0) + $totalDeposits - $totalWithdrawals;

        return DB::transaction(function () use ($data) {
            return BankStatement::create($data);
        });
    }

    public function matchLine(BankStatementLine $line, string $transactionType, int $transactionId): void
    {
        $line->update([
            'matching_status' => 'matched',
            'matched_transaction_type' => $transactionType,
            'matched_transaction_id' => $transactionId,
        ]);
    }

    public function unmatchLine(BankStatementLine $line): void
    {
        $line->update([
            'matching_status' => 'unmatched',
            'matched_transaction_type' => null,
            'matched_transaction_id' => null,
        ]);
    }

    public function postStatement(BankStatement $statement): void
    {
        $statement->update([
            'status' => 'posted',
            'posted_at' => now(),
            'posted_by' => auth()->id(),
        ]);
    }

    public function updateBalance(BankAccount $bankAccount, string $date): BankAccountBalance
    {
        $prevBalance = BankAccountBalance::where('bank_account_id', $bankAccount->id)
            ->where('balance_date', '<', $date)
            ->orderBy('balance_date', 'desc')
            ->first();

        $openingBalance = $prevBalance ? $prevBalance->ending_balance : ($bankAccount->opening_balance ?? 0);

        $totalDebit = BankStatementLine::whereHas('bankStatement', function ($q) use ($bankAccount, $date) {
            $q->where('bank_account_id', $bankAccount->id)->where('statement_date', '<=', $date);
        })->sum('debit');

        $totalCredit = BankStatementLine::whereHas('bankStatement', function ($q) use ($bankAccount, $date) {
            $q->where('bank_account_id', $bankAccount->id)->where('statement_date', '<=', $date);
        })->sum('credit');

        $endingBalance = $openingBalance + $totalDebit - $totalCredit;

        return BankAccountBalance::updateOrCreate(
            ['bank_account_id' => $bankAccount->id, 'balance_date' => $date],
            [
                'opening_balance' => $openingBalance,
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'ending_balance' => $endingBalance,
            ]
        );
    }
}
