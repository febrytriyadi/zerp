<?php
namespace App\Services\Finance;

use App\Models\Finance\PaymentRun;
use App\Models\Finance\PaymentRunItem;
use App\Models\Purchasing\PurchaseInvoice;
use App\Services\NumberingService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PaymentRunService
{
    public function __construct(
        protected NumberingService $numberingService,
    ) {}

    public function generateProposal(array $supplierIds = []): PaymentRun
    {
        $query = PurchaseInvoice::where('status', 'posted')
            ->where('outstanding_amount', '>', 0)
            ->where('due_date', '<=', now());

        if (!empty($supplierIds)) {
            $query->whereIn('supplier_id', $supplierIds);
        }

        $invoices = $query->get();

        if ($invoices->isEmpty()) {
            throw new \RuntimeException('Tidak ada faktur jatuh tempo yang ditemukan untuk proposal pembayaran.');
        }

        return DB::transaction(function () use ($invoices) {
            $companyId = $invoices->first()->company_id;
            $branchId = $invoices->first()->branch_id;

            $run = PaymentRun::create([
                'company_id' => $companyId,
                'branch_id' => $branchId,
                'run_number' => $this->numberingService->generate(
                    'payment_run',
                    $companyId,
                    $branchId,
                    now()->toDateString()
                ),
                'run_date' => now(),
                'payment_method' => 'bank_transfer',
                'total_amount' => 0,
                'created_by' => auth()->id(),
                'status' => 'draft',
            ]);

            $totalAmount = 0;
            $supplierIds = [];

            foreach ($invoices as $invoice) {
                $discount = $this->calculateDiscount(
                    $invoice->outstanding_amount,
                    $invoice->due_date->toDateString()
                );

                $paymentAmount = $invoice->outstanding_amount - $discount['discount_amount'];
                $netPayment = $paymentAmount - 0;

                $run->items()->create([
                    'supplier_id' => $invoice->supplier_id,
                    'invoice_type' => 'purchase_invoice',
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'due_date' => $invoice->due_date,
                    'original_amount' => $invoice->total,
                    'outstanding_amount' => $invoice->outstanding_amount,
                    'discount_percent' => $discount['discount_percent'],
                    'discount_amount' => $discount['discount_amount'],
                    'payment_amount' => $paymentAmount,
                    'withholding_tax_amount' => 0,
                    'net_payment' => $netPayment,
                    'status' => 'pending',
                ]);

                $totalAmount += $netPayment;
                $supplierIds[$invoice->supplier_id] = true;
            }

            $run->update([
                'total_suppliers' => count($supplierIds),
                'total_invoices' => $invoices->count(),
                'total_amount' => $totalAmount,
            ]);

            return $run;
        });
    }

    public function calculateDiscount(float $outstandingAmount, string $dueDate): array
    {
        $due = Carbon::parse($dueDate);
        $daysUntilDue = now()->diffInDays($due, false);

        if ($daysUntilDue >= 10) {
            return [
                'discount_percent' => 2.00,
                'discount_amount' => round($outstandingAmount * 0.02, 2),
            ];
        }

        return [
            'discount_percent' => 0,
            'discount_amount' => 0,
        ];
    }

    public function postRun(PaymentRun $run): void
    {
        if ($run->status !== 'draft') {
            throw new \RuntimeException('Hanya payment run dengan status draft yang dapat diposting.');
        }

        DB::transaction(function () use ($run) {
            $run->update([
                'status' => 'posted',
                'posted_by' => auth()->id(),
                'posted_at' => now(),
            ]);

            $run->items()->update(['status' => 'posted']);
        });
    }

    public function voidRun(PaymentRun $run): void
    {
        if (!in_array($run->status, ['draft', 'posted'])) {
            throw new \RuntimeException('Payment run tidak dapat divoid.');
        }

        DB::transaction(function () use ($run) {
            $run->update(['status' => 'voided']);
            $run->items()->update(['status' => 'voided']);
        });
    }
}
