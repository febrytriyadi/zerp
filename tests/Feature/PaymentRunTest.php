<?php

namespace Tests\Feature;

use App\Models\Finance\PaymentRun;
use App\Models\Master\BankAccount;
use App\Models\Master\Branch;
use App\Models\Master\ChartOfAccount;
use App\Models\Master\Company;
use App\Models\Master\Currency;
use App\Models\Master\FiscalPeriod;
use App\Models\Master\NumberingFormat;
use App\Models\Master\PaymentTerm;
use App\Models\Master\Supplier;
use App\Models\Master\TaxRate;
use App\Models\Purchasing\PurchaseInvoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentRunTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Company $company;
    private Branch $branch;
    private Supplier $supplier;
    private ChartOfAccount $coa;
    private PaymentTerm $paymentTerm;
    private Currency $currency;
    private TaxRate $taxRate;

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
        $this->paymentTerm = PaymentTerm::create([
            'code' => 'NET30',
            'name' => 'Net 30',
            'days' => 30,
        ]);
        $this->coa = ChartOfAccount::create([
            'company_id' => $this->company->id,
            'code' => '1-1000',
            'name' => 'Default COA',
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
        $this->taxRate = TaxRate::create([
            'name' => 'PPN 11%',
            'rate' => 11,
            'is_active' => true,
        ]);
        $this->supplier = Supplier::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'code' => 'SUPP-001',
            'name' => 'Test Supplier',
            'phone' => '08123456788',
            'email' => 'supplier@test.com',
            'payment_term_id' => $this->paymentTerm->id,
            'chart_of_account_id' => $this->coa->id,
            'is_active' => true,
        ]);

        NumberingFormat::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'transaction_type' => 'payment_run',
            'format' => '{PREFIX}{YEAR}{MONTH}{NUMBER}',
            'prefix' => 'PR-',
            'next_number' => 1,
            'last_year' => 2026,
            'last_month' => 6,
            'reset_period' => 'monthly',
        ]);

        FiscalPeriod::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'code' => '2026-01',
            'name' => 'Januari 2026',
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
            'is_open' => true,
        ]);
    }

    public function test_can_view_payment_run_index(): void
    {
        $response = $this->actingAs($this->user)->get(route('finance.payment-runs.index'));
        $response->assertStatus(200);
    }

    public function test_can_view_payment_run_create(): void
    {
        $response = $this->actingAs($this->user)->get(route('finance.payment-runs.create'));
        $response->assertStatus(200);
    }

    public function test_can_create_payment_run(): void
    {
        PurchaseInvoice::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'invoice_number' => 'INV-001',
            'invoice_date' => '2026-05-01',
            'due_date' => '2026-05-15',
            'supplier_id' => $this->supplier->id,
            'payment_term_id' => $this->paymentTerm->id,
            'currency_id' => $this->currency->id,
            'exchange_rate' => 1,
            'subtotal' => 100000,
            'tax_amount' => 11000,
            'total' => 111000,
            'outstanding_amount' => 111000,
            'status' => 'posted',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->post(route('finance.payment-runs.store'), [
            'payment_method' => 'bank_transfer',
            'run_date' => '2026-06-09',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('payment_runs', [
            'total_invoices' => 1,
            'total_suppliers' => 1,
            'status' => 'draft',
        ]);
    }

    public function test_can_view_payment_run_show(): void
    {
        $run = PaymentRun::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'run_number' => 'PR-202606-000001',
            'run_date' => '2026-06-09',
            'payment_method' => 'bank_transfer',
            'total_suppliers' => 1,
            'total_invoices' => 1,
            'total_amount' => 100000,
            'status' => 'draft',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('finance.payment-runs.show', $run));
        $response->assertStatus(200);
    }

    public function test_can_post_payment_run(): void
    {
        $run = PaymentRun::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'run_number' => 'PR-202606-000002',
            'run_date' => '2026-06-09',
            'payment_method' => 'bank_transfer',
            'total_suppliers' => 1,
            'total_invoices' => 1,
            'total_amount' => 100000,
            'status' => 'draft',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->post(route('finance.payment-runs.post', $run));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('payment_runs', [
            'id' => $run->id,
            'status' => 'posted',
        ]);
    }

    public function test_can_void_payment_run(): void
    {
        $run = PaymentRun::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'run_number' => 'PR-202606-000003',
            'run_date' => '2026-06-09',
            'payment_method' => 'bank_transfer',
            'total_suppliers' => 1,
            'total_invoices' => 1,
            'total_amount' => 100000,
            'status' => 'draft',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->post(route('finance.payment-runs.void', $run));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('payment_runs', [
            'id' => $run->id,
            'status' => 'voided',
        ]);
    }
}
