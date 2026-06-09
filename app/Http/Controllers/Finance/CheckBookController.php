<?php
namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\CheckBook;
use App\Models\Master\BankAccount;
use Illuminate\Http\Request;

class CheckBookController extends Controller
{
    public function index()
    {
        $checkBooks = CheckBook::with(['bankAccount', 'createdBy'])
            ->orderBy('issued_date', 'desc')
            ->paginate(10);

        return view('finance.check-books.index', compact('checkBooks'));
    }

    public function create()
    {
        $bankAccounts = BankAccount::where('is_active', true)->orderBy('bank_name')->get();

        return view('finance.check-books.create', compact('bankAccounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'check_book_number' => 'required|max:100',
            'start_number' => 'required|max:50',
            'end_number' => 'required|max:50',
            'current_number' => 'required|max:50',
            'status' => 'required|in:active,used,voided',
            'issued_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $validated['company_id'] = $request->company_id ?? auth()->user()->company_id ?? 1;
        $validated['branch_id'] = $request->branch_id ?? auth()->user()->branch_id ?? 1;
        $validated['created_by'] = auth()->id();

        CheckBook::create($validated);

        return redirect()->route('finance.check-books.index')
            ->with('success', 'Buku cek berhasil dibuat.');
    }

    public function show(CheckBook $checkBook)
    {
        $checkBook->load(['bankAccount', 'createdBy']);

        return view('finance.check-books.show', compact('checkBook'));
    }

    public function edit(CheckBook $checkBook)
    {
        $bankAccounts = BankAccount::where('is_active', true)->orderBy('bank_name')->get();

        return view('finance.check-books.edit', compact('checkBook', 'bankAccounts'));
    }

    public function update(Request $request, CheckBook $checkBook)
    {
        $validated = $request->validate([
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'check_book_number' => 'required|max:100',
            'start_number' => 'required|max:50',
            'end_number' => 'required|max:50',
            'current_number' => 'required|max:50',
            'status' => 'required|in:active,used,voided',
            'issued_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $checkBook->update($validated);

        return redirect()->route('finance.check-books.index')
            ->with('success', 'Buku cek berhasil diupdate.');
    }

    public function destroy(CheckBook $checkBook)
    {
        $checkBook->delete();

        return redirect()->route('finance.check-books.index')
            ->with('success', 'Buku cek berhasil dihapus.');
    }
}
