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

class CashDisbursementController extends Controller
{
    public function __construct(
        protected CashTransactionService $cashTransactionService
    ) {}

    public function index()
    {
        $cashDisbursements = CashTransaction::where('type', 'disbursement')->paginate(10);
        return view('finance.cash-disbursements.index', compact('cashDisbursements'));
    }

    public function create()
    {
        $cashAccounts = CashAccount::all();
        $chartOfAccounts = ChartOfAccount::all();
        $currencies = Currency::all();
        return view('finance.cash-disbursements.create', compact('cashAccounts', 'chartOfAccounts', 'currencies'));
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

        $validated['type'] = 'disbursement';
        $validated['created_by'] = auth()->id();

        $this->cashTransactionService->create($validated);

        return redirect()->route('finance.cash-disbursements.index')
            ->with('success', 'Cash disbursement created successfully.');
    }

    public function show(CashTransaction $cashDisbursement)
    {
        return view('finance.cash-disbursements.show', compact('cashDisbursement'));
    }

    public function edit(CashTransaction $cashDisbursement)
    {
        return view('finance.cash-disbursements.edit', compact('cashDisbursement'));
    }

    public function update(Request $request, CashTransaction $cashDisbursement)
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

        $this->cashTransactionService->update($cashDisbursement, $validated);

        return redirect()->route('finance.cash-disbursements.index')
            ->with('success', 'Cash disbursement updated successfully.');
    }

    public function destroy(CashTransaction $cashDisbursement)
    {
        $cashDisbursement->delete();
        return redirect()->route('finance.cash-disbursements.index')
            ->with('success', 'Cash disbursement deleted successfully.');
    }

    public function submit(CashTransaction $cashDisbursement)
    {
        Gate::authorize('submit', $cashDisbursement);
        $cashDisbursement->update(['status' => 'submitted']);
        return redirect()->route('finance.cash-disbursements.index')
            ->with('success', 'Cash disbursement submitted.');
    }

    public function approve(CashTransaction $cashDisbursement)
    {
        Gate::authorize('approve', $cashDisbursement);
        $cashDisbursement->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
        ]);
        return redirect()->route('finance.cash-disbursements.index')
            ->with('success', 'Cash disbursement approved.');
    }

    public function reject(CashTransaction $cashDisbursement)
    {
        Gate::authorize('reject', $cashDisbursement);
        $cashDisbursement->update(['status' => 'rejected']);
        return redirect()->route('finance.cash-disbursements.index')
            ->with('success', 'Cash disbursement rejected.');
    }

    public function post(CashTransaction $cashDisbursement)
    {
        Gate::authorize('post', $cashDisbursement);
        $this->cashTransactionService->post($cashDisbursement);
        return redirect()->route('finance.cash-disbursements.index')
            ->with('success', 'Cash disbursement posted.');
    }

    public function void(CashTransaction $cashDisbursement)
    {
        Gate::authorize('void', $cashDisbursement);
        $this->cashTransactionService->void($cashDisbursement);
        return redirect()->route('finance.cash-disbursements.index')
            ->with('success', 'Cash disbursement voided.');
    }
}
