<?php

namespace App\Http\Controllers\Purchasing;

use App\Http\Controllers\Controller;
use App\Data\CreateJournalData;
use App\Data\JournalLineData;
use App\Models\Master\BankAccount;
use App\Models\Master\CashAccount;
use App\Models\Master\ChartOfAccount;
use App\Models\Master\Supplier;
use App\Models\Purchasing\PurchaseInvoice;
use App\Models\Purchasing\SupplierPayment;
use App\Services\Accounting\JournalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class SupplierPaymentController extends Controller
{
    public function __construct(
        protected JournalService $journalService
    ) {}

    public function index()
    {
        $supplierPayments = SupplierPayment::with('supplier')->paginate(10);
        return view('purchasing.supplier-payments.index', compact('supplierPayments'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $purchaseInvoices = PurchaseInvoice::all();
        $cashAccounts = CashAccount::all();
        $bankAccounts = BankAccount::all();
        return view('purchasing.supplier-payments.create', compact('suppliers', 'purchaseInvoices', 'cashAccounts', 'bankAccounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'required|exists:branches,id',
            'payment_number' => 'required|string|max:50',
            'payment_date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'payment_method' => 'required|string|max:50',
            'cash_account_id' => 'nullable|exists:cash_accounts,id',
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['status'] = 'draft';

        SupplierPayment::create($validated);

        return redirect()->route('purchasing.supplier-payments.index')
            ->with('success', 'Supplier payment created successfully.');
    }

    public function show(SupplierPayment $supplierPayment)
    {
        $supplierPayment->load('supplier', 'invoices');
        return view('purchasing.supplier-payments.show', compact('supplierPayment'));
    }

    public function edit(SupplierPayment $supplierPayment)
    {
        $suppliers = Supplier::all();
        $purchaseInvoices = PurchaseInvoice::all();
        $cashAccounts = CashAccount::all();
        $bankAccounts = BankAccount::all();
        return view('purchasing.supplier-payments.edit', compact('supplierPayment', 'suppliers', 'purchaseInvoices', 'cashAccounts', 'bankAccounts'));
    }

    public function update(Request $request, SupplierPayment $supplierPayment)
    {
        $validated = $request->validate([
            'payment_date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'payment_method' => 'required|string|max:50',
            'cash_account_id' => 'nullable|exists:cash_accounts,id',
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $supplierPayment->update($validated);

        return redirect()->route('purchasing.supplier-payments.index')
            ->with('success', 'Supplier payment updated successfully.');
    }

    public function destroy(SupplierPayment $supplierPayment)
    {
        $supplierPayment->delete();
        return redirect()->route('purchasing.supplier-payments.index')
            ->with('success', 'Supplier payment deleted successfully.');
    }

    public function post(SupplierPayment $supplierPayment)
    {
        DB::transaction(function () use ($supplierPayment) {
            $apAccountId = ChartOfAccount::where('company_id', $supplierPayment->company_id)->where('code', Config::get('coa.accounts_payable'))->value('id');
            $debitAccountId = $supplierPayment->cash_account_id ?? $supplierPayment->bank_account_id;

            $lines = [];
            $lines[] = new JournalLineData(
                chartOfAccountId: $apAccountId,
                debit: $supplierPayment->amount,
                credit: 0,
                description: 'Accounts payable reduction',
            );
            $lines[] = new JournalLineData(
                chartOfAccountId: $debitAccountId,
                debit: 0,
                credit: $supplierPayment->amount,
                description: 'Supplier payment disbursement',
            );

            $journalData = new CreateJournalData(
                companyId: $supplierPayment->company_id,
                branchId: $supplierPayment->branch_id,
                transactionDate: $supplierPayment->payment_date->toDateString(),
                description: "Supplier payment {$supplierPayment->payment_number}",
                referenceType: 'supplier_payment',
                referenceId: $supplierPayment->id,
                lines: $lines,
            );

            $journal = $this->journalService->createAndPost($journalData);

            $supplierPayment->update([
                'status' => 'posted',
                'journal_entry_id' => $journal->id,
            ]);
        });

        return redirect()->route('purchasing.supplier-payments.index')
            ->with('success', 'Supplier payment posted.');
    }
}
