<?php

namespace Tests\Feature;

use App\Models\Finance\TaxInvoice;
use App\Models\Finance\TaxReport;
use App\Models\Master\Branch;
use App\Models\Master\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaxManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Company $company;
    private Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->company = Company::create([
            'code' => 'TAX',
            'name' => 'Tax Test Co',
            'is_active' => true,
        ]);
        $this->branch = Branch::create([
            'company_id' => $this->company->id,
            'code' => 'HQ',
            'name' => 'Head Office',
            'is_active' => true,
        ]);
    }

    public function test_can_view_tax_invoice_index(): void
    {
        TaxInvoice::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'tax_invoice_number' => 'FK-202606-000001',
            'tax_invoice_date' => '2026-06-15',
            'transaction_type' => 'sales',
            'taxpayer_name' => 'PT Test',
            'taxpayer_npwp' => '01.234.567.8-999.000',
            'dpp' => 10000000,
            'ppn_amount' => 1100000,
            'ppnbm_amount' => 0,
            'status' => 'draft',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('finance.tax-invoices.index'));

        $response->assertStatus(200);
        $response->assertSee('FK-202606-000001');
    }

    public function test_can_create_tax_invoice(): void
    {
        $response = $this->actingAs($this->user)->post(route('finance.tax-invoices.store'), [
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'tax_invoice_number' => 'FK-202606-000002',
            'tax_invoice_date' => '2026-06-20',
            'transaction_type' => 'purchase',
            'taxpayer_name' => 'PT Supplier',
            'taxpayer_npwp' => '02.345.678.9-888.000',
            'taxpayer_address' => 'Jakarta',
            'dpp' => 5000000,
            'ppn_amount' => 550000,
            'ppnbm_amount' => 0,
        ]);

        $response->assertRedirect(route('finance.tax-invoices.index'));
        $this->assertDatabaseHas('tax_invoices', [
            'tax_invoice_number' => 'FK-202606-000002',
            'dpp' => 5000000,
        ]);
    }

    public function test_can_show_tax_invoice(): void
    {
        $invoice = TaxInvoice::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'tax_invoice_number' => 'FK-202606-000003',
            'tax_invoice_date' => '2026-06-25',
            'transaction_type' => 'sales',
            'taxpayer_name' => 'PT Customer',
            'taxpayer_npwp' => '03.456.789.0-777.000',
            'dpp' => 20000000,
            'ppn_amount' => 2200000,
            'ppnbm_amount' => 0,
            'status' => 'draft',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('finance.tax-invoices.show', $invoice));

        $response->assertStatus(200);
        $response->assertSee('PT Customer');
    }

    public function test_can_update_tax_invoice(): void
    {
        $invoice = TaxInvoice::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'tax_invoice_number' => 'FK-202606-000004',
            'tax_invoice_date' => '2026-06-25',
            'transaction_type' => 'sales',
            'taxpayer_name' => 'PT Lama',
            'taxpayer_npwp' => '04.567.890.1-666.000',
            'dpp' => 10000000,
            'ppn_amount' => 1100000,
            'ppnbm_amount' => 0,
            'status' => 'draft',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->put(route('finance.tax-invoices.update', $invoice), [
            'tax_invoice_number' => 'FK-202606-000004',
            'tax_invoice_date' => '2026-06-25',
            'transaction_type' => 'sales',
            'taxpayer_name' => 'PT Baru',
            'taxpayer_npwp' => '04.567.890.1-666.000',
            'dpp' => 15000000,
            'ppn_amount' => 1650000,
            'ppnbm_amount' => 0,
        ]);

        $response->assertRedirect(route('finance.tax-invoices.index'));
        $this->assertDatabaseHas('tax_invoices', [
            'id' => $invoice->id,
            'taxpayer_name' => 'PT Baru',
            'dpp' => 15000000,
        ]);
    }

    public function test_can_delete_tax_invoice(): void
    {
        $invoice = TaxInvoice::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'tax_invoice_number' => 'FK-202606-000005',
            'tax_invoice_date' => '2026-06-25',
            'transaction_type' => 'purchase',
            'taxpayer_name' => 'PT Hapus',
            'taxpayer_npwp' => '05.678.901.2-555.000',
            'dpp' => 3000000,
            'ppn_amount' => 330000,
            'ppnbm_amount' => 0,
            'status' => 'draft',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->delete(route('finance.tax-invoices.destroy', $invoice));

        $response->assertRedirect(route('finance.tax-invoices.index'));
        $this->assertSoftDeleted('tax_invoices', ['id' => $invoice->id]);
    }

    public function test_can_create_tax_report(): void
    {
        TaxInvoice::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'tax_invoice_number' => 'FK-202606-000006',
            'tax_invoice_date' => '2026-06-15',
            'transaction_type' => 'sales',
            'taxpayer_name' => 'PT Data',
            'taxpayer_npwp' => '06.789.012.3-444.000',
            'dpp' => 50000000,
            'ppn_amount' => 5500000,
            'ppnbm_amount' => 0,
            'status' => 'posted',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->post(route('finance.tax-reports.store'), [
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'report_type' => 'ppn_1111',
            'period_code' => '2026-06',
            'period_start' => '2026-06-01',
            'period_end' => '2026-06-30',
        ]);

        $response->assertRedirect(route('finance.tax-reports.index'));
        $this->assertDatabaseHas('tax_reports', [
            'report_type' => 'ppn_1111',
            'period_code' => '2026-06',
        ]);
    }

    public function test_can_view_tax_report_index(): void
    {
        $response = $this->actingAs($this->user)->get(route('finance.tax-reports.index'));

        $response->assertStatus(200);
    }

    public function test_tax_calculation_service_ppn(): void
    {
        $service = app(\App\Services\Finance\TaxCalculationService::class);

        $result = $service->calculatePpn(1000000);

        $this->assertEquals(1000000, $result['dpp']);
        $this->assertEquals(11, $result['rate']);
        $this->assertEquals(110000, $result['ppn']);
        $this->assertEquals(1110000, $result['total']);
    }

    public function test_tax_calculation_service_pph23(): void
    {
        $service = app(\App\Services\Finance\TaxCalculationService::class);

        $result = $service->calculatePph23(10000000);

        $this->assertEquals(10000000, $result['gross_amount']);
        $this->assertEquals(2, $result['rate']);
        $this->assertEquals(200000, $result['pph23']);
        $this->assertEquals(9800000, $result['net_amount']);
    }

    public function test_tax_calculation_service_pph21(): void
    {
        $service = app(\App\Services\Finance\TaxCalculationService::class);

        $result = $service->calculatePph21(100000000, [5000000]);

        $this->assertEquals(100000000, $result['gross_income']);
        $this->assertEquals(5000000, $result['deductions']);
        $this->assertEquals(95000000, $result['net_income']);
        $this->assertGreaterThan(0, $result['pph21']);
    }
}
