<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Master\ChartOfAccount;
use Illuminate\Http\Request;

class BalanceSheetController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->filled('date') ? $request->date : now()->toDateString();

        $assets = ChartOfAccount::where('is_active', true)
            ->where('type', 'asset')
            ->where('is_header', false)
            ->orderBy('code')
            ->get();

        $liabilities = ChartOfAccount::where('is_active', true)
            ->where('type', 'liability')
            ->where('is_header', false)
            ->orderBy('code')
            ->get();

        $equity = ChartOfAccount::where('is_active', true)
            ->where('type', 'equity')
            ->where('is_header', false)
            ->orderBy('code')
            ->get();

        $totalAssets = $assets->sum('balance');
        $totalLiabilities = $liabilities->sum('balance');
        $totalEquity = $equity->sum('balance');

        return view('reports.balance-sheet', compact(
            'date', 'assets', 'liabilities', 'equity',
            'totalAssets', 'totalLiabilities', 'totalEquity'
        ));
    }
}
