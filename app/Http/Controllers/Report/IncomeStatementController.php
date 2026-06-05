<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Master\ChartOfAccount;
use Illuminate\Http\Request;

class IncomeStatementController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->filled('start_date') ? $request->start_date : now()->startOfMonth()->toDateString();
        $endDate = $request->filled('end_date') ? $request->end_date : now()->toDateString();

        $revenues = ChartOfAccount::where('is_active', true)
            ->where('type', 'revenue')
            ->where('is_header', false)
            ->orderBy('code')
            ->get();

        $expenses = ChartOfAccount::where('is_active', true)
            ->where('type', 'expense')
            ->where('is_header', false)
            ->orderBy('code')
            ->get();

        $totalRevenue = $revenues->sum('balance');
        $totalExpense = $expenses->sum('balance');
        $netIncome = $totalRevenue - $totalExpense;

        return view('reports.income-statement', compact(
            'startDate', 'endDate', 'revenues', 'expenses',
            'totalRevenue', 'totalExpense', 'netIncome'
        ));
    }
}
