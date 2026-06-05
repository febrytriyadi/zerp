@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-semibold text-gray-900 mb-6">Dashboard</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Saldo Kas</p>
                        <p class="text-lg font-semibold text-gray-900">Rp {{ number_format($saldoKas ?? 0, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Saldo Bank</p>
                        <p class="text-lg font-semibold text-gray-900">Rp {{ number_format($saldoBank ?? 0, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 text-red-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Total Piutang</p>
                        <p class="text-lg font-semibold text-gray-900">Rp {{ number_format($totalPiutang ?? 0, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Total Utang</p>
                        <p class="text-lg font-semibold text-gray-900">Rp {{ number_format($totalUtang ?? 0, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Transaksi Menunggu Approval</h3>
                </div>
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nomor</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nilai</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($pendingApprovals ?? [] as $item)
                                <tr>
                                    <td class="px-3 py-2 text-sm text-gray-900">{{ $item->type ?? '-' }}</td>
                                    <td class="px-3 py-2 text-sm text-gray-900">{{ $item->number ?? '-' }}</td>
                                    <td class="px-3 py-2 text-sm text-gray-900">Rp {{ number_format($item->amount ?? 0, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-3 py-4 text-sm text-gray-500 text-center">Tidak ada transaksi menunggu approval</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Invoice Jatuh Tempo</h3>
                </div>
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Invoice</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jatuh Tempo</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nilai</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($dueInvoices ?? [] as $invoice)
                                <tr>
                                    <td class="px-3 py-2 text-sm text-gray-900">{{ $invoice->invoice_number ?? '-' }}</td>
                                    <td class="px-3 py-2 text-sm text-gray-900">{{ $invoice->customer->name ?? '-' }}</td>
                                    <td class="px-3 py-2 text-sm text-gray-900">{{ $invoice->due_date ?? '-' }}</td>
                                    <td class="px-3 py-2 text-sm text-gray-900">Rp {{ number_format($invoice->outstanding ?? 0, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-3 py-4 text-sm text-gray-500 text-center">Tidak ada invoice jatuh tempo</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection