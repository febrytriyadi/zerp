@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-semibold text-gray-900 mb-6">Trial Balance</h1>

        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <form method="GET" action="{{ route('reports.trial-balance') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">As of Date</label>
                    <input type="date" name="as_of_date" value="{{ request('as_of_date', date('Y-m-d')) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">View</button>
                    <a href="{{ route('reports.trial-balance') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-sm ml-2">Reset</a>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Account Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Account Name</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Debit</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Credit</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($accounts ?? [] as $account)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $account->code }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $account->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600 text-right">{{ $account->debit_balance ? number_format($account->debit_balance, 0, ',', '.') : '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600 text-right">{{ $account->credit_balance ? number_format($account->credit_balance, 0, ',', '.') : '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-sm text-gray-500 text-center">No accounts found.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="2" class="px-6 py-3 text-sm font-medium text-gray-900 text-right">Total</td>
                        <td class="px-6 py-3 text-sm font-medium text-gray-900 text-right">{{ number_format(collect($accounts ?? [])->sum('debit_balance'), 0, ',', '.') }}</td>
                        <td class="px-6 py-3 text-sm font-medium text-gray-900 text-right">{{ number_format(collect($accounts ?? [])->sum('credit_balance'), 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection