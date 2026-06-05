@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Suppliers</h1>
            <div class="space-x-3">
                <a href="{{ route('master.suppliers.export-excel') }}" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm">Export Excel</a>
                <a href="{{ route('master.suppliers.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">Create New</a>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Credit Limit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($suppliers ?? [] as $supplier)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $supplier->code }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $supplier->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $supplier->phone }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $supplier->email }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ number_format($supplier->credit_limit, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-sm">
                                <a href="{{ route('master.suppliers.show', $supplier) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                <a href="{{ route('master.suppliers.edit', $supplier) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-sm text-gray-500 text-center">No suppliers found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if (isset($suppliers) && $suppliers instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="mt-4">{{ $suppliers->links() }}</div>
        @endif
    </div>
</div>
@endsection