@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Products</h1>
            <div class="space-x-3">
                <a href="{{ route('master.products.export-excel') }}" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm">Export Excel</a>
                <a href="{{ route('master.products.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">Create New</a>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Selling Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Average Cost</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($products ?? [] as $product)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $product->code }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $product->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $product->unit }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ number_format($product->selling_price, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ number_format($product->average_cost, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-sm">
                                <a href="{{ route('master.products.show', $product) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                <a href="{{ route('master.products.edit', $product) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-sm text-gray-500 text-center">No products found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if (isset($products) && $products instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="mt-4">{{ $products->links() }}</div>
        @endif
    </div>
</div>
@endsection