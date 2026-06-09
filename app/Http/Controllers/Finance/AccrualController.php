<?php
namespace App\Http\Controllers\Finance;

use App\DTOs\Finance\CreateAccrualData;
use App\Http\Controllers\Controller;
use App\Models\Finance\Accrual;
use App\Models\Master\ChartOfAccount;
use App\Services\Finance\ClosingService;
use Illuminate\Http\Request;

class AccrualController extends Controller
{
    public function index()
    {
        $accruals = Accrual::with(['debitAccount', 'creditAccount'])
            ->paginate(10);

        return view('finance.accruals.index', compact('accruals'));
    }

    public function create()
    {
        $accounts = ChartOfAccount::where('is_active', true)->orderBy('code')->get();

        return view('finance.accruals.create', compact('accounts'));
    }

    public function store(Request $request, ClosingService $closingService)
    {
        $validated = $request->validate([
            'accrual_type' => 'required|in:accrual,deferral',
            'category' => 'required|in:prepaid_expense,accrued_revenue,deferred_revenue,accrued_expense',
            'description' => 'required|max:500',
            'total_amount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'total_periods' => 'required|integer|min:1',
            'amount_per_period' => 'required|numeric|min:0',
            'debit_account_id' => 'required|exists:chart_of_accounts,id',
            'credit_account_id' => 'required|exists:chart_of_accounts,id',
            'notes' => 'nullable|max:1000',
        ]);

        $dto = new CreateAccrualData(
            companyId: $request->company_id ?? auth()->user()->company_id ?? 1,
            branchId: $request->branch_id ?? auth()->user()->branch_id ?? 1,
            accrualType: $validated['accrual_type'],
            category: $validated['category'],
            description: $validated['description'],
            totalAmount: $validated['total_amount'],
            startDate: $validated['start_date'],
            endDate: $validated['end_date'],
            totalPeriods: $validated['total_periods'],
            amountPerPeriod: $validated['amount_per_period'],
            debitAccountId: $validated['debit_account_id'],
            creditAccountId: $validated['credit_account_id'],
            notes: $validated['notes'] ?? null,
        );

        $closingService->createAccrual($dto);

        return redirect()->route('finance.accruals.index')
            ->with('success', 'Akrual / deferral berhasil dibuat.');
    }

    public function show(Accrual $accrual)
    {
        $accrual->load(['debitAccount', 'creditAccount', 'createdBy']);

        return view('finance.accruals.show', compact('accrual'));
    }

    public function edit(Accrual $accrual)
    {
        $accounts = ChartOfAccount::where('is_active', true)->orderBy('code')->get();

        return view('finance.accruals.edit', compact('accrual', 'accounts'));
    }

    public function update(Request $request, Accrual $accrual)
    {
        $validated = $request->validate([
            'accrual_type' => 'required|in:accrual,deferral',
            'category' => 'required|in:prepaid_expense,accrued_revenue,deferred_revenue,accrued_expense',
            'description' => 'required|max:500',
            'total_amount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'total_periods' => 'required|integer|min:1',
            'amount_per_period' => 'required|numeric|min:0',
            'debit_account_id' => 'required|exists:chart_of_accounts,id',
            'credit_account_id' => 'required|exists:chart_of_accounts,id',
            'notes' => 'nullable|max:1000',
        ]);

        $accrual->update($validated);

        return redirect()->route('finance.accruals.index')
            ->with('success', 'Akrual / deferral berhasil diupdate.');
    }

    public function destroy(Accrual $accrual)
    {
        $accrual->delete();

        return redirect()->route('finance.accruals.index')
            ->with('success', 'Akrual / deferral berhasil dihapus.');
    }

    public function recognize(Accrual $accrual, ClosingService $closingService)
    {
        $closingService->recognizeAccrual($accrual);

        return redirect()->back()
            ->with('success', 'Akrual / deferral berhasil direkognisi.');
    }

    public function void(Request $request, Accrual $accrual)
    {
        $accrual->update([
            'status' => 'voided',
            'voided_by' => auth()->id(),
            'voided_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Akrual / deferral berhasil dibatalkan.');
    }
}
