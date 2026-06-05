<?php

namespace App\Http\Controllers\Purchasing;

use App\Http\Controllers\Controller;
use App\Models\Master\Currency;
use App\Models\Master\PaymentTerm;
use App\Models\Master\Supplier;
use App\Models\Master\Warehouse;
use App\Models\Purchasing\PurchaseOrder;
use App\Models\Purchasing\PurchaseRequest;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        $orders = PurchaseOrder::with('supplier')->paginate(10);
        return view('purchasing.orders.index', compact('orders'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $purchaseRequests = PurchaseRequest::all();
        $paymentTerms = PaymentTerm::all();
        $currencies = Currency::all();
        $warehouses = Warehouse::all();
        return view('purchasing.orders.create', compact('suppliers', 'purchaseRequests', 'paymentTerms', 'currencies', 'warehouses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'required|exists:branches,id',
            'order_number' => 'required|string|max:50',
            'order_date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_request_id' => 'nullable|exists:purchase_requests,id',
            'payment_term_id' => 'nullable|exists:payment_terms,id',
            'currency_id' => 'nullable|exists:currencies,id',
            'exchange_rate' => 'nullable|numeric|min:0',
            'tax_rate_id' => 'nullable|exists:tax_rates,id',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'description' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['status'] = 'draft';

        PurchaseOrder::create($validated);

        return redirect()->route('purchasing.orders.index')
            ->with('success', 'Purchase order created successfully.');
    }

    public function show(PurchaseOrder $order)
    {
        $order->load('supplier', 'items');
        return view('purchasing.orders.show', compact('order'));
    }

    public function edit(PurchaseOrder $order)
    {
        $suppliers = Supplier::all();
        $purchaseRequests = PurchaseRequest::all();
        $paymentTerms = PaymentTerm::all();
        $currencies = Currency::all();
        $warehouses = Warehouse::all();
        return view('purchasing.orders.edit', compact('order', 'suppliers', 'purchaseRequests', 'paymentTerms', 'currencies', 'warehouses'));
    }

    public function update(Request $request, PurchaseOrder $order)
    {
        $validated = $request->validate([
            'order_date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_request_id' => 'nullable|exists:purchase_requests,id',
            'payment_term_id' => 'nullable|exists:payment_terms,id',
            'currency_id' => 'nullable|exists:currencies,id',
            'exchange_rate' => 'nullable|numeric|min:0',
            'tax_rate_id' => 'nullable|exists:tax_rates,id',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'description' => 'nullable|string',
        ]);

        $order->update($validated);

        return redirect()->route('purchasing.orders.index')
            ->with('success', 'Purchase order updated successfully.');
    }

    public function destroy(PurchaseOrder $order)
    {
        $order->delete();
        return redirect()->route('purchasing.orders.index')
            ->with('success', 'Purchase order deleted successfully.');
    }

    public function submit(PurchaseOrder $order)
    {
        $order->update(['status' => 'submitted']);
        return redirect()->route('purchasing.orders.index')
            ->with('success', 'Purchase order submitted.');
    }

    public function approve(PurchaseOrder $order)
    {
        $order->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
        ]);
        return redirect()->route('purchasing.orders.index')
            ->with('success', 'Purchase order approved.');
    }

    public function cancel(PurchaseOrder $order)
    {
        $order->update(['status' => 'cancelled']);
        return redirect()->route('purchasing.orders.index')
            ->with('success', 'Purchase order cancelled.');
    }

    public function print(PurchaseOrder $order)
    {
        $order->load('supplier', 'items', 'company');
        return view('purchasing.orders.print', compact('order'));
    }
}
