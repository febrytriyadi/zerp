<?php

namespace App\Http\Controllers\Purchasing;

use App\Http\Controllers\Controller;
use App\Models\Master\ChartOfAccount;
use App\Models\Master\Currency;
use App\Models\Master\PaymentTerm;
use App\Models\Master\Supplier;
use App\Models\Purchasing\PurchaseInvoice;
use App\Models\Purchasing\PurchaseOrder;
use App\Models\Purchasing\ReceivedGoods;
use App\Services\Accounting\JournalService;
use App\Services\Accounting\FiscalPeriodService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class PurchaseInvoiceController extends Controller
{
    public function __construct(
        protected JournalService $journalService,
        protected FiscalPeriodService $fiscalPeriodService
    ) {}

    public function index()
    {
        $invoices = PurchaseInvoice::with('supplier')->paginate(10);
        return view('purchasing.invoices.index', compact('invoices'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $purchaseOrders = PurchaseOrder::all();
        $receivedGoods = ReceivedGoods::all();
        $paymentTerms = PaymentTerm::all();
        $currencies = Currency::all();
        return view('purchasing.invoices.create', compact('suppliers', 'purchaseOrders', 'receivedGoods', 'paymentTerms', 'currencies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'required|exists:branches,id',
            'invoice_number' => 'required|string|max:50',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'received_goods_id' => 'nullable|exists:received_goods,id',
            'payment_term_id' => 'nullable|exists:payment_terms,id',
            'currency_id' => 'nullable|exists:currencies,id',
            'exchange_rate' => 'nullable|numeric|min:0',
            'tax_rate_id' => 'nullable|exists:tax_rates,id',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'outstanding_amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['status'] = 'draft';

        PurchaseInvoice::create($validated);

        return redirect()->route('purchasing.invoices.index')
            ->with('success', 'Purchase invoice created successfully.');
    }

    public function show(PurchaseInvoice $invoice)
    {
        $purchaseInvoice = $invoice->load('supplier', 'items');
        return view('purchasing.invoices.show', compact('purchaseInvoice'));
    }

    public function edit(PurchaseInvoice $invoice)
    {
        $purchaseInvoice = $invoice;
        $suppliers = Supplier::all();
        $purchaseOrders = PurchaseOrder::all();
        $receivedGoods = ReceivedGoods::all();
        $paymentTerms = PaymentTerm::all();
        $currencies = Currency::all();
        return view('purchasing.invoices.edit', compact('purchaseInvoice', 'suppliers', 'purchaseOrders', 'receivedGoods', 'paymentTerms', 'currencies'));
    }

    public function update(Request $request, PurchaseInvoice $invoice)
    {
        $validated = $request->validate([
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'payment_term_id' => 'nullable|exists:payment_terms,id',
            'currency_id' => 'nullable|exists:currencies,id',
            'exchange_rate' => 'nullable|numeric|min:0',
            'tax_rate_id' => 'nullable|exists:tax_rates,id',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $invoice->update($validated);

        return redirect()->route('purchasing.invoices.index')
            ->with('success', 'Purchase invoice updated successfully.');
    }

    public function destroy(PurchaseInvoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('purchasing.invoices.index')
            ->with('success', 'Purchase invoice deleted successfully.');
    }

    public function submit(PurchaseInvoice $invoice)
    {
        $invoice->update(['status' => 'submitted']);
        return redirect()->route('purchasing.invoices.index')
            ->with('success', 'Invoice submitted.');
    }

    public function approve(PurchaseInvoice $invoice)
    {
        $invoice->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
        ]);
        return redirect()->route('purchasing.invoices.index')
            ->with('success', 'Invoice approved.');
    }

    public function post(PurchaseInvoice $invoice)
    {
        DB::transaction(function () use ($invoice) {
            $this->fiscalPeriodService->assertDateIsOpen(
                $invoice->company_id,
                $invoice->branch_id,
                $invoice->invoice_date->toDateString()
            );

            $apAccountId = ChartOfAccount::where('company_id', $invoice->company_id)->where('code', Config::get('coa.accounts_payable'))->value('id');
            $expenseAccountId = ChartOfAccount::where('company_id', $invoice->company_id)->where('code', Config::get('coa.purchase_expense'))->value('id');
            $vatInputAccountId = ChartOfAccount::where('company_id', $invoice->company_id)->where('code', Config::get('coa.vat_input'))->value('id');

            $lines = [];
            $lines[] = new \App\Data\JournalLineData(
                chartOfAccountId: $expenseAccountId,
                debit: $invoice->subtotal,
                credit: 0,
                description: 'Purchase expense',
            );

            if ($invoice->tax_amount > 0) {
                $lines[] = new \App\Data\JournalLineData(
                    chartOfAccountId: $vatInputAccountId,
                    debit: $invoice->tax_amount,
                    credit: 0,
                    description: 'VAT input',
                );
            }

            $lines[] = new \App\Data\JournalLineData(
                chartOfAccountId: $apAccountId,
                debit: 0,
                credit: $invoice->total,
                description: 'Accounts payable',
            );

            $journalData = new \App\Data\CreateJournalData(
                companyId: $invoice->company_id,
                branchId: $invoice->branch_id,
                transactionDate: $invoice->invoice_date->toDateString(),
                description: "Purchase invoice {$invoice->invoice_number}",
                referenceType: 'purchase_invoice',
                referenceId: $invoice->id,
                lines: $lines,
            );

            $journal = $this->journalService->createAndPost($journalData);

            $invoice->update([
                'status' => 'posted',
                'posted_at' => now(),
                'posted_by' => auth()->id(),
                'journal_entry_id' => $journal->id,
            ]);
        });

        return redirect()->route('purchasing.invoices.index')
            ->with('success', 'Invoice posted.');
    }

    public function void(PurchaseInvoice $invoice)
    {
        DB::transaction(function () use ($invoice) {
            if ($invoice->journalEntry) {
                $this->journalService->void($invoice->journalEntry);
            }

            $invoice->update([
                'status' => 'voided',
                'voided_at' => now(),
                'voided_by' => auth()->id(),
            ]);
        });

        return redirect()->route('purchasing.invoices.index')
            ->with('success', 'Invoice voided.');
    }
}
