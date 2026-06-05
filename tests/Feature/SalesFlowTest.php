<?php

namespace Tests\Feature;

use App\Models\Master\Branch;
use App\Models\Master\ChartOfAccount;
use App\Models\Master\Company;
use App\Models\Master\Currency;
use App\Models\Master\Customer;
use App\Models\Master\PaymentTerm;
use App\Models\Master\ProductCategory;
use App\Models\Master\TaxRate;
use App\Models\Master\Unit;
use App\Models\Master\Warehouse;
use App\Models\Sales\SalesOrder;
use App\Models\Finance\CashTransaction;
use App\Models\Master\CashAccount;
use App\Models\Master\Product;
use App\Models\Sales\CustomerPayment;
use App\Models\Sales\SalesInvoice;
use App\Models\Sales\SalesQuotation;
use App\Models\Sales\SalesReturn;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalesFlowTest extends TestCase
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
    private Warehouse $warehouse;
    private CashAccount $cashAccount;
    private Product $product;

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
        $this->currency = Currency::create([
            'code' => 'IDR',
            'name' => 'Indonesian Rupiah',
            'symbol' => 'Rp',
            'is_base' => true,
            'exchange_rate' => 1,
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
            'code' => 'CUST-TEST',
            'name' => 'Test Customer',
            'phone' => '08123456789',
            'email' => 'customer@test.com',
            'payment_term_id' => $this->paymentTerm->id,
            'chart_of_account_id' => $this->coa->id,
            'is_active' => true,
        ]);
        $this->warehouse = Warehouse::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'code' => 'WH-MAIN',
            'name' => 'Main Warehouse',
            'is_active' => true,
        ]);
        $this->product = Product::create([
            'company_id' => $this->company->id,
            'category_id' => $this->productCategory->id,
            'code' => 'PRD-TEST',
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
    }

    public function test_can_create_quotation(): void
    {
        $response = $this->actingAs($this->user)->post(route('sales.quotations.store'), [
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'quotation_number' => 'SQ-2026-0001',
            'quotation_date' => '2026-06-01',
            'customer_id' => $this->customer->id,
            'customer_address' => 'Jl. Customer No. 1',
            'payment_term_id' => $this->paymentTerm->id,
            'currency_id' => $this->currency->id,
            'exchange_rate' => 1,
            'tax_rate_id' => $this->taxRate->id,
            'subtotal' => 100000,
            'tax_amount' => 11000,
            'total' => 111000,
            'description' => 'Test quotation',
        ]);

        $response->assertRedirect(route('sales.quotations.index'));
        $this->assertDatabaseHas('sales_quotations', [
            'customer_id' => $this->customer->id,
            'status' => 'draft',
            'created_by' => $this->user->id,
            'total' => 111000,
        ]);
    }

    public function test_can_update_quotation(): void
    {
        $quotation = SalesQuotation::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'quotation_number' => 'SQ-2026-0001',
            'quotation_date' => '2026-06-01',
            'customer_id' => $this->customer->id,
            'customer_address' => 'Jl. Customer No. 1',
            'payment_term_id' => $this->paymentTerm->id,
            'currency_id' => $this->currency->id,
            'exchange_rate' => 1,
            'subtotal' => 100000,
            'total' => 111000,
            'created_by' => $this->user->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)->put(route('sales.quotations.update', $quotation), [
            'quotation_date' => '2026-06-01',
            'customer_id' => $this->customer->id,
            'subtotal' => 200000,
            'tax_amount' => 22000,
            'total' => 222000,
            'description' => 'Updated quotation',
        ]);

        $response->assertRedirect(route('sales.quotations.index'));
        $this->assertDatabaseHas('sales_quotations', [
            'id' => $quotation->id,
            'total' => 222000,
            'description' => 'Updated quotation',
        ]);
    }

    public function test_can_delete_quotation(): void
    {
        $quotation = SalesQuotation::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'quotation_number' => 'SQ-2026-0001',
            'quotation_date' => '2026-06-01',
            'customer_id' => $this->customer->id,
            'customer_address' => 'Jl. Customer No. 1',
            'payment_term_id' => $this->paymentTerm->id,
            'currency_id' => $this->currency->id,
            'subtotal' => 100000,
            'total' => 111000,
            'created_by' => $this->user->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)->delete(route('sales.quotations.destroy', $quotation));

        $response->assertRedirect(route('sales.quotations.index'));
        $this->assertDatabaseMissing('sales_quotations', ['id' => $quotation->id]);
    }

    public function test_can_submit_quotation(): void
    {
        $quotation = SalesQuotation::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'quotation_number' => 'SQ-2026-0001',
            'quotation_date' => '2026-06-01',
            'customer_id' => $this->customer->id,
            'customer_address' => 'Jl. Customer No. 1',
            'payment_term_id' => $this->paymentTerm->id,
            'currency_id' => $this->currency->id,
            'subtotal' => 100000,
            'total' => 111000,
            'created_by' => $this->user->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)->post(route('sales.quotations.submit', $quotation));

        $response->assertRedirect(route('sales.quotations.index'));
        $this->assertDatabaseHas('sales_quotations', [
            'id' => $quotation->id,
            'status' => 'submitted',
        ]);
    }

    public function test_can_approve_quotation(): void
    {
        $quotation = SalesQuotation::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'quotation_number' => 'SQ-2026-0001',
            'quotation_date' => '2026-06-01',
            'customer_id' => $this->customer->id,
            'customer_address' => 'Jl. Customer No. 1',
            'payment_term_id' => $this->paymentTerm->id,
            'currency_id' => $this->currency->id,
            'subtotal' => 100000,
            'total' => 111000,
            'created_by' => $this->user->id,
            'status' => 'submitted',
        ]);

        $response = $this->actingAs($this->user)->post(route('sales.quotations.approve', $quotation));

        $response->assertRedirect(route('sales.quotations.index'));
        $this->assertDatabaseHas('sales_quotations', [
            'id' => $quotation->id,
            'status' => 'approved',
            'approved_by' => $this->user->id,
        ]);
    }

    public function test_can_reject_quotation(): void
    {
        $quotation = SalesQuotation::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'quotation_number' => 'SQ-2026-0001',
            'quotation_date' => '2026-06-01',
            'customer_id' => $this->customer->id,
            'customer_address' => 'Jl. Customer No. 1',
            'payment_term_id' => $this->paymentTerm->id,
            'currency_id' => $this->currency->id,
            'subtotal' => 100000,
            'total' => 111000,
            'created_by' => $this->user->id,
            'status' => 'submitted',
        ]);

        $response = $this->actingAs($this->user)->post(route('sales.quotations.reject', $quotation));

        $response->assertRedirect(route('sales.quotations.index'));
        $this->assertDatabaseHas('sales_quotations', [
            'id' => $quotation->id,
            'status' => 'rejected',
        ]);
    }

    public function test_can_create_sales_order(): void
    {
        $response = $this->actingAs($this->user)->post(route('sales.orders.store'), [
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'order_number' => 'SO-2026-0001',
            'order_date' => '2026-06-01',
            'customer_id' => $this->customer->id,
            'customer_address' => 'Jl. Customer No. 1',
            'payment_term_id' => $this->paymentTerm->id,
            'currency_id' => $this->currency->id,
            'exchange_rate' => 1,
            'tax_rate_id' => $this->taxRate->id,
            'subtotal' => 100000,
            'tax_amount' => 11000,
            'total' => 111000,
            'down_payment_amount' => 50000,
            'outstanding_amount' => 61000,
            'warehouse_id' => $this->warehouse->id,
            'description' => 'Test sales order',
        ]);

        $response->assertRedirect(route('sales.orders.index'));
        $this->assertDatabaseHas('sales_orders', [
            'customer_id' => $this->customer->id,
            'status' => 'draft',
            'created_by' => $this->user->id,
            'total' => 111000,
            'down_payment_amount' => 50000,
        ]);
    }

    public function test_can_update_sales_order(): void
    {
        $order = SalesOrder::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'order_number' => 'SO-2026-0001',
            'order_date' => '2026-06-01',
            'customer_id' => $this->customer->id,
            'customer_address' => 'Jl. Customer No. 1',
            'payment_term_id' => $this->paymentTerm->id,
            'currency_id' => $this->currency->id,
            'exchange_rate' => 1,
            'subtotal' => 100000,
            'tax_amount' => 11000,
            'total' => 111000,
            'outstanding_amount' => 111000,
            'created_by' => $this->user->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)->put(route('sales.orders.update', $order), [
            'order_date' => '2026-06-01',
            'customer_id' => $this->customer->id,
            'subtotal' => 200000,
            'tax_amount' => 22000,
            'total' => 222000,
            'description' => 'Updated order',
        ]);

        $response->assertRedirect(route('sales.orders.index'));
        $this->assertDatabaseHas('sales_orders', [
            'id' => $order->id,
            'total' => 222000,
            'description' => 'Updated order',
        ]);
    }

    public function test_can_delete_sales_order(): void
    {
        $order = SalesOrder::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'order_number' => 'SO-2026-0001',
            'order_date' => '2026-06-01',
            'customer_id' => $this->customer->id,
            'customer_address' => 'Jl. Customer No. 1',
            'payment_term_id' => $this->paymentTerm->id,
            'currency_id' => $this->currency->id,
            'subtotal' => 100000,
            'tax_amount' => 11000,
            'total' => 111000,
            'outstanding_amount' => 111000,
            'created_by' => $this->user->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)->delete(route('sales.orders.destroy', $order));

        $response->assertRedirect(route('sales.orders.index'));
        $this->assertDatabaseMissing('sales_orders', ['id' => $order->id]);
    }

    public function test_can_submit_sales_order(): void
    {
        $order = SalesOrder::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'order_number' => 'SO-2026-0001',
            'order_date' => '2026-06-01',
            'customer_id' => $this->customer->id,
            'customer_address' => 'Jl. Customer No. 1',
            'payment_term_id' => $this->paymentTerm->id,
            'currency_id' => $this->currency->id,
            'subtotal' => 100000,
            'tax_amount' => 11000,
            'total' => 111000,
            'outstanding_amount' => 111000,
            'created_by' => $this->user->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)->post(route('sales.orders.submit', $order));

        $response->assertRedirect(route('sales.orders.index'));
        $this->assertDatabaseHas('sales_orders', [
            'id' => $order->id,
            'status' => 'submitted',
        ]);
    }

    public function test_can_approve_sales_order(): void
    {
        $order = SalesOrder::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'order_number' => 'SO-2026-0001',
            'order_date' => '2026-06-01',
            'customer_id' => $this->customer->id,
            'customer_address' => 'Jl. Customer No. 1',
            'payment_term_id' => $this->paymentTerm->id,
            'currency_id' => $this->currency->id,
            'subtotal' => 100000,
            'tax_amount' => 11000,
            'total' => 111000,
            'outstanding_amount' => 111000,
            'created_by' => $this->user->id,
            'status' => 'submitted',
        ]);

        $response = $this->actingAs($this->user)->post(route('sales.orders.approve', $order));

        $response->assertRedirect(route('sales.orders.index'));
        $this->assertDatabaseHas('sales_orders', [
            'id' => $order->id,
            'status' => 'approved',
            'approved_by' => $this->user->id,
        ]);
    }

    public function test_can_cancel_sales_order(): void
    {
        $order = SalesOrder::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'order_number' => 'SO-2026-0001',
            'order_date' => '2026-06-01',
            'customer_id' => $this->customer->id,
            'customer_address' => 'Jl. Customer No. 1',
            'payment_term_id' => $this->paymentTerm->id,
            'currency_id' => $this->currency->id,
            'subtotal' => 100000,
            'tax_amount' => 11000,
            'total' => 111000,
            'outstanding_amount' => 111000,
            'created_by' => $this->user->id,
            'status' => 'submitted',
        ]);

        $response = $this->actingAs($this->user)->post(route('sales.orders.cancel', $order));

        $response->assertRedirect(route('sales.orders.index'));
        $this->assertDatabaseHas('sales_orders', [
            'id' => $order->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_can_create_sales_invoice(): void
    {
        $response = $this->actingAs($this->user)->post(route('sales.invoices.store'), [
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'invoice_number' => 'INV-2026-0001',
            'invoice_date' => '2026-06-01',
            'due_date' => '2026-07-01',
            'customer_id' => $this->customer->id,
            'payment_term_id' => $this->paymentTerm->id,
            'currency_id' => $this->currency->id,
            'exchange_rate' => 1,
            'subtotal' => 200000,
            'tax_amount' => 22000,
            'total' => 222000,
            'outstanding_amount' => 222000,
            'description' => 'Test sales invoice',
        ]);

        $response->assertRedirect(route('sales.invoices.index'));
        $this->assertDatabaseHas('sales_invoices', [
            'customer_id' => $this->customer->id,
            'status' => 'draft',
            'created_by' => $this->user->id,
            'total' => 222000,
        ]);
    }

    public function test_can_update_sales_invoice(): void
    {
        $invoice = SalesInvoice::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'invoice_number' => 'INV-2026-0001',
            'invoice_date' => '2026-06-01',
            'due_date' => '2026-07-01',
            'customer_id' => $this->customer->id,
            'payment_term_id' => $this->paymentTerm->id,
            'currency_id' => $this->currency->id,
            'exchange_rate' => 1,
            'subtotal' => 200000,
            'tax_amount' => 22000,
            'total' => 222000,
            'outstanding_amount' => 222000,
            'created_by' => $this->user->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)->put(route('sales.invoices.update', $invoice), [
            'invoice_date' => '2026-06-01',
            'due_date' => '2026-07-01',
            'customer_id' => $this->customer->id,
            'subtotal' => 300000,
            'tax_amount' => 33000,
            'total' => 333000,
            'description' => 'Updated invoice',
        ]);

        $response->assertRedirect(route('sales.invoices.index'));
        $this->assertDatabaseHas('sales_invoices', [
            'id' => $invoice->id,
            'total' => 333000,
            'description' => 'Updated invoice',
        ]);
    }

    public function test_can_delete_sales_invoice(): void
    {
        $invoice = SalesInvoice::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'invoice_number' => 'INV-2026-0001',
            'invoice_date' => '2026-06-01',
            'due_date' => '2026-07-01',
            'customer_id' => $this->customer->id,
            'payment_term_id' => $this->paymentTerm->id,
            'currency_id' => $this->currency->id,
            'exchange_rate' => 1,
            'subtotal' => 200000,
            'tax_amount' => 22000,
            'total' => 222000,
            'outstanding_amount' => 222000,
            'created_by' => $this->user->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)->delete(route('sales.invoices.destroy', $invoice));

        $response->assertRedirect(route('sales.invoices.index'));
        $this->assertSoftDeleted('sales_invoices', ['id' => $invoice->id]);
    }

    public function test_can_submit_sales_invoice(): void
    {
        $invoice = SalesInvoice::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'invoice_number' => 'INV-2026-0001',
            'invoice_date' => '2026-06-01',
            'due_date' => '2026-07-01',
            'customer_id' => $this->customer->id,
            'payment_term_id' => $this->paymentTerm->id,
            'currency_id' => $this->currency->id,
            'exchange_rate' => 1,
            'subtotal' => 200000,
            'tax_amount' => 22000,
            'total' => 222000,
            'outstanding_amount' => 222000,
            'created_by' => $this->user->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)->post(route('sales.invoices.submit', $invoice));

        $response->assertRedirect(route('sales.invoices.index'));
        $this->assertDatabaseHas('sales_invoices', [
            'id' => $invoice->id,
            'status' => 'submitted',
        ]);
    }

    public function test_can_approve_sales_invoice(): void
    {
        $invoice = SalesInvoice::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'invoice_number' => 'INV-2026-0001',
            'invoice_date' => '2026-06-01',
            'due_date' => '2026-07-01',
            'customer_id' => $this->customer->id,
            'payment_term_id' => $this->paymentTerm->id,
            'currency_id' => $this->currency->id,
            'exchange_rate' => 1,
            'subtotal' => 200000,
            'tax_amount' => 22000,
            'total' => 222000,
            'outstanding_amount' => 222000,
            'created_by' => $this->user->id,
            'status' => 'submitted',
        ]);

        $response = $this->actingAs($this->user)->post(route('sales.invoices.approve', $invoice));

        $response->assertRedirect(route('sales.invoices.index'));
        $this->assertDatabaseHas('sales_invoices', [
            'id' => $invoice->id,
            'status' => 'approved',
        ]);
    }

    public function test_can_create_sales_return(): void
    {
        $invoice = SalesInvoice::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'invoice_number' => 'INV-2026-0001',
            'invoice_date' => '2026-06-01',
            'due_date' => '2026-07-01',
            'customer_id' => $this->customer->id,
            'payment_term_id' => $this->paymentTerm->id,
            'currency_id' => $this->currency->id,
            'exchange_rate' => 1,
            'subtotal' => 200000,
            'tax_amount' => 22000,
            'total' => 222000,
            'outstanding_amount' => 222000,
            'created_by' => $this->user->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)->post(route('sales.returns.store'), [
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'return_number' => 'SR-2026-0001',
            'return_date' => '2026-06-10',
            'sales_invoice_id' => $invoice->id,
            'customer_id' => $this->customer->id,
            'warehouse_id' => $this->warehouse->id,
            'return_type' => 'full',
            'description' => 'Test sales return',
        ]);

        $response->assertRedirect(route('sales.returns.index'));
        $this->assertDatabaseHas('sales_returns', [
            'customer_id' => $this->customer->id,
            'status' => 'draft',
            'created_by' => $this->user->id,
        ]);
    }

    public function test_can_update_sales_return(): void
    {
        $invoice = SalesInvoice::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'invoice_number' => 'INV-2026-0001',
            'invoice_date' => '2026-06-01',
            'due_date' => '2026-07-01',
            'customer_id' => $this->customer->id,
            'payment_term_id' => $this->paymentTerm->id,
            'currency_id' => $this->currency->id,
            'exchange_rate' => 1,
            'subtotal' => 200000,
            'tax_amount' => 22000,
            'total' => 222000,
            'outstanding_amount' => 222000,
            'created_by' => $this->user->id,
            'status' => 'draft',
        ]);

        $return = SalesReturn::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'return_number' => 'SR-2026-0001',
            'return_date' => '2026-06-10',
            'sales_invoice_id' => $invoice->id,
            'customer_id' => $this->customer->id,
            'warehouse_id' => $this->warehouse->id,
            'return_type' => 'full',
            'created_by' => $this->user->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)->put(route('sales.returns.update', $return), [
            'return_date' => '2026-06-11',
            'sales_invoice_id' => $invoice->id,
            'customer_id' => $this->customer->id,
            'warehouse_id' => $this->warehouse->id,
            'return_type' => 'partial',
            'description' => 'Updated return',
        ]);

        $response->assertRedirect(route('sales.returns.index'));
        $this->assertDatabaseHas('sales_returns', [
            'id' => $return->id,
            'return_type' => 'partial',
            'description' => 'Updated return',
        ]);
    }

    public function test_can_delete_sales_return(): void
    {
        $invoice = SalesInvoice::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'invoice_number' => 'INV-2026-0001',
            'invoice_date' => '2026-06-01',
            'due_date' => '2026-07-01',
            'customer_id' => $this->customer->id,
            'payment_term_id' => $this->paymentTerm->id,
            'currency_id' => $this->currency->id,
            'exchange_rate' => 1,
            'subtotal' => 200000,
            'tax_amount' => 22000,
            'total' => 222000,
            'outstanding_amount' => 222000,
            'created_by' => $this->user->id,
            'status' => 'draft',
        ]);

        $return = SalesReturn::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'return_number' => 'SR-2026-0001',
            'return_date' => '2026-06-10',
            'sales_invoice_id' => $invoice->id,
            'customer_id' => $this->customer->id,
            'warehouse_id' => $this->warehouse->id,
            'return_type' => 'full',
            'created_by' => $this->user->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)->delete(route('sales.returns.destroy', $return));

        $response->assertRedirect(route('sales.returns.index'));
        $this->assertSoftDeleted('sales_returns', ['id' => $return->id]);
    }

    public function test_can_create_customer_payment(): void
    {
        $response = $this->actingAs($this->user)->post(route('sales.customer-payments.store'), [
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'payment_number' => 'PYT-2026-0001',
            'payment_date' => '2026-06-15',
            'customer_id' => $this->customer->id,
            'payment_method' => 'cash',
            'cash_account_id' => $this->cashAccount->id,
            'amount' => 100000,
            'description' => 'Test payment',
        ]);

        $response->assertRedirect(route('sales.customer-payments.index'));
        $this->assertDatabaseHas('customer_payments', [
            'customer_id' => $this->customer->id,
            'status' => 'draft',
            'created_by' => $this->user->id,
            'amount' => 100000,
        ]);
    }

    public function test_can_update_customer_payment(): void
    {
        $payment = CustomerPayment::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'payment_number' => 'PYT-2026-0001',
            'payment_date' => '2026-06-15',
            'customer_id' => $this->customer->id,
            'payment_method' => 'cash',
            'amount' => 100000,
            'created_by' => $this->user->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)->put(route('sales.customer-payments.update', $payment), [
            'payment_date' => '2026-06-16',
            'customer_id' => $this->customer->id,
            'payment_method' => 'bank_transfer',
            'amount' => 150000,
            'description' => 'Updated payment',
        ]);

        $response->assertRedirect(route('sales.customer-payments.index'));
        $this->assertDatabaseHas('customer_payments', [
            'id' => $payment->id,
            'amount' => 150000,
            'description' => 'Updated payment',
        ]);
    }

    public function test_can_delete_customer_payment(): void
    {
        $payment = CustomerPayment::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'payment_number' => 'PYT-2026-0001',
            'payment_date' => '2026-06-15',
            'customer_id' => $this->customer->id,
            'payment_method' => 'cash',
            'amount' => 100000,
            'created_by' => $this->user->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)->delete(route('sales.customer-payments.destroy', $payment));

        $response->assertRedirect(route('sales.customer-payments.index'));
        $this->assertSoftDeleted('customer_payments', ['id' => $payment->id]);
    }

    public function test_can_print_sales_invoice(): void
    {
        $invoice = SalesInvoice::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'invoice_number' => 'INV-2026-9999',
            'invoice_date' => '2026-06-01',
            'due_date' => '2026-07-01',
            'customer_id' => $this->customer->id,
            'payment_term_id' => $this->paymentTerm->id,
            'currency_id' => $this->currency->id,
            'exchange_rate' => 1,
            'subtotal' => 100000,
            'tax_amount' => 11000,
            'total' => 111000,
            'outstanding_amount' => 111000,
            'created_by' => $this->user->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)->get(route('sales.invoices.print', $invoice));

        $response->assertStatus(200);
        $response->assertSee($invoice->invoice_number);
    }

    public function test_can_view_sales_invoice_show(): void
    {
        $invoice = SalesInvoice::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'invoice_number' => 'INV-SHOW-0001',
            'invoice_date' => '2026-06-01',
            'due_date' => '2026-07-01',
            'customer_id' => $this->customer->id,
            'payment_term_id' => $this->paymentTerm->id,
            'currency_id' => $this->currency->id,
            'exchange_rate' => 1,
            'subtotal' => 100000,
            'tax_amount' => 11000,
            'total' => 111000,
            'outstanding_amount' => 111000,
            'created_by' => $this->user->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)->get(route('sales.invoices.show', $invoice));
        $response->assertStatus(200);
        $response->assertSee($invoice->invoice_number);
    }

    public function test_can_view_sales_quotation_index(): void
    {
        $response = $this->actingAs($this->user)->get(route('sales.quotations.index'));
        $response->assertStatus(200);
    }

    public function test_can_view_sales_invoice_index(): void
    {
        $response = $this->actingAs($this->user)->get(route('sales.invoices.index'));
        $response->assertStatus(200);
    }

    public function test_can_view_sales_order_index(): void
    {
        $response = $this->actingAs($this->user)->get(route('sales.orders.index'));
        $response->assertStatus(200);
    }

    public function test_can_view_sales_return_index(): void
    {
        $response = $this->actingAs($this->user)->get(route('sales.returns.index'));
        $response->assertStatus(200);
    }

    public function test_can_view_customer_payment_index(): void
    {
        $response = $this->actingAs($this->user)->get(route('sales.customer-payments.index'));
        $response->assertStatus(200);
    }
}
