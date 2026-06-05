<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Branch;
use App\Models\Master\Company;
use App\Models\Master\FiscalPeriod;
use App\Services\Accounting\FiscalPeriodService;
use Illuminate\Http\Request;

class FiscalPeriodController extends Controller
{
    public function __construct(
        protected FiscalPeriodService $fiscalPeriodService
    ) {}

    public function index()
    {
        $fiscalPeriods = FiscalPeriod::with('company', 'branch')->paginate(10);
        return view('master.fiscal-periods.index', compact('fiscalPeriods'));
    }

    public function create()
    {
        $companies = Company::all();
        $branches = Branch::all();
        return view('master.fiscal-periods.create', compact('companies', 'branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'required|exists:branches,id',
            'code' => 'required|string|max:20',
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $validated['is_open'] = true;
        $validated['is_closed'] = false;

        FiscalPeriod::create($validated);

        return redirect()->route('master.fiscal-periods.index')
            ->with('success', 'Fiscal period created successfully.');
    }

    public function edit(FiscalPeriod $fiscalPeriod)
    {
        return view('master.fiscal-periods.edit', compact('fiscalPeriod'));
    }

    public function update(Request $request, FiscalPeriod $fiscalPeriod)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20',
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $fiscalPeriod->update($validated);

        return redirect()->route('master.fiscal-periods.index')
            ->with('success', 'Fiscal period updated successfully.');
    }

    public function destroy(FiscalPeriod $fiscalPeriod)
    {
        $fiscalPeriod->delete();
        return redirect()->route('master.fiscal-periods.index')
            ->with('success', 'Fiscal period deleted successfully.');
    }

    public function close(FiscalPeriod $fiscalPeriod)
    {
        $this->fiscalPeriodService->closePeriod($fiscalPeriod);
        return redirect()->route('master.fiscal-periods.index')
            ->with('success', 'Fiscal period closed successfully.');
    }

    public function open(FiscalPeriod $fiscalPeriod)
    {
        $this->fiscalPeriodService->openPeriod($fiscalPeriod);
        return redirect()->route('master.fiscal-periods.index')
            ->with('success', 'Fiscal period opened successfully.');
    }
}
