<?php

namespace Tests\Feature;

use App\Models\Master\Branch;
use App\Models\Master\ChartOfAccount;
use App\Models\Master\Company;
use App\Models\Master\Customer;
use App\Models\Master\FiscalPeriod;
use App\Models\Master\PaymentTerm;
use App\Models\Master\Product;
use App\Models\Master\ProductCategory;
use App\Models\Master\Supplier;
use App\Models\Master\Unit;
use App\Models\Master\Warehouse;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterDataTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Company $company;
    private Branch $branch;
    private PaymentTerm $paymentTerm;
    private ChartOfAccount $coa;
    private ProductCategory $productCategory;
    private Unit $unit;

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
        $this->productCategory = ProductCategory::create([
            'company_id' => $this->company->id,
            'code' => 'GEN',
            'name' => 'General',
        ]);
        $this->unit = Unit::create([
            'code' => 'PCS',
            'name' => 'Pieces',
        ]);
    }

    public function test_can_create_company(): void
    {
        $response = $this->actingAs($this->user)->post(route('master.companies.store'), [
            'code' => 'TEST-01',
            'name' => 'Test Company',
            'address' => 'Jl. Test No. 1',
            'phone' => '021-123456',
            'email' => 'test@company.com',
            'tax_id' => '1234567890',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('master.companies.index'));
        $this->assertDatabaseHas('companies', ['code' => 'TEST-01']);
    }

    public function test_can_update_company(): void
    {
        $company = Company::create(['code' => 'UPDATE', 'name' => 'Before', 'is_active' => true]);

        $response = $this->actingAs($this->user)->put(route('master.companies.update', $company), [
            'code' => 'UPDATE',
            'name' => 'After Update',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('master.companies.index'));
        $this->assertDatabaseHas('companies', ['name' => 'After Update']);
    }

    public function test_can_delete_company(): void
    {
        $company = Company::create(['code' => 'DELETE', 'name' => 'To Delete', 'is_active' => true]);

        $response = $this->actingAs($this->user)->delete(route('master.companies.destroy', $company));

        $response->assertRedirect(route('master.companies.index'));
        $this->assertSoftDeleted($company);
    }

    public function test_can_create_chart_of_account(): void
    {
        $response = $this->actingAs($this->user)->post(route('master.chart-of-accounts.store'), [
            'company_id' => $this->company->id,
            'code' => '1-2000',
            'name' => 'Test Asset',
            'type' => 'asset',
            'normal_balance' => 'debit',
            'is_header' => true,
            'is_active' => true,
            'level' => 1,
        ]);

        $response->assertRedirect(route('master.chart-of-accounts.index'));
        $this->assertDatabaseHas('chart_of_accounts', ['code' => '1-1000']);
    }

    public function test_can_create_chart_of_account_with_parent(): void
    {
        $parent = ChartOfAccount::create([
            'company_id' => $this->company->id,
            'code' => '1-0000',
            'name' => 'Parent',
            'type' => 'asset',
            'normal_balance' => 'debit',
            'is_header' => true,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)->post(route('master.chart-of-accounts.store'), [
            'company_id' => $this->company->id,
            'code' => '1-1010',
            'name' => 'Petty Cash',
            'type' => 'asset',
            'normal_balance' => 'debit',
            'is_header' => false,
            'parent_id' => $parent->id,
            'level' => 2,
            'is_active' => true,
        ]);

        $response->assertRedirect(route('master.chart-of-accounts.index'));
        $this->assertDatabaseHas('chart_of_accounts', ['code' => '1-1010', 'parent_id' => $parent->id]);
    }

    public function test_can_update_chart_of_account(): void
    {
        $account = ChartOfAccount::create([
            'company_id' => $this->company->id,
            'code' => '2-1000',
            'name' => 'Before',
            'type' => 'liability',
            'normal_balance' => 'credit',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)->put(route('master.chart-of-accounts.update', $account), [
            'code' => '2-1000',
            'name' => 'After Update',
            'type' => 'liability',
            'normal_balance' => 'credit',
            'is_header' => false,
            'is_active' => true,
        ]);

        $response->assertRedirect(route('master.chart-of-accounts.index'));
        $this->assertDatabaseHas('chart_of_accounts', ['name' => 'After Update']);
    }

    public function test_can_delete_chart_of_account(): void
    {
        $account = ChartOfAccount::create([
            'company_id' => $this->company->id,
            'code' => '9-9999',
            'name' => 'To Delete',
            'type' => 'expense',
            'normal_balance' => 'debit',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)->delete(route('master.chart-of-accounts.destroy', $account));

        $response->assertRedirect(route('master.chart-of-accounts.index'));
        $this->assertDatabaseMissing('chart_of_accounts', ['code' => '9-9999']);
    }

    public function test_can_create_fiscal_period(): void
    {
        $response = $this->actingAs($this->user)->post(route('master.fiscal-periods.store'), [
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'code' => '2026-01',
            'name' => 'Januari 2026',
            'start_date' => '2026-01-01',
            'end_date' => '2026-01-31',
        ]);

        $response->assertRedirect(route('master.fiscal-periods.index'));
        $this->assertDatabaseHas('fiscal_periods', ['code' => '2026-01']);
    }

    public function test_can_create_product(): void
    {
        $response = $this->actingAs($this->user)->post(route('master.products.store'), [
            'company_id' => $this->company->id,
            'category_id' => $this->productCategory->id,
            'code' => 'PRD-001',
            'name' => 'Product Test',
            'unit_id' => $this->unit->id,
            'purchase_price' => 10000,
            'selling_price' => 15000,
            'cost_method' => 'average',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('master.products.index'));
        $this->assertDatabaseHas('products', ['code' => 'PRD-001']);
    }

    public function test_can_update_product(): void
    {
        $product = Product::create([
            'company_id' => $this->company->id,
            'category_id' => $this->productCategory->id,
            'code' => 'PRD-UPD',
            'name' => 'Before',
            'unit_id' => $this->unit->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)->put(route('master.products.update', $product), [
            'code' => 'PRD-UPD',
            'name' => 'After Update',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('master.products.index'));
        $this->assertDatabaseHas('products', ['name' => 'After Update']);
    }

    public function test_can_delete_product(): void
    {
        $product = Product::create([
            'company_id' => $this->company->id,
            'category_id' => $this->productCategory->id,
            'code' => 'PRD-DEL',
            'name' => 'To Delete',
            'unit_id' => $this->unit->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)->delete(route('master.products.destroy', $product));

        $response->assertRedirect(route('master.products.index'));
        $this->assertDatabaseMissing('products', ['code' => 'PRD-DEL']);
    }

    public function test_can_create_customer(): void
    {
        $response = $this->actingAs($this->user)->post(route('master.customers.store'), [
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'code' => 'CUST-001',
            'name' => 'Customer Test',
            'phone' => '08123456789',
            'email' => 'customer@test.com',
            'payment_term_id' => $this->paymentTerm->id,
            'chart_of_account_id' => $this->coa->id,
            'is_active' => true,
        ]);

        $response->assertRedirect(route('master.customers.index'));
        $this->assertDatabaseHas('customers', ['code' => 'CUST-001']);
    }

    public function test_can_update_customer(): void
    {
        $customer = Customer::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'code' => 'CUST-UPD',
            'name' => 'Before',
            'phone' => '08123456789',
            'email' => 'upd@test.com',
            'payment_term_id' => $this->paymentTerm->id,
            'chart_of_account_id' => $this->coa->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)->put(route('master.customers.update', $customer), [
            'code' => 'CUST-UPD',
            'name' => 'After Update',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('master.customers.index'));
        $this->assertDatabaseHas('customers', ['name' => 'After Update']);
    }

    public function test_can_delete_customer(): void
    {
        $customer = Customer::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'code' => 'CUST-DEL',
            'name' => 'To Delete',
            'phone' => '08123456789',
            'email' => 'del@test.com',
            'payment_term_id' => $this->paymentTerm->id,
            'chart_of_account_id' => $this->coa->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)->delete(route('master.customers.destroy', $customer));

        $response->assertRedirect(route('master.customers.index'));
        $this->assertDatabaseMissing('customers', ['code' => 'CUST-DEL']);
    }

    public function test_can_create_supplier(): void
    {
        $response = $this->actingAs($this->user)->post(route('master.suppliers.store'), [
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'code' => 'SUPP-001',
            'name' => 'Supplier Test',
            'phone' => '08123456788',
            'email' => 'supplier@test.com',
            'payment_term_id' => $this->paymentTerm->id,
            'chart_of_account_id' => $this->coa->id,
            'is_active' => true,
        ]);

        $response->assertRedirect(route('master.suppliers.index'));
        $this->assertDatabaseHas('suppliers', ['code' => 'SUPP-001']);
    }

    public function test_can_create_warehouse(): void
    {
        $response = $this->actingAs($this->user)->post(route('master.warehouses.store'), [
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'code' => 'WH-001',
            'name' => 'Main Warehouse',
            'address' => 'Jl. Warehouse No. 1',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('master.warehouses.index'));
        $this->assertDatabaseHas('warehouses', ['code' => 'WH-001']);
    }

    public function test_can_update_warehouse(): void
    {
        $warehouse = Warehouse::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'code' => 'WH-UPD',
            'name' => 'Before',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)->put(route('master.warehouses.update', $warehouse), [
            'code' => 'WH-UPD',
            'name' => 'After Update',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('master.warehouses.index'));
        $this->assertDatabaseHas('warehouses', ['name' => 'After Update']);
    }

    public function test_can_export_companies_to_excel(): void
    {
        $response = $this->actingAs($this->user)->get(route('master.companies.export-excel'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_can_export_customers_to_excel(): void
    {
        $response = $this->actingAs($this->user)->get(route('master.customers.export-excel'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_can_export_suppliers_to_excel(): void
    {
        $response = $this->actingAs($this->user)->get(route('master.suppliers.export-excel'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_can_export_products_to_excel(): void
    {
        $response = $this->actingAs($this->user)->get(route('master.products.export-excel'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_can_update_supplier(): void
    {
        $supplier = Supplier::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'code' => 'SUP-UPD',
            'name' => 'Before',
            'phone' => '021-123456',
            'email' => 'supplier@test.com',
            'payment_term_id' => $this->paymentTerm->id,
            'chart_of_account_id' => $this->coa->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)->put(route('master.suppliers.update', $supplier), [
            'code' => 'SUP-UPD',
            'name' => 'After Update',
            'payment_term_id' => $this->paymentTerm->id,
            'chart_of_account_id' => $this->coa->id,
            'is_active' => true,
        ]);

        $response->assertRedirect(route('master.suppliers.index'));
        $this->assertDatabaseHas('suppliers', ['name' => 'After Update']);
    }

    public function test_can_delete_supplier(): void
    {
        $supplier = Supplier::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'code' => 'SUP-DEL',
            'name' => 'To Delete',
            'phone' => '021-789012',
            'email' => 'supplier-del@test.com',
            'payment_term_id' => $this->paymentTerm->id,
            'chart_of_account_id' => $this->coa->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)->delete(route('master.suppliers.destroy', $supplier));

        $response->assertRedirect(route('master.suppliers.index'));
        $this->assertDatabaseMissing('suppliers', ['id' => $supplier->id]);
    }

    public function test_can_delete_warehouse(): void
    {
        $warehouse = Warehouse::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'code' => 'WH-DEL',
            'name' => 'To Delete',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)->delete(route('master.warehouses.destroy', $warehouse));

        $response->assertRedirect(route('master.warehouses.index'));
        $this->assertDatabaseMissing('warehouses', ['id' => $warehouse->id]);
    }

    public function test_can_close_fiscal_period(): void
    {
        $period = FiscalPeriod::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'code' => '2026-CLOSE',
            'name' => 'Close Test',
            'start_date' => '2026-01-01',
            'end_date' => '2026-01-31',
            'is_open' => true,
            'is_closed' => false,
        ]);

        $response = $this->actingAs($this->user)->post(route('master.fiscal-periods.close', $period));

        $response->assertRedirect(route('master.fiscal-periods.index'));
        $this->assertDatabaseHas('fiscal_periods', [
            'id' => $period->id,
            'is_open' => false,
            'is_closed' => true,
        ]);
    }

    public function test_can_open_fiscal_period(): void
    {
        $period = FiscalPeriod::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'code' => '2026-OPEN',
            'name' => 'Open Test',
            'start_date' => '2026-02-01',
            'end_date' => '2026-02-28',
            'is_open' => false,
            'is_closed' => true,
            'closed_at' => now(),
            'closed_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->post(route('master.fiscal-periods.open', $period));

        $response->assertRedirect(route('master.fiscal-periods.index'));
        $this->assertDatabaseHas('fiscal_periods', [
            'id' => $period->id,
            'is_open' => true,
            'is_closed' => false,
        ]);
    }

    public function test_can_view_company_index(): void
    {
        $response = $this->actingAs($this->user)->get(route('master.companies.index'));
        $response->assertStatus(200);
    }

    public function test_can_view_product_index(): void
    {
        $response = $this->actingAs($this->user)->get(route('master.products.index'));
        $response->assertStatus(200);
    }

    public function test_can_view_customer_index(): void
    {
        $response = $this->actingAs($this->user)->get(route('master.customers.index'));
        $response->assertStatus(200);
    }

    public function test_can_view_supplier_index(): void
    {
        $response = $this->actingAs($this->user)->get(route('master.suppliers.index'));
        $response->assertStatus(200);
    }

    public function test_can_view_chart_of_account_index(): void
    {
        $response = $this->actingAs($this->user)->get(route('master.chart-of-accounts.index'));
        $response->assertStatus(200);
    }

    public function test_can_view_fiscal_period_index(): void
    {
        $response = $this->actingAs($this->user)->get(route('master.fiscal-periods.index'));
        $response->assertStatus(200);
    }

    public function test_can_view_warehouse_index(): void
    {
        $response = $this->actingAs($this->user)->get(route('master.warehouses.index'));
        $response->assertStatus(200);
    }

    public function test_can_update_fiscal_period(): void
    {
        $period = FiscalPeriod::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'code' => '2026-UPD',
            'name' => 'Before',
            'start_date' => '2026-03-01',
            'end_date' => '2026-03-31',
            'is_open' => true,
            'is_closed' => false,
        ]);

        $response = $this->actingAs($this->user)->put(route('master.fiscal-periods.update', $period), [
            'code' => '2026-UPD',
            'name' => 'After Update',
            'start_date' => '2026-03-01',
            'end_date' => '2026-03-31',
        ]);

        $response->assertRedirect(route('master.fiscal-periods.index'));
        $this->assertDatabaseHas('fiscal_periods', [
            'id' => $period->id,
            'name' => 'After Update',
        ]);
    }

    public function test_can_delete_fiscal_period(): void
    {
        $period = FiscalPeriod::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'code' => '2026-DEL',
            'name' => 'To Delete',
            'start_date' => '2026-04-01',
            'end_date' => '2026-04-30',
            'is_open' => true,
            'is_closed' => false,
        ]);

        $response = $this->actingAs($this->user)->delete(route('master.fiscal-periods.destroy', $period));

        $response->assertRedirect(route('master.fiscal-periods.index'));
        $this->assertDatabaseMissing('fiscal_periods', ['id' => $period->id]);
    }
}
