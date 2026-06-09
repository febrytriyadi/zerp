<?php
namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\DunningLevel;
use App\Models\Finance\DunningRun;
use App\Models\Finance\DunningRunItem;
use App\Models\Master\Customer;
use App\Services\Finance\DunningService;
use Illuminate\Http\Request;

class DunningController extends Controller
{
    public function __construct(protected DunningService $dunningService) {}

    public function index()
    {
        $dunningRuns = DunningRun::with(['dunningLevel', 'createdBy', 'postedBy'])
            ->paginate(10);

        return view('finance.dunning-runs.index', compact('dunningRuns'));
    }

    public function create()
    {
        $levels = DunningLevel::where('is_active', true)->orderBy('name')->get();
        $customers = Customer::where('is_active', true)->orderBy('name')->get();

        return view('finance.dunning-runs.create', compact('levels', 'customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'run_number' => 'required|max:100|unique:dunning_runs',
            'run_date' => 'required|date',
            'dunning_level_id' => 'required|exists:dunning_levels,id',
            'notes' => 'nullable|max:500',
        ]);

        $validated['company_id'] = $request->company_id ?? auth()->user()->company_id ?? 1;
        $validated['branch_id'] = $request->branch_id ?? auth()->user()->branch_id ?? 1;
        $validated['created_by'] = auth()->id();
        $validated['status'] = 'draft';

        DunningRun::create($validated);

        return redirect()->route('finance.dunning-runs.index')
            ->with('success', 'Dunning run berhasil dibuat.');
    }

    public function show(DunningRun $dunningRun)
    {
        $dunningRun->load(['dunningLevel', 'items.customer', 'createdBy', 'postedBy']);

        return view('finance.dunning-runs.show', compact('dunningRun'));
    }

    public function edit(DunningRun $dunningRun)
    {
        if ($dunningRun->status !== 'draft') {
            return redirect()->route('finance.dunning-runs.index')
                ->with('error', 'Hanya dunning run dengan status draft yang dapat diedit.');
        }

        $levels = DunningLevel::where('is_active', true)->orderBy('name')->get();
        $customers = Customer::where('is_active', true)->orderBy('name')->get();

        return view('finance.dunning-runs.edit', compact('dunningRun', 'levels', 'customers'));
    }

    public function update(Request $request, DunningRun $dunningRun)
    {
        if ($dunningRun->status !== 'draft') {
            return redirect()->route('finance.dunning-runs.index')
                ->with('error', 'Hanya dunning run dengan status draft yang dapat diubah.');
        }

        $validated = $request->validate([
            'run_number' => 'required|max:100|unique:dunning_runs,run_number,' . $dunningRun->id,
            'run_date' => 'required|date',
            'dunning_level_id' => 'required|exists:dunning_levels,id',
            'notes' => 'nullable|max:500',
        ]);

        $dunningRun->update($validated);

        return redirect()->route('finance.dunning-runs.index')
            ->with('success', 'Dunning run berhasil diupdate.');
    }

    public function destroy(DunningRun $dunningRun)
    {
        if ($dunningRun->status !== 'draft') {
            return redirect()->route('finance.dunning-runs.index')
                ->with('error', 'Hanya dunning run dengan status draft yang dapat dihapus.');
        }

        $dunningRun->delete();

        return redirect()->route('finance.dunning-runs.index')
            ->with('success', 'Dunning run berhasil dihapus.');
    }

    public function generate(Request $request)
    {
        $request->validate([
            'dunning_level_id' => 'required|exists:dunning_levels,id',
            'customer_ids' => 'nullable|array',
            'customer_ids.*' => 'exists:customers,id',
        ]);

        $level = DunningLevel::findOrFail($request->dunning_level_id);
        $customerIds = $request->customer_ids ?? [];

        $run = $this->dunningService->generateRun($level, $customerIds);

        return redirect()->route('finance.dunning-runs.show', $run)
            ->with('success', 'Dunning run berhasil digenerate.');
    }

    public function post(DunningRun $dunningRun)
    {
        if ($dunningRun->status !== 'draft') {
            return redirect()->route('finance.dunning-runs.index')
                ->with('error', 'Hanya dunning run dengan status draft yang dapat diposting.');
        }

        $this->dunningService->postRun($dunningRun);

        return redirect()->route('finance.dunning-runs.show', $dunningRun)
            ->with('success', 'Dunning run berhasil diposting.');
    }
}
