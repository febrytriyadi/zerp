<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Master\Currency;
use App\Models\Master\Customer;
use App\Models\Master\PaymentTerm;
use App\Models\Master\Warehouse;
use App\Models\Sales\SalesOrder;
use App\Models\Sales\SalesQuotation;
use Illuminate\Http\Request;

class SalesOrderController extends Controller
{
    public function index()
    {
        $orders = SalesOrder::with('customer')->paginate(10);
        return view('sales.orders.index', compact('orders'));
    }

    public function create()
    {
        $customers = Customer::all();
        $quotations = SalesQuotation::all();
        $paymentTerms = PaymentTerm::all();
        $currencies = Currency::all();
        $warehouses = Warehouse::all();
        return view('sales.orders.create', compact('customers', 'quotations', 'paymentTerms', 'currencies', 'warehouses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'required|exists:branches,id',
            'order_number' => 'required|string|max:50',
            'order_date' => 'required|date',
            'customer_id' => 'required|exists:customers,id',
            'customer_address' => 'nullable|string',
            'payment_term_id' => 'nullable|exists:payment_terms,id',
            'currency_id' => 'nullable|exists:currencies,id',
            'exchange_rate' => 'nullable|numeric|min:0',
            'tax_rate_id' => 'nullable|exists:tax_rates,id',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'outstanding_amount' => 'nullable|numeric|min:0',
            'down_payment_amount' => 'nullable|numeric|min:0',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'description' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['status'] = 'draft';

        SalesOrder::create($validated);

        return redirect()->route('sales.orders.index')
            ->with('success', 'Sales order created successfully.');
    }

    public function show(SalesOrder $order)
    {
        $order->load('customer', 'items');
        return view('sales.orders.show', compact('order'));
    }

    public function edit(SalesOrder $order)
    {
        $customers = Customer::all();
        $quotations = SalesQuotation::all();
        $paymentTerms = PaymentTerm::all();
        $currencies = Currency::all();
        $warehouses = Warehouse::all();
        return view('sales.orders.edit', compact('order', 'customers', 'quotations', 'paymentTerms', 'currencies', 'warehouses'));
    }

    public function update(Request $request, SalesOrder $order)
    {
        $validated = $request->validate([
            'order_date' => 'required|date',
            'customer_id' => 'required|exists:customers,id',
            'customer_address' => 'nullable|string',
            'payment_term_id' => 'nullable|exists:payment_terms,id',
            'currency_id' => 'nullable|exists:currencies,id',
            'exchange_rate' => 'nullable|numeric|min:0',
            'tax_rate_id' => 'nullable|exists:tax_rates,id',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'down_payment_amount' => 'nullable|numeric|min:0',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'description' => 'nullable|string',
        ]);

        $order->update($validated);

        return redirect()->route('sales.orders.index')
            ->with('success', 'Sales order updated successfully.');
    }

    public function destroy(SalesOrder $order)
    {
        $order->delete();
        return redirect()->route('sales.orders.index')
            ->with('success', 'Sales order deleted successfully.');
    }

    public function submit(SalesOrder $order)
    {
        $order->update(['status' => 'submitted']);
        return redirect()->route('sales.orders.index')
            ->with('success', 'Order submitted.');
    }

    public function approve(SalesOrder $order)
    {
        $order->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
        ]);
        return redirect()->route('sales.orders.index')
            ->with('success', 'Order approved.');
    }

    public function cancel(SalesOrder $order)
    {
        $order->update(['status' => 'cancelled']);
        return redirect()->route('sales.orders.index')
            ->with('success', 'Order cancelled.');
    }
}
