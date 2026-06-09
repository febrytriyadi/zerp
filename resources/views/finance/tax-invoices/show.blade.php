@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('finance.tax-invoices.index') }}" class="text-blue-600 hover:text-blue-900 text-sm">&larr; Back to Faktur Pajak</a>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-lg font-medium text-gray-900">Faktur Pajak Detail</h2>
                        <span class="text-sm text-gray-500">{{ $taxInvoice->tax_invoice_number }}</span>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Tax Invoice Number</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $taxInvoice->tax_invoice_number }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Tax Invoice Date</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $taxInvoice->tax_invoice_date }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Transaction Type</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $taxInvoice->transaction_type }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Customer</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $taxInvoice->customer->name ?? $taxInvoice->customer_id ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Supplier</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $taxInvoice->supplier->name ?? $taxInvoice->supplier_id ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Taxpayer Name</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $taxInvoice->taxpayer_name }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Taxpayer NPWP</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $taxInvoice->taxpayer_npwp }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Taxpayer Address</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $taxInvoice->taxpayer_address ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">DPP</dt>
                                <dd class="mt-1 text-sm text-gray-900">Rp {{ number_format($taxInvoice->dpp, 0, ',', '.') }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">PPN Amount</dt>
                                <dd class="mt-1 text-sm text-gray-900">Rp {{ number_format($taxInvoice->ppn_amount, 0, ',', '.') }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">PPNBM Amount</dt>
                                <dd class="mt-1 text-sm text-gray-900">Rp {{ number_format($taxInvoice->ppnbm_amount ?? 0, 0, ',', '.') }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Status</dt>
                                <dd class="mt-1">
                                    @if ($taxInvoice->status === 'draft') <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Draft</span>
                                    @elseif ($taxInvoice->status === 'submitted') <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">Submitted</span>
                                    @elseif ($taxInvoice->status === 'approved') <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Approved</span>
                                    @elseif ($taxInvoice->status === 'rejected') <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-700 rounded-full">Rejected</span>
                                    @elseif ($taxInvoice->status === 'posted') <span class="px-2 py-1 text-xs font-medium bg-purple-100 text-purple-700 rounded-full">Posted</span>
                                    @elseif ($taxInvoice->status === 'voided') <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">Voided</span>
                                    @else <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">{{ $taxInvoice->status }}</span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="space-y-6">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-md font-medium text-gray-900">Informasi</h3>
                    </div>
                    <div class="p-6">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
