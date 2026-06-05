<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Data\CreateJournalData;
use App\Data\JournalLineData;
use App\Models\Master\ChartOfAccount;
use App\Models\Master\Customer;
use App\Models\Master\Warehouse;
use App\Models\Sales\SalesInvoice;
use App\Models\Sales\SalesReturn;
use App\Services\Accounting\JournalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class SalesReturnController extends Controller
{
    public function __construct(
        protected JournalService $journalService
    ) {}

    public function index()
    {
        $returns = SalesReturn::with('customer')->paginate(10);
        return view('sales.returns.index', compact('returns'));
    }

    public function create()
    {
        $customers = Customer::all();
        $invoices = SalesInvoice::all();
        $warehouses = Warehouse::all();
        return view('sales.returns.create', compact('customers', 'invoices', 'warehouses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'required|exists:branches,id',
            'return_number' => 'required|string|max:50',
            'return_date' => 'required|date',
            'sales_invoice_id' => 'required|exists:sales_invoices,id',
            'customer_id' => 'required|exists:customers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'return_type' => 'required|string|max:50',
            'description' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['status'] = 'draft';

        SalesReturn::create($validated);

        return redirect()->route('sales.returns.index')
            ->with('success', 'Sales return created successfully.');
    }

    public function show(SalesReturn $return)
    {
        $return->load('customer', 'items', 'salesInvoice');
        return view('sales.returns.show', compact('return'));
    }

    public function edit(SalesReturn $return)
    {
        $customers = Customer::all();
        $invoices = SalesInvoice::all();
        $warehouses = Warehouse::all();
        return view('sales.returns.edit', compact('return', 'customers', 'invoices', 'warehouses'));
    }

    public function update(Request $request, SalesReturn $return)
    {
        $validated = $request->validate([
            'return_date' => 'required|date',
            'sales_invoice_id' => 'required|exists:sales_invoices,id',
            'customer_id' => 'required|exists:customers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'return_type' => 'required|string|max:50',
            'description' => 'nullable|string',
        ]);

        $return->update($validated);

        return redirect()->route('sales.returns.index')
            ->with('success', 'Sales return updated successfully.');
    }

    public function destroy(SalesReturn $return)
    {
        $return->delete();
        return redirect()->route('sales.returns.index')
            ->with('success', 'Sales return deleted successfully.');
    }

    public function post(SalesReturn $return)
    {
        DB::transaction(function () use ($return) {
            $companyId = $return->company_id;
            $arAccountId = ChartOfAccount::where('company_id', $companyId)->where('code', Config::get('coa.accounts_receivable'))->value('id');
            $salesReturnAccountId = ChartOfAccount::where('company_id', $companyId)->where('code', Config::get('coa.sales_return'))->value('id');
            $inventoryAccountId = ChartOfAccount::where('company_id', $companyId)->where('code', Config::get('coa.inventory'))->value('id');
            $cogsAccountId = ChartOfAccount::where('company_id', $companyId)->where('code', Config::get('coa.cogs'))->value('id');

            $lines = [];
            $lines[] = new JournalLineData(
                chartOfAccountId: $salesReturnAccountId,
                debit: $return->items->sum('total'),
                credit: 0,
                description: 'Sales return',
            );
            $lines[] = new JournalLineData(
                chartOfAccountId: $arAccountId,
                debit: 0,
                credit: $return->items->sum('total'),
                description: 'Accounts receivable reduction',
            );

            $journalData = new CreateJournalData(
                companyId: $return->company_id,
                branchId: $return->branch_id,
                transactionDate: $return->return_date->toDateString(),
                description: "Sales return #{$return->return_number}",
                referenceType: 'sales_return',
                referenceId: $return->id,
                lines: $lines,
            );

            $journal = $this->journalService->createAndPost($journalData);

            $return->update([
                'status' => 'posted',
                'journal_entry_id' => $journal->id,
            ]);
        });

        return redirect()->route('sales.returns.index')
            ->with('success', 'Sales return posted.');
    }
}
