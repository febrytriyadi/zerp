<?php
namespace App\Services\Finance;

use App\Models\Finance\DunningLevel;
use App\Models\Finance\DunningRun;
use App\Models\Finance\DunningRunItem;
use App\Models\Purchasing\PurchaseInvoice;
use App\Models\Sales\SalesInvoice;
use Illuminate\Support\Facades\DB;

class DunningService
{
    public function generateRun(DunningLevel $level, array $customerIds = []): DunningRun
    {
        $overdueSalesInvoices = SalesInvoice::where('status', 'posted')
            ->where('outstanding_amount', '>', 0)
            ->where('due_date', '<', now())
            ->when(!empty($customerIds), fn($q) => $q->whereIn('customer_id', $customerIds))
            ->get();

        $overduePurchaseInvoices = PurchaseInvoice::where('status', 'posted')
            ->where('outstanding_amount', '>', 0)
            ->where('due_date', '<', now())
            ->when(!empty($customerIds), fn($q) => $q->whereIn('supplier_id', $customerIds))
            ->get();

        $items = collect();

        foreach ($overdueSalesInvoices as $inv) {
            $daysOverdue = now()->diffInDays($inv->due_date, false);
            $charge = $this->calculateCharge($inv->outstanding_amount, $level);
            $items->push([
                'customer_id' => $inv->customer_id,
                'invoice_type' => SalesInvoice::class,
                'invoice_id' => $inv->id,
                'invoice_number' => $inv->invoice_number,
                'due_date' => $inv->due_date,
                'days_overdue' => (int) $daysOverdue,
                'original_amount' => $inv->total,
                'outstanding_amount' => $inv->outstanding_amount,
                'dunning_charge' => $charge,
                'total_due' => $inv->outstanding_amount + $charge,
            ]);
        }

        foreach ($overduePurchaseInvoices as $inv) {
            $daysOverdue = now()->diffInDays($inv->due_date, false);
            $charge = $this->calculateCharge($inv->outstanding_amount, $level);
            $items->push([
                'customer_id' => $inv->supplier_id,
                'invoice_type' => PurchaseInvoice::class,
                'invoice_id' => $inv->id,
                'invoice_number' => $inv->invoice_number,
                'due_date' => $inv->due_date,
                'days_overdue' => (int) $daysOverdue,
                'original_amount' => $inv->total,
                'outstanding_amount' => $inv->outstanding_amount,
                'dunning_charge' => $charge,
                'total_due' => $inv->outstanding_amount + $charge,
            ]);
        }

        $uniqueCustomers = $items->pluck('customer_id')->unique()->count();

        return DB::transaction(function () use ($level, $items, $uniqueCustomers) {
            $run = DunningRun::create([
                'company_id' => $level->company_id,
                'branch_id' => $level->branch_id,
                'run_number' => 'DUN-' . now()->format('Ymd') . '-' . str_pad(DunningRun::withTrashed()->count() + 1, 4, '0', STR_PAD_LEFT),
                'run_date' => now()->toDateString(),
                'dunning_level_id' => $level->id,
                'total_customers' => $uniqueCustomers,
                'total_invoices' => $items->count(),
                'total_amount' => $items->sum('total_due'),
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]);

            foreach ($items as $item) {
                $run->items()->create($item);
            }

            return $run;
        });
    }

    public function calculateCharge(float $outstandingAmount, DunningLevel $level): float
    {
        $percentCharge = $outstandingAmount * ($level->charge_percent / 100);
        return round($percentCharge + $level->charge_amount, 2);
    }

    public function postRun(DunningRun $run): void
    {
        $run->update([
            'status' => 'posted',
            'posted_at' => now(),
            'posted_by' => auth()->id(),
        ]);
    }

    public function printLetter(DunningRunItem $item): string
    {
        return "Dunning letter for invoice {$item->invoice_number}";
    }
}
