<?php

namespace App\Exports;

use App\Models\Finance\JournalEntryLine;
use App\Models\Master\ChartOfAccount;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CashBookExport implements FromView, WithTitle, ShouldAutoSize
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        $cashAccountId = self::resolveCoaId(Config::get('coa.cash'));
        $bankAccountId = self::resolveCoaId(Config::get('coa.bank'));

        $query = JournalEntryLine::select('journal_entry_lines.*')
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
            ->whereIn('journal_entry_lines.chart_of_account_id', array_filter([$cashAccountId, $bankAccountId]))
            ->where('journal_entries.status', '!=', 'voided');

        if ($this->request->filled('start_date')) {
            $query->where('journal_entries.transaction_date', '>=', $this->request->start_date);
        }

        if ($this->request->filled('end_date')) {
            $query->where('journal_entries.transaction_date', '<=', $this->request->end_date);
        }

        $lines = $query->with('journalEntry', 'chartOfAccount')
            ->orderBy('journal_entries.transaction_date')
            ->orderBy('journal_entries.id')
            ->get();

        return view('exports.cash-book', compact('lines'));
    }

    public function title(): string
    {
        return 'Cash Book';
    }

    private static function resolveCoaId(string $code): ?int
    {
        return ChartOfAccount::where('code', $code)->value('id');
    }
}
