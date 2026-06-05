<?php

namespace App\Http\Controllers\Report;

use App\Exports\CashBookExport;
use App\Http\Controllers\Controller;
use App\Models\Finance\JournalEntryLine;
use App\Models\Master\ChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Maatwebsite\Excel\Facades\Excel;

class CashBookController extends Controller
{
    public function index(Request $request)
    {
        $cashAccountId = self::resolveCoaId(Config::get('coa.cash'));
        $bankAccountId = self::resolveCoaId(Config::get('coa.bank'));

        $query = JournalEntryLine::select('journal_entry_lines.*')
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
            ->whereIn('journal_entry_lines.chart_of_account_id', array_filter([$cashAccountId, $bankAccountId]))
            ->where('journal_entries.status', '!=', 'voided');

        if ($request->filled('start_date')) {
            $query->where('journal_entries.transaction_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('journal_entries.transaction_date', '<=', $request->end_date);
        }

        if ($request->filled('account_id')) {
            $query->where('journal_entry_lines.chart_of_account_id', $request->account_id);
        }

        $lines = $query->with('journalEntry', 'chartOfAccount')
            ->orderBy('journal_entries.transaction_date')
            ->orderBy('journal_entries.id')
            ->paginate(50);

        $accounts = ChartOfAccount::whereIn('id', array_filter([$cashAccountId, $bankAccountId]))->get();

        return view('reports.cash-book', compact('lines', 'accounts'));
    }

    public function exportPdf(Request $request)
    {
        $data = $this->getCashBookData($request);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf.cash-book', $data);
        return $pdf->download('cash-book.pdf');
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new CashBookExport($request), 'cash-book.xlsx');
    }

    protected function getCashBookData(Request $request): array
    {
        $cashAccountId = self::resolveCoaId(Config::get('coa.cash'));
        $bankAccountId = self::resolveCoaId(Config::get('coa.bank'));

        $query = JournalEntryLine::select('journal_entry_lines.*')
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
            ->whereIn('journal_entry_lines.chart_of_account_id', array_filter([$cashAccountId, $bankAccountId]))
            ->where('journal_entries.status', '!=', 'voided');

        if ($request->filled('start_date')) {
            $query->where('journal_entries.transaction_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('journal_entries.transaction_date', '<=', $request->end_date);
        }

        $lines = $query->with('journalEntry', 'chartOfAccount')
            ->orderBy('journal_entries.transaction_date')
            ->orderBy('journal_entries.id')
            ->get();

        return compact('lines');
    }

    private static function resolveCoaId(string $code): ?int
    {
        return ChartOfAccount::where('code', $code)->value('id');
    }
}
