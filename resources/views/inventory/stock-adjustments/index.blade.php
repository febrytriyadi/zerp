@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Stock Adjustments</h1>
            <a href="{{ route('inventory.stock-adjustments.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">Create New</a>
        </div>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Adjustment #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Warehouse</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($stockAdjustments ?? [] as $adjustment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $adjustment->adjustment_number }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $adjustment->adjustment_date }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $adjustment->warehouse->name ?? $adjustment->warehouse_id }}</td>
                            <td class="px-6 py-4 text-sm"><span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">{{ $adjustment->adjustment_type }}</span></td>
                            <td class="px-6 py-4 text-sm">
                                @if ($adjustment->status === 'draft') <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Draft</span>
                                @elseif ($adjustment->status === 'submitted') <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">Submitted</span>
                                @elseif ($adjustment->status === 'approved') <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Approved</span>
                                @elseif ($adjustment->status === 'rejected') <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-700 rounded-full">Rejected</span>
                                @elseif ($adjustment->status === 'posted') <span class="px-2 py-1 text-xs font-medium bg-purple-100 text-purple-700 rounded-full">Posted</span>
                                @elseif ($adjustment->status === 'voided') <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">Voided</span>
                                @else <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">{{ $adjustment->status }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <a href="{{ route('inventory.stock-adjustments.show', $adjustment) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                @if ($adjustment->status === 'draft')
                                    <a href="{{ route('inventory.stock-adjustments.edit', $adjustment) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-sm text-gray-500 text-center">No stock adjustments found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if (isset($stockAdjustments) && $stockAdjustments instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="mt-4">{{ $stockAdjustments->links() }}</div>
        @endif
    </div>
</div>
@endsection