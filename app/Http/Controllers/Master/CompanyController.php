<?php

namespace App\Http\Controllers\Master;

use App\Exports\Master\CompanyExport;
use App\Http\Controllers\Controller;
use App\Models\Master\Company;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::paginate(10);
        return view('master.companies.index', compact('companies'));
    }

    public function create()
    {
        return view('master.companies.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:companies,code',
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'tax_id' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        Company::create($validated);

        return redirect()->route('master.companies.index')
            ->with('success', 'Company created successfully.');
    }

    public function edit(Company $company)
    {
        return view('master.companies.edit', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:companies,code,' . $company->id,
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'tax_id' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        $company->update($validated);

        return redirect()->route('master.companies.index')
            ->with('success', 'Company updated successfully.');
    }

    public function destroy(Company $company)
    {
        $company->delete();
        return redirect()->route('master.companies.index')
            ->with('success', 'Company deleted successfully.');
    }

    public function exportExcel()
    {
        return Excel::download(new CompanyExport, 'companies.xlsx');
    }
}
