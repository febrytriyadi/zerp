@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('master.chart-of-accounts.index') }}" class="text-blue-600 hover:text-blue-900 text-sm">&larr; Back to Chart of Accounts</a>
        </div>
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-lg font-medium text-gray-900">Chart of Account Detail</h2>
                <div class="flex items-center gap-2">
                    <a href="{{ route('master.chart-of-accounts.edit', $chartOfAccount) }}" class="px-3 py-1.5 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm">Edit</a>
                </div>
            </div>
            <div class="p-6">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Code</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $chartOfAccount->code }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Name</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $chartOfAccount->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Company</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $chartOfAccount->company->name ?? $chartOfAccount->company_id }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Type</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($chartOfAccount->type) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Normal Balance</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($chartOfAccount->normal_balance) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Parent</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $chartOfAccount->parent->code ?? '-' }} - {{ $chartOfAccount->parent->name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Level</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $chartOfAccount->level ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Is Header</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $chartOfAccount->is_header ? 'Yes' : 'No' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Balance</dt>
                        <dd class="mt-1 text-sm text-gray-900">Rp {{ number_format($chartOfAccount->balance ?? 0, 0, ',', '.') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Status</dt>
                        <dd class="mt-1">
                            @if ($chartOfAccount->is_active)
                                <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Active</span>
                            @else
                                <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">Inactive</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
