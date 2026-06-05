@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Inventory Movements</h1>

        </div>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Warehouse</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Transaction Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qty In</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qty Out</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($movements ?? [] as $movement)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $movement->product->name ?? $movement->product_id }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $movement->warehouse->name ?? $movement->warehouse_id }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $movement->transaction_type }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $movement->quantity_in ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $movement->quantity_out ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $movement->transaction_date }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-sm text-gray-500 text-center">No movements found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if (isset($movements) && $movements instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="mt-4">{{ $movements->links() }}</div>
        @endif
    </div>
</div>
@endsection