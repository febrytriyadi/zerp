<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Inventory\StockAdjustment;
use App\Models\Inventory\StockOpname;
use App\Models\Master\Warehouse;
use Illuminate\Http\Request;

class StockAdjustmentController extends Controller
{
    public function index()
    {
        $stockAdjustments = StockAdjustment::with('warehouse')->paginate(10);
        return view('inventory.stock-adjustments.index', compact('stockAdjustments'));
    }

    public function create()
    {
        $warehouses = Warehouse::all();
        $stockOpnames = StockOpname::all();
        return view('inventory.stock-adjustments.create', compact('warehouses', 'stockOpnames'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'required|exists:branches,id',
            'adjustment_number' => 'required|string|max:50',
            'adjustment_date' => 'required|date',
            'warehouse_id' => 'required|exists:warehouses,id',
            'stock_opname_id' => 'nullable|exists:stock_opnames,id',
            'adjustment_type' => 'required|string|max:50',
            'description' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['status'] = 'draft';

        StockAdjustment::create($validated);

        return redirect()->route('inventory.stock-adjustments.index')
            ->with('success', 'Stock adjustment created successfully.');
    }

    public function show(StockAdjustment $stockAdjustment)
    {
        $stockAdjustment->load('warehouse', 'items');
        return view('inventory.stock-adjustments.show', compact('stockAdjustment'));
    }

    public function edit(StockAdjustment $stockAdjustment)
    {
        return view('inventory.stock-adjustments.edit', compact('stockAdjustment'));
    }

    public function update(Request $request, StockAdjustment $stockAdjustment)
    {
        $validated = $request->validate([
            'adjustment_date' => 'required|date',
            'warehouse_id' => 'required|exists:warehouses,id',
            'adjustment_type' => 'required|string|max:50',
            'description' => 'nullable|string',
        ]);

        $stockAdjustment->update($validated);

        return redirect()->route('inventory.stock-adjustments.index')
            ->with('success', 'Stock adjustment updated successfully.');
    }

    public function destroy(StockAdjustment $stockAdjustment)
    {
        $stockAdjustment->delete();
        return redirect()->route('inventory.stock-adjustments.index')
            ->with('success', 'Stock adjustment deleted successfully.');
    }

    public function post(StockAdjustment $adjustment)
    {
        $adjustment->update([
            'status' => 'posted',
            'posted_at' => now(),
            'posted_by' => auth()->id(),
        ]);
        return redirect()->route('inventory.stock-adjustments.index')
            ->with('success', 'Stock adjustment posted.');
    }
}
