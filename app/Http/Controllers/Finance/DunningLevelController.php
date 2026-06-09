<?php
namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\DunningLevel;
use App\Models\Master\ChartOfAccount;
use Illuminate\Http\Request;

class DunningLevelController extends Controller
{
    public function index()
    {
        $dunningLevels = DunningLevel::with(['company', 'branch', 'chargeAccount'])
            ->paginate(10);

        return view('finance.dunning-levels.index', compact('dunningLevels'));
    }

    public function create()
    {
        $accounts = ChartOfAccount::where('is_active', true)->orderBy('code')->get();

        return view('finance.dunning-levels.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|max:50|unique:dunning_levels',
            'name' => 'required|max:200',
            'days_from' => 'required|integer|min:0',
            'days_to' => 'required|integer|min:0',
            'charge_percent' => 'required|numeric|min:0|max:999.99',
            'charge_amount' => 'required|numeric|min:0',
            'charge_account_id' => 'nullable|exists:chart_of_accounts,id',
            'is_active' => 'boolean',
        ]);

        $validated['company_id'] = $request->company_id ?? auth()->user()->company_id ?? 1;
        $validated['branch_id'] = $request->branch_id ?? auth()->user()->branch_id ?? 1;

        DunningLevel::create($validated);

        return redirect()->route('finance.dunning-levels.index')
            ->with('success', 'Level dunning berhasil dibuat.');
    }

    public function show(DunningLevel $dunningLevel)
    {
        $dunningLevel->load(['company', 'branch', 'chargeAccount']);

        return view('finance.dunning-levels.show', compact('dunningLevel'));
    }

    public function edit(DunningLevel $dunningLevel)
    {
        $accounts = ChartOfAccount::where('is_active', true)->orderBy('code')->get();

        return view('finance.dunning-levels.edit', compact('dunningLevel', 'accounts'));
    }

    public function update(Request $request, DunningLevel $dunningLevel)
    {
        $validated = $request->validate([
            'code' => 'required|max:50|unique:dunning_levels,code,' . $dunningLevel->id,
            'name' => 'required|max:200',
            'days_from' => 'required|integer|min:0',
            'days_to' => 'required|integer|min:0',
            'charge_percent' => 'required|numeric|min:0|max:999.99',
            'charge_amount' => 'required|numeric|min:0',
            'charge_account_id' => 'nullable|exists:chart_of_accounts,id',
            'is_active' => 'boolean',
        ]);

        $dunningLevel->update($validated);

        return redirect()->route('finance.dunning-levels.index')
            ->with('success', 'Level dunning berhasil diupdate.');
    }

    public function destroy(DunningLevel $dunningLevel)
    {
        $dunningLevel->delete();

        return redirect()->route('finance.dunning-levels.index')
            ->with('success', 'Level dunning berhasil dihapus.');
    }
}
