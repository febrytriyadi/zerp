<?php
namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\BankStatement;
use App\Models\Finance\BankStatementLine;
use App\Models\Master\BankAccount;
use App\Models\Master\Currency;
use App\Services\Finance\BankStatementService;
use Illuminate\Http\Request;

class BankStatementController extends Controller
{
    public function __construct(protected BankStatementService $bankStatementService) {}

    public function index()
    {
        $statements = BankStatement::with(['bankAccount', 'createdBy'])
            ->orderBy('statement_date', 'desc')
            ->paginate(10);

        return view('finance.bank-statements.index', compact('statements'));
    }

    public function create()
    {
        $bankAccounts = BankAccount::where('is_active', true)->orderBy('bank_name')->get();
        $currencies = Currency::where('is_active', true)->orderBy('code')->get();

        return view('finance.bank-statements.create', compact('bankAccounts', 'currencies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'statement_number' => 'required|max:100|unique:bank_statements',
            'statement_date' => 'required|date',
            'beginning_balance' => 'required|numeric|min:0',
            'ending_balance' => 'required|numeric|min:0',
            'total_deposits' => 'required|numeric|min:0',
            'total_withdrawals' => 'required|numeric|min:0',
            'currency_id' => 'nullable|exists:currencies,id',
            'exchange_rate' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $this->bankStatementService->import($validated, $request->file('import_file'));

        return redirect()->route('finance.bank-statements.index')
            ->with('success', 'Bank statement berhasil dibuat.');
    }

    public function show(BankStatement $bankStatement)
    {
        $bankStatement->load(['bankAccount', 'currency', 'lines', 'createdBy', 'postedBy']);

        return view('finance.bank-statements.show', compact('bankStatement'));
    }

    public function edit(BankStatement $bankStatement)
    {
        $bankAccounts = BankAccount::where('is_active', true)->orderBy('bank_name')->get();
        $currencies = Currency::where('is_active', true)->orderBy('code')->get();

        return view('finance.bank-statements.edit', compact('bankStatement', 'bankAccounts', 'currencies'));
    }

    public function update(Request $request, BankStatement $bankStatement)
    {
        $validated = $request->validate([
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'statement_number' => 'required|max:100|unique:bank_statements,statement_number,' . $bankStatement->id,
            'statement_date' => 'required|date',
            'beginning_balance' => 'required|numeric|min:0',
            'ending_balance' => 'required|numeric|min:0',
            'total_deposits' => 'required|numeric|min:0',
            'total_withdrawals' => 'required|numeric|min:0',
            'currency_id' => 'nullable|exists:currencies,id',
            'exchange_rate' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $bankStatement->update($validated);

        return redirect()->route('finance.bank-statements.index')
            ->with('success', 'Bank statement berhasil diupdate.');
    }

    public function destroy(BankStatement $bankStatement)
    {
        $bankStatement->delete();

        return redirect()->route('finance.bank-statements.index')
            ->with('success', 'Bank statement berhasil dihapus.');
    }

    public function post(BankStatement $bankStatement)
    {
        $this->bankStatementService->postStatement($bankStatement);

        return redirect()->route('finance.bank-statements.index')
            ->with('success', 'Bank statement berhasil diposting.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'statement_number' => 'required|max:100|unique:bank_statements',
            'statement_date' => 'required|date',
            'beginning_balance' => 'required|numeric|min:0',
            'import_file' => 'nullable|file|mimes:csv,xlsx,xls',
        ]);

        $data = $request->only([
            'bank_account_id', 'statement_number', 'statement_date',
            'beginning_balance', 'notes',
        ]);

        $this->bankStatementService->import($data, $request->file('import_file'));

        return redirect()->route('finance.bank-statements.index')
            ->with('success', 'Bank statement berhasil diimport.');
    }

    public function matchLine(Request $request, BankStatementLine $line)
    {
        $validated = $request->validate([
            'matched_transaction_type' => 'required|string|max:50',
            'matched_transaction_id' => 'required|integer',
        ]);

        $this->bankStatementService->matchLine(
            $line,
            $validated['matched_transaction_type'],
            $validated['matched_transaction_id']
        );

        return redirect()->back()
            ->with('success', 'Transaksi berhasil dicocokkan.');
    }

    public function unmatchLine(BankStatementLine $line)
    {
        $this->bankStatementService->unmatchLine($line);

        return redirect()->back()
            ->with('success', 'Pencocokan transaksi berhasil dibatalkan.');
    }
}
