<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Branch;
use App\Models\Master\Company;
use App\Models\Master\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index()
    {
        $warehouses = Warehouse::with('company', 'branch')->paginate(10);
        return view('master.warehouses.index', compact('warehouses'));
    }

    public function create()
    {
        $companies = Company::all();
        $branches = Branch::all();
        return view('master.warehouses.create', compact('companies', 'branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'required|exists:branches,id',
            'code' => 'required|string|max:20',
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        Warehouse::create($validated);

        return redirect()->route('master.warehouses.index')
            ->with('success', 'Warehouse created successfully.');
    }

    public function edit(Warehouse $warehouse)
    {
        return view('master.warehouses.edit', compact('warehouse'));
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20',
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $warehouse->update($validated);

        return redirect()->route('master.warehouses.index')
            ->with('success', 'Warehouse updated successfully.');
    }

    public function destroy(Warehouse $warehouse)
    {
        $warehouse->delete();
        return redirect()->route('master.warehouses.index')
            ->with('success', 'Warehouse deleted successfully.');
    }
}
