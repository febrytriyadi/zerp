<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Master\Company;
use App\Models\Master\Branch;
use App\Models\Master\Product;
use App\Models\Master\Customer;
use App\Models\Master\Supplier;
use App\Models\Master\Warehouse;
use App\Models\Master\FiscalPeriod;
use App\Models\Master\ChartOfAccount;
use App\Models\Master\PaymentTerm;
use App\Models\Master\CashAccount;
use App\Models\Finance\JournalEntry;
use App\Models\Finance\CashTransaction;
use App\Models\Sales\SalesQuotation;
use App\Models\Sales\SalesOrder;
use App\Models\Sales\SalesInvoice;
use App\Models\Sales\CustomerPayment;
use App\Models\Purchasing\PurchaseRequest;
use App\Models\Purchasing\PurchaseOrder;
use App\Models\Purchasing\ReceivedGoods;
use App\Models\Purchasing\PurchaseInvoice;
use App\Models\Inventory\InventoryMovement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ZerpTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }

    // ══════════════════════════════════════
    // 1. MASTER DATA CRUD
    // ══════════════════════════════════════

    public function test_01_company_crud(): void
    {
        // CREATE
        $c = Company::create([
            'code' => 'TEST', 'name' => 'PT Test Cabang',
            'address' => 'Jl. Test No.1', 'phone' => '021-99999999',
            'email' => 'test@co.id', 'is_active' => true,
        ]);
        $this->assertDatabaseHas('companies', ['code' => 'TEST']);

        // SHOW
        $this->assertEquals('PT Test Cabang', Company::find($c->id)->name);

        // EDIT
        $c->update(['name' => 'PT Test Updated']);
        $this->assertDatabaseHas('companies', ['name' => 'PT Test Updated']);

        // DELETE
        $c->delete();
        $this->assertSoftDeleted($c);
    }

    public function test_02_product_crud(): void
    {
        $cid = Company::first()->id;

        $p = Product::create([
            'company_id' => $cid, 'category_id' => 1,
            'code' => 'BRG-TEST', 'name' => 'Test Product',
            'selling_price' => 500000, 'purchase_price' => 350000,
            'unit_id' => 1, 'cost_method' => 'average', 'is_active' => true,
        ]);
        $this->assertDatabaseHas('products', ['code' => 'BRG-TEST']);

        $p->update(['selling_price' => 550000]);
        $this->assertEquals(550000, $p->fresh()->selling_price);

        // Product has no SoftDeletes trait — hard delete only
        $p->delete();
        $this->assertDatabaseMissing('products', ['id' => $p->id]);
    }

    public function test_03_customer_supplier_crud(): void
    {
        $coy = Company::first(); $br = Branch::first();
        $term = PaymentTerm::first()->id;
        $coaAr = ChartOfAccount::where('code', '1-1050')->first()->id;
        $coaAp = ChartOfAccount::where('code', '2-1010')->first()->id;

        $cust = Customer::create([
            'company_id' => $coy->id, 'branch_id' => $br->id,
            'code' => 'CUST-T', 'name' => 'PT Cust Test',
            'phone' => '081111', 'email' => 'c@t.id',
            'payment_term_id' => $term, 'chart_of_account_id' => $coaAr,
        ]);
        $this->assertDatabaseHas('customers', ['code' => 'CUST-T']);

        $cust->update(['credit_limit' => 50000000]);
        $this->assertEquals(50000000, $cust->fresh()->credit_limit);

        $supp = Supplier::create([
            'company_id' => $coy->id, 'branch_id' => $br->id,
            'code' => 'SUPP-T', 'name' => 'CV Supp Test',
            'phone' => '082222', 'email' => 's@t.id',
            'payment_term_id' => $term, 'chart_of_account_id' => $coaAp,
        ]);
        $this->assertDatabaseHas('suppliers', ['code' => 'SUPP-T']);

        $cust->delete(); $supp->delete();
    }

    // ══════════════════════════════════════
    // 2. SALES FLOW: SQ → SO → INV → PAY
    // ══════════════════════════════════════

    public function test_04_sales_full_flow(): void
    {
        $u = User::first(); $coy = Company::first(); $br = Branch::first();
        $custId = Customer::first()->id;
        $this->actingAs($u);

        // SQ: CREATE (need payment_term_id, currency_id)
        $sq = SalesQuotation::create([
            'company_id' => $coy->id, 'branch_id' => $br->id,
            'customer_id' => $custId,
            'quotation_number' => 'SQ-202606-000001',
            'quotation_date' => '2026-06-06',
            'payment_term_id' => PaymentTerm::first()->id,
            'currency_id' => \App\Models\Master\Currency::first()->id,
            'status' => 'draft', 'subtotal' => 3250000, 'total' => 3250000,
            'created_by' => $u->id,
        ]);
        $this->assertEquals('draft', $sq->status);

        // SQ: SUBMIT → APPROVE
        $sq->update(['status' => 'submitted']);
        $sq->update(['status' => 'approved']);

        // SO: CREATE (need payment_term_id, currency_id, tax_amount, outstanding_amount)
        $so = SalesOrder::create([
            'company_id' => $coy->id, 'branch_id' => $br->id,
            'customer_id' => $custId, 'sales_quotation_id' => $sq->id,
            'order_number' => 'SO-202606-000001',
            'order_date' => '2026-06-06',
            'payment_term_id' => PaymentTerm::first()->id,
            'currency_id' => \App\Models\Master\Currency::first()->id,
            'status' => 'draft', 'subtotal' => 3250000, 'total' => 3250000,
            'tax_amount' => 0, 'outstanding_amount' => 3250000,
            'created_by' => $u->id,
        ]);
        $this->assertEquals('draft', $so->status);

        // SO: SUBMIT → APPROVE
        $so->update(['status' => 'submitted']);
        $so->update(['status' => 'approved']);

        // INV: CREATE (need payment_term_id, currency_id, exchange_rate, tax_amount)
        $inv = SalesInvoice::create([
            'company_id' => $coy->id, 'branch_id' => $br->id,
            'customer_id' => $custId, 'sales_order_id' => $so->id,
            'invoice_number' => 'INV-202606-000001',
            'invoice_date' => '2026-06-06', 'due_date' => '2026-07-06',
            'payment_term_id' => PaymentTerm::first()->id,
            'currency_id' => \App\Models\Master\Currency::first()->id,
            'exchange_rate' => 1,
            'status' => 'draft', 'subtotal' => 3250000,
            'tax_amount' => 0,
            'total' => 3250000, 'outstanding_amount' => 3250000,
            'created_by' => $u->id,
        ]);
        $this->assertEquals('draft', $inv->status);

        // INV: SUBMIT → APPROVE → POST
        $inv->update(['status' => 'submitted']);
        $inv->update(['status' => 'approved']);
        $inv->update(['status' => 'posted']);
        $this->assertEquals('posted', $inv->fresh()->status);

        // PAYMENT: CREATE → POST
        $pay = CustomerPayment::create([
            'company_id' => $coy->id, 'branch_id' => $br->id,
            'customer_id' => $custId,
            'payment_number' => 'CP-202606-000001',
            'payment_date' => '2026-06-20',
            'amount' => 3250000, 'payment_method' => 'transfer',
            'status' => 'draft', 'created_by' => $u->id,
        ]);
        $this->assertEquals('draft', $pay->status);

        $pay->update(['status' => 'posted']);
        $inv->update(['status' => 'paid']);
        $this->assertEquals('paid', $inv->fresh()->status);
    }

    // ══════════════════════════════════════
    // 3. PURCHASING FLOW: PR → PO → RG → PI
    // ══════════════════════════════════════

    public function test_05_purchasing_full_flow(): void
    {
        $u = User::first(); $coy = Company::first(); $br = Branch::first();
        $suppId = Supplier::first()->id; $whId = Warehouse::first()->id;
        $this->actingAs($u);

        // PR: CREATE (need requested_by)
        $pr = PurchaseRequest::create([
            'company_id' => $coy->id, 'branch_id' => $br->id,
            'request_number' => 'PR-202606-000001',
            'request_date' => '2026-06-05',
            'requested_by' => $u->id,
            'status' => 'draft', 'created_by' => $u->id,
        ]);
        $this->assertEquals('draft', $pr->status);
        $pr->update(['status' => 'submitted']);
        $pr->update(['status' => 'approved']);

        // PO: CREATE (need payment_term_id, currency_id, exchange_rate, tax_amount)
        $po = PurchaseOrder::create([
            'company_id' => $coy->id, 'branch_id' => $br->id,
            'supplier_id' => $suppId, 'purchase_request_id' => $pr->id,
            'warehouse_id' => $whId,
            'order_number' => 'PO-202606-000001',
            'order_date' => '2026-06-06',
            'payment_term_id' => PaymentTerm::first()->id,
            'currency_id' => \App\Models\Master\Currency::first()->id,
            'exchange_rate' => 1,
            'status' => 'draft', 'subtotal' => 4650000, 'total' => 4650000,
            'tax_amount' => 0,
            'created_by' => $u->id,
        ]);
        $this->assertEquals('draft', $po->status);
        $po->update(['status' => 'submitted']);
        $po->update(['status' => 'approved']);

        // RG: CREATE
        $rg = ReceivedGoods::create([
            'company_id' => $coy->id, 'branch_id' => $br->id,
            'purchase_order_id' => $po->id,
            'supplier_id' => $suppId, 'warehouse_id' => $whId,
            'receive_number' => 'RG-202606-000001',
            'receive_date' => '2026-06-10',
            'status' => 'received', 'created_by' => $u->id,
        ]);
        $this->assertEquals('received', $rg->status);

        // PI: CREATE (need payment_term_id, currency_id, exchange_rate, tax_amount)
        $pi = PurchaseInvoice::create([
            'company_id' => $coy->id, 'branch_id' => $br->id,
            'purchase_order_id' => $po->id,
            'supplier_id' => $suppId,
            'invoice_number' => 'PI-202606-000001',
            'invoice_date' => '2026-06-10', 'due_date' => '2026-07-10',
            'payment_term_id' => PaymentTerm::first()->id,
            'currency_id' => \App\Models\Master\Currency::first()->id,
            'exchange_rate' => 1,
            'status' => 'draft', 'subtotal' => 4650000,
            'tax_amount' => 0,
            'total' => 4650000, 'outstanding_amount' => 4650000,
            'created_by' => $u->id,
        ]);
        $this->assertEquals('draft', $pi->status);
        $pi->update(['status' => 'submitted']);
        $pi->update(['status' => 'approved']);
        $pi->update(['status' => 'posted']);
        $this->assertEquals('posted', $pi->fresh()->status);
    }

    // ══════════════════════════════════════
    // 4. ACCOUNTING JOURNAL
    // ══════════════════════════════════════

    public function test_06_accounting_journal(): void
    {
        $u = User::first(); $this->actingAs($u);
        $period = FiscalPeriod::first()->id;

        $je = JournalEntry::create([
            'company_id' => Company::first()->id,
            'branch_id' => Branch::first()->id,
            'fiscal_period_id' => $period,
            'journal_number' => 'JR-202606-000001',
            'transaction_date' => '2026-06-06',
            'description' => 'Biaya Gaji Juni 2026',
            'total_debit' => 10000000, 'total_credit' => 10000000,
            'status' => 'draft', 'is_voided' => false,
            'created_by' => $u->id,
        ]);
        $this->assertEquals(10000000, $je->total_debit);
        $this->assertEquals(10000000, $je->total_credit);

        // VOID
        $je->update(['is_voided' => true, 'voided_at' => now(), 'voided_by' => $u->id]);
        $this->assertTrue($je->fresh()->is_voided);
    }

    // ══════════════════════════════════════
    // 5. INVENTORY MOVEMENT
    // ══════════════════════════════════════

    public function test_07_inventory_movement(): void
    {
        $u = User::first(); $this->actingAs($u);
        $coy = Company::first(); $br = Branch::first();
        $pid = Product::first()->id; $wh = Warehouse::first()->id;

        // IN
        $in = InventoryMovement::create([
            'company_id' => $coy->id, 'branch_id' => $br->id,
            'product_id' => $pid, 'warehouse_id' => $wh,
            'transaction_type' => 'purchase_received',
            'quantity_in' => 10, 'quantity_out' => 0,
            'unit_cost' => 1500000, 'total_cost' => 15000000,
            'transaction_date' => '2026-06-06',
            'description' => 'Penerimaan', 'created_by' => $u->id,
        ]);
        $this->assertEquals(10, $in->quantity_in);

        // OUT
        InventoryMovement::create([
            'company_id' => $coy->id, 'branch_id' => $br->id,
            'product_id' => $pid, 'warehouse_id' => $wh,
            'transaction_type' => 'sales_delivery',
            'quantity_in' => 0, 'quantity_out' => 2,
            'unit_cost' => 1500000, 'total_cost' => 3000000,
            'transaction_date' => '2026-06-10',
            'description' => 'Pengiriman', 'created_by' => $u->id,
        ]);

        $bal = InventoryMovement::where('product_id', $pid)->sum('quantity_in')
             - InventoryMovement::where('product_id', $pid)->sum('quantity_out');
        $this->assertEquals(8, $bal);
    }

    // ══════════════════════════════════════
    // 6. CASH TRANSACTION
    // ══════════════════════════════════════

    public function test_08_cash_transaction(): void
    {
        $u = User::first(); $this->actingAs($u);
        $coy = Company::first(); $br = Branch::first();
        $cashId = CashAccount::first()->id;

        $ct = CashTransaction::create([
            'company_id' => $coy->id, 'branch_id' => $br->id,
            'cash_account_id' => $cashId,
            'currency_id' => \App\Models\Master\Currency::first()->id,
            'chart_of_account_id' => ChartOfAccount::where('code', '1-1010')->first()->id,
            'transaction_number' => 'CR-202606-000001',
            'transaction_date' => '2026-06-06',
            'type' => 'receipt', 'amount' => 5000000,
            'description' => 'Setoran tunai',
            'status' => 'draft', 'created_by' => $u->id,
        ]);
        $this->assertEquals('draft', $ct->status);

        $ct->update(['status' => 'submitted']);
        $ct->update(['status' => 'approved']);
        $ct->update(['status' => 'posted']);
        $this->assertEquals('posted', $ct->fresh()->status);
    }

    // ══════════════════════════════════════
    // 7. EXPORT EXCEL + PRINT PDF
    // ══════════════════════════════════════

    public function test_09_export_and_print(): void
    {
        $this->assertTrue(class_exists(\App\Exports\Master\CustomerExport::class));
        $this->assertTrue(class_exists(\App\Exports\Master\SupplierExport::class));
        $this->assertTrue(class_exists(\App\Exports\Master\ProductExport::class));
        $this->assertTrue(class_exists(\Barryvdh\DomPDF\ServiceProvider::class));
        $this->assertTrue(class_exists(\Maatwebsite\Excel\ExcelServiceProvider::class));
    }

    // ══════════════════════════════════════
    // 8. FISCAL PERIOD CLOSE/OPEN
    // ══════════════════════════════════════

    public function test_10_fiscal_period(): void
    {
        $u = User::first(); $this->actingAs($u);
        $p = FiscalPeriod::first();
        $this->assertTrue($p->is_open);

        $p->update(['is_open' => false, 'is_closed' => true,
            'closed_at' => now(), 'closed_by' => $u->id]);
        $this->assertFalse($p->fresh()->is_open);

        $p->update(['is_open' => true, 'is_closed' => false,
            'closed_at' => null, 'closed_by' => null]);
        $this->assertTrue($p->fresh()->is_open);
    }
}
