<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Data\CreateJournalData;
use App\Data\JournalLineData;
use App\Models\Master\BankAccount;
use App\Models\Master\CashAccount;
use App\Models\Master\ChartOfAccount;
use App\Models\Master\Customer;
use App\Models\Sales\CustomerPayment;
use App\Services\Accounting\JournalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class CustomerPaymentController extends Controller
{
    public function __construct(
        protected JournalService $journalService
    ) {}

    public function index()
    {
        $customerPayments = CustomerPayment::with('customer')->paginate(10);
        return view('sales.customer-payments.index', compact('customerPayments'));
    }

    public function create()
    {
        $customers = Customer::all();
        $cashAccounts = CashAccount::all();
        $bankAccounts = BankAccount::all();
        return view('sales.customer-payments.create', compact('customers', 'cashAccounts', 'bankAccounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'required|exists:branches,id',
            'payment_number' => 'required|string|max:50',
            'payment_date' => 'required|date',
            'customer_id' => 'required|exists:customers,id',
            'payment_method' => 'required|string|max:50',
            'cash_account_id' => 'nullable|exists:cash_accounts,id',
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['status'] = 'draft';

        CustomerPayment::create($validated);

        return redirect()->route('sales.customer-payments.index')
            ->with('success', 'Customer payment created successfully.');
    }

    public function show(CustomerPayment $customerPayment)
    {
        $customerPayment->load('customer', 'invoices');
        return view('sales.customer-payments.show', compact('customerPayment'));
    }

    public function edit(CustomerPayment $customerPayment)
    {
        $customers = Customer::all();
        $cashAccounts = CashAccount::all();
        $bankAccounts = BankAccount::all();
        return view('sales.customer-payments.edit', compact('customerPayment', 'customers', 'cashAccounts', 'bankAccounts'));
    }

    public function update(Request $request, CustomerPayment $customerPayment)
    {
        $validated = $request->validate([
            'payment_date' => 'required|date',
            'customer_id' => 'required|exists:customers,id',
            'payment_method' => 'required|string|max:50',
            'cash_account_id' => 'nullable|exists:cash_accounts,id',
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $customerPayment->update($validated);

        return redirect()->route('sales.customer-payments.index')
            ->with('success', 'Customer payment updated successfully.');
    }

    public function destroy(CustomerPayment $customerPayment)
    {
        $customerPayment->delete();
        return redirect()->route('sales.customer-payments.index')
            ->with('success', 'Customer payment deleted successfully.');
    }

    public function post(CustomerPayment $customerPayment)
    {
        DB::transaction(function () use ($customerPayment) {
            $arAccountId = ChartOfAccount::where('company_id', $customerPayment->company_id)->where('code', Config::get('coa.accounts_receivable'))->value('id');
            $cashAccountId = $customerPayment->cash_account_id;
            $bankAccountId = $customerPayment->bank_account_id;

            $debitAccountId = $cashAccountId ?? $bankAccountId;

            $lines = [];
            $lines[] = new JournalLineData(
                chartOfAccountId: $debitAccountId,
                debit: $customerPayment->amount,
                credit: 0,
                description: 'Customer payment receipt',
            );
            $lines[] = new JournalLineData(
                chartOfAccountId: $arAccountId,
                debit: 0,
                credit: $customerPayment->amount,
                description: 'Accounts receivable reduction',
            );

            $journalData = new CreateJournalData(
                companyId: $customerPayment->company_id,
                branchId: $customerPayment->branch_id,
                transactionDate: $customerPayment->payment_date->toDateString(),
                description: "Customer payment {$customerPayment->payment_number}",
                referenceType: 'customer_payment',
                referenceId: $customerPayment->id,
                lines: $lines,
            );

            $journal = $this->journalService->createAndPost($journalData);

            $customerPayment->update([
                'status' => 'posted',
                'journal_entry_id' => $journal->id,
            ]);
        });

        return redirect()->route('sales.customer-payments.index')
            ->with('success', 'Customer payment posted.');
    }
}
