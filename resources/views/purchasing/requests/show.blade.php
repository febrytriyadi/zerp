@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('purchasing.requests.index') }}" class="text-blue-600 hover:text-blue-900 text-sm">&larr; Back to Purchase Requests</a>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-lg font-medium text-gray-900">Purchase Request Detail</h2>
                        <span class="text-sm text-gray-500">#{{ $purchaseRequest->request_number }}</span>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Request Number</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $purchaseRequest->request_number }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Request Date</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $purchaseRequest->request_date }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Requested By</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $purchaseRequest->requested_by }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Status</dt>
                                <dd class="mt-1">
                                    @if ($purchaseRequest->status === 'draft') <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Draft</span>
                                    @elseif ($purchaseRequest->status === 'submitted') <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">Submitted</span>
                                    @elseif ($purchaseRequest->status === 'approved') <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Approved</span>
                                    @elseif ($purchaseRequest->status === 'rejected') <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-700 rounded-full">Rejected</span>
                                    @elseif ($purchaseRequest->status === 'posted') <span class="px-2 py-1 text-xs font-medium bg-purple-100 text-purple-700 rounded-full">Posted</span>
                                    @elseif ($purchaseRequest->status === 'voided') <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">Voided</span>
                                    @else <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">{{ $purchaseRequest->status }}</span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                        @if ($purchaseRequest->notes)
                        <div class="mt-4">
                            <dt class="text-xs font-medium text-gray-500 uppercase">Notes</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $purchaseRequest->notes }}</dd>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-md font-medium text-gray-900">Items</h3>
                    </div>
                    <div class="p-6">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Qty</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Price</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($purchaseRequest->items ?? [] as $item)
                                    <tr>
                                        <td class="px-3 py-2 text-sm text-gray-900">{{ $item->product->name ?? $item->product_id }}</td>
                                        <td class="px-3 py-2 text-sm text-gray-600 text-right">{{ $item->quantity }}</td>
                                        <td class="px-3 py-2 text-sm text-gray-600 text-right">{{ number_format($item->price, 0, ',', '.') }}</td>
                                        <td class="px-3 py-2 text-sm text-gray-900 text-right">{{ number_format($item->total, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-3 py-4 text-sm text-gray-500 text-center">No items</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="space-y-6">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-md font-medium text-gray-900">Actions</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        @if ($purchaseRequest->status === 'draft')
                            <form action="{{ route('purchasing.requests.submit', $purchaseRequest) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">Submit</button>
                            </form>
                        @endif
                        @if ($purchaseRequest->status === 'submitted')
                            <form action="{{ route('purchasing.requests.approve', $purchaseRequest) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm">Approve</button>
                            </form>
                            <form action="{{ route('purchasing.requests.reject', $purchaseRequest) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm">Reject</button>
                            </form>
                        @endif
                        @if ($purchaseRequest->status === 'approved')
                            <form action="{{ route('purchasing.requests.post', $purchaseRequest) }}" method="POST">
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
