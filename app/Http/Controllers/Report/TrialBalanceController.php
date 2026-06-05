<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Master\ChartOfAccount;
use Illuminate\Http\Request;

class TrialBalanceController extends Controller
{
    public function index(Request $request)
    {
        $query = ChartOfAccount::where('is_active', true)
            ->where('is_header', false);

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('date')) {
            $date = $request->date;
        } else {
            $date = now()->toDateString();
        }

        $accounts = $query->orderBy('code')->get();

        $totalDebit = $accounts->sum(function ($account) {
            return $account->balance > 0 ? $account->balance : 0;
        });

        $totalCredit = $accounts->sum(function ($account) {
            return $account->balance < 0 ? abs($account->balance) : 0;
        });

        return view('reports.trial-balance', compact('accounts', 'date', 'totalDebit', 'totalCredit'));
    }
}
