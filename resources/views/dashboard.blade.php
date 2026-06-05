<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Saldo Kas</p>
                            <p class="text-lg font-semibold text-gray-900">Rp {{ number_format($finance_summary['cash_balance'] ?? 0, 0, ',', '.') }}</p>
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
                            <p class="text-lg font-semibold text-gray-900">Rp {{ number_format($finance_summary['bank_balance'] ?? 0, 0, ',', '.') }}</p>
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
                            <p class="text-lg font-semibold text-gray-900">Rp {{ number_format($finance_summary['total_receivables'] ?? 0, 0, ',', '.') }}</p>
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
                            <p class="text-lg font-semibold text-gray-900">Rp {{ number_format($finance_summary['total_payables'] ?? 0, 0, ',', '.') }}</p>
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
                        <dl class="grid grid-cols-2 gap-4">
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <dt class="text-xs text-gray-500 uppercase">Sales Orders</dt>
                                <dd class="text-lg font-semibold text-gray-900">{{ $pending_approvals['sales_orders'] ?? 0 }}</dd>
                            </div>
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <dt class="text-xs text-gray-500 uppercase">Sales Invoices</dt>
                                <dd class="text-lg font-semibold text-gray-900">{{ $pending_approvals['sales_invoices'] ?? 0 }}</dd>
                            </div>
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <dt class="text-xs text-gray-500 uppercase">Purchase Orders</dt>
                                <dd class="text-lg font-semibold text-gray-900">{{ $pending_approvals['purchase_orders'] ?? 0 }}</dd>
                            </div>
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <dt class="text-xs text-gray-500 uppercase">Cash Transaksi</dt>
                                <dd class="text-lg font-semibold text-gray-900">{{ $pending_approvals['cash_transactions'] ?? 0 }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Invoice Jatuh Tempo</h3>
                    </div>
                    <div class="p-6">
                        @forelse ($due_invoices ?? [] as $invoice)
                            <div class="flex justify-between items-center py-2 border-b border-gray-100 last:border-0">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $invoice['invoice_number'] ?? '-' }}</p>
                                    <p class="text-xs text-gray-500">Jatuh tempo: {{ $invoice['due_date'] ?? '-' }}</p>
                                </div>
                                <span class="text-sm font-semibold text-red-600">Rp {{ number_format($invoice['outstanding'] ?? 0, 0, ',', '.') }}</span>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 text-center py-4">Tidak ada invoice jatuh tempo</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
