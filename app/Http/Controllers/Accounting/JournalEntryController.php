<?php

namespace App\Http\Controllers\Accounting;

use App\Data\CreateJournalData;
use App\Data\JournalLineData;
use App\Http\Controllers\Controller;
use App\Models\Finance\JournalEntry;
use App\Models\Master\ChartOfAccount;
use App\Services\Accounting\JournalService;
use App\Services\Accounting\FiscalPeriodService;
use Illuminate\Http\Request;

class JournalEntryController extends Controller
{
    public function __construct(
        protected JournalService $journalService,
        protected FiscalPeriodService $fiscalPeriodService
    ) {}

    public function index()
    {
        $journalEntries = JournalEntry::with('lines', 'fiscalPeriod')->paginate(10);
        return view('accounting.journal-entries.index', compact('journalEntries'));
    }

    public function create()
    {
        $chartOfAccounts = ChartOfAccount::all();
        return view('accounting.journal-entries.create', compact('chartOfAccounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'required|exists:branches,id',
            'transaction_date' => 'required|date',
            'description' => 'nullable|string',
            'lines' => 'required|array|min:2',
            'lines.*.chart_of_account_id' => 'required|exists:chart_of_accounts,id',
            'lines.*.debit' => 'required|numeric|min:0',
            'lines.*.credit' => 'required|numeric|min:0',
            'lines.*.description' => 'nullable|string',
        ]);

        $lines = array_map(fn($line) => new JournalLineData(
            chartOfAccountId: $line['chart_of_account_id'],
            debit: $line['debit'],
            credit: $line['credit'],
            description: $line['description'] ?? null,
        ), $validated['lines']);

        $journalData = new CreateJournalData(
            companyId: $validated['company_id'],
            branchId: $validated['branch_id'],
            transactionDate: $validated['transaction_date'],
            description: $validated['description'] ?? '',
            lines: $lines,
        );

        $this->journalService->createAndPost($journalData);

        return redirect()->route('accounting.journal-entries.index')
            ->with('success', 'Journal entry created successfully.');
    }

    public function show(JournalEntry $journalEntry)
    {
        $journalEntry->load('lines.chartOfAccount', 'fiscalPeriod');
        return view('accounting.journal-entries.show', compact('journalEntry'));
    }

    public function edit(JournalEntry $journalEntry)
    {
        $journalEntry->load('lines');
        return view('accounting.journal-entries.edit', compact('journalEntry'));
    }

    public function post(JournalEntry $journalEntry)
    {
        $this->fiscalPeriodService->assertDateIsOpen(
            $journalEntry->company_id,
            $journalEntry->branch_id,
            $journalEntry->transaction_date->toDateString()
        );

        $journalEntry->update([
            'status' => 'posted',
            'posted_at' => now(),
            'posted_by' => auth()->id(),
        ]);

        return redirect()->route('accounting.journal-entries.index')
            ->with('success', 'Journal entry posted.');
    }

    public function void(JournalEntry $journalEntry)
    {
        $this->journalService->void($journalEntry);
        return redirect()->route('accounting.journal-entries.index')
            ->with('success', 'Journal entry voided.');
    }
}
