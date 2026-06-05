<?php
namespace App\Services\Sales;

use App\Data\CreateJournalData;
use App\Data\JournalLineData;
use App\Models\Finance\JournalEntry;
use App\Models\Sales\SalesInvoice;
use App\Models\Inventory\InventoryMovement;
use App\Services\Accounting\FiscalPeriodService;
use App\Services\Accounting\JournalService;
use App\Services\NumberingService;
use App\Services\Inventory\InventoryMovementService;
use App\Services\Inventory\AverageCostCalculator;
use Illuminate\Support\Facades\DB;

class SalesInvoiceService
{
    public function __construct(
        protected FiscalPeriodService $fiscalPeriodService,
        protected JournalService $journalService,
        protected NumberingService $numberingService,
        protected InventoryMovementService $inventoryMovementService,
        protected AverageCostCalculator $averageCostCalculator,
    ) {}

    public function post(SalesInvoice $invoice): void
    {
        if ($invoice->status !== 'approved') {
            throw new \RuntimeException('Only approved invoices can be posted.');
        }

        DB::transaction(function () use ($invoice) {
            $this->fiscalPeriodService->assertDateIsOpen(
                $invoice->company_id,
                $invoice->branch_id,
                $invoice->invoice_date->toDateString()
            );

            $invoice->invoice_number = $this->numberingService->generate(
                'sales_invoice',
                $invoice->company_id,
                $invoice->branch_id,
                $invoice->invoice_date->toDateString()
            );

            $arAccountId = $this->resolveCoaId($invoice->company_id, '1-1050');
            $revenueAccountId = $this->resolveCoaId($invoice->company_id, '4-1010');
            $vatOutputAccountId = $this->resolveCoaId($invoice->company_id, '2-1020');
            $cogsAccountId = $this->resolveCoaId($invoice->company_id, '5-1010');
            $inventoryAccountId = $this->resolveCoaId($invoice->company_id, '1-1060');

            $lines = [];
            $lines[] = new JournalLineData(
                chartOfAccountId: $arAccountId,
                debit: $invoice->total,
                credit: 0,
                description: 'Sales invoice receivable',
            );
            $lines[] = new JournalLineData(
                chartOfAccountId: $revenueAccountId,
                debit: 0,
                credit: $invoice->subtotal,
                description: 'Sales revenue',
            );

            if ($invoice->tax_amount > 0) {
                $lines[] = new JournalLineData(
                    chartOfAccountId: $vatOutputAccountId,
                    debit: 0,
                    credit: $invoice->tax_amount,
                    description: 'VAT output',
                );
            }

            $journalData = new CreateJournalData(
                companyId: $invoice->company_id,
                branchId: $invoice->branch_id,
                transactionDate: $invoice->invoice_date->toDateString(),
                description: "Sales invoice {$invoice->invoice_number}",
                referenceType: 'sales_invoice',
                referenceId: $invoice->id,
                lines: $lines,
            );

            $journal = $this->journalService->createAndPost($journalData);

            $totalCost = 0;
            $invoice->load('items.product');
            foreach ($invoice->items as $item) {
                $unitCost = $this->averageCostCalculator->calculate($item->product_id, $invoice->branch_id);
                $itemTotal = $unitCost * $item->quantity;
                $totalCost += $itemTotal;

                $this->inventoryMovementService->recordMovement([
                    'company_id' => $invoice->company_id,
                    'branch_id' => $invoice->branch_id,
                    'product_id' => $item->product_id,
                    'warehouse_id' => $invoice->branch_id,
                    'quantity_out' => $item->quantity,
                    'unit_cost' => $unitCost,
                    'total_cost' => $itemTotal,
                    'transaction_type' => 'sales_delivery',
                    'reference_type' => 'sales_invoice',
                    'reference_id' => $invoice->id,
                    'transaction_date' => $invoice->invoice_date->toDateString(),
                ]);
            }

            if ($totalCost > 0) {
                $cogsLines = [];
                $cogsLines[] = new JournalLineData(
                    chartOfAccountId: $cogsAccountId,
                    debit: $totalCost,
                    credit: 0,
                    description: 'Cost of goods sold',
                );
                $cogsLines[] = new JournalLineData(
                    chartOfAccountId: $inventoryAccountId,
                    debit: 0,
                    credit: $totalCost,
                    description: 'Inventory reduction',
                );

                $cogsJournalData = new CreateJournalData(
                    companyId: $invoice->company_id,
                    branchId: $invoice->branch_id,
                    transactionDate: $invoice->invoice_date->toDateString(),
                    description: "COGS for invoice {$invoice->invoice_number}",
                    referenceType: 'sales_invoice_cogs',
                    referenceId: $invoice->id,
                    lines: $cogsLines,
                );

                $this->journalService->createAndPost($cogsJournalData);
            }

            $invoice->status = 'posted';
            $invoice->posted_at = now();
            $invoice->posted_by = auth()->id();
            $invoice->save();
        });
    }

    public function void(SalesInvoice $invoice): void
    {
        DB::transaction(function () use ($invoice) {
            $journal = JournalEntry::where('reference_type', 'sales_invoice')
                ->where('reference_id', $invoice->id)
                ->where('is_voided', false)
                ->first();
            if ($journal) {
                $this->journalService->void($journal);
            }

            $cogsJournal = JournalEntry::where('reference_type', 'sales_invoice_cogs')
                ->where('reference_id', $invoice->id)
                ->where('is_voided', false)
                ->first();
            if ($cogsJournal) {
                $this->journalService->void($cogsJournal);
            }

            $invoice->status = 'voided';
            $invoice->voided_at = now();
            $invoice->voided_by = auth()->id();
            $invoice->save();
        });
    }

    private function resolveCoaId(int $companyId, string $code): int
    {
        return \App\Models\Master\ChartOfAccount::where('company_id', $companyId)
            ->where('code', $code)
            ->value('id');
    }
}
