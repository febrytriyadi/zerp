<?php
namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\TaxInvoice;
use App\Models\Master\Customer;
use App\Models\Master\Supplier;
use Illuminate\Http\Request;

class TaxInvoiceController extends Controller
{
    public function index()
    {
        $taxInvoices = TaxInvoice::with(['customer', 'supplier'])
            ->paginate(10);

        return view('finance.tax-invoices.index', compact('taxInvoices'));
    }

    public function create()
    {
        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();

        $transactionTypes = [
            'sales' => 'Penjualan',
            'purchase' => 'Pembelian',
            'sales_return' => 'Retur Penjualan',
            'purchase_return' => 'Retur Pembelian',
        ];

        return view('finance.tax-invoices.create', compact('customers', 'suppliers', 'transactionTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tax_invoice_number' => 'required|max:100|unique:tax_invoices',
            'tax_invoice_date' => 'required|date',
            'transaction_type' => 'required|in:sales,purchase,sales_return,purchase_return',
            'customer_id' => 'nullable|exists:customers,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'taxpayer_name' => 'required|max:200',
            'taxpayer_npwp' => 'required|max:30',
            'taxpayer_address' => 'nullable|max:500',
            'dpp' => 'required|numeric|min:0',
            'ppn_amount' => 'required|numeric|min:0',
            'ppnbm_amount' => 'required|numeric|min:0',
        ]);

        $validated['company_id'] = $request->company_id ?? auth()->user()->company_id ?? 1;
        $validated['branch_id'] = $request->branch_id ?? auth()->user()->branch_id ?? 1;
        $validated['created_by'] = auth()->id();

        TaxInvoice::create($validated);

        return redirect()->route('finance.tax-invoices.index')
            ->with('success', 'Faktur pajak berhasil dibuat.');
    }

    public function show(TaxInvoice $taxInvoice)
    {
        $taxInvoice->load(['customer', 'supplier', 'createdBy']);

        return view('finance.tax-invoices.show', compact('taxInvoice'));
    }

    public function edit(TaxInvoice $taxInvoice)
    {
        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();

        $transactionTypes = [
            'sales' => 'Penjualan',
            'purchase' => 'Pembelian',
            'sales_return' => 'Retur Penjualan',
            'purchase_return' => 'Retur Pembelian',
        ];

        return view('finance.tax-invoices.edit', compact('taxInvoice', 'customers', 'suppliers', 'transactionTypes'));
    }

    public function update(Request $request, TaxInvoice $taxInvoice)
    {
        $validated = $request->validate([
            'tax_invoice_number' => 'required|max:100|unique:tax_invoices,tax_invoice_number,' . $taxInvoice->id,
            'tax_invoice_date' => 'required|date',
            'transaction_type' => 'required|in:sales,purchase,sales_return,purchase_return',
            'customer_id' => 'nullable|exists:customers,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'taxpayer_name' => 'required|max:200',
            'taxpayer_npwp' => 'required|max:30',
            'taxpayer_address' => 'nullable|max:500',
            'dpp' => 'required|numeric|min:0',
            'ppn_amount' => 'required|numeric|min:0',
            'ppnbm_amount' => 'required|numeric|min:0',
        ]);

        $taxInvoice->update($validated);

        return redirect()->route('finance.tax-invoices.index')
            ->with('success', 'Faktur pajak berhasil diupdate.');
    }

    public function destroy(TaxInvoice $taxInvoice)
    {
        $taxInvoice->delete();

        return redirect()->route('finance.tax-invoices.index')
            ->with('success', 'Faktur pajak berhasil dihapus.');
    }
}
