@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Faktur Pajak</h1>
            <a href="{{ route('finance.tax-invoices.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">Create New</a>
        </div>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tax Invoice #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Taxpayer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">DPP</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">PPN Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($taxInvoices ?? [] as $invoice)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $invoice->tax_invoice_number }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $invoice->tax_invoice_date }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $invoice->transaction_type }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $invoice->taxpayer_name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">Rp {{ number_format($invoice->dpp, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">Rp {{ number_format($invoice->ppn_amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-sm">
                                @if ($invoice->status === 'draft') <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Draft</span>
                                @elseif ($invoice->status === 'submitted') <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">Submitted</span>
                                @elseif ($invoice->status === 'approved') <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Approved</span>
                                @elseif ($invoice->status === 'rejected') <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-700 rounded-full">Rejected</span>
                                @elseif ($invoice->status === 'posted') <span class="px-2 py-1 text-xs font-medium bg-purple-100 text-purple-700 rounded-full">Posted</span>
                                @elseif ($invoice->status === 'voided') <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">Voided</span>
                                @else <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">{{ $invoice->status }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <a href="{{ route('finance.tax-invoices.show', $invoice) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                @if ($invoice->status === 'draft')
                                    <a href="{{ route('finance.tax-invoices.edit', $invoice) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-sm text-gray-500 text-center">No tax invoices found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if (isset($taxInvoices) && $taxInvoices instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="mt-4">{{ $taxInvoices->links() }}</div>
        @endif
    </div>
</div>
@endsection
