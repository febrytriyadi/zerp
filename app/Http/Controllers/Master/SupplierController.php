<?php

namespace App\Http\Controllers\Master;

use App\Exports\Master\SupplierExport;
use App\Http\Controllers\Controller;
use App\Models\Master\Branch;
use App\Models\Master\ChartOfAccount;
use App\Models\Master\Company;
use App\Models\Master\PaymentTerm;
use App\Models\Master\Supplier;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::with('company', 'branch')->paginate(10);
        return view('master.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        $companies = Company::all();
        $branches = Branch::all();
        $paymentTerms = PaymentTerm::all();
        $chartOfAccounts = ChartOfAccount::all();
        return view('master.suppliers.create', compact('companies', 'branches', 'paymentTerms', 'chartOfAccounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'required|exists:branches,id',
            'code' => 'required|string|max:20',
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'tax_id' => 'nullable|string|max:50',
            'payment_term_id' => 'nullable|exists:payment_terms,id',
            'chart_of_account_id' => 'nullable|exists:chart_of_accounts,id',
            'is_active' => 'boolean',
        ]);

        Supplier::create($validated);

        return redirect()->route('master.suppliers.index')
            ->with('success', 'Supplier created successfully.');
    }

    public function edit(Supplier $supplier)
    {
        $companies = Company::all();
        $branches = Branch::all();
        $paymentTerms = PaymentTerm::all();
        $chartOfAccounts = ChartOfAccount::all();
        return view('master.suppliers.edit', compact('supplier', 'companies', 'branches', 'paymentTerms', 'chartOfAccounts'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20',
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'tax_id' => 'nullable|string|max:50',
            'payment_term_id' => 'nullable|exists:payment_terms,id',
            'chart_of_account_id' => 'nullable|exists:chart_of_accounts,id',
            'is_active' => 'boolean',
        ]);

        $supplier->update($validated);

        return redirect()->route('master.suppliers.index')
            ->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('master.suppliers.index')
            ->with('success', 'Supplier deleted successfully.');
    }

    public function exportExcel()
    {
        return Excel::download(new SupplierExport, 'suppliers.xlsx');
    }
}
