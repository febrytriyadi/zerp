@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('purchasing.supplier-payments.index') }}" class="text-blue-600 hover:text-blue-900 text-sm">&larr; Back to Supplier Payments</a>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-lg font-medium text-gray-900">Supplier Payment Detail</h2>
                        <span class="text-sm text-gray-500">#{{ $supplierPayment->payment_number }}</span>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Payment Number</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $supplierPayment->payment_number }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Payment Date</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $supplierPayment->payment_date }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Supplier</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $supplierPayment->supplier->name ?? $supplierPayment->supplier_id }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Invoice</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $supplierPayment->purchaseInvoice->invoice_number ?? $supplierPayment->purchase_invoice_id }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Amount</dt>
                                <dd class="mt-1 text-sm text-gray-900">Rp {{ number_format($supplierPayment->amount, 0, ',', '.') }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Payment Method</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ Str::title(str_replace('_', ' ', $supplierPayment->payment_method)) }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Cash Account</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $supplierPayment->cashAccount->name ?? $supplierPayment->cash_account_id }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Status</dt>
                                <dd class="mt-1">
                                    @if ($supplierPayment->status === 'draft') <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Draft</span>
                                    @elseif ($supplierPayment->status === 'submitted') <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">Submitted</span>
                                    @elseif ($supplierPayment->status === 'approved') <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Approved</span>
                                    @elseif ($supplierPayment->status === 'rejected') <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-700 rounded-full">Rejected</span>
                                    @elseif ($supplierPayment->status === 'posted') <span class="px-2 py-1 text-xs font-medium bg-purple-100 text-purple-700 rounded-full">Posted</span>
                                    @elseif ($supplierPayment->status === 'voided') <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">Voided</span>
                                    @else <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">{{ $supplierPayment->status }}</span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                        @if ($supplierPayment->description)
                        <div class="mt-4">
                            <dt class="text-xs font-medium text-gray-500 uppercase">Description</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $supplierPayment->description }}</dd>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="space-y-6">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-md font-medium text-gray-900">Actions</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        @if ($supplierPayment->status === 'draft')
                            <form action="{{ route('purchasing.supplier-payments.submit', $supplierPayment) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">Submit</button>
                            </form>
                        @endif
                        @if ($supplierPayment->status === 'submitted')
                            <form action="{{ route('purchasing.supplier-payments.approve', $supplierPayment) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm">Approve</button>
                            </form>
                            <form action="{{ route('purchasing.supplier-payments.reject', $supplierPayment) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm">Reject</button>
                            </form>
                        @endif
                        @if ($supplierPayment->status === 'approved')
                            <form action="{{ route('purchasing.supplier-payments.post', $supplierPayment) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 text-sm">Post</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
