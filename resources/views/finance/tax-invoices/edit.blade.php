@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('finance.tax-invoices.index') }}" class="text-blue-600 hover:text-blue-900 text-sm">&larr; Back to Faktur Pajak</a>
        </div>
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Edit Faktur Pajak</h2>
            </div>
            <form action="{{ route('finance.tax-invoices.update', $taxInvoice) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tax Invoice Number <span class="text-red-500">*</span></label>
                        <input type="text" name="tax_invoice_number" value="{{ old('tax_invoice_number', $taxInvoice->tax_invoice_number) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                        @error('tax_invoice_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tax Invoice Date <span class="text-red-500">*</span></label>
                        <input type="date" name="tax_invoice_date" value="{{ old('tax_invoice_date', $taxInvoice->tax_invoice_date) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                        @error('tax_invoice_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Transaction Type <span class="text-red-500">*</span></label>
                        <select name="transaction_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                            <option value="">Select Type</option>
                            <option value="penjualan" {{ old('transaction_type', $taxInvoice->transaction_type) == 'penjualan' ? 'selected' : '' }}>Penjualan</option>
                            <option value="pembelian" {{ old('transaction_type', $taxInvoice->transaction_type) == 'pembelian' ? 'selected' : '' }}>Pembelian</option>
                            <option value="retur_penjualan" {{ old('transaction_type', $taxInvoice->transaction_type) == 'retur_penjualan' ? 'selected' : '' }}>Retur Penjualan</option>
                            <option value="retur_pembelian" {{ old('transaction_type', $taxInvoice->transaction_type) == 'retur_pembelian' ? 'selected' : '' }}>Retur Pembelian</option>
                            <option value="lainnya" {{ old('transaction_type', $taxInvoice->transaction_type) == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        @error('transaction_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Customer</label>
                        <select name="customer_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <option value="">Select Customer</option>
                            @foreach ($customers ?? [] as $customer)
                                <option value="{{ $customer->id }}" {{ old('customer_id', $taxInvoice->customer_id) == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                            @endforeach
                        </select>
                        @error('customer_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                        <select name="supplier_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <option value="">Select Supplier</option>
                            @foreach ($suppliers ?? [] as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id', $taxInvoice->supplier_id) == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                        @error('supplier_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Taxpayer Name <span class="text-red-500">*</span></label>
                        <input type="text" name="taxpayer_name" value="{{ old('taxpayer_name', $taxInvoice->taxpayer_name) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                        @error('taxpayer_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Taxpayer NPWP <span class="text-red-500">*</span></label>
                        <input type="text" name="taxpayer_npwp" value="{{ old('taxpayer_npwp', $taxInvoice->taxpayer_npwp) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                        @error('taxpayer_npwp') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Taxpayer Address</label>
                        <textarea name="taxpayer_address" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">{{ old('taxpayer_address', $taxInvoice->taxpayer_address) }}</textarea>
                        @error('taxpayer_address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">DPP <span class="text-red-500">*</span></label>
                        <input type="number" step="0.01" name="dpp" value="{{ old('dpp', $taxInvoice->dpp) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                        @error('dpp') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">PPN Amount <span class="text-red-500">*</span></label>
                        <input type="number" step="0.01" name="ppn_amount" value="{{ old('ppn_amount', $taxInvoice->ppn_amount) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                        @error('ppn_amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">PPNBM Amount</label>
                        <input type="number" step="0.01" name="ppnbm_amount" value="{{ old('ppnbm_amount', $taxInvoice->ppnbm_amount ?? 0) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        @error('ppnbm_amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="flex justify-end mt-6">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
