@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Chart of Accounts</h1>
            <a href="{{ route('master.chart-of-accounts.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">Create New</a>
        </div>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Normal Balance</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Level</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($chartOfAccounts ?? [] as $account)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $account->code }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $account->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $account->type }}</td>
                            <td class="px-6 py-4 text-sm"><span class="px-2 py-1 text-xs font-medium {{ $account->normal_balance === 'Debit' ? 'bg-blue-100 text-blue-700' : 'bg-orange-100 text-orange-700' }} rounded-full">{{ $account->normal_balance }}</span></td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $account->level }}</td>
                            <td class="px-6 py-4 text-sm">
                                <a href="{{ route('master.chart-of-accounts.show', $account) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                <a href="{{ route('master.chart-of-accounts.edit', $account) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-sm text-gray-500 text-center">No accounts found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if (isset($chartOfAccounts) && $chartOfAccounts instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="mt-4">{{ $chartOfAccounts->links() }}</div>
        @endif
    </div>
</div>
@endsection