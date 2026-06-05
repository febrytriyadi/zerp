<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\CashTransaction;
use App\Models\Master\CashAccount;
use App\Models\Master\ChartOfAccount;
use App\Models\Master\Currency;
use App\Services\Finance\CashTransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CashReceiptController extends Controller
{
    public function __construct(
        protected CashTransactionService $cashTransactionService
    ) {}

    public function index()
    {
        $cashReceipts = CashTransaction::where('type', 'receipt')->paginate(10);
        return view('finance.cash-receipts.index', compact('cashReceipts'));
    }

    public function create()
    {
        $cashAccounts = CashAccount::all();
        $chartOfAccounts = ChartOfAccount::all();
        $currencies = Currency::all();
        return view('finance.cash-receipts.create', compact('cashAccounts', 'chartOfAccounts', 'currencies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'required|exists:branches,id',
            'transaction_date' => 'required|date',
            'cash_account_id' => 'required|exists:cash_accounts,id',
            'chart_of_account_id' => 'required|exists:chart_of_accounts,id',
            'amount' => 'required|numeric|min:0',
            'currency_id' => 'nullable|exists:currencies,id',
            'exchange_rate' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $validated['type'] = 'receipt';
        $validated['created_by'] = auth()->id();

        $this->cashTransactionService->create($validated);

        return redirect()->route('finance.cash-receipts.index')
            ->with('success', 'Cash receipt created successfully.');
    }

    public function show(CashTransaction $cashReceipt)
    {
        return view('finance.cash-receipts.show', compact('cashReceipt'));
    }

    public function edit(CashTransaction $cashReceipt)
    {
        return view('finance.cash-receipts.edit', compact('cashReceipt'));
    }

    public function update(Request $request, CashTransaction $cashReceipt)
    {
        $validated = $request->validate([
            'transaction_date' => 'required|date',
            'cash_account_id' => 'required|exists:cash_accounts,id',
            'chart_of_account_id' => 'required|exists:chart_of_accounts,id',
            'amount' => 'required|numeric|min:0',
            'currency_id' => 'nullable|exists:currencies,id',
            'exchange_rate' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $this->cashTransactionService->update($cashReceipt, $validated);

        return redirect()->route('finance.cash-receipts.index')
            ->with('success', 'Cash receipt updated successfully.');
    }

    public function destroy(CashTransaction $cashReceipt)
    {
        $cashReceipt->delete();
        return redirect()->route('finance.cash-receipts.index')
            ->with('success', 'Cash receipt deleted successfully.');
    }

    public function submit(CashTransaction $cashReceipt)
    {
        Gate::authorize('submit', $cashReceipt);
        $cashReceipt->update(['status' => 'submitted']);
        return redirect()->route('finance.cash-receipts.index')
            ->with('success', 'Cash receipt submitted.');
    }

    public function approve(CashTransaction $cashReceipt)
    {
        Gate::authorize('approve', $cashReceipt);
        $cashReceipt->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
        ]);
        return redirect()->route('finance.cash-receipts.index')
            ->with('success', 'Cash receipt approved.');
    }

    public function reject(CashTransaction $cashReceipt)
    {
        Gate::authorize('reject', $cashReceipt);
        $cashReceipt->update(['status' => 'rejected']);
        return redirect()->route('finance.cash-receipts.index')
            ->with('success', 'Cash receipt rejected.');
    }

    public function post(CashTransaction $cashReceipt)
    {
        Gate::authorize('post', $cashReceipt);
        $this->cashTransactionService->post($cashReceipt);
        return redirect()->route('finance.cash-receipts.index')
            ->with('success', 'Cash receipt posted.');
    }

    public function void(CashTransaction $cashReceipt)
    {
        Gate::authorize('void', $cashReceipt);
        $this->cashTransactionService->void($cashReceipt);
        return redirect()->route('finance.cash-receipts.index')
            ->with('success', 'Cash receipt voided.');
    }

    public function cancel(CashTransaction $cashReceipt)
    {
        Gate::authorize('cancel', $cashReceipt);
        $cashReceipt->update(['status' => 'cancelled']);
        return redirect()->route('finance.cash-receipts.index')
            ->with('success', 'Cash receipt cancelled.');
    }
}
