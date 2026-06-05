@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Warehouses</h1>
            <a href="{{ route('master.warehouses.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">Create New</a>
        </div>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($warehouses ?? [] as $warehouse)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $warehouse->code }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $warehouse->name }}</td>
                            <td class="px-6 py-4 text-sm">
                                <a href="{{ route('master.warehouses.show', $warehouse) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                <a href="{{ route('master.warehouses.edit', $warehouse) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-10 text-sm text-gray-500 text-center">No warehouses found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if (isset($warehouses) && $warehouses instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="mt-4">{{ $warehouses->links() }}</div>
        @endif
    </div>
</div>
@endsection