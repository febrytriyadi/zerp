<?php
namespace App\Services\Accounting;

use App\Models\Finance\JournalEntryLine;
use App\Models\Master\ChartOfAccount;

class LedgerService
{
    public function post(JournalEntryLine $line): void
    {
        $coa = ChartOfAccount::findOrFail($line->chart_of_account_id);

        if ($coa->normal_balance === 'debit') {
            $coa->balance += $line->debit - $line->credit;
        } else {
            $coa->balance += $line->credit - $line->debit;
        }

        $coa->save();
    }
}
