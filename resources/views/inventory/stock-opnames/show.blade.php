@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('inventory.stock-opnames.index') }}" class="text-blue-600 hover:text-blue-900 text-sm">&larr; Back to Stock Opnames</a>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-lg font-medium text-gray-900">Stock Opname Detail</h2>
                        <span class="text-sm text-gray-500">#{{ $stockOpname->opname_number }}</span>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Opname Number</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $stockOpname->opname_number }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Opname Date</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $stockOpname->opname_date }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Warehouse</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $stockOpname->warehouse->name ?? $stockOpname->warehouse_id }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Status</dt>
                                <dd class="mt-1">
                                    @if ($stockOpname->status === 'draft') <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Draft</span>
                                    @elseif ($stockOpname->status === 'submitted') <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">Submitted</span>
                                    @elseif ($stockOpname->status === 'approved') <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Approved</span>
                                    @elseif ($stockOpname->status === 'rejected') <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-700 rounded-full">Rejected</span>
                                    @elseif ($stockOpname->status === 'posted') <span class="px-2 py-1 text-xs font-medium bg-purple-100 text-purple-700 rounded-full">Posted</span>
                                    @elseif ($stockOpname->status === 'voided') <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">Voided</span>
                                    @else <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">{{ $stockOpname->status }}</span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                        @if ($stockOpname->notes)
                        <div class="mt-4">
                            <dt class="text-xs font-medium text-gray-500 uppercase">Notes</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $stockOpname->notes }}</dd>
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
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">System Qty</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Actual Qty</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Difference</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($stockOpname->items ?? [] as $item)
                                    <tr>
                                        <td class="px-3 py-2 text-sm text-gray-900">{{ $item->product->name ?? $item->product_id }}</td>
                                        <td class="px-3 py-2 text-sm text-gray-600 text-right">{{ $item->system_quantity }}</td>
                                        <td class="px-3 py-2 text-sm text-gray-600 text-right">{{ $item->actual_quantity }}</td>
                                        <td class="px-3 py-2 text-sm text-gray-900 text-right">{{ $item->difference }}</td>
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
                        @if ($stockOpname->status === 'draft')
                            <form action="{{ route('inventory.stock-opnames.submit', $stockOpname) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">Submit</button>
                            </form>
                        @endif
                        @if ($stockOpname->status === 'submitted')
                            <form action="{{ route('inventory.stock-opnames.approve', $stockOpname) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm">Approve</button>
                            </form>
                            <form action="{{ route('inventory.stock-opnames.reject', $stockOpname) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm">Reject</button>
                            </form>
                        @endif
                        @if ($stockOpname->status === 'approved')
                            <form action="{{ route('inventory.stock-opnames.post', $stockOpname) }}" method="POST">
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
