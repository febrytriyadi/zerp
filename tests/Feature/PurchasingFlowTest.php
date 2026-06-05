<?php

namespace Tests\Feature;

use App\Models\Master\Branch;
use App\Models\Master\ChartOfAccount;
use App\Models\Master\Company;
use App\Models\Master\Currency;
use App\Models\Master\PaymentTerm;
use App\Models\Master\Product;
use App\Models\Master\ProductCategory;
use App\Models\Master\Supplier;
use App\Models\Master\TaxRate;
use App\Models\Master\Unit;
use App\Models\Master\Warehouse;
use App\Models\Purchasing\PurchaseOrder;
use App\Models\Purchasing\PurchaseRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Finance\CashTransaction;
use App\Models\Master\CashAccount;
use App\Models\Purchasing\PurchaseInvoice;
use App\Models\Purchasing\ReceivedGoods;
use App\Models\Purchasing\SupplierPayment;
use Tests\TestCase;

class PurchasingFlowTest extends TestCase
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
    private Supplier $supplier;
    private Warehouse $warehouse;
    private Product $product;
    private CashAccount $cashAccount;

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
            'code' => 'SUPP-TEST',
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
            'code' => 'WH-TEST',
            'name' => 'Test Warehouse',
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

    public function test_can_create_purchase_request(): void
    {
        $response = $this->actingAs($this->user)->post(route('purchasing.requests.store'), [
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'request_number' => 'PR-001',
            'request_date' => now()->toDateString(),
            'description' => 'Test purchase request',
        ]);

        $response->assertRedirect(route('purchasing.requests.index'));
        $this->assertDatabaseHas('purchase_requests', [
            'requested_by' => $this->user->id,
            'status' => 'draft',
            'description' => 'Test purchase request',
        ]);
    }

    public function test_can_update_purchase_request(): void
    {
        $purchaseRequest = PurchaseRequest::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'request_number' => 'PR-001',
            'request_date' => now()->toDateString(),
            'requested_by' => $this->user->id,
            'description' => 'Before',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)->put(route('purchasing.requests.update', $purchaseRequest), [
            'request_date' => now()->toDateString(),
            'description' => 'After Update',
        ]);

        $response->assertRedirect(route('purchasing.requests.index'));
        $this->assertDatabaseHas('purchase_requests', [
            'id' => $purchaseRequest->id,
            'description' => 'After Update',
        ]);
    }

    public function test_can_delete_purchase_request(): void
    {
        $purchaseRequest = PurchaseRequest::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'request_number' => 'PR-DEL',
            'request_date' => now()->toDateString(),
            'requested_by' => $this->user->id,
            'description' => 'To Delete',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)->delete(route('purchasing.requests.destroy', $purchaseRequest));

        $response->assertRedirect(route('purchasing.requests.index'));
        $this->assertDatabaseMissing('purchase_requests', ['id' => $purchaseRequest->id]);
    }

    public function test_can_submit_purchase_request(): void
    {
        $purchaseRequest = PurchaseRequest::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'request_number' => 'PR-SUB',
            'request_date' => now()->toDateString(),
            'requested_by' => $this->user->id,
            'description' => 'To Submit',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)->post(route('purchasing.purchase-requests.submit', $purchaseRequest));

        $response->assertRedirect(route('purchasing.requests.index'));
        $this->assertDatabaseHas('purchase_requests', [
            'id' => $purchaseRequest->id,
            'status' => 'submitted',
        ]);
    }

    public function test_can_approve_purchase_request(): void
    {
        $purchaseRequest = PurchaseRequest::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'request_number' => 'PR-APP',
            'request_date' => now()->toDateString(),
            'requested_by' => $this->user->id,
            'description' => 'To Approve',
            'status' => 'draft',
        ]);

        $this->actingAs($this->user)->post(route('purchasing.purchase-requests.submit', $purchaseRequest));

        $response = $this->actingAs($this->user)->post(route('purchasing.purchase-requests.approve', $purchaseRequest));

        $response->assertRedirect(route('purchasing.requests.index'));
        $this->assertDatabaseHas('purchase_requests', [
            'id' => $purchaseRequest->id,
            'status' => 'approved',
            'approved_by' => $this->user->id,
        ]);
    }

    public function test_can_reject_purchase_request(): void
    {
        $purchaseRequest = PurchaseRequest::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'request_number' => 'PR-REJ',
            'request_date' => now()->toDateString(),
            'requested_by' => $this->user->id,
            'description' => 'To Reject',
            'status' => 'draft',
        ]);

        $this->actingAs($this->user)->post(route('purchasing.purchase-requests.submit', $purchaseRequest));

        $response = $this->actingAs($this->user)->post(route('purchasing.purchase-requests.reject', $purchaseRequest));

        $response->assertRedirect(route('purchasing.requests.index'));
        $this->assertDatabaseHas('purchase_requests', [
            'id' => $purchaseRequest->id,
            'status' => 'rejected',
        ]);
    }

    public function test_can_create_purchase_order(): void
    {
        $response = $this->actingAs($this->user)->post(route('purchasing.orders.store'), [
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'order_number' => 'PO-001',
            'order_date' => now()->toDateString(),
            'supplier_id' => $this->supplier->id,
            'payment_term_id' => $this->paymentTerm->id,
            'currency_id' => $this->currency->id,
            'exchange_rate' => 1,
            'tax_rate_id' => $this->taxRate->id,
            'subtotal' => 100000,
            'tax_amount' => 11000,
            'total' => 111000,
            'warehouse_id' => $this->warehouse->id,
            'description' => 'Test purchase order',
        ]);

        $response->assertRedirect(route('purchasing.orders.index'));
        $this->assertDatabaseHas('purchase_orders', [
            'created_by' => $this->user->id,
            'status' => 'draft',
            'supplier_id' => $this->supplier->id,
        ]);
    }

    public function test_can_update_purchase_order(): void
    {
        $purchaseOrder = PurchaseOrder::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'order_number' => 'PO-001',
            'order_date' => now()->toDateString(),
            'supplier_id' => $this->supplier->id,
            'payment_term_id' => $this->paymentTerm->id,
            'currency_id' => $this->currency->id,
            'exchange_rate' => 1,
            'tax_rate_id' => $this->taxRate->id,
            'subtotal' => 100000,
            'tax_amount' => 11000,
            'total' => 111000,
            'down_payment_amount' => 0,
            'description' => 'Before',
            'status' => 'draft',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->put(route('purchasing.orders.update', $purchaseOrder), [
            'order_date' => now()->toDateString(),
            'supplier_id' => $this->supplier->id,
            'payment_term_id' => $this->paymentTerm->id,
            'currency_id' => $this->currency->id,
            'exchange_rate' => 1,
            'tax_rate_id' => $this->taxRate->id,
            'subtotal' => 200000,
            'tax_amount' => 22000,
            'total' => 222000,
            'description' => 'After Update',
        ]);

        $response->assertRedirect(route('purchasing.orders.index'));
        $this->assertDatabaseHas('purchase_orders', [
            'id' => $purchaseOrder->id,
            'description' => 'After Update',
            'subtotal' => 200000,
        ]);
    }

    public function test_can_delete_purchase_order(): void
    {
        $purchaseOrder = PurchaseOrder::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'order_number' => 'PO-DEL',
            'order_date' => now()->toDateString(),
            'supplier_id' => $this->supplier->id,
            'payment_term_id' => $this->paymentTerm->id,
            'currency_id' => $this->currency->id,
            'exchange_rate' => 1,
            'tax_rate_id' => $this->taxRate->id,
            'subtotal' => 100000,
            'tax_amount' => 11000,
            'total' => 111000,
            'down_payment_amount' => 0,
            'description' => 'To Delete',
            'status' => 'draft',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->delete(route('purchasing.orders.destroy', $purchaseOrder));

        $response->assertRedirect(route('purchasing.orders.index'));
        $this->assertDatabaseMissing('purchase_orders', ['id' => $purchaseOrder->id]);
    }

    public function test_can_submit_purchase_order(): void
    {
        $purchaseOrder = PurchaseOrder::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'order_number' => 'PO-SUB',
            'order_date' => now()->toDateString(),
            'supplier_id' => $this->supplier->id,
            'payment_term_id' => $this->paymentTerm->id,
            'currency_id' => $this->currency->id,
            'exchange_rate' => 1,
            'tax_rate_id' => $this->taxRate->id,
            'subtotal' => 100000,
            'tax_amount' => 11000,
            'total' => 111000,
            'down_payment_amount' => 0,
            'description' => 'To Submit',
            'status' => 'draft',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->post(route('purchasing.purchase-orders.submit', $purchaseOrder));

        $response->assertRedirect(route('purchasing.orders.index'));
        $this->assertDatabaseHas('purchase_orders', [
            'id' => $purchaseOrder->id,
            'status' => 'submitted',
        ]);
    }

    public function test_can_approve_purchase_order(): void
    {
        $purchaseOrder = PurchaseOrder::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'order_number' => 'PO-APP',
            'order_date' => now()->toDateString(),
            'supplier_id' => $this->supplier->id,
            'payment_term_id' => $this->paymentTerm->id,
            'currency_id' => $this->currency->id,
            'exchange_rate' => 1,
            'tax_rate_id' => $this->taxRate->id,
            'subtotal' => 100000,
            'tax_amount' => 11000,
            'total' => 111000,
            'down_payment_amount' => 0,
            'description' => 'To Approve',
            'status' => 'draft',
            'created_by' => $this->user->id,
        ]);

        $this->actingAs($this->user)->post(route('purchasing.purchase-orders.submit', $purchaseOrder));

        $response = $this->actingAs($this->user)->post(route('purchasing.purchase-orders.approve', $purchaseOrder));

        $response->assertRedirect(route('purchasing.orders.index'));
        $this->assertDatabaseHas('purchase_orders', [
            'id' => $purchaseOrder->id,
            'status' => 'approved',
            'approved_by' => $this->user->id,
        ]);
    }

    public function test_can_cancel_purchase_order(): void
    {
        $purchaseOrder = PurchaseOrder::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'order_number' => 'PO-CAN',
            'order_date' => now()->toDateString(),
            'supplier_id' => $this->supplier->id,
            'payment_term_id' => $this->paymentTerm->id,
            'currency_id' => $this->currency->id,
            'exchange_rate' => 1,
            'tax_rate_id' => $this->taxRate->id,
            'subtotal' => 100000,
            'tax_amount' => 11000,
            'total' => 111000,
            'down_payment_amount' => 0,
            'description' => 'To Cancel',
            'status' => 'draft',
            'created_by' => $this->user->id,
        ]);

        $this->actingAs($this->user)->post(route('purchasing.purchase-orders.submit', $purchaseOrder));

        $response = $this->actingAs($this->user)->post(route('purchasing.purchase-orders.cancel', $purchaseOrder));

        $response->assertRedirect(route('purchasing.orders.index'));
        $this->assertDatabaseHas('purchase_orders', [
            'id' => $purchaseOrder->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_can_create_received_goods(): void
    {
        $purchaseOrder = PurchaseOrder::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'order_number' => 'PO-RG',
            'order_date' => now()->toDateString(),
            'supplier_id' => $this->supplier->id,
            'payment_term_id' => $this->paymentTerm->id,
            'currency_id' => $this->currency->id,
            'exchange_rate' => 1,
            'tax_rate_id' => $this->taxRate->id,
            'subtotal' => 0,
            'tax_amount' => 0,
            'total' => 0,
            'down_payment_amount' => 0,
            'status' => 'draft',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->post(route('purchasing.received-goods.store'), [
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'receive_number' => 'RG-001',
            'receive_date' => now()->toDateString(),
            'purchase_order_id' => $purchaseOrder->id,
            'supplier_id' => $this->supplier->id,
            'warehouse_id' => $this->warehouse->id,
            'description' => 'Test received goods',
        ]);

        $response->assertRedirect(route('purchasing.received-goods.index'));
        $this->assertDatabaseHas('received_goods', [
            'created_by' => $this->user->id,
            'status' => 'draft',
            'supplier_id' => $this->supplier->id,
            'description' => 'Test received goods',
        ]);
    }

    public function test_can_update_received_goods(): void
    {
        $purchaseOrder = PurchaseOrder::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'order_number' => 'PO-RGU',
            'order_date' => now()->toDateString(),
            'supplier_id' => $this->supplier->id,
            'payment_term_id' => $this->paymentTerm->id,
            'currency_id' => $this->currency->id,
            'exchange_rate' => 1,
            'tax_rate_id' => $this->taxRate->id,
            'subtotal' => 0,
            'tax_amount' => 0,
            'total' => 0,
            'down_payment_amount' => 0,
            'status' => 'draft',
            'created_by' => $this->user->id,
        ]);

        $receivedGoods = ReceivedGoods::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'receive_number' => 'RG-001',
            'receive_date' => now()->toDateString(),
            'purchase_order_id' => $purchaseOrder->id,
            'supplier_id' => $this->supplier->id,
            'warehouse_id' => $this->warehouse->id,
            'description' => 'Before',
            'status' => 'draft',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->put(route('purchasing.received-goods.update', $receivedGoods), [
            'receive_date' => now()->toDateString(),
            'purchase_order_id' => $purchaseOrder->id,
            'supplier_id' => $this->supplier->id,
            'warehouse_id' => $this->warehouse->id,
            'description' => 'After Update',
        ]);

        $response->assertRedirect(route('purchasing.received-goods.index'));
        $this->assertDatabaseHas('received_goods', [
            'id' => $receivedGoods->id,
            'description' => 'After Update',
        ]);
    }

    public function test_can_delete_received_goods(): void
    {
        $purchaseOrder = PurchaseOrder::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'order_number' => 'PO-RGD',
            'order_date' => now()->toDateString(),
            'supplier_id' => $this->supplier->id,
            'payment_term_id' => $this->paymentTerm->id,
            'currency_id' => $this->currency->id,
            'exchange_rate' => 1,
            'tax_rate_id' => $this->taxRate->id,
            'subtotal' => 0,
            'tax_amount' => 0,
            'total' => 0,
            'down_payment_amount' => 0,
            'status' => 'draft',
            'created_by' => $this->user->id,
        ]);

        $receivedGoods = ReceivedGoods::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'receive_number' => 'RG-DEL',
            'receive_date' => now()->toDateString(),
            'purchase_order_id' => $purchaseOrder->id,
            'supplier_id' => $this->supplier->id,
            'warehouse_id' => $this->warehouse->id,
            'description' => 'To Delete',
            'status' => 'draft',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->delete(route('purchasing.received-goods.destroy', $receivedGoods));

        $response->assertRedirect(route('purchasing.received-goods.index'));
        $this->assertSoftDeleted('received_goods', ['id' => $receivedGoods->id]);
    }

    public function test_can_create_purchase_invoice(): void
    {
        $response = $this->actingAs($this->user)->post(route('purchasing.invoices.store'), [
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'invoice_number' => 'INV-001',
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'supplier_id' => $this->supplier->id,
            'payment_term_id' => $this->paymentTerm->id,
            'currency_id' => $this->currency->id,
            'exchange_rate' => 1,
            'tax_rate_id' => $this->taxRate->id,
            'subtotal' => 100000,
            'tax_amount' => 11000,
            'total' => 111000,
            'outstanding_amount' => 111000,
            'description' => 'Test purchase invoice',
        ]);

        $response->assertRedirect(route('purchasing.invoices.index'));
        $this->assertDatabaseHas('purchase_invoices', [
            'created_by' => $this->user->id,
            'status' => 'draft',
            'supplier_id' => $this->supplier->id,
            'description' => 'Test purchase invoice',
        ]);
    }

    public function test_can_update_purchase_invoice(): void
    {
        $purchaseInvoice = PurchaseInvoice::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'invoice_number' => 'INV-001',
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'supplier_id' => $this->supplier->id,
            'payment_term_id' => $this->paymentTerm->id,
            'currency_id' => $this->currency->id,
            'exchange_rate' => 1,
            'tax_rate_id' => $this->taxRate->id,
            'subtotal' => 100000,
            'tax_amount' => 11000,
            'total' => 111000,
            'outstanding_amount' => 111000,
            'description' => 'Before',
            'status' => 'draft',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->put(route('purchasing.invoices.update', $purchaseInvoice), [
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'supplier_id' => $this->supplier->id,
            'payment_term_id' => $this->paymentTerm->id,
            'currency_id' => $this->currency->id,
            'exchange_rate' => 1,
            'tax_rate_id' => $this->taxRate->id,
            'subtotal' => 200000,
            'tax_amount' => 22000,
            'total' => 222000,
            'description' => 'After Update',
        ]);

        $response->assertRedirect(route('purchasing.invoices.index'));
        $this->assertDatabaseHas('purchase_invoices', [
            'id' => $purchaseInvoice->id,
            'description' => 'After Update',
            'subtotal' => 200000,
        ]);
    }

    public function test_can_delete_purchase_invoice(): void
    {
        $purchaseInvoice = PurchaseInvoice::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'invoice_number' => 'INV-DEL',
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'supplier_id' => $this->supplier->id,
            'payment_term_id' => $this->paymentTerm->id,
            'currency_id' => $this->currency->id,
            'exchange_rate' => 1,
            'tax_rate_id' => $this->taxRate->id,
            'subtotal' => 100000,
            'tax_amount' => 11000,
            'total' => 111000,
            'outstanding_amount' => 111000,
            'description' => 'To Delete',
            'status' => 'draft',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->delete(route('purchasing.invoices.destroy', $purchaseInvoice));

        $response->assertRedirect(route('purchasing.invoices.index'));
        $this->assertSoftDeleted('purchase_invoices', ['id' => $purchaseInvoice->id]);
    }

    public function test_can_create_supplier_payment(): void
    {
        $response = $this->actingAs($this->user)->post(route('purchasing.supplier-payments.store'), [
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'payment_number' => 'SP-001',
            'payment_date' => now()->toDateString(),
            'supplier_id' => $this->supplier->id,
            'payment_method' => 'cash',
            'cash_account_id' => $this->cashAccount->id,
            'amount' => 50000,
            'description' => 'Test supplier payment',
        ]);

        $response->assertRedirect(route('purchasing.supplier-payments.index'));
        $this->assertDatabaseHas('supplier_payments', [
            'created_by' => $this->user->id,
            'status' => 'draft',
            'supplier_id' => $this->supplier->id,
            'amount' => 50000,
        ]);
    }

    public function test_can_update_supplier_payment(): void
    {
        $supplierPayment = SupplierPayment::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'payment_number' => 'SP-001',
            'payment_date' => now()->toDateString(),
            'supplier_id' => $this->supplier->id,
            'payment_method' => 'cash',
            'amount' => 50000,
            'status' => 'draft',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->put(route('purchasing.supplier-payments.update', $supplierPayment), [
            'payment_date' => now()->toDateString(),
            'supplier_id' => $this->supplier->id,
            'payment_method' => 'bank_transfer',
            'amount' => 75000,
            'description' => 'After Update',
        ]);

        $response->assertRedirect(route('purchasing.supplier-payments.index'));
        $this->assertDatabaseHas('supplier_payments', [
            'id' => $supplierPayment->id,
            'amount' => 75000,
            'description' => 'After Update',
        ]);
    }

    public function test_can_delete_supplier_payment(): void
    {
        $supplierPayment = SupplierPayment::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'payment_number' => 'SP-DEL',
            'payment_date' => now()->toDateString(),
            'supplier_id' => $this->supplier->id,
            'payment_method' => 'cash',
            'amount' => 50000,
            'status' => 'draft',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->delete(route('purchasing.supplier-payments.destroy', $supplierPayment));

        $response->assertRedirect(route('purchasing.supplier-payments.index'));
        $this->assertSoftDeleted('supplier_payments', ['id' => $supplierPayment->id]);
    }

    public function test_can_submit_purchase_invoice(): void
    {
        $purchaseInvoice = PurchaseInvoice::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'invoice_number' => 'INV-SUB',
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'supplier_id' => $this->supplier->id,
            'payment_term_id' => $this->paymentTerm->id,
            'currency_id' => $this->currency->id,
            'exchange_rate' => 1,
            'tax_rate_id' => $this->taxRate->id,
            'subtotal' => 100000,
            'tax_amount' => 11000,
            'total' => 111000,
            'outstanding_amount' => 111000,
            'description' => 'To Submit',
            'status' => 'draft',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->post(route('purchasing.purchase-invoices.submit', $purchaseInvoice));

        $response->assertRedirect(route('purchasing.invoices.index'));
        $this->assertDatabaseHas('purchase_invoices', [
            'id' => $purchaseInvoice->id,
            'status' => 'submitted',
        ]);
    }

    public function test_can_approve_purchase_invoice(): void
    {
        $purchaseInvoice = PurchaseInvoice::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'invoice_number' => 'INV-APP',
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'supplier_id' => $this->supplier->id,
            'payment_term_id' => $this->paymentTerm->id,
            'currency_id' => $this->currency->id,
            'exchange_rate' => 1,
            'tax_rate_id' => $this->taxRate->id,
            'subtotal' => 100000,
            'tax_amount' => 11000,
            'total' => 111000,
            'outstanding_amount' => 111000,
            'description' => 'To Approve',
            'status' => 'draft',
            'created_by' => $this->user->id,
        ]);

        $this->actingAs($this->user)->post(route('purchasing.purchase-invoices.submit', $purchaseInvoice));

        $response = $this->actingAs($this->user)->post(route('purchasing.purchase-invoices.approve', $purchaseInvoice));

        $response->assertRedirect(route('purchasing.invoices.index'));
        $this->assertDatabaseHas('purchase_invoices', [
            'id' => $purchaseInvoice->id,
            'status' => 'approved',
            'approved_by' => $this->user->id,
        ]);
    }

    public function test_can_print_purchase_order(): void
    {
        $order = PurchaseOrder::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'order_number' => 'PO-PRINT-0001',
            'order_date' => '2026-06-01',
            'supplier_id' => $this->supplier->id,
            'payment_term_id' => $this->paymentTerm->id,
            'currency_id' => $this->currency->id,
            'exchange_rate' => 1,
            'subtotal' => 100000,
            'tax_amount' => 11000,
            'total' => 111000,
            'warehouse_id' => $this->warehouse->id,
            'status' => 'draft',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('purchasing.purchase-orders.print', $order));

        $response->assertStatus(200);
        $response->assertSee($order->order_number);
    }

    public function test_can_view_purchase_invoice_show(): void
    {
        $purchaseInvoice = PurchaseInvoice::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'invoice_number' => 'PI-SHOW-0001',
            'invoice_date' => '2026-06-01',
            'due_date' => '2026-07-01',
            'supplier_id' => $this->supplier->id,
            'payment_term_id' => $this->paymentTerm->id,
            'currency_id' => $this->currency->id,
            'exchange_rate' => 1,
            'subtotal' => 100000,
            'tax_amount' => 11000,
            'total' => 111000,
            'outstanding_amount' => 111000,
            'description' => 'Show Test',
            'status' => 'draft',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('purchasing.invoices.show', $purchaseInvoice));
        $response->assertStatus(200);
        $response->assertSee($purchaseInvoice->invoice_number);
    }

    public function test_can_view_purchase_request_index(): void
    {
        $response = $this->actingAs($this->user)->get(route('purchasing.requests.index'));
        $response->assertStatus(200);
    }

    public function test_can_view_purchase_order_index(): void
    {
        $response = $this->actingAs($this->user)->get(route('purchasing.orders.index'));
        $response->assertStatus(200);
    }

    public function test_can_view_purchase_invoice_index(): void
    {
        $response = $this->actingAs($this->user)->get(route('purchasing.invoices.index'));
        $response->assertStatus(200);
    }

    public function test_can_view_received_goods_index(): void
    {
        $response = $this->actingAs($this->user)->get(route('purchasing.received-goods.index'));
        $response->assertStatus(200);
    }

    public function test_can_view_supplier_payment_index(): void
    {
        $response = $this->actingAs($this->user)->get(route('purchasing.supplier-payments.index'));
        $response->assertStatus(200);
    }
}
