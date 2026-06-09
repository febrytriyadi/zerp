<?php

namespace Tests\Feature;

use App\Models\Finance\FixedAsset;
use App\Models\Finance\AssetDepreciation;
use App\Models\Finance\AssetTransaction;
use App\Models\Master\Branch;
use App\Models\Master\ChartOfAccount;
use App\Models\Master\Company;
use App\Models\Master\FiscalPeriod;
use App\Models\Master\NumberingFormat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssetAccountingTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Company $company;
    private Branch $branch;
    private ChartOfAccount $assetAccount;
    private ChartOfAccount $accumulatedAccount;
    private ChartOfAccount $expenseAccount;
    private FiscalPeriod $fiscalPeriod;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'company_id' => null,
            'branch_id' => null,
        ]);

        $this->company = Company::create([
            'code' => 'ASSET',
            'name' => 'Asset Test Co',
            'is_active' => true,
        ]);

        $this->branch = Branch::create([
            'company_id' => $this->company->id,
            'code' => 'HQ',
            'name' => 'Head Office',
            'is_active' => true,
        ]);

        $this->assetAccount = ChartOfAccount::create([
            'company_id' => $this->company->id,
            'code' => '1-2010',
            'name' => 'Aset Tetap Test',
            'type' => 'asset',
            'normal_balance' => 'debit',
            'is_active' => true,
        ]);

        $this->accumulatedAccount = ChartOfAccount::create([
            'company_id' => $this->company->id,
            'code' => '1-2011',
            'name' => 'Akumulasi Penyusutan Test',
            'type' => 'asset',
            'normal_balance' => 'credit',
            'is_active' => true,
        ]);

        $this->expenseAccount = ChartOfAccount::create([
            'company_id' => $this->company->id,
            'code' => '5-2070',
            'name' => 'Beban Penyusutan Test',
            'type' => 'expense',
            'normal_balance' => 'debit',
            'is_active' => true,
        ]);

        $this->fiscalPeriod = FiscalPeriod::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'code' => '2026-06',
            'name' => 'Juni 2026',
            'start_date' => '2026-06-01',
            'end_date' => '2026-06-30',
            'is_open' => true,
        ]);

        NumberingFormat::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'transaction_type' => 'fixed_asset',
            'format' => '{PREFIX}{YEAR}{MONTH}{NUMBER}',
            'prefix' => 'AST-',
            'next_number' => 1,
            'last_year' => 2026,
            'last_month' => 6,
            'reset_period' => 'monthly',
        ]);
    }

    public function test_can_view_asset_index(): void
    {
        FixedAsset::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'asset_number' => 'AST-202606000001',
            'asset_name' => 'Test Asset 1',
            'asset_category' => 'vehicle',
            'purchase_date' => '2026-06-01',
            'purchase_cost' => 100000000,
            'salvage_value' => 10000000,
            'useful_life_years' => 5,
            'depreciation_method' => 'straight_line',
            'accumulated_depreciation' => 0,
            'book_value' => 100000000,
            'chart_of_account_id' => $this->assetAccount->id,
            'accumulated_depr_account_id' => $this->accumulatedAccount->id,
            'depreciation_expense_account_id' => $this->expenseAccount->id,
            'status' => 'active',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('finance.assets.index'));

        $response->assertStatus(200);
        $response->assertSee('Test Asset 1');
    }

    public function test_can_view_asset_create_form(): void
    {
        $response = $this->actingAs($this->user)->get(route('finance.assets.create'));

        $response->assertStatus(200);
    }

    public function test_can_create_asset(): void
    {
        $response = $this->actingAs($this->user)->post(route('finance.assets.store'), [
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'asset_name' => 'New Vehicle',
            'asset_category' => 'vehicle',
            'purchase_date' => '2026-06-15',
            'purchase_cost' => 200000000,
            'salvage_value' => 20000000,
            'useful_life_years' => 8,
            'depreciation_method' => 'straight_line',
            'location' => 'Jakarta',
        ]);

        $response->assertRedirect(route('finance.assets.index'));
        $this->assertDatabaseHas('fixed_assets', [
            'asset_name' => 'New Vehicle',
            'purchase_cost' => 200000000,
        ]);
        $this->assertDatabaseHas('asset_transactions', [
            'transaction_type' => 'acquisition',
            'amount' => 200000000,
        ]);
    }

    public function test_can_view_asset_detail(): void
    {
        $asset = FixedAsset::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'asset_number' => 'AST-202606000002',
            'asset_name' => 'Detail Asset',
            'asset_category' => 'computer',
            'purchase_date' => '2026-06-01',
            'purchase_cost' => 15000000,
            'salvage_value' => 1000000,
            'useful_life_years' => 4,
            'depreciation_method' => 'straight_line',
            'accumulated_depreciation' => 0,
            'book_value' => 15000000,
            'chart_of_account_id' => $this->assetAccount->id,
            'accumulated_depr_account_id' => $this->accumulatedAccount->id,
            'depreciation_expense_account_id' => $this->expenseAccount->id,
            'status' => 'active',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('finance.assets.show', $asset));

        $response->assertStatus(200);
        $response->assertSee('Detail Asset');
    }

    public function test_can_edit_asset(): void
    {
        $asset = FixedAsset::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'asset_number' => 'AST-202606000003',
            'asset_name' => 'Editable Asset',
            'asset_category' => 'machinery',
            'purchase_date' => '2026-06-01',
            'purchase_cost' => 50000000,
            'salvage_value' => 5000000,
            'useful_life_years' => 10,
            'depreciation_method' => 'straight_line',
            'accumulated_depreciation' => 0,
            'book_value' => 50000000,
            'chart_of_account_id' => $this->assetAccount->id,
            'accumulated_depr_account_id' => $this->accumulatedAccount->id,
            'depreciation_expense_account_id' => $this->expenseAccount->id,
            'status' => 'active',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('finance.assets.edit', $asset));

        $response->assertStatus(200);
        $response->assertSee('Editable Asset');
    }

    public function test_can_update_asset(): void
    {
        $asset = FixedAsset::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'asset_number' => 'AST-202606000004',
            'asset_name' => 'Update Asset',
            'asset_category' => 'furniture',
            'purchase_date' => '2026-06-01',
            'purchase_cost' => 30000000,
            'salvage_value' => 3000000,
            'useful_life_years' => 5,
            'depreciation_method' => 'straight_line',
            'accumulated_depreciation' => 0,
            'book_value' => 30000000,
            'chart_of_account_id' => $this->assetAccount->id,
            'accumulated_depr_account_id' => $this->accumulatedAccount->id,
            'depreciation_expense_account_id' => $this->expenseAccount->id,
            'status' => 'active',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->put(route('finance.assets.update', $asset), [
            'asset_name' => 'Updated Asset Name',
            'asset_category' => 'furniture',
            'location' => 'Bandung',
        ]);

        $response->assertRedirect(route('finance.assets.index'));
        $this->assertDatabaseHas('fixed_assets', [
            'id' => $asset->id,
            'asset_name' => 'Updated Asset Name',
            'location' => 'Bandung',
        ]);
    }

    public function test_can_delete_asset(): void
    {
        $asset = FixedAsset::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'asset_number' => 'AST-202606000005',
            'asset_name' => 'Deletable Asset',
            'asset_category' => 'other',
            'purchase_date' => '2026-06-01',
            'purchase_cost' => 10000000,
            'salvage_value' => 0,
            'useful_life_years' => 3,
            'depreciation_method' => 'straight_line',
            'accumulated_depreciation' => 0,
            'book_value' => 10000000,
            'chart_of_account_id' => $this->assetAccount->id,
            'accumulated_depr_account_id' => $this->accumulatedAccount->id,
            'depreciation_expense_account_id' => $this->expenseAccount->id,
            'status' => 'active',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->delete(route('finance.assets.destroy', $asset));

        $response->assertRedirect(route('finance.assets.index'));
        $this->assertSoftDeleted('fixed_assets', ['id' => $asset->id]);
    }

    public function test_can_calculate_depreciation_straight_line(): void
    {
        $asset = FixedAsset::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'asset_number' => 'AST-202606000006',
            'asset_name' => 'Depreciation Asset',
            'asset_category' => 'building',
            'purchase_date' => '2026-01-01',
            'purchase_cost' => 120000000,
            'salvage_value' => 12000000,
            'useful_life_years' => 10,
            'depreciation_method' => 'straight_line',
            'accumulated_depreciation' => 0,
            'book_value' => 120000000,
            'chart_of_account_id' => $this->assetAccount->id,
            'accumulated_depr_account_id' => $this->accumulatedAccount->id,
            'depreciation_expense_account_id' => $this->expenseAccount->id,
            'status' => 'active',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->post(route('finance.assets.calculate-depreciation', $asset), [
            'period_date' => '2026-06-30',
        ]);

        $response->assertRedirect(route('finance.assets.show', $asset));

        $expectedMonthly = (120000000 - 12000000) / (10 * 12);

        $this->assertDatabaseHas('asset_depreciations', [
            'fixed_asset_id' => $asset->id,
            'depreciation_amount' => $expectedMonthly,
        ]);

        $asset->refresh();
        $this->assertEquals($expectedMonthly, $asset->accumulated_depreciation);
        $this->assertEquals(120000000 - $expectedMonthly, $asset->book_value);
    }

    public function test_can_sell_asset(): void
    {
        $asset = FixedAsset::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'asset_number' => 'AST-202606000007',
            'asset_name' => 'Sold Asset',
            'asset_category' => 'computer',
            'purchase_date' => '2026-01-01',
            'purchase_cost' => 20000000,
            'salvage_value' => 2000000,
            'useful_life_years' => 4,
            'depreciation_method' => 'straight_line',
            'accumulated_depreciation' => 0,
            'book_value' => 20000000,
            'chart_of_account_id' => $this->assetAccount->id,
            'accumulated_depr_account_id' => $this->accumulatedAccount->id,
            'depreciation_expense_account_id' => $this->expenseAccount->id,
            'status' => 'active',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->post(route('finance.assets.sell', $asset), [
            'sale_date' => '2026-06-20',
            'sale_amount' => 15000000,
            'description' => 'Sold to third party',
        ]);

        $response->assertRedirect(route('finance.assets.index'));
        $this->assertDatabaseHas('fixed_assets', [
            'id' => $asset->id,
            'status' => 'sold',
        ]);
        $this->assertDatabaseHas('asset_transactions', [
            'fixed_asset_id' => $asset->id,
            'transaction_type' => 'sale',
            'amount' => 15000000,
        ]);
    }

    public function test_can_retire_asset(): void
    {
        $asset = FixedAsset::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'asset_number' => 'AST-202606000008',
            'asset_name' => 'Retired Asset',
            'asset_category' => 'machinery',
            'purchase_date' => '2026-01-01',
            'purchase_cost' => 80000000,
            'salvage_value' => 8000000,
            'useful_life_years' => 10,
            'depreciation_method' => 'straight_line',
            'accumulated_depreciation' => 0,
            'book_value' => 80000000,
            'chart_of_account_id' => $this->assetAccount->id,
            'accumulated_depr_account_id' => $this->accumulatedAccount->id,
            'depreciation_expense_account_id' => $this->expenseAccount->id,
            'status' => 'active',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->post(route('finance.assets.retire', $asset), [
            'retire_date' => '2026-06-20',
            'description' => 'No longer usable',
        ]);

        $response->assertRedirect(route('finance.assets.index'));
        $this->assertDatabaseHas('fixed_assets', [
            'id' => $asset->id,
            'status' => 'retired',
        ]);
        $this->assertDatabaseHas('asset_transactions', [
            'fixed_asset_id' => $asset->id,
            'transaction_type' => 'retirement',
        ]);
    }

    public function test_can_revalue_asset(): void
    {
        $asset = FixedAsset::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'asset_number' => 'AST-202606000009',
            'asset_name' => 'Revalued Asset',
            'asset_category' => 'land',
            'purchase_date' => '2026-01-01',
            'purchase_cost' => 500000000,
            'salvage_value' => 0,
            'useful_life_years' => 20,
            'depreciation_method' => 'straight_line',
            'accumulated_depreciation' => 0,
            'book_value' => 500000000,
            'chart_of_account_id' => $this->assetAccount->id,
            'accumulated_depr_account_id' => $this->accumulatedAccount->id,
            'depreciation_expense_account_id' => $this->expenseAccount->id,
            'status' => 'active',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->post(route('finance.assets.revalue', $asset), [
            'new_value' => 600000000,
            'revalue_date' => '2026-06-20',
            'description' => 'Market adjustment',
        ]);

        $response->assertRedirect(route('finance.assets.index'));
        $this->assertDatabaseHas('asset_transactions', [
            'fixed_asset_id' => $asset->id,
            'transaction_type' => 'revaluation',
            'amount' => 100000000,
        ]);
    }

    public function test_calculate_monthly_depreciation_straight_line(): void
    {
        $asset = FixedAsset::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'asset_number' => 'AST-202606000010',
            'asset_name' => 'Calc Test',
            'asset_category' => 'furniture',
            'purchase_date' => '2026-01-01',
            'purchase_cost' => 12000000,
            'salvage_value' => 0,
            'useful_life_years' => 5,
            'depreciation_method' => 'straight_line',
            'accumulated_depreciation' => 0,
            'book_value' => 12000000,
            'chart_of_account_id' => $this->assetAccount->id,
            'accumulated_depr_account_id' => $this->accumulatedAccount->id,
            'depreciation_expense_account_id' => $this->expenseAccount->id,
            'status' => 'active',
            'created_by' => $this->user->id,
        ]);

        $service = app(\App\Services\Finance\DepreciationService::class);
        $amount = $service->calculateMonthlyDepreciation($asset, '2026-06-30');

        $this->assertEquals(200000, $amount);
    }

    public function test_asset_status_changes_to_fully_depreciated(): void
    {
        $asset = FixedAsset::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'asset_number' => 'AST-202606000011',
            'asset_name' => 'Full Depr Test',
            'asset_category' => 'computer',
            'purchase_date' => '2026-01-01',
            'purchase_cost' => 2400000,
            'salvage_value' => 0,
            'useful_life_years' => 1,
            'depreciation_method' => 'straight_line',
            'accumulated_depreciation' => 0,
            'book_value' => 2400000,
            'chart_of_account_id' => $this->assetAccount->id,
            'accumulated_depr_account_id' => $this->accumulatedAccount->id,
            'depreciation_expense_account_id' => $this->expenseAccount->id,
            'status' => 'active',
            'created_by' => $this->user->id,
        ]);

        $service = app(\App\Services\Finance\DepreciationService::class);

        $monthlyAmount = $service->calculateMonthlyDepreciation($asset, '2026-06-30');
        $this->assertEquals(200000, $monthlyAmount);
    }
}
