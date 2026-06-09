<?php
namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\BankAccountBalance;
use App\Models\Master\BankAccount;
use App\Services\Finance\BankStatementService;
use Illuminate\Http\Request;

class BankAccountBalanceController extends Controller
{
    public function __construct(protected BankStatementService $bankStatementService) {}

    public function index(Request $request)
    {
        $query = BankAccountBalance::with('bankAccount');

        if ($request->filled('bank_account_id')) {
            $query->where('bank_account_id', $request->bank_account_id);
        }

        if ($request->filled('start_date')) {
            $query->where('balance_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('balance_date', '<=', $request->end_date);
        }

        $balances = $query->orderBy('balance_date', 'desc')->paginate(10);
        $bankAccounts = BankAccount::where('is_active', true)->orderBy('bank_name')->get();

        return view('finance.bank-balances.index', compact('balances', 'bankAccounts'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'balance_date' => 'required|date',
        ]);

        $bankAccount = BankAccount::findOrFail($request->bank_account_id);

        $this->bankStatementService->updateBalance($bankAccount, $request->balance_date);

        return redirect()->route('finance.bank-balances.index')
            ->with('success', 'Saldo bank berhasil diperbarui.');
    }
}
