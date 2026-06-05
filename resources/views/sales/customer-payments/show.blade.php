@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('sales.customer-payments.index') }}" class="text-blue-600 hover:text-blue-900 text-sm">&larr; Back to Customer Payments</a>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-lg font-medium text-gray-900">Customer Payment Detail</h2>
                        <span class="text-sm text-gray-500">#{{ $customerPayment->payment_number }}</span>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Payment Number</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $customerPayment->payment_number }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Payment Date</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $customerPayment->payment_date }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Customer</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $customerPayment->customer->name ?? $customerPayment->customer_id }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Invoice</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $customerPayment->salesInvoice->invoice_number ?? $customerPayment->sales_invoice_id }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Amount</dt>
                                <dd class="mt-1 text-sm text-gray-900">Rp {{ number_format($customerPayment->amount, 0, ',', '.') }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Payment Method</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ Str::title(str_replace('_', ' ', $customerPayment->payment_method)) }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Cash Account</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $customerPayment->cashAccount->name ?? $customerPayment->cash_account_id }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Status</dt>
                                <dd class="mt-1">
                                    @if ($customerPayment->status === 'draft') <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Draft</span>
                                    @elseif ($customerPayment->status === 'submitted') <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">Submitted</span>
                                    @elseif ($customerPayment->status === 'approved') <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Approved</span>
                                    @elseif ($customerPayment->status === 'rejected') <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-700 rounded-full">Rejected</span>
                                    @elseif ($customerPayment->status === 'posted') <span class="px-2 py-1 text-xs font-medium bg-purple-100 text-purple-700 rounded-full">Posted</span>
                                    @elseif ($customerPayment->status === 'voided') <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">Voided</span>
                                    @else <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">{{ $customerPayment->status }}</span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                        @if ($customerPayment->description)
                        <div class="mt-4">
                            <dt class="text-xs font-medium text-gray-500 uppercase">Description</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $customerPayment->description }}</dd>
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
                        @if ($customerPayment->status === 'draft')
                            <form action="{{ route('sales.customer-payments.submit', $customerPayment) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">Submit</button>
                            </form>
                        @endif
                        @if ($customerPayment->status === 'submitted')
                            <form action="{{ route('sales.customer-payments.approve', $customerPayment) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm">Approve</button>
                            </form>
                            <form action="{{ route('sales.customer-payments.reject', $customerPayment) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm">Reject</button>
                            </form>
                        @endif
                        @if ($customerPayment->status === 'approved')
                            <form action="{{ route('sales.customer-payments.post', $customerPayment) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 text-sm">Post</button>
                            </form>
                        @endif
                        @if (in_array($customerPayment->status, ['draft', 'submitted', 'approved']))
                            <form action="{{ route('sales.customer-payments.void', $customerPayment) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 text-sm">Void</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
