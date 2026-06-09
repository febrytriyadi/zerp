@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Saldo Bank</h1>
        </div>

        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <form method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bank Account</label>
                    <select name="bank_account_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="">Semua Bank</option>
                        @foreach ($bankAccounts ?? [] as $account)
                            <option value="{{ $account->id }}" {{ request('bank_account_id') == $account->id ? 'selected' : '' }}>{{ $account->bank_name }} - {{ $account->account_number }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">Filter</button>
                    <a href="{{ route('finance.bank-balances.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 text-sm">Reset</a>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bank</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Saldo Awal</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Debit</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Kredit</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Saldo Akhir</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($balances ?? [] as $balance)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $balance->bankAccount?->bank_name ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $balance->balance_date }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600 text-right">Rp {{ number_format($balance->opening_balance, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600 text-right">Rp {{ number_format($balance->total_debit, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600 text-right">Rp {{ number_format($balance->total_credit, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900 text-right">Rp {{ number_format($balance->ending_balance, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-sm text-gray-500 text-center">Tidak ada data saldo bank.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if (isset($balances) && $balances instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="mt-4">{{ $balances->links() }}</div>
        @endif
    </div>
</div>
@endsection
