<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Master\Currency;
use App\Models\Master\Customer;
use App\Models\Master\PaymentTerm;
use App\Models\Sales\SalesInvoice;
use App\Models\Sales\SalesOrder;
use App\Services\Sales\SalesInvoiceService;
use Illuminate\Http\Request;

class SalesInvoiceController extends Controller
{
    public function __construct(
        protected SalesInvoiceService $salesInvoiceService
    ) {}

    public function index()
    {
        $invoices = SalesInvoice::with('customer')->paginate(10);
        return view('sales.invoices.index', compact('invoices'));
    }

    public function create()
    {
        $customers = Customer::all();
        $orders = SalesOrder::all();
        $paymentTerms = PaymentTerm::all();
        $currencies = Currency::all();
        return view('sales.invoices.create', compact('customers', 'orders', 'paymentTerms', 'currencies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'required|exists:branches,id',
            'invoice_number' => 'required|string|max:50',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'customer_id' => 'required|exists:customers,id',
            'sales_order_id' => 'nullable|exists:sales_orders,id',
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

        SalesInvoice::create($validated);

        return redirect()->route('sales.invoices.index')
            ->with('success', 'Sales invoice created successfully.');
    }

    public function show(SalesInvoice $invoice)
    {
        $invoice->load('customer', 'items');
        return view('sales.invoices.show', compact('invoice'));
    }

    public function edit(SalesInvoice $invoice)
    {
        $customers = Customer::all();
        $orders = SalesOrder::all();
        $paymentTerms = PaymentTerm::all();
        $currencies = Currency::all();
        return view('sales.invoices.edit', compact('invoice', 'customers', 'orders', 'paymentTerms', 'currencies'));
    }

    public function update(Request $request, SalesInvoice $invoice)
    {
        $validated = $request->validate([
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'customer_id' => 'required|exists:customers,id',
            'sales_order_id' => 'nullable|exists:sales_orders,id',
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

        return redirect()->route('sales.invoices.index')
            ->with('success', 'Sales invoice updated successfully.');
    }

    public function destroy(SalesInvoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('sales.invoices.index')
            ->with('success', 'Sales invoice deleted successfully.');
    }

    public function submit(SalesInvoice $invoice)
    {
        $invoice->update(['status' => 'submitted']);
        return redirect()->route('sales.invoices.index')
            ->with('success', 'Invoice submitted.');
    }

    public function approve(SalesInvoice $invoice)
    {
        $invoice->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
        ]);
        return redirect()->route('sales.invoices.index')
            ->with('success', 'Invoice approved.');
    }

    public function post(SalesInvoice $invoice)
    {
        $this->salesInvoiceService->post($invoice);
        return redirect()->route('sales.invoices.index')
            ->with('success', 'Invoice posted.');
    }

    public function void(SalesInvoice $invoice)
    {
        $this->salesInvoiceService->void($invoice);
        return redirect()->route('sales.invoices.index')
            ->with('success', 'Invoice voided.');
    }

    public function print(SalesInvoice $invoice)
    {
        $invoice->load('customer', 'items', 'company');
        return view('sales.invoices.print', compact('invoice'));
    }
}
