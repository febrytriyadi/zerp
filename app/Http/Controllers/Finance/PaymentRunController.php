<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\PaymentRun;
use App\Models\Master\BankAccount;
use App\Models\Master\Supplier;
use App\Services\Finance\PaymentRunService;
use Illuminate\Http\Request;

class PaymentRunController extends Controller
{
    public function __construct(
        protected PaymentRunService $paymentRunService
    ) {}

    public function index()
    {
        $paymentRuns = PaymentRun::with(['createdBy', 'items'])->latest()->paginate(10);
        return view('finance.payment-runs.index', compact('paymentRuns'));
    }

    public function create()
    {
        $suppliers = Supplier::where('is_active', true)->get();
        $bankAccounts = BankAccount::where('is_active', true)->get();
        return view('finance.payment-runs.create', compact('suppliers', 'bankAccounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_ids' => 'nullable|array',
            'supplier_ids.*' => 'exists:suppliers,id',
            'payment_method' => 'required|string|max:50',
            'bank_account_id' => 'nullable|exists:master_bank_accounts,id',
            'run_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        try {
            $run = $this->paymentRunService->generateProposal($validated['supplier_ids'] ?? []);

            $run->update([
                'payment_method' => $validated['payment_method'],
                'bank_account_id' => $validated['bank_account_id'] ?? null,
                'run_date' => $validated['run_date'],
                'notes' => $validated['notes'] ?? null,
            ]);

            return redirect()->route('finance.payment-runs.show', $run)
                ->with('success', 'Payment run berhasil dibuat.');
        } catch (\RuntimeException $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function show(PaymentRun $paymentRun)
    {
        $paymentRun->load(['items.supplier', 'createdBy', 'postedBy', 'bankAccount']);
        return view('finance.payment-runs.show', compact('paymentRun'));
    }

    public function edit(PaymentRun $paymentRun)
    {
        if ($paymentRun->status !== 'draft') {
            return redirect()->route('finance.payment-runs.show', $paymentRun)
                ->with('error', 'Hanya payment run draft yang dapat diedit.');
        }

        $suppliers = Supplier::where('is_active', true)->get();
        $bankAccounts = BankAccount::where('is_active', true)->get();
        return view('finance.payment-runs.edit', compact('paymentRun', 'suppliers', 'bankAccounts'));
    }

    public function update(Request $request, PaymentRun $paymentRun)
    {
        if ($paymentRun->status !== 'draft') {
            return redirect()->route('finance.payment-runs.show', $paymentRun)
                ->with('error', 'Hanya payment run draft yang dapat diupdate.');
        }

        $validated = $request->validate([
            'payment_method' => 'required|string|max:50',
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'run_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $paymentRun->update($validated);

        return redirect()->route('finance.payment-runs.show', $paymentRun)
            ->with('success', 'Payment run berhasil diupdate.');
    }

    public function destroy(PaymentRun $paymentRun)
    {
        if ($paymentRun->status !== 'draft') {
            return redirect()->route('finance.payment-runs.index')
                ->with('error', 'Hanya payment run draft yang dapat dihapus.');
        }

        $paymentRun->delete();

        return redirect()->route('finance.payment-runs.index')
            ->with('success', 'Payment run berhasil dihapus.');
    }

    public function generate(PaymentRun $paymentRun)
    {
        try {
            $newRun = $this->paymentRunService->generateProposal([]);
            return redirect()->route('finance.payment-runs.show', $newRun)
                ->with('success', 'Proposal pembayaran berhasil digenerate.');
        } catch (\RuntimeException $e) {
            return redirect()->route('finance.payment-runs.index')
                ->with('error', $e->getMessage());
        }
    }

    public function post(PaymentRun $paymentRun)
    {
        try {
            $this->paymentRunService->postRun($paymentRun);
            return redirect()->route('finance.payment-runs.show', $paymentRun)
                ->with('success', 'Payment run berhasil diposting.');
        } catch (\RuntimeException $e) {
            return redirect()->route('finance.payment-runs.show', $paymentRun)
                ->with('error', $e->getMessage());
        }
    }

    public function void(PaymentRun $paymentRun)
    {
        try {
            $this->paymentRunService->voidRun($paymentRun);
            return redirect()->route('finance.payment-runs.show', $paymentRun)
                ->with('success', 'Payment run berhasil divoid.');
        } catch (\RuntimeException $e) {
            return redirect()->route('finance.payment-runs.show', $paymentRun)
                ->with('error', $e->getMessage());
        }
    }
}
