<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\ChartOfAccount;
use App\Models\Master\Company;
use Illuminate\Http\Request;

class ChartOfAccountController extends Controller
{
    public function index()
    {
        $chartOfAccounts = ChartOfAccount::with('parent')->paginate(10);
        return view('master.chart-of-accounts.index', compact('chartOfAccounts'));
    }

    public function create()
    {
        $companies = Company::all();
        $parents = ChartOfAccount::where('is_header', true)->get();
        return view('master.chart-of-accounts.create', compact('companies', 'parents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'code' => 'required|string|max:20',
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'normal_balance' => 'required|in:debit,credit',
            'is_active' => 'boolean',
            'is_header' => 'boolean',
            'parent_id' => 'nullable|exists:chart_of_accounts,id',
            'level' => 'nullable|integer|min:0',
        ]);

        ChartOfAccount::create($validated);

        return redirect()->route('master.chart-of-accounts.index')
            ->with('success', 'Chart of account created successfully.');
    }

    public function edit(ChartOfAccount $chartOfAccount)
    {
        $parents = ChartOfAccount::where('is_header', true)
            ->where('id', '!=', $chartOfAccount->id)
            ->get();
        return view('master.chart-of-accounts.edit', compact('chartOfAccount', 'parents'));
    }

    public function update(Request $request, ChartOfAccount $chartOfAccount)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20',
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'normal_balance' => 'required|in:debit,credit',
            'is_active' => 'boolean',
            'is_header' => 'boolean',
            'parent_id' => 'nullable|exists:chart_of_accounts,id',
            'level' => 'nullable|integer|min:0',
        ]);

        $chartOfAccount->update($validated);

        return redirect()->route('master.chart-of-accounts.index')
            ->with('success', 'Chart of account updated successfully.');
    }

    public function destroy(ChartOfAccount $chartOfAccount)
    {
        $chartOfAccount->delete();
        return redirect()->route('master.chart-of-accounts.index')
            ->with('success', 'Chart of account deleted successfully.');
    }
}
