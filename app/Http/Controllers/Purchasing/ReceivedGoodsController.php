<?php

namespace App\Http\Controllers\Purchasing;

use App\Http\Controllers\Controller;
use App\Models\Master\Supplier;
use App\Models\Master\Warehouse;
use App\Models\Purchasing\PurchaseOrder;
use App\Models\Purchasing\ReceivedGoods;
use Illuminate\Http\Request;

class ReceivedGoodsController extends Controller
{
    public function index()
    {
        $receivedGoods = ReceivedGoods::with('supplier', 'purchaseOrder')->paginate(10);
        return view('purchasing.received-goods.index', compact('receivedGoods'));
    }

    public function create()
    {
        $purchaseOrders = PurchaseOrder::all();
        $suppliers = Supplier::all();
        $warehouses = Warehouse::all();
        return view('purchasing.received-goods.create', compact('purchaseOrders', 'suppliers', 'warehouses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'required|exists:branches,id',
            'receive_number' => 'required|string|max:50',
            'receive_date' => 'required|date',
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'description' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['status'] = 'draft';

        ReceivedGoods::create($validated);

        return redirect()->route('purchasing.received-goods.index')
            ->with('success', 'Received goods created successfully.');
    }

    public function show(ReceivedGoods $receivedGoods)
    {
        $receivedGoods->load('supplier', 'purchaseOrder', 'items');
        return view('purchasing.received-goods.show', compact('receivedGoods'));
    }

    public function edit(ReceivedGoods $receivedGoods)
    {
        $purchaseOrders = PurchaseOrder::all();
        $suppliers = Supplier::all();
        $warehouses = Warehouse::all();
        return view('purchasing.received-goods.edit', compact('receivedGoods', 'purchaseOrders', 'suppliers', 'warehouses'));
    }

    public function update(Request $request, ReceivedGoods $receivedGoods)
    {
        $validated = $request->validate([
            'receive_date' => 'required|date',
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'description' => 'nullable|string',
        ]);

        $receivedGoods->update($validated);

        return redirect()->route('purchasing.received-goods.index')
            ->with('success', 'Received goods updated successfully.');
    }

    public function destroy(ReceivedGoods $receivedGoods)
    {
        $receivedGoods->delete();
        return redirect()->route('purchasing.received-goods.index')
            ->with('success', 'Received goods deleted successfully.');
    }
}
