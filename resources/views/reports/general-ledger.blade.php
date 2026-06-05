@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-semibold text-gray-900 mb-6">General Ledger Report</h1>

        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <form method="GET" action="{{ route('reports.general-ledger') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Account</label>
                    <select name="account_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="">All Accounts</option>
                        @foreach ($accounts ?? [] as $account)
                            <option value="{{ $account->id }}" {{ request('account_id') == $account->id ? 'selected' : '' }}>{{ $account->code }} - {{ $account->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">Filter</button>
                    <a href="{{ route('reports.general-ledger') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-sm ml-2">Reset</a>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Account</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Debit</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Credit</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Balance</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($entries ?? [] as $entry)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $entry->transaction_date }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $entry->account->code ?? $entry->account_id }} - {{ $entry->account->name ?? '' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $entry->description }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600 text-right">{{ $entry->debit ? number_format($entry->debit, 0, ',', '.') : '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600 text-right">{{ $entry->credit ? number_format($entry->credit, 0, ',', '.') : '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 text-right font-medium">{{ number_format($entry->balance, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-sm text-gray-500 text-center">No entries found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if (isset($entries) && $entries instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="mt-4">{{ $entries->links() }}</div>
        @endif
    </div>
</div>
@endsection