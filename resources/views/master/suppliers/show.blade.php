@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('master.suppliers.index') }}" class="text-blue-600 hover:text-blue-900 text-sm">&larr; Back to Suppliers</a>
        </div>
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-lg font-medium text-gray-900">Supplier Detail</h2>
                <div class="flex items-center gap-2">
                    <a href="{{ route('master.suppliers.edit', $supplier) }}" class="px-3 py-1.5 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm">Edit</a>
                </div>
            </div>
            <div class="p-6">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Code</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $supplier->code }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Name</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $supplier->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Company</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $supplier->company->name ?? $supplier->company_id }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Branch</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $supplier->branch->name ?? $supplier->branch_id }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Contact Person</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $supplier->contact_person ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Phone</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $supplier->phone ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Email</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $supplier->email ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Tax ID</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $supplier->tax_id ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Payment Term</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $supplier->paymentTerm->name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Chart of Account</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $supplier->chartOfAccount->code ?? '-' }} - {{ $supplier->chartOfAccount->name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Status</dt>
                        <dd class="mt-1">
                            @if ($supplier->is_active)
                                <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Active</span>
                            @else
                                <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">Inactive</span>
                            @endif
                        </dd>
                    </div>
                </dl>
                <div class="mt-4">
                    <dt class="text-xs font-medium text-gray-500 uppercase">Address</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $supplier->address ?? '-' }}</dd>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
