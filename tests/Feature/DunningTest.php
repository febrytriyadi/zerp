<?php

namespace Tests\Feature;

use App\Models\Finance\DunningLevel;
use App\Models\Finance\DunningRun;
use App\Models\Finance\DunningRunItem;
use App\Models\Master\Branch;
use App\Models\Master\ChartOfAccount;
use App\Models\Master\Company;
use App\Models\Master\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DunningTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Company $company;
    private Branch $branch;
    private ChartOfAccount $coa;
    private Customer $customer;
    private DunningLevel $level;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->company = Company::create([
            'code' => 'DEFAULT',
            'name' => 'Default Company',
            'is_active' => true,
        ]);
        $this->branch = Branch::create([
            'company_id' => $this->company->id,
            'code' => 'HQ',
            'name' => 'Head Office',
            'is_active' => true,
        ]);
        $this->coa = ChartOfAccount::create([
            'company_id' => $this->company->id,
            'code' => '1-1000',
            'name' => 'Default COA',
            'type' => 'asset',
            'normal_balance' => 'debit',
            'is_active' => true,
        ]);
        $this->customer = Customer::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'code' => 'CUST-001',
            'name' => 'Test Customer',
            'phone' => '08123456789',
            'email' => 'customer@test.com',
            'is_active' => true,
        ]);
    }

    public function test_can_create_dunning_level(): void
    {
        $response = $this->actingAs($this->user)->post(route('finance.dunning-levels.store'), [
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'code' => 'LVL1',
            'name' => 'Level 1 - Ringan',
            'days_from' => 1,
            'days_to' => 30,
            'charge_percent' => 2.5,
            'charge_amount' => 5000,
            'charge_account_id' => $this->coa->id,
            'is_active' => true,
        ]);

        $response->assertRedirect(route('finance.dunning-levels.index'));
        $this->assertDatabaseHas('dunning_levels', [
            'code' => 'LVL1',
            'name' => 'Level 1 - Ringan',
            'charge_percent' => 2.5,
        ]);
    }

    public function test_can_update_dunning_level(): void
    {
        $level = DunningLevel::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'code' => 'LVL2',
            'name' => 'Level 2',
            'days_from' => 31,
            'days_to' => 60,
            'charge_percent' => 5,
            'charge_amount' => 10000,
            'charge_account_id' => $this->coa->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)->put(route('finance.dunning-levels.update', $level), [
            'code' => 'LVL2',
            'name' => 'Level 2 - Sedang',
            'days_from' => 31,
            'days_to' => 60,
            'charge_percent' => 7.5,
            'charge_amount' => 15000,
            'charge_account_id' => $this->coa->id,
            'is_active' => true,
        ]);

        $response->assertRedirect(route('finance.dunning-levels.index'));
        $this->assertDatabaseHas('dunning_levels', [
            'id' => $level->id,
            'name' => 'Level 2 - Sedang',
            'charge_percent' => 7.5,
        ]);
    }

    public function test_can_delete_dunning_level(): void
    {
        $level = DunningLevel::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'code' => 'LVL3',
            'name' => 'Level 3',
            'days_from' => 61,
            'days_to' => 90,
            'charge_percent' => 10,
            'charge_amount' => 20000,
            'charge_account_id' => $this->coa->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)->delete(route('finance.dunning-levels.destroy', $level));

        $response->assertRedirect(route('finance.dunning-levels.index'));
        $this->assertSoftDeleted('dunning_levels', ['id' => $level->id]);
    }

    public function test_can_create_dunning_run_manually(): void
    {
        $level = DunningLevel::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'code' => 'LVL1',
            'name' => 'Level 1',
            'days_from' => 1,
            'days_to' => 30,
            'charge_percent' => 2,
            'charge_amount' => 5000,
            'charge_account_id' => $this->coa->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)->post(route('finance.dunning-runs.store'), [
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'run_number' => 'DUN-20260609-0001',
            'run_date' => '2026-06-09',
            'dunning_level_id' => $level->id,
            'notes' => 'Test dunning run',
        ]);

        $response->assertRedirect(route('finance.dunning-runs.index'));
        $this->assertDatabaseHas('dunning_runs', [
            'run_number' => 'DUN-20260609-0001',
            'status' => 'draft',
            'created_by' => $this->user->id,
        ]);
    }

    public function test_can_post_dunning_run(): void
    {
        $level = DunningLevel::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'code' => 'LVL1',
            'name' => 'Level 1',
            'days_from' => 1,
            'days_to' => 30,
            'charge_percent' => 2,
            'charge_amount' => 5000,
            'is_active' => true,
        ]);

        $run = DunningRun::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'run_number' => 'DUN-20260609-0002',
            'run_date' => '2026-06-09',
            'dunning_level_id' => $level->id,
            'total_customers' => 1,
            'total_invoices' => 1,
            'total_amount' => 100000,
            'status' => 'draft',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->post(route('finance.dunning-runs.post', $run));

        $response->assertRedirect(route('finance.dunning-runs.show', $run));
        $this->assertDatabaseHas('dunning_runs', [
            'id' => $run->id,
            'status' => 'posted',
            'posted_by' => $this->user->id,
        ]);
    }
}
