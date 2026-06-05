@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('inventory.stock-adjustments.index') }}" class="text-blue-600 hover:text-blue-900 text-sm">&larr; Back to Stock Adjustments</a>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-lg font-medium text-gray-900">Stock Adjustment Detail</h2>
                        <span class="text-sm text-gray-500">#{{ $stockAdjustment->adjustment_number }}</span>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Adjustment Number</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $stockAdjustment->adjustment_number }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Adjustment Date</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $stockAdjustment->adjustment_date }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Warehouse</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $stockAdjustment->warehouse->name ?? $stockAdjustment->warehouse_id }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Type</dt>
                                <dd class="mt-1"><span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">{{ $stockAdjustment->adjustment_type }}</span></dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Status</dt>
                                <dd class="mt-1">
                                    @if ($stockAdjustment->status === 'draft') <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Draft</span>
                                    @elseif ($stockAdjustment->status === 'submitted') <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">Submitted</span>
                                    @elseif ($stockAdjustment->status === 'approved') <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Approved</span>
                                    @elseif ($stockAdjustment->status === 'rejected') <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-700 rounded-full">Rejected</span>
                                    @elseif ($stockAdjustment->status === 'posted') <span class="px-2 py-1 text-xs font-medium bg-purple-100 text-purple-700 rounded-full">Posted</span>
                                    @elseif ($stockAdjustment->status === 'voided') <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">Voided</span>
                                    @else <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">{{ $stockAdjustment->status }}</span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                        @if ($stockAdjustment->reason)
                        <div class="mt-4">
                            <dt class="text-xs font-medium text-gray-500 uppercase">Reason</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $stockAdjustment->reason }}</dd>
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
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Current Qty</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Adjustment Qty</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">New Qty</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($stockAdjustment->items ?? [] as $item)
                                    <tr>
                                        <td class="px-3 py-2 text-sm text-gray-900">{{ $item->product->name ?? $item->product_id }}</td>
                                        <td class="px-3 py-2 text-sm text-gray-600 text-right">{{ $item->current_quantity }}</td>
                                        <td class="px-3 py-2 text-sm text-gray-600 text-right">{{ $item->adjustment_quantity }}</td>
                                        <td class="px-3 py-2 text-sm text-gray-900 text-right">{{ $item->new_quantity }}</td>
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
                        @if ($stockAdjustment->status === 'draft')
                            <form action="{{ route('inventory.stock-adjustments.submit', $stockAdjustment) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">Submit</button>
                            </form>
                        @endif
                        @if ($stockAdjustment->status === 'submitted')
                            <form action="{{ route('inventory.stock-adjustments.approve', $stockAdjustment) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm">Approve</button>
                            </form>
                            <form action="{{ route('inventory.stock-adjustments.reject', $stockAdjustment) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm">Reject</button>
                            </form>
                        @endif
                        @if ($stockAdjustment->status === 'approved')
                            <form action="{{ route('inventory.stock-adjustments.post', $stockAdjustment) }}" method="POST">
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
