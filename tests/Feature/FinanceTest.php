<?php

namespace Tests\Feature;

use App\Models\Finance\CashTransaction;
use App\Models\Finance\JournalEntry;
use App\Models\Inventory\StockOpname;
use App\Models\Inventory\StockAdjustment;
use App\Models\Master\Branch;
use App\Models\Master\CashAccount;
use App\Models\Master\ChartOfAccount;
use App\Models\Master\Company;
use App\Models\Master\Currency;
use App\Models\Master\Customer;
use App\Models\Master\FiscalPeriod;
use App\Models\Master\NumberingFormat;
use App\Models\Master\PaymentTerm;
use App\Models\Master\Product;
use App\Models\Master\ProductCategory;
use App\Models\Master\Supplier;
use App\Models\Master\TaxRate;
use App\Models\Master\Unit;
use App\Models\Master\Warehouse;
use App\Models\Production\Assembly;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Company $company;
    private Branch $branch;
    private PaymentTerm $paymentTerm;
    private ChartOfAccount $coa;
    private ProductCategory $productCategory;
    private Unit $unit;
    private Currency $currency;
    private TaxRate $taxRate;
    private Customer $customer;
    private Supplier $supplier;
    private Warehouse $warehouse;
    private Product $product;
    private CashAccount $cashAccount;
    private FiscalPeriod $fiscalPeriod;

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
        $this->productCategory = ProductCategory::create([
            'company_id' => $this->company->id,
            'code' => 'GEN',
            'name' => 'General',
        ]);
        $this->unit = Unit::create([
            'code' => 'PCS',
            'name' => 'Pieces',
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
        $this->customer = Customer::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'code' => 'CUST-001',
            'name' => 'Test Customer',
            'phone' => '08123456789',
            'email' => 'customer@test.com',
            'payment_term_id' => $this->paymentTerm->id,
            'chart_of_account_id' => $this->coa->id,
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
        $this->warehouse = Warehouse::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'code' => 'WH-001',
            'name' => 'Main Warehouse',
            'is_active' => true,
        ]);
        $this->product = Product::create([
            'company_id' => $this->company->id,
            'category_id' => $this->productCategory->id,
            'code' => 'PRD-001',
            'name' => 'Test Product',
            'unit_id' => $this->unit->id,
            'purchase_price' => 10000,
            'selling_price' => 15000,
            'cost_method' => 'average',
            'is_active' => true,
        ]);
        $this->cashAccount = CashAccount::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'code' => 'CASH-TEST',
            'name' => 'Test Cash',
            'chart_of_account_id' => $this->coa->id,
            'is_active' => true,
        ]);
        $this->fiscalPeriod = FiscalPeriod::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'code' => '2026-01',
            'name' => 'Januari 2026',
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
            'is_open' => true,
        ]);

        NumberingFormat::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'transaction_type' => 'cash_transaction',
            'format' => '{PREFIX}{YEAR}{MONTH}{NUMBER}',
            'prefix' => 'CT-',
            'next_number' => 1,
            'last_year' => 2026,
            'last_month' => 1,
            'reset_period' => 'monthly',
        ]);

        NumberingFormat::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'transaction_type' => 'journal_entry',
            'format' => '{PREFIX}{YEAR}{MONTH}{NUMBER}',
            'prefix' => 'JE-',
            'next_number' => 1,
            'last_year' => 2026,
            'last_month' => 1,
            'reset_period' => 'monthly',
        ]);
    }

    public function test_can_create_cash_receipt(): void
    {
        $response = $this->actingAs($this->user)->post(route('finance.cash-receipts.store'), [
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'transaction_date' => '2026-06-01',
            'cash_account_id' => $this->cashAccount->id,
            'chart_of_account_id' => $this->coa->id,
            'amount' => 500000,
            'currency_id' => $this->currency->id,
        ]);

        $response->assertRedirect(route('finance.cash-receipts.index'));
        $this->assertDatabaseHas('cash_transactions', [
            'type' => 'receipt',
            'amount' => 500000,
            'created_by' => $this->user->id,
        ]);
    }

    public function test_can_update_cash_receipt(): void
    {
        $receipt = CashTransaction::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'type' => 'receipt',
            'transaction_number' => 'CT-202606-000001',
            'transaction_date' => '2026-06-01',
            'cash_account_id' => $this->cashAccount->id,
            'chart_of_account_id' => $this->coa->id,
            'amount' => 300000,
            'currency_id' => $this->currency->id,
            'created_by' => $this->user->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)->put(route('finance.cash-receipts.update', $receipt), [
            'transaction_date' => '2026-06-01',
            'cash_account_id' => $this->cashAccount->id,
            'chart_of_account_id' => $this->coa->id,
            'amount' => 750000,
        ]);

        $response->assertRedirect(route('finance.cash-receipts.index'));
        $this->assertDatabaseHas('cash_transactions', [
            'id' => $receipt->id,
            'amount' => 750000,
        ]);
    }

    public function test_can_delete_cash_receipt(): void
    {
        $receipt = CashTransaction::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'type' => 'receipt',
            'transaction_number' => 'CT-202606-000002',
            'transaction_date' => '2026-06-01',
            'cash_account_id' => $this->cashAccount->id,
            'chart_of_account_id' => $this->coa->id,
            'amount' => 200000,
            'currency_id' => $this->currency->id,
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->delete(route('finance.cash-receipts.destroy', $receipt));

        $response->assertRedirect(route('finance.cash-receipts.index'));
        $this->assertDatabaseMissing('cash_transactions', ['id' => $receipt->id]);
    }

    public function test_can_create_cash_disbursement(): void
    {
        $response = $this->actingAs($this->user)->post(route('finance.cash-disbursements.store'), [
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'transaction_date' => '2026-06-01',
            'cash_account_id' => $this->cashAccount->id,
            'chart_of_account_id' => $this->coa->id,
            'amount' => 250000,
            'currency_id' => $this->currency->id,
        ]);

        $response->assertRedirect(route('finance.cash-disbursements.index'));
        $this->assertDatabaseHas('cash_transactions', [
            'type' => 'disbursement',
            'amount' => 250000,
            'created_by' => $this->user->id,
        ]);
    }

    public function test_can_update_cash_disbursement(): void
    {
        $disbursement = CashTransaction::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'type' => 'disbursement',
            'transaction_number' => 'CT-202606-000003',
            'transaction_date' => '2026-06-01',
            'cash_account_id' => $this->cashAccount->id,
            'chart_of_account_id' => $this->coa->id,
            'amount' => 100000,
            'currency_id' => $this->currency->id,
            'created_by' => $this->user->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)->put(route('finance.cash-disbursements.update', $disbursement), [
            'transaction_date' => '2026-06-01',
            'cash_account_id' => $this->cashAccount->id,
            'chart_of_account_id' => $this->coa->id,
            'amount' => 180000,
        ]);

        $response->assertRedirect(route('finance.cash-disbursements.index'));
        $this->assertDatabaseHas('cash_transactions', [
            'id' => $disbursement->id,
            'amount' => 180000,
        ]);
    }

    public function test_can_delete_cash_disbursement(): void
    {
        $disbursement = CashTransaction::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'type' => 'disbursement',
            'transaction_number' => 'CT-202606-000004',
            'transaction_date' => '2026-06-01',
            'cash_account_id' => $this->cashAccount->id,
            'chart_of_account_id' => $this->coa->id,
            'amount' => 50000,
            'currency_id' => $this->currency->id,
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->delete(route('finance.cash-disbursements.destroy', $disbursement));

        $response->assertRedirect(route('finance.cash-disbursements.index'));
        $this->assertDatabaseMissing('cash_transactions', ['id' => $disbursement->id]);
    }

    public function test_can_create_journal_entry(): void
    {
        $response = $this->actingAs($this->user)->post(route('accounting.journal-entries.store'), [
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'transaction_date' => '2026-06-01',
            'description' => 'Test journal entry',
            'lines' => [
                [
                    'chart_of_account_id' => $this->coa->id,
                    'debit' => 100000,
                    'credit' => 0,
                ],
                [
                    'chart_of_account_id' => $this->coa->id,
                    'debit' => 0,
                    'credit' => 100000,
                ],
            ],
        ]);

        $response->assertRedirect(route('accounting.journal-entries.index'));
        $this->assertDatabaseHas('journal_entries', [
            'description' => 'Test journal entry',
            'total_debit' => 100000,
            'total_credit' => 100000,
        ]);
    }

    public function test_can_create_stock_opname(): void
    {
        $response = $this->actingAs($this->user)->post(route('inventory.stock-opnames.store'), [
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'opname_number' => 'SO-202606-000001',
            'opname_date' => '2026-06-01',
            'warehouse_id' => $this->warehouse->id,
            'description' => 'Monthly stock opname',
        ]);

        $response->assertRedirect(route('inventory.stock-opnames.index'));
        $this->assertDatabaseHas('stock_opnames', [
            'description' => 'Monthly stock opname',
            'status' => 'draft',
            'created_by' => $this->user->id,
        ]);
    }

    public function test_can_update_stock_opname(): void
    {
        $opname = StockOpname::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'opname_number' => 'SO-202606-000002',
            'opname_date' => '2026-06-01',
            'warehouse_id' => $this->warehouse->id,
            'description' => 'Initial opname',
            'status' => 'draft',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->put(route('inventory.stock-opnames.update', $opname), [
            'opname_date' => '2026-06-01',
            'warehouse_id' => $this->warehouse->id,
            'description' => 'Updated opname',
        ]);

        $response->assertRedirect(route('inventory.stock-opnames.index'));
        $this->assertDatabaseHas('stock_opnames', [
            'id' => $opname->id,
            'description' => 'Updated opname',
        ]);
    }

    public function test_can_delete_stock_opname(): void
    {
        $opname = StockOpname::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'opname_number' => 'SO-202606-000003',
            'opname_date' => '2026-06-01',
            'warehouse_id' => $this->warehouse->id,
            'description' => 'To delete',
            'status' => 'draft',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->delete(route('inventory.stock-opnames.destroy', $opname));

        $response->assertRedirect(route('inventory.stock-opnames.index'));
        $this->assertDatabaseMissing('stock_opnames', ['id' => $opname->id]);
    }

    public function test_can_create_stock_adjustment(): void
    {
        $response = $this->actingAs($this->user)->post(route('inventory.stock-adjustments.store'), [
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'adjustment_number' => 'SA-202606-000001',
            'adjustment_date' => '2026-06-01',
            'warehouse_id' => $this->warehouse->id,
            'adjustment_type' => 'in',
            'description' => 'Stock adjustment',
        ]);

        $response->assertRedirect(route('inventory.stock-adjustments.index'));
        $this->assertDatabaseHas('stock_adjustments', [
            'adjustment_type' => 'in',
            'status' => 'draft',
            'created_by' => $this->user->id,
        ]);
    }

    public function test_can_update_stock_adjustment(): void
    {
        $adjustment = StockAdjustment::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'adjustment_number' => 'SA-202606-000002',
            'adjustment_date' => '2026-06-01',
            'warehouse_id' => $this->warehouse->id,
            'adjustment_type' => 'in',
            'description' => 'Initial adjustment',
            'status' => 'draft',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->put(route('inventory.stock-adjustments.update', $adjustment), [
            'adjustment_date' => '2026-06-01',
            'warehouse_id' => $this->warehouse->id,
            'adjustment_type' => 'out',
            'description' => 'Updated adjustment',
        ]);

        $response->assertRedirect(route('inventory.stock-adjustments.index'));
        $this->assertDatabaseHas('stock_adjustments', [
            'id' => $adjustment->id,
            'adjustment_type' => 'out',
        ]);
    }

    public function test_can_delete_stock_adjustment(): void
    {
        $adjustment = StockAdjustment::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'adjustment_number' => 'SA-202606-000003',
            'adjustment_date' => '2026-06-01',
            'warehouse_id' => $this->warehouse->id,
            'adjustment_type' => 'in',
            'description' => 'To delete',
            'status' => 'draft',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->delete(route('inventory.stock-adjustments.destroy', $adjustment));

        $response->assertRedirect(route('inventory.stock-adjustments.index'));
        $this->assertDatabaseMissing('stock_adjustments', ['id' => $adjustment->id]);
    }

    public function test_can_create_assembly(): void
    {
        $response = $this->actingAs($this->user)->post(route('production.assemblies.store'), [
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'assembly_number' => 'A-202606-000001',
            'assembly_date' => '2026-06-01',
            'product_id' => $this->product->id,
            'quantity' => 10,
            'warehouse_id' => $this->warehouse->id,
        ]);

        $response->assertRedirect(route('production.assemblies.index'));
        $this->assertDatabaseHas('assemblies', [
            'quantity' => 10,
            'status' => 'draft',
            'created_by' => $this->user->id,
        ]);
    }

    public function test_can_update_assembly(): void
    {
        $assembly = Assembly::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'assembly_number' => 'A-202606-000002',
            'assembly_date' => '2026-06-01',
            'product_id' => $this->product->id,
            'quantity' => 5,
            'warehouse_id' => $this->warehouse->id,
            'status' => 'draft',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->put(route('production.assemblies.update', $assembly), [
            'assembly_date' => '2026-06-01',
            'product_id' => $this->product->id,
            'quantity' => 15,
            'warehouse_id' => $this->warehouse->id,
        ]);

        $response->assertRedirect(route('production.assemblies.index'));
        $this->assertDatabaseHas('assemblies', [
            'id' => $assembly->id,
            'quantity' => 15,
        ]);
    }

    public function test_can_delete_assembly(): void
    {
        $assembly = Assembly::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'assembly_number' => 'A-202606-000003',
            'assembly_date' => '2026-06-01',
            'product_id' => $this->product->id,
            'quantity' => 3,
            'warehouse_id' => $this->warehouse->id,
            'status' => 'draft',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->delete(route('production.assemblies.destroy', $assembly));

        $response->assertRedirect(route('production.assemblies.index'));
        $this->assertDatabaseMissing('assemblies', ['id' => $assembly->id]);
    }

    public function test_can_view_cash_book_report(): void
    {
        $response = $this->actingAs($this->user)->get(route('reports.cash-book'));

        $response->assertStatus(200);
        $response->assertSee('Cash Book Report');
    }

    public function test_can_export_cash_book_to_pdf(): void
    {
        $response = $this->actingAs($this->user)->get(route('reports.cash-book.export-pdf'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_can_export_cash_book_to_excel(): void
    {
        $response = $this->actingAs($this->user)->get(route('reports.cash-book.export-excel'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_can_view_cash_receipt_index(): void
    {
        $response = $this->actingAs($this->user)->get(route('finance.cash-receipts.index'));
        $response->assertStatus(200);
    }

    public function test_can_view_cash_disbursement_index(): void
    {
        $response = $this->actingAs($this->user)->get(route('finance.cash-disbursements.index'));
        $response->assertStatus(200);
    }

    public function test_can_view_journal_entry_index(): void
    {
        $response = $this->actingAs($this->user)->get(route('accounting.journal-entries.index'));
        $response->assertStatus(200);
    }

    public function test_can_view_stock_opname_index(): void
    {
        $response = $this->actingAs($this->user)->get(route('inventory.stock-opnames.index'));
        $response->assertStatus(200);
    }

    public function test_can_view_stock_adjustment_index(): void
    {
        $response = $this->actingAs($this->user)->get(route('inventory.stock-adjustments.index'));
        $response->assertStatus(200);
    }

    public function test_can_view_assembly_index(): void
    {
        $response = $this->actingAs($this->user)->get(route('production.assemblies.index'));
        $response->assertStatus(200);
    }

    public function test_can_view_inventory_movements(): void
    {
        $response = $this->actingAs($this->user)->get(route('inventory.movements.index'));
        $response->assertStatus(200);
    }

    public function test_can_view_general_ledger_report(): void
    {
        $response = $this->actingAs($this->user)->get(route('reports.general-ledger'));
        $response->assertStatus(200);
    }

    public function test_can_view_trial_balance_report(): void
    {
        $response = $this->actingAs($this->user)->get(route('reports.trial-balance'));
        $response->assertStatus(200);
    }

    public function test_can_view_balance_sheet_report(): void
    {
        $response = $this->actingAs($this->user)->get(route('reports.balance-sheet'));
        $response->assertStatus(200);
    }

    public function test_can_view_income_statement_report(): void
    {
        $response = $this->actingAs($this->user)->get(route('reports.income-statement'));
        $response->assertStatus(200);
    }

    public function test_can_view_cash_receipt_show(): void
    {
        $receipt = CashTransaction::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'type' => 'receipt',
            'transaction_number' => 'CT-202606-SHOWR',
            'transaction_date' => '2026-06-01',
            'cash_account_id' => $this->cashAccount->id,
            'chart_of_account_id' => $this->coa->id,
            'amount' => 100000,
            'currency_id' => $this->currency->id,
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('finance.cash-receipts.show', $receipt));
        $response->assertStatus(200);
    }
}
