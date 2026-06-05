<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Master\Currency;
use App\Models\Master\Customer;
use App\Models\Master\PaymentTerm;
use App\Models\Sales\SalesQuotation;
use Illuminate\Http\Request;

class SalesQuotationController extends Controller
{
    public function index()
    {
        $quotations = SalesQuotation::with('customer')->paginate(10);
        return view('sales.quotations.index', compact('quotations'));
    }

    public function create()
    {
        $customers = Customer::all();
        $paymentTerms = PaymentTerm::all();
        $currencies = Currency::all();
        return view('sales.quotations.create', compact('customers', 'paymentTerms', 'currencies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'required|exists:branches,id',
            'quotation_number' => 'required|string|max:50',
            'quotation_date' => 'required|date',
            'customer_id' => 'required|exists:customers,id',
            'customer_address' => 'nullable|string',
            'payment_term_id' => 'nullable|exists:payment_terms,id',
            'currency_id' => 'nullable|exists:currencies,id',
            'exchange_rate' => 'nullable|numeric|min:0',
            'tax_rate_id' => 'nullable|exists:tax_rates,id',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['status'] = 'draft';

        SalesQuotation::create($validated);

        return redirect()->route('sales.quotations.index')
            ->with('success', 'Sales quotation created successfully.');
    }

    public function show(SalesQuotation $quotation)
    {
        $quotation->load('customer', 'items');
        return view('sales.quotations.show', compact('quotation'));
    }

    public function edit(SalesQuotation $quotation)
    {
        $customers = Customer::all();
        $paymentTerms = PaymentTerm::all();
        $currencies = Currency::all();
        return view('sales.quotations.edit', compact('quotation', 'customers', 'paymentTerms', 'currencies'));
    }

    public function update(Request $request, SalesQuotation $quotation)
    {
        $validated = $request->validate([
            'quotation_date' => 'required|date',
            'customer_id' => 'required|exists:customers,id',
            'customer_address' => 'nullable|string',
            'payment_term_id' => 'nullable|exists:payment_terms,id',
            'currency_id' => 'nullable|exists:currencies,id',
            'exchange_rate' => 'nullable|numeric|min:0',
            'tax_rate_id' => 'nullable|exists:tax_rates,id',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $quotation->update($validated);

        return redirect()->route('sales.quotations.index')
            ->with('success', 'Sales quotation updated successfully.');
    }

    public function destroy(SalesQuotation $quotation)
    {
        $quotation->delete();
        return redirect()->route('sales.quotations.index')
            ->with('success', 'Sales quotation deleted successfully.');
    }

    public function submit(SalesQuotation $quotation)
    {
        $quotation->update(['status' => 'submitted']);
        return redirect()->route('sales.quotations.index')
            ->with('success', 'Quotation submitted.');
    }

    public function approve(SalesQuotation $quotation)
    {
        $quotation->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
        ]);
        return redirect()->route('sales.quotations.index')
            ->with('success', 'Quotation approved.');
    }

    public function reject(SalesQuotation $quotation)
    {
        $quotation->update(['status' => 'rejected']);
        return redirect()->route('sales.quotations.index')
            ->with('success', 'Quotation rejected.');
    }

    public function convertToSO(SalesQuotation $quotation)
    {
        $quotation->update(['status' => 'converted']);
        return redirect()->route('sales.quotations.index')
            ->with('success', 'Quotation converted to sales order.');
    }
}
