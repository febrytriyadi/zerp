@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('finance.closing-journals.index') }}" class="text-blue-600 hover:text-blue-900 text-sm">&larr; Back to Jurnal Penutup</a>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-lg font-medium text-gray-900">Detail Jurnal Penutup</h2>
                        <span class="text-sm text-gray-500">{{ $closingJournal->closing_number }}</span>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">No. Jurnal</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $closingJournal->closing_number }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Tipe</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $closingJournal->closing_type }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Periode</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $closingJournal->fiscalPeriod?->name ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Deskripsi</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $closingJournal->description }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Total Debit</dt>
                                <dd class="mt-1 text-sm text-gray-900">Rp {{ number_format($closingJournal->total_debit, 2, ',', '.') }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Total Kredit</dt>
                                <dd class="mt-1 text-sm text-gray-900">Rp {{ number_format($closingJournal->total_credit, 2, ',', '.') }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Status</dt>
                                <dd class="mt-1">
                                    @if ($closingJournal->status === 'draft') <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Draft</span>
                                    @elseif ($closingJournal->status === 'posted') <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Posted</span>
                                    @elseif ($closingJournal->status === 'voided') <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-700 rounded-full">Voided</span>
                                    @else <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">{{ $closingJournal->status }}</span>
                                    @endif
                                </dd>
                            </div>
                            @if ($closingJournal->journalEntry)
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Jurnal Terkait</dt>
                                <dd class="mt-1 text-sm">
                                    <a href="{{ route('accounting.journal-entries.show', $closingJournal->journalEntry) }}" class="text-blue-600 hover:text-blue-900">
                                        {{ $closingJournal->journalEntry->journal_number }}
                                    </a>
                                </dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-md font-medium text-gray-900">Items Jurnal</h3>
                    </div>
                    <div class="p-6">
                        @php $items = is_array($closingJournal->items) ? $closingJournal->items : []; @endphp
                        @if (count($items) > 0)
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Akun</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Debit</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Kredit</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($items as $item)
                                        <tr>
                                            <td class="px-4 py-2 text-sm text-gray-900">
                                                {{ $accounts[$item['account_id']]?->code ?? '' }} - {{ $accounts[$item['account_id']]?->name ?? $item['account_id'] }}
                                            </td>
                                            <td class="px-4 py-2 text-sm text-gray-600 text-right">Rp {{ number_format($item['debit'] ?? 0, 2, ',', '.') }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-600 text-right">Rp {{ number_format($item['credit'] ?? 0, 2, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td class="px-4 py-2 text-sm font-medium text-gray-700 text-right">Total</td>
                                        <td class="px-4 py-2 text-sm font-medium text-gray-900 text-right">Rp {{ number_format(collect($items)->sum('debit'), 2, ',', '.') }}</td>
                                        <td class="px-4 py-2 text-sm font-medium text-gray-900 text-right">Rp {{ number_format(collect($items)->sum('credit'), 2, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        @else
                            <p class="text-sm text-gray-500">No items.</p>
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
                        @if ($closingJournal->status === 'draft')
                            <form action="{{ route('finance.closing-journals.post', $closingJournal) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm"
                                    onclick="return confirm('Post jurnal penutup ini?')">Post</button>
                            </form>
                        @endif
                        @if ($closingJournal->status !== 'voided')
                            <form action="{{ route('finance.closing-journals.void', $closingJournal) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm"
                                    onclick="return confirm('Void jurnal penutup ini?')">Void</button>
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
                            <span class="text-gray-900">{{ $closingJournal->createdBy?->name ?? '-' }}</span>
                        </div>
                        @if ($closingJournal->postedBy)
                            <div>
                                <span class="text-gray-500">Diposting oleh:</span>
                                <span class="text-gray-900">{{ $closingJournal->postedBy->name }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Diposting pada:</span>
                                <span class="text-gray-900">{{ $closingJournal->posted_at?->format('d/m/Y H:i') ?? '-' }}</span>
                            </div>
                        @endif
                        @if ($closingJournal->voidedBy)
                            <div>
                                <span class="text-gray-500">Divoid oleh:</span>
                                <span class="text-gray-900">{{ $closingJournal->voidedBy->name }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Divoid pada:</span>
                                <span class="text-gray-900">{{ $closingJournal->voided_at?->format('d/m/Y H:i') ?? '-' }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
