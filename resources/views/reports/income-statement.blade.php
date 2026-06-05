@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-semibold text-gray-900 mb-6">Income Statement</h1>

        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <form method="GET" action="{{ route('reports.income-statement') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                    <input type="date" name="date_from" value="{{ request('date_from', date('Y-m-01')) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                    <input type="date" name="date_to" value="{{ request('date_to', date('Y-m-d')) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">View</button>
                    <a href="{{ route('reports.income-statement') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-sm ml-2">Reset</a>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Account Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Account Name</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr class="bg-gray-50">
                        <td colspan="3" class="px-6 py-3 text-sm font-semibold text-gray-900">Revenue</td>
                    </tr>
                    @forelse ($revenues ?? [] as $revenue)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 pl-12">{{ $revenue->code }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $revenue->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600 text-right">{{ number_format($revenue->balance, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-sm text-gray-500 text-center pl-12">No revenue accounts found.</td>
                        </tr>
                    @endforelse
                    <tr class="bg-gray-100 font-semibold">
                        <td colspan="2" class="px-6 py-3 text-sm text-gray-900 text-right">Total Revenue</td>
                        <td class="px-6 py-3 text-sm text-gray-900 text-right">{{ number_format(collect($revenues ?? [])->sum('balance'), 0, ',', '.') }}</td>
                    </tr>
                    <tr class="bg-gray-50">
                        <td colspan="3" class="px-6 py-3 text-sm font-semibold text-gray-900">Cost of Goods Sold</td>
                    </tr>
                    @forelse ($cogs ?? [] as $cog)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 pl-12">{{ $cog->code }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $cog->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600 text-right">{{ number_format($cog->balance, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-sm text-gray-500 text-center pl-12">No COGS accounts found.</td>
                        </tr>
                    @endforelse
                    <tr class="bg-gray-100 font-semibold">
                        <td colspan="2" class="px-6 py-3 text-sm text-gray-900 text-right">Total COGS</td>
                        <td class="px-6 py-3 text-sm text-gray-900 text-right">{{ number_format(collect($cogs ?? [])->sum('balance'), 0, ',', '.') }}</td>
                    </tr>
                    <tr class="bg-gray-50">
                        <td colspan="3" class="px-6 py-3 text-sm font-semibold text-gray-900">Expenses</td>
                    </tr>
                    @forelse ($expenses ?? [] as $expense)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 pl-12">{{ $expense->code }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $expense->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600 text-right">{{ number_format($expense->balance, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-sm text-gray-500 text-center pl-12">No expense accounts found.</td>
                        </tr>
                    @endforelse
                    <tr class="bg-gray-100 font-semibold">
                        <td colspan="2" class="px-6 py-3 text-sm text-gray-900 text-right">Total Expenses</td>
                        <td class="px-6 py-3 text-sm text-gray-900 text-right">{{ number_format(collect($expenses ?? [])->sum('balance'), 0, ',', '.') }}</td>
                    </tr>
                </tbody>
                <tfoot class="bg-gray-200">
                    <tr>
                        <td colspan="2" class="px-6 py-3 text-sm font-bold text-gray-900 text-right">Net Income / (Loss)</td>
                        <td class="px-6 py-3 text-sm font-bold text-gray-900 text-right">{{ number_format($netIncome ?? 0, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
