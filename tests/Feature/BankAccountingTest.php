<?php
namespace Tests\Feature;

use App\Models\Finance\BankStatement;
use App\Models\Finance\BankStatementLine;
use App\Models\Finance\CheckBook;
use App\Models\Finance\BankAccountBalance;
use App\Models\Master\BankAccount;
use App\Models\Master\Branch;
use App\Models\Master\ChartOfAccount;
use App\Models\Master\Company;
use App\Models\Master\Currency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BankAccountingTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Company $company;
    private Branch $branch;
    private BankAccount $bankAccount;
    private Currency $currency;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->company = Company::create([
            'code' => 'BNK',
            'name' => 'Bank Test Co',
            'is_active' => true,
        ]);
        $this->branch = Branch::create([
            'company_id' => $this->company->id,
            'code' => 'HQ',
            'name' => 'Head Office',
            'is_active' => true,
        ]);

        $coa = ChartOfAccount::create([
            'company_id' => $this->company->id,
            'code' => '1-1100',
            'name' => 'Cash & Bank',
            'type' => 'asset',
            'normal_balance' => 'debit',
            'is_active' => true,
        ]);

        $this->currency = Currency::create([
            'code' => 'IDR',
            'name' => 'Indonesian Rupiah',
            'symbol' => 'Rp',
            'is_active' => true,
        ]);

        $this->bankAccount = BankAccount::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'code' => 'BCA-001',
            'bank_name' => 'BCA',
            'account_name' => 'Test Account',
            'account_number' => '1234567890',
            'currency_id' => $this->currency->id,
            'chart_of_account_id' => $coa->id,
            'opening_balance' => 10000000,
            'is_active' => true,
        ]);
    }

    public function test_can_view_bank_statements_index(): void
    {
        BankStatement::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'bank_account_id' => $this->bankAccount->id,
            'statement_number' => 'STMT-202606-00001',
            'statement_date' => '2026-06-30',
            'beginning_balance' => 10000000,
            'ending_balance' => 15000000,
            'total_deposits' => 7000000,
            'total_withdrawals' => 2000000,
            'status' => 'draft',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('finance.bank-statements.index'));

        $response->assertStatus(200);
        $response->assertSee('STMT-202606-00001');
    }

    public function test_can_create_bank_statement(): void
    {
        $response = $this->actingAs($this->user)->post(route('finance.bank-statements.store'), [
            'bank_account_id' => $this->bankAccount->id,
            'statement_number' => 'STMT-202606-00002',
            'statement_date' => '2026-06-30',
            'beginning_balance' => 10000000,
            'ending_balance' => 15000000,
            'total_deposits' => 7000000,
            'total_withdrawals' => 2000000,
            'exchange_rate' => 1,
            'notes' => 'Monthly statement',
        ]);

        $response->assertRedirect(route('finance.bank-statements.index'));
        $this->assertDatabaseHas('bank_statements', [
            'statement_number' => 'STMT-202606-00002',
        ]);
    }

    public function test_can_view_check_books_index(): void
    {
        CheckBook::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'bank_account_id' => $this->bankAccount->id,
            'check_book_number' => 'CB-2026-001',
            'start_number' => '1001',
            'end_number' => '1050',
            'current_number' => '1001',
            'status' => 'active',
            'issued_date' => '2026-06-01',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('finance.check-books.index'));

        $response->assertStatus(200);
        $response->assertSee('CB-2026-001');
    }

    public function test_can_create_check_book(): void
    {
        $response = $this->actingAs($this->user)->post(route('finance.check-books.store'), [
            'bank_account_id' => $this->bankAccount->id,
            'check_book_number' => 'CB-2026-002',
            'start_number' => '2001',
            'end_number' => '2050',
            'current_number' => '2001',
            'status' => 'active',
            'issued_date' => '2026-06-15',
            'notes' => 'Check book for AP',
        ]);

        $response->assertRedirect(route('finance.check-books.index'));
        $this->assertDatabaseHas('check_books', [
            'check_book_number' => 'CB-2026-002',
        ]);
    }

    public function test_can_view_bank_balances_index(): void
    {
        BankAccountBalance::create([
            'bank_account_id' => $this->bankAccount->id,
            'balance_date' => '2026-06-30',
            'opening_balance' => 10000000,
            'total_debit' => 5000000,
            'total_credit' => 2000000,
            'ending_balance' => 13000000,
            'currency_id' => $this->currency->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('finance.bank-balances.index'));

        $response->assertStatus(200);
    }

    public function test_can_delete_bank_statement(): void
    {
        $statement = BankStatement::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'bank_account_id' => $this->bankAccount->id,
            'statement_number' => 'STMT-202606-00003',
            'statement_date' => '2026-06-30',
            'beginning_balance' => 10000000,
            'ending_balance' => 12000000,
            'total_deposits' => 3000000,
            'total_withdrawals' => 1000000,
            'status' => 'draft',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->delete(route('finance.bank-statements.destroy', $statement));

        $response->assertRedirect(route('finance.bank-statements.index'));
        $this->assertSoftDeleted('bank_statements', ['id' => $statement->id]);
    }

    public function test_can_delete_check_book(): void
    {
        $checkBook = CheckBook::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'bank_account_id' => $this->bankAccount->id,
            'check_book_number' => 'CB-2026-003',
            'start_number' => '3001',
            'end_number' => '3050',
            'current_number' => '3001',
            'status' => 'active',
            'issued_date' => '2026-06-01',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->delete(route('finance.check-books.destroy', $checkBook));

        $response->assertRedirect(route('finance.check-books.index'));
        $this->assertSoftDeleted('check_books', ['id' => $checkBook->id]);
    }
}
