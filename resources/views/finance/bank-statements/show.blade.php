@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('finance.bank-statements.index') }}" class="text-blue-600 hover:text-blue-900 text-sm">&larr; Kembali ke Bank Statement</a>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-lg font-medium text-gray-900">Detail Bank Statement</h2>
                        <span class="text-sm text-gray-500">{{ $bankStatement->statement_number }}</span>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Statement Number</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $bankStatement->statement_number }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Statement Date</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $bankStatement->statement_date }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Bank Account</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $bankStatement->bankAccount?->bank_name ?? '-' }} ({{ $bankStatement->bankAccount?->account_number ?? '-' }})</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Currency</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $bankStatement->currency?->code ?? 'IDR' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Beginning Balance</dt>
                                <dd class="mt-1 text-sm text-gray-900">Rp {{ number_format($bankStatement->beginning_balance, 0, ',', '.') }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Ending Balance</dt>
                                <dd class="mt-1 text-sm text-gray-900">Rp {{ number_format($bankStatement->ending_balance, 0, ',', '.') }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Total Deposits</dt>
                                <dd class="mt-1 text-sm text-gray-900">Rp {{ number_format($bankStatement->total_deposits, 0, ',', '.') }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Total Withdrawals</dt>
                                <dd class="mt-1 text-sm text-gray-900">Rp {{ number_format($bankStatement->total_withdrawals, 0, ',', '.') }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Exchange Rate</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ number_format($bankStatement->exchange_rate, 4, ',', '.') }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Status</dt>
                                <dd class="mt-1">
                                    @if ($bankStatement->status === 'draft') <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Draft</span>
                                    @elseif ($bankStatement->status === 'posted') <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Posted</span>
                                    @elseif ($bankStatement->status === 'voided') <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">Voided</span>
                                    @else <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">{{ $bankStatement->status }}</span>
                                    @endif
                                </dd>
                            </div>
                            @if ($bankStatement->notes)
                            <div class="sm:col-span-2">
                                <dt class="text-xs font-medium text-gray-500 uppercase">Notes</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $bankStatement->notes }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-md font-medium text-gray-900">Statement Lines</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Debit</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Kredit</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($bankStatement->lines ?? [] as $line)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $line->transaction_date }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $line->description }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600 text-right">Rp {{ number_format($line->debit, 0, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600 text-right">Rp {{ number_format($line->credit, 0, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-sm text-center">
                                            @if ($line->matching_status === 'matched')
                                                <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Matched</span>
                                            @else
                                                <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Unmatched</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-6 text-sm text-gray-500 text-center">Tidak ada transaksi.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="space-y-6">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-md font-medium text-gray-900">Informasi</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase">Dibuat Oleh</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $bankStatement->createdBy?->name ?? '-' }}</dd>
                        </div>
                        @if ($bankStatement->postedBy)
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase">Diposting Oleh</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $bankStatement->postedBy->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase">Diposting Pada</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $bankStatement->posted_at }}</dd>
                        </div>
                        @endif
                    </div>
                </div>
                @if ($bankStatement->status === 'draft')
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-md font-medium text-gray-900">Aksi</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <a href="{{ route('finance.bank-statements.edit', $bankStatement) }}" class="block w-full text-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm">Edit</a>
                        <form action="{{ route('finance.bank-statements.post', $bankStatement) }}" method="POST">
                            @csrf
                            <button type="submit" class="block w-full text-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm">Post</button>
                        </form>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
