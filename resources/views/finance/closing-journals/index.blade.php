@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Jurnal Penutup</h1>
            <a href="{{ route('finance.closing-journals.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">Create New</a>
        </div>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Jurnal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Periode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Debit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Kredit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($closingJournals ?? [] as $journal)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $journal->closing_number }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $journal->closing_type }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $journal->fiscalPeriod?->name ?? $journal->fiscal_period_id }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ Str::limit($journal->description, 50) }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">Rp {{ number_format($journal->total_debit, 2, ',', '.') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">Rp {{ number_format($journal->total_credit, 2, ',', '.') }}</td>
                            <td class="px-6 py-4 text-sm">
                                @if ($journal->status === 'draft') <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Draft</span>
                                @elseif ($journal->status === 'posted') <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Posted</span>
                                @elseif ($journal->status === 'voided') <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-700 rounded-full">Voided</span>
                                @else <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">{{ $journal->status }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <a href="{{ route('finance.closing-journals.show', $journal) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                @if ($journal->status === 'draft')
                                    <a href="{{ route('finance.closing-journals.edit', $journal) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-sm text-gray-500 text-center">No closing journals found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if (isset($closingJournals) && $closingJournals instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="mt-4">{{ $closingJournals->links() }}</div>
        @endif
    </div>
</div>
@endsection
