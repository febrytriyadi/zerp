<?php
namespace App\Http\Controllers\Finance;

use App\Data\CreateAssetData;
use App\Http\Controllers\Controller;
use App\Models\Finance\FixedAsset;
use App\Models\Master\ChartOfAccount;
use App\Services\Finance\AssetService;
use App\Services\Finance\DepreciationService;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function __construct(
        protected AssetService $assetService,
        protected DepreciationService $depreciationService,
    ) {}

    public function index()
    {
        $assets = FixedAsset::with(['company', 'branch'])
            ->paginate(10);

        return view('finance.assets.index', compact('assets'));
    }

    public function create()
    {
        $categories = [
            'land' => 'Tanah',
            'building' => 'Bangunan',
            'machinery' => 'Mesin',
            'vehicle' => 'Kendaraan',
            'furniture' => 'Furniture',
            'computer' => 'Komputer',
            'other' => 'Lainnya',
        ];

        $depreciationMethods = [
            'straight_line' => 'Garis Lurus',
            'declining_balance' => 'Saldo Menurun Ganda',
        ];

        $chartOfAccounts = ChartOfAccount::where('type', 'asset')
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        $accumulatedAccounts = ChartOfAccount::where('type', 'asset')
            ->where('is_header', false)
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        $expenseAccounts = ChartOfAccount::where('type', 'expense')
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        return view('finance.assets.create', compact(
            'categories', 'depreciationMethods',
            'chartOfAccounts', 'accumulatedAccounts', 'expenseAccounts'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_name' => 'required|max:200',
            'asset_category' => 'required|in:land,building,machinery,vehicle,furniture,computer,other',
            'purchase_date' => 'required|date',
            'purchase_cost' => 'required|numeric|min:0',
            'salvage_value' => 'required|numeric|min:0',
            'useful_life_years' => 'required|integer|min:1',
            'depreciation_method' => 'required|in:straight_line,declining_balance',
            'location' => 'nullable|max:200',
            'description' => 'nullable',
            'chart_of_account_id' => 'nullable|exists:chart_of_accounts,id',
            'accumulated_depr_account_id' => 'nullable|exists:chart_of_accounts,id',
            'depreciation_expense_account_id' => 'nullable|exists:chart_of_accounts,id',
        ]);

        $data = new CreateAssetData(
            companyId: (int) ($request->company_id ?? auth()->user()->company_id),
            branchId: (int) ($request->branch_id ?? auth()->user()->branch_id),
            assetName: $validated['asset_name'],
            assetCategory: $validated['asset_category'],
            purchaseDate: $validated['purchase_date'],
            purchaseCost: (float) $validated['purchase_cost'],
            salvageValue: (float) $validated['salvage_value'],
            usefulLifeYears: (int) $validated['useful_life_years'],
            depreciationMethod: $validated['depreciation_method'],
            location: $validated['location'] ?? null,
            description: $validated['description'] ?? null,
            chartOfAccountId: $validated['chart_of_account_id'] ?? null,
            accumulatedDeprAccountId: $validated['accumulated_depr_account_id'] ?? null,
            depreciationExpenseAccountId: $validated['depreciation_expense_account_id'] ?? null,
        );

        $this->assetService->create($data);

        return redirect()->route('finance.assets.index')
            ->with('success', 'Aset tetap berhasil dibuat.');
    }

    public function show(FixedAsset $asset)
    {
        $asset->load(['depreciations', 'transactions', 'company', 'branch']);

        return view('finance.assets.show', compact('asset'));
    }

    public function edit(FixedAsset $asset)
    {
        $categories = [
            'land' => 'Tanah',
            'building' => 'Bangunan',
            'machinery' => 'Mesin',
            'vehicle' => 'Kendaraan',
            'furniture' => 'Furniture',
            'computer' => 'Komputer',
            'other' => 'Lainnya',
        ];

        $depreciationMethods = [
            'straight_line' => 'Garis Lurus',
            'declining_balance' => 'Saldo Menurun Ganda',
        ];

        $chartOfAccounts = ChartOfAccount::where('type', 'asset')
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        $accumulatedAccounts = ChartOfAccount::where('type', 'asset')
            ->where('is_header', false)
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        $expenseAccounts = ChartOfAccount::where('type', 'expense')
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        return view('finance.assets.edit', compact(
            'asset', 'categories', 'depreciationMethods',
            'chartOfAccounts', 'accumulatedAccounts', 'expenseAccounts'
        ));
    }

    public function update(Request $request, FixedAsset $asset)
    {
        $validated = $request->validate([
            'asset_name' => 'required|max:200',
            'asset_category' => 'required|in:land,building,machinery,vehicle,furniture,computer,other',
            'location' => 'nullable|max:200',
            'description' => 'nullable',
            'chart_of_account_id' => 'nullable|exists:chart_of_accounts,id',
            'accumulated_depr_account_id' => 'nullable|exists:chart_of_accounts,id',
            'depreciation_expense_account_id' => 'nullable|exists:chart_of_accounts,id',
        ]);

        $asset->update($validated);

        return redirect()->route('finance.assets.index')
            ->with('success', 'Aset tetap berhasil diupdate.');
    }

    public function destroy(FixedAsset $asset)
    {
        $asset->delete();

        return redirect()->route('finance.assets.index')
            ->with('success', 'Aset tetap berhasil dihapus.');
    }

    public function calculateDepreciation(Request $request, FixedAsset $asset)
    {
        $request->validate([
            'period_date' => 'required|date',
        ]);

        $depreciation = $this->depreciationService->generateDepreciation(
            $asset,
            $request->period_date
        );

        return redirect()->route('finance.assets.show', $asset)
            ->with('success', 'Penyusutan periode ' . $request->period_date . ' berhasil dihitung.');
    }

    public function sell(Request $request, FixedAsset $asset)
    {
        $validated = $request->validate([
            'sale_date' => 'required|date',
            'sale_amount' => 'required|numeric|min:0',
            'description' => 'nullable',
        ]);

        $this->assetService->sell(
            $asset,
            $validated['sale_date'],
            (float) $validated['sale_amount'],
            $validated['description'] ?? null,
        );

        return redirect()->route('finance.assets.index')
            ->with('success', 'Aset berhasil dijual.');
    }

    public function retire(Request $request, FixedAsset $asset)
    {
        $request->validate([
            'retire_date' => 'required|date',
            'description' => 'nullable',
        ]);

        $this->assetService->retire(
            $asset,
            $request->retire_date,
            $request->description ?? null,
        );

        return redirect()->route('finance.assets.index')
            ->with('success', 'Aset berhasil diretire.');
    }

    public function revalue(Request $request, FixedAsset $asset)
    {
        $validated = $request->validate([
            'new_value' => 'required|numeric|min:0',
            'revalue_date' => 'required|date',
            'description' => 'nullable',
        ]);

        $this->assetService->revalue(
            $asset,
            (float) $validated['new_value'],
            $validated['revalue_date'],
            $validated['description'] ?? null,
        );

        return redirect()->route('finance.assets.index')
            ->with('success', 'Aset berhasil direvaluasi.');
    }
}
