@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('finance.dunning-runs.index') }}" class="text-blue-600 hover:text-blue-900 text-sm">&larr; Kembali ke Dunning Run</a>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-lg font-medium text-gray-900">Detail Dunning Run</h2>
                        <span class="text-sm text-gray-500">{{ $dunningRun->run_number }}</span>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">No. Run</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $dunningRun->run_number }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Tanggal Run</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $dunningRun->run_date }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Level Dunning</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $dunningRun->dunningLevel?->name ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Status</dt>
                                <dd class="mt-1">
                                    @if ($dunningRun->status === 'draft') <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Draft</span>
                                    @elseif ($dunningRun->status === 'posted') <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Posted</span>
                                    @else <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">{{ $dunningRun->status }}</span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Total Pelanggan</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ number_format($dunningRun->total_customers, 0, ',', '.') }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Total Invoice</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ number_format($dunningRun->total_invoices, 0, ',', '.') }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Total Amount</dt>
                                <dd class="mt-1 text-sm text-gray-900">Rp {{ number_format($dunningRun->total_amount, 2, ',', '.') }}</dd>
                            </div>
                            @if ($dunningRun->notes)
                            <div class="sm:col-span-2">
                                <dt class="text-xs font-medium text-gray-500 uppercase">Catatan</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $dunningRun->notes }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-md font-medium text-gray-900">Items Dunning</h3>
                    </div>
                    <div class="p-6">
                        @if ($dunningRun->items->count() > 0)
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Pelanggan</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Invoice</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jatuh Tempo</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Hari Overdue</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Outstanding</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Biaya Dunning</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total Due</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($dunningRun->items as $item)
                                        <tr>
                                            <td class="px-4 py-2 text-sm text-gray-900">{{ $item->customer?->name ?? $item->customer_id }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-600">{{ $item->invoice_number }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-600">{{ $item->due_date }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-600 text-right">{{ $item->days_overdue }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-600 text-right">Rp {{ number_format($item->outstanding_amount, 2, ',', '.') }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-600 text-right">Rp {{ number_format($item->dunning_charge, 2, ',', '.') }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-900 font-medium text-right">Rp {{ number_format($item->total_due, 2, ',', '.') }}</td>
                                            <td class="px-4 py-2 text-sm">
                                                @if ($item->status === 'pending') <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Pending</span>
                                                @elseif ($item->status === 'sent') <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">Sent</span>
                                                @elseif ($item->status === 'paid') <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Paid</span>
                                                @else <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">{{ $item->status }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td colspan="4" class="px-4 py-2 text-sm font-medium text-gray-700 text-right">Total</td>
                                        <td class="px-4 py-2 text-sm font-medium text-gray-900 text-right">Rp {{ number_format($dunningRun->items->sum('outstanding_amount'), 2, ',', '.') }}</td>
                                        <td class="px-4 py-2 text-sm font-medium text-gray-900 text-right">Rp {{ number_format($dunningRun->items->sum('dunning_charge'), 2, ',', '.') }}</td>
                                        <td class="px-4 py-2 text-sm font-medium text-gray-900 text-right">Rp {{ number_format($dunningRun->items->sum('total_due'), 2, ',', '.') }}</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        @else
                            <p class="text-sm text-gray-500">Tidak ada items.</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="space-y-6">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-md font-medium text-gray-900">Aksi</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        @if ($dunningRun->status === 'draft')
                            <form action="{{ route('finance.dunning-runs.post', $dunningRun) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm"
                                    onclick="return confirm('Post dunning run ini?')">Post</button>
                            </form>
                        @endif
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-md font-medium text-gray-900">Informasi</h3>
                    </div>
                    <div class="p-6 space-y-3 text-sm">
                        <div>
                            <span class="text-gray-500">Dibuat oleh:</span>
                            <span class="text-gray-900">{{ $dunningRun->createdBy?->name ?? '-' }}</span>
                        </div>
                        @if ($dunningRun->postedBy)
                            <div>
                                <span class="text-gray-500">Diposting oleh:</span>
                                <span class="text-gray-900">{{ $dunningRun->postedBy->name }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Diposting pada:</span>
                                <span class="text-gray-900">{{ $dunningRun->posted_at?->format('d/m/Y H:i') ?? '-' }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
