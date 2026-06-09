<?php
namespace Tests\Feature;

use App\Models\Finance\Accrual;
use App\Models\Finance\ClosingJournal;
use App\Models\Master\Branch;
use App\Models\Master\ChartOfAccount;
use App\Models\Master\Company;
use App\Models\Master\FiscalPeriod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancialClosingTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Company $company;
    private Branch $branch;
    private ChartOfAccount $debitAccount;
    private ChartOfAccount $creditAccount;
    private FiscalPeriod $fiscalPeriod;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->company = Company::create([
            'code' => 'FCL',
            'name' => 'Financial Closing Co',
            'is_active' => true,
        ]);
        $this->branch = Branch::create([
            'company_id' => $this->company->id,
            'code' => 'HQ',
            'name' => 'Head Office',
            'is_active' => true,
        ]);

        $this->debitAccount = ChartOfAccount::create([
            'company_id' => $this->company->id,
            'code' => '1-1000',
            'name' => 'Prepaid Expense',
            'type' => 'asset',
            'normal_balance' => 'debit',
            'is_active' => true,
        ]);

        $this->creditAccount = ChartOfAccount::create([
            'company_id' => $this->company->id,
            'code' => '5-1000',
            'name' => 'Expense Account',
            'type' => 'expense',
            'normal_balance' => 'debit',
            'is_active' => true,
        ]);

        $this->fiscalPeriod = FiscalPeriod::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'code' => '2026-06',
            'name' => 'June 2026',
            'start_date' => '2026-06-01',
            'end_date' => '2026-06-30',
            'is_open' => true,
            'is_closed' => false,
        ]);
    }

    public function test_can_view_accrual_index(): void
    {
        Accrual::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'accrual_number' => 'ACR-202606-00001',
            'accrual_type' => 'accrual',
            'category' => 'accrued_expense',
            'description' => 'Test accrual',
            'total_amount' => 1000000,
            'recognized_amount' => 0,
            'remaining_amount' => 1000000,
            'start_date' => '2026-06-01',
            'end_date' => '2026-08-31',
            'total_periods' => 3,
            'recognized_periods' => 0,
            'amount_per_period' => 333333,
            'debit_account_id' => $this->debitAccount->id,
            'credit_account_id' => $this->creditAccount->id,
            'status' => 'active',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('finance.accruals.index'));

        $response->assertStatus(200);
        $response->assertSee('ACR-202606-00001');
    }

    public function test_can_create_accrual(): void
    {
        $response = $this->actingAs($this->user)->post(route('finance.accruals.store'), [
            'accrual_type' => 'deferral',
            'category' => 'prepaid_expense',
            'description' => 'Prepaid rent',
            'total_amount' => 6000000,
            'start_date' => '2026-06-01',
            'end_date' => '2026-08-31',
            'total_periods' => 3,
            'amount_per_period' => 2000000,
            'debit_account_id' => $this->debitAccount->id,
            'credit_account_id' => $this->creditAccount->id,
        ]);

        $response->assertRedirect(route('finance.accruals.index'));
        $this->assertDatabaseHas('accruals', [
            'description' => 'Prepaid rent',
            'total_amount' => 6000000,
            'status' => 'active',
        ]);
    }

    public function test_can_show_accrual(): void
    {
        $accrual = Accrual::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'accrual_number' => 'ACR-202606-00002',
            'accrual_type' => 'accrual',
            'category' => 'accrued_revenue',
            'description' => 'Accrued revenue test',
            'total_amount' => 5000000,
            'recognized_amount' => 0,
            'remaining_amount' => 5000000,
            'start_date' => '2026-06-01',
            'end_date' => '2026-06-30',
            'total_periods' => 1,
            'recognized_periods' => 0,
            'amount_per_period' => 5000000,
            'debit_account_id' => $this->debitAccount->id,
            'credit_account_id' => $this->creditAccount->id,
            'status' => 'active',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('finance.accruals.show', $accrual));

        $response->assertStatus(200);
        $response->assertSee('Accrued revenue test');
    }

    public function test_can_view_closing_journal_index(): void
    {
        ClosingJournal::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'closing_number' => 'CLM-202606-00001',
            'closing_type' => 'month_end',
            'fiscal_period_id' => $this->fiscalPeriod->id,
            'description' => 'Month end closing June 2026',
            'total_debit' => 0,
            'total_credit' => 0,
            'status' => 'draft',
            'items' => [],
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('finance.closing-journals.index'));

        $response->assertStatus(200);
        $response->assertSee('CLM-202606-00001');
    }

    public function test_can_create_closing_journal(): void
    {
        $response = $this->actingAs($this->user)->post(route('finance.closing-journals.store'), [
            'closing_type' => 'month_end',
            'fiscal_period_id' => $this->fiscalPeriod->id,
            'description' => 'Month end closing',
            'items' => [
                ['account_id' => $this->creditAccount->id, 'debit' => 5000000, 'credit' => 0],
                ['account_id' => $this->debitAccount->id, 'debit' => 0, 'credit' => 5000000],
            ],
        ]);

        $response->assertRedirect(route('finance.closing-journals.index'));
        $this->assertDatabaseHas('closing_journals', [
            'closing_type' => 'month_end',
            'status' => 'draft',
        ]);
    }

    public function test_can_show_closing_journal(): void
    {
        $closingJournal = ClosingJournal::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'closing_number' => 'CLM-202606-00002',
            'closing_type' => 'month_end',
            'fiscal_period_id' => $this->fiscalPeriod->id,
            'description' => 'Month end closing',
            'total_debit' => 0,
            'total_credit' => 0,
            'status' => 'draft',
            'items' => [
                ['account_id' => $this->creditAccount->id, 'debit' => 5000000, 'credit' => 0],
            ],
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('finance.closing-journals.show', $closingJournal));

        $response->assertStatus(200);
        $response->assertSee('CLM-202606-00002');
    }

    public function test_can_delete_accrual(): void
    {
        $accrual = Accrual::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'accrual_number' => 'ACR-202606-00003',
            'accrual_type' => 'accrual',
            'category' => 'accrued_expense',
            'description' => 'To delete',
            'total_amount' => 1000000,
            'recognized_amount' => 0,
            'remaining_amount' => 1000000,
            'start_date' => '2026-06-01',
            'end_date' => '2026-06-30',
            'total_periods' => 1,
            'recognized_periods' => 0,
            'amount_per_period' => 1000000,
            'debit_account_id' => $this->debitAccount->id,
            'credit_account_id' => $this->creditAccount->id,
            'status' => 'active',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->delete(route('finance.accruals.destroy', $accrual));

        $response->assertRedirect(route('finance.accruals.index'));
        $this->assertSoftDeleted('accruals', ['id' => $accrual->id]);
    }

    public function test_can_delete_closing_journal(): void
    {
        $closingJournal = ClosingJournal::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'closing_number' => 'CLM-202606-00003',
            'closing_type' => 'month_end',
            'fiscal_period_id' => $this->fiscalPeriod->id,
            'description' => 'To delete',
            'total_debit' => 0,
            'total_credit' => 0,
            'status' => 'draft',
            'items' => [],
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->delete(route('finance.closing-journals.destroy', $closingJournal));

        $response->assertRedirect(route('finance.closing-journals.index'));
        $this->assertSoftDeleted('closing_journals', ['id' => $closingJournal->id]);
    }
}
