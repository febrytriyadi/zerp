<?php

namespace App\Http\Controllers\Master;

use App\Exports\Master\CustomerExport;
use App\Http\Controllers\Controller;
use App\Models\Master\Branch;
use App\Models\Master\ChartOfAccount;
use App\Models\Master\Company;
use App\Models\Master\Customer;
use App\Models\Master\PaymentTerm;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::with('company', 'branch')->paginate(10);
        return view('master.customers.index', compact('customers'));
    }

    public function create()
    {
        $companies = Company::all();
        $branches = Branch::all();
        $paymentTerms = PaymentTerm::all();
        $chartOfAccounts = ChartOfAccount::all();
        return view('master.customers.create', compact('companies', 'branches', 'paymentTerms', 'chartOfAccounts'));
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
            'credit_limit' => 'nullable|numeric|min:0',
            'payment_term_id' => 'nullable|exists:payment_terms,id',
            'chart_of_account_id' => 'nullable|exists:chart_of_accounts,id',
            'is_active' => 'boolean',
        ]);

        Customer::create($validated);

        return redirect()->route('master.customers.index')
            ->with('success', 'Customer created successfully.');
    }

    public function edit(Customer $customer)
    {
        $companies = Company::all();
        $branches = Branch::all();
        $paymentTerms = PaymentTerm::all();
        $chartOfAccounts = ChartOfAccount::all();
        return view('master.customers.edit', compact('customer', 'companies', 'branches', 'paymentTerms', 'chartOfAccounts'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20',
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'tax_id' => 'nullable|string|max:50',
            'credit_limit' => 'nullable|numeric|min:0',
            'payment_term_id' => 'nullable|exists:payment_terms,id',
            'chart_of_account_id' => 'nullable|exists:chart_of_accounts,id',
            'is_active' => 'boolean',
        ]);

        $customer->update($validated);

        return redirect()->route('master.customers.index')
            ->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('master.customers.index')
            ->with('success', 'Customer deleted successfully.');
    }

    public function exportExcel()
    {
        return Excel::download(new CustomerExport, 'customers.xlsx');
    }
}
