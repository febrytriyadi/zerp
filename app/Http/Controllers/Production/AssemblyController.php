<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use App\Models\Master\Product;
use App\Models\Master\Warehouse;
use App\Models\Production\Assembly;
use Illuminate\Http\Request;

class AssemblyController extends Controller
{
    public function index()
    {
        $assemblies = Assembly::with('product')->paginate(10);
        return view('production.assemblies.index', compact('assemblies'));
    }

    public function create()
    {
        $products = Product::all();
        $warehouses = Warehouse::all();
        return view('production.assemblies.create', compact('products', 'warehouses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'required|exists:branches,id',
            'assembly_number' => 'required|string|max:50',
            'assembly_date' => 'required|date',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0',
            'warehouse_id' => 'required|exists:warehouses,id',
            'total_cost' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['status'] = 'draft';

        Assembly::create($validated);

        return redirect()->route('production.assemblies.index')
            ->with('success', 'Assembly created successfully.');
    }

    public function show(Assembly $assembly)
    {
        $assembly->load('product', 'warehouse', 'items');
        return view('production.assemblies.show', compact('assembly'));
    }

    public function edit(Assembly $assembly)
    {
        return view('production.assemblies.edit', compact('assembly'));
    }

    public function update(Request $request, Assembly $assembly)
    {
        $validated = $request->validate([
            'assembly_date' => 'required|date',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0',
            'warehouse_id' => 'required|exists:warehouses,id',
            'total_cost' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $assembly->update($validated);

        return redirect()->route('production.assemblies.index')
            ->with('success', 'Assembly updated successfully.');
    }

    public function destroy(Assembly $assembly)
    {
        $assembly->delete();
        return redirect()->route('production.assemblies.index')
            ->with('success', 'Assembly deleted successfully.');
    }

    public function post(Assembly $assembly)
    {
        $assembly->update([
            'status' => 'posted',
            'posted_at' => now(),
            'posted_by' => auth()->id(),
        ]);
        return redirect()->route('production.assemblies.index')
            ->with('success', 'Assembly posted.');
    }
}
