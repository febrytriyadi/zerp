<?php
namespace App\Services;

use App\Models\Finance\CashTransaction;
use App\Models\Master\ChartOfAccount;
use App\Models\Purchasing\PurchaseOrder;
use App\Models\Purchasing\PurchaseRequest;
use App\Models\Sales\SalesInvoice;
use App\Models\Sales\SalesOrder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getFinanceSummary(int $companyId, int $branchId): array
    {
        $coaByCode = function (string $code) use ($companyId) {
            return ChartOfAccount::where('company_id', $companyId)
                ->where('code', $code)
                ->first();
        };

        $cashAccount = $coaByCode('1-1010');
        $bankAccount = $coaByCode('1-1030');
        $arAccount = $coaByCode('1-1050');
        $apAccount = $coaByCode('2-1010');

        return [
            'cash_balance' => $cashAccount?->balance ?? 0,
            'bank_balance' => $bankAccount?->balance ?? 0,
            'total_receivables' => $arAccount?->balance ?? 0,
            'total_payables' => $apAccount?->balance ?? 0,
        ];
    }

    public function getPendingApprovals(int $companyId): array
    {
        $pendingSalesOrders = SalesOrder::where('company_id', $companyId)
            ->whereIn('status', ['draft', 'submitted'])
            ->count();

        $pendingSalesInvoices = SalesInvoice::where('company_id', $companyId)
            ->whereIn('status', ['draft', 'submitted'])
            ->count();

        $pendingPurchaseOrders = PurchaseOrder::where('company_id', $companyId)
            ->whereIn('status', ['draft', 'submitted'])
            ->count();

        $pendingPurchaseRequests = PurchaseRequest::where('company_id', $companyId)
            ->whereIn('status', ['draft', 'submitted'])
            ->count();

        $pendingCashTransactions = CashTransaction::where('company_id', $companyId)
            ->whereIn('status', ['draft', 'submitted'])
            ->count();

        return [
            'sales_orders' => $pendingSalesOrders,
            'sales_invoices' => $pendingSalesInvoices,
            'purchase_orders' => $pendingPurchaseOrders,
            'purchase_requests' => $pendingPurchaseRequests,
            'cash_transactions' => $pendingCashTransactions,
        ];
    }

    public function getDueInvoices(int $companyId): array
    {
        return SalesInvoice::where('company_id', $companyId)
            ->where('due_date', '<', Carbon::now())
            ->whereIn('status', ['posted', 'partially_paid'])
            ->orderBy('due_date')
            ->get()
            ->toArray();
    }
}
