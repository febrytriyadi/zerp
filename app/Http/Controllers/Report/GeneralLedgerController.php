<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Finance\JournalEntryLine;
use App\Models\Master\ChartOfAccount;
use Illuminate\Http\Request;

class GeneralLedgerController extends Controller
{
    public function index(Request $request)
    {
        $query = JournalEntryLine::select('journal_entry_lines.*')
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
            ->with('journalEntry', 'chartOfAccount')
            ->where('journal_entries.is_voided', false);

        if ($request->filled('chart_of_account_id')) {
            $query->where('journal_entry_lines.chart_of_account_id', $request->chart_of_account_id);
        }

        if ($request->filled('start_date')) {
            $query->where('journal_entries.transaction_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('journal_entries.transaction_date', '<=', $request->end_date);
        }

        $lines = $query->orderBy('journal_entry_lines.chart_of_account_id')
            ->orderBy('journal_entries.transaction_date')
            ->orderBy('journal_entries.id')
            ->paginate(50);

        $accounts = ChartOfAccount::where('is_active', true)->orderBy('code')->get();

        return view('reports.general-ledger', compact('lines', 'accounts'));
    }
}
