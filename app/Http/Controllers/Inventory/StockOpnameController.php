<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Inventory\StockOpname;
use App\Models\Master\Warehouse;
use Illuminate\Http\Request;

class StockOpnameController extends Controller
{
    public function index()
    {
        $stockOpnames = StockOpname::with('warehouse')->paginate(10);
        return view('inventory.stock-opnames.index', compact('stockOpnames'));
    }

    public function create()
    {
        $warehouses = Warehouse::all();
        return view('inventory.stock-opnames.create', compact('warehouses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'required|exists:branches,id',
            'opname_number' => 'required|string|max:50',
            'opname_date' => 'required|date',
            'warehouse_id' => 'required|exists:warehouses,id',
            'description' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['status'] = 'draft';

        StockOpname::create($validated);

        return redirect()->route('inventory.stock-opnames.index')
            ->with('success', 'Stock opname created successfully.');
    }

    public function show(StockOpname $stockOpname)
    {
        $stockOpname->load('warehouse', 'items');
        return view('inventory.stock-opnames.show', compact('stockOpname'));
    }

    public function edit(StockOpname $stockOpname)
    {
        return view('inventory.stock-opnames.edit', compact('stockOpname'));
    }

    public function update(Request $request, StockOpname $stockOpname)
    {
        $validated = $request->validate([
            'opname_date' => 'required|date',
            'warehouse_id' => 'required|exists:warehouses,id',
            'description' => 'nullable|string',
        ]);

        $stockOpname->update($validated);

        return redirect()->route('inventory.stock-opnames.index')
            ->with('success', 'Stock opname updated successfully.');
    }

    public function destroy(StockOpname $stockOpname)
    {
        $stockOpname->delete();
        return redirect()->route('inventory.stock-opnames.index')
            ->with('success', 'Stock opname deleted successfully.');
    }

    public function process(StockOpname $opname)
    {
        $opname->update(['status' => 'processed']);
        return redirect()->route('inventory.stock-opnames.index')
            ->with('success', 'Stock opname processed.');
    }

    public function generateAdjustment(StockOpname $opname)
    {
        $opname->update(['status' => 'adjustment_generated']);
        return redirect()->route('inventory.stock-opnames.index')
            ->with('success', 'Stock adjustment generated from opname.');
    }
}
