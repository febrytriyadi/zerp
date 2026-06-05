<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    public function index()
    {
        $companyId = Auth::user()->company_id ?? 1;
        $branchId = Auth::user()->branch_id ?? 1;

        return view('dashboard', [
            'finance_summary' => $this->dashboardService->getFinanceSummary($companyId, $branchId),
            'pending_approvals' => $this->dashboardService->getPendingApprovals($companyId),
            'due_invoices' => $this->dashboardService->getDueInvoices($companyId),
        ]);
    }
}
