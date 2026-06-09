<?php
namespace App\Http\Controllers\Finance;

use App\DTOs\Finance\CreateClosingJournalData;
use App\Http\Controllers\Controller;
use App\Models\Finance\ClosingJournal;
use App\Models\Master\ChartOfAccount;
use App\Models\Master\FiscalPeriod;
use App\Services\Finance\ClosingService;
use Illuminate\Http\Request;

class ClosingJournalController extends Controller
{
    public function __construct(
        protected ClosingService $closingService,
    ) {}

    public function index()
    {
        $closingJournals = ClosingJournal::with(['fiscalPeriod', 'postedBy', 'createdBy'])
            ->paginate(10);

        return view('finance.closing-journals.index', compact('closingJournals'));
    }

    public function create()
    {
        $fiscalPeriods = FiscalPeriod::where('is_open', true)->get();
        $accounts = ChartOfAccount::where('is_active', true)->orderBy('code')->get();

        return view('finance.closing-journals.create', compact('fiscalPeriods', 'accounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'closing_type' => 'required|in:month_end,year_end,adjustment',
            'fiscal_period_id' => 'required|exists:fiscal_periods,id',
            'description' => 'required|max:500',
            'items' => 'required|array|min:1',
            'items.*.account_id' => 'required|exists:chart_of_accounts,id',
            'items.*.debit' => 'required|numeric|min:0',
            'items.*.credit' => 'required|numeric|min:0',
        ]);

        $data = CreateClosingJournalData::fromArray([
            'company_id' => $request->company_id ?? auth()->user()->company_id ?? 1,
            'branch_id' => $request->branch_id ?? auth()->user()->branch_id ?? 1,
            'closing_type' => $validated['closing_type'],
            'fiscal_period_id' => $validated['fiscal_period_id'],
            'description' => $validated['description'],
            'items' => $validated['items'],
        ]);

        if ($data->closingType === 'year_end') {
            $this->closingService->createYearEndClosing($data);
        } else {
            $this->closingService->createMonthEndClosing($data);
        }

        return redirect()->route('finance.closing-journals.index')
            ->with('success', 'Jurnal penutup berhasil dibuat.');
    }

    public function show(ClosingJournal $closingJournal)
    {
        $closingJournal->load(['fiscalPeriod', 'journalEntry', 'postedBy', 'createdBy']);

        $accountIds = collect($closingJournal->items ?? [])->pluck('account_id')->unique()->toArray();
        $accounts = ChartOfAccount::whereIn('id', $accountIds)->get()->keyBy('id');

        return view('finance.closing-journals.show', compact('closingJournal', 'accounts'));
    }

    public function edit(ClosingJournal $closingJournal)
    {
        if ($closingJournal->status !== 'draft') {
            return redirect()->route('finance.closing-journals.index')
                ->with('error', 'Hanya jurnal penutup dengan status draft yang dapat diedit.');
        }

        $fiscalPeriods = FiscalPeriod::where('is_open', true)->get();
        $accounts = ChartOfAccount::where('is_active', true)->orderBy('code')->get();

        return view('finance.closing-journals.edit', compact('closingJournal', 'fiscalPeriods', 'accounts'));
    }

    public function update(Request $request, ClosingJournal $closingJournal)
    {
        if ($closingJournal->status !== 'draft') {
            return redirect()->route('finance.closing-journals.index')
                ->with('error', 'Hanya jurnal penutup dengan status draft yang dapat diubah.');
        }

        $validated = $request->validate([
            'closing_type' => 'required|in:month_end,year_end,adjustment',
            'fiscal_period_id' => 'required|exists:fiscal_periods,id',
            'description' => 'required|max:500',
            'items' => 'required|array|min:1',
            'items.*.account_id' => 'required|exists:chart_of_accounts,id',
            'items.*.debit' => 'required|numeric|min:0',
            'items.*.credit' => 'required|numeric|min:0',
        ]);

        $closingJournal->update([
            'closing_type' => $validated['closing_type'],
            'fiscal_period_id' => $validated['fiscal_period_id'],
            'description' => $validated['description'],
            'items' => $validated['items'],
        ]);

        return redirect()->route('finance.closing-journals.index')
            ->with('success', 'Jurnal penutup berhasil diupdate.');
    }

    public function destroy(ClosingJournal $closingJournal)
    {
        if ($closingJournal->status !== 'draft') {
            return redirect()->route('finance.closing-journals.index')
                ->with('error', 'Hanya jurnal penutup dengan status draft yang dapat dihapus.');
        }

        $closingJournal->delete();

        return redirect()->route('finance.closing-journals.index')
            ->with('success', 'Jurnal penutup berhasil dihapus.');
    }

    public function post(ClosingJournal $closingJournal)
    {
        if ($closingJournal->status !== 'draft') {
            return redirect()->route('finance.closing-journals.index')
                ->with('error', 'Hanya jurnal penutup dengan status draft yang dapat diposting.');
        }

        $this->closingService->postClosingJournal($closingJournal);

        return redirect()->route('finance.closing-journals.index')
            ->with('success', 'Jurnal penutup berhasil diposting.');
    }

    public function void(ClosingJournal $closingJournal)
    {
        if ($closingJournal->status === 'voided') {
            return redirect()->back()
                ->with('error', 'Jurnal penutup sudah void.');
        }

        $closingJournal->update([
            'status' => 'voided',
            'voided_by' => auth()->id(),
            'voided_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Jurnal penutup berhasil di-void.');
    }
}
