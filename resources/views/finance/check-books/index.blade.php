@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Buku Cek</h1>
            <a href="{{ route('finance.check-books.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">Buat Baru</a>
        </div>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Buku Cek</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bank</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Range No.</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Current No.</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Terbit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($checkBooks ?? [] as $checkBook)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $checkBook->check_book_number }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $checkBook->bankAccount?->bank_name ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $checkBook->start_number }} - {{ $checkBook->end_number }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $checkBook->current_number }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $checkBook->issued_date }}</td>
                            <td class="px-6 py-4 text-sm">
                                @if ($checkBook->status === 'active') <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Active</span>
                                @elseif ($checkBook->status === 'used') <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">Used</span>
                                @elseif ($checkBook->status === 'voided') <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-700 rounded-full">Voided</span>
                                @else <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">{{ $checkBook->status }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <a href="{{ route('finance.check-books.show', $checkBook) }}" class="text-blue-600 hover:text-blue-900 mr-3">Detail</a>
                                @if ($checkBook->status === 'active')
                                    <a href="{{ route('finance.check-books.edit', $checkBook) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-sm text-gray-500 text-center">Tidak ada data buku cek.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if (isset($checkBooks) && $checkBooks instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="mt-4">{{ $checkBooks->links() }}</div>
        @endif
    </div>
</div>
@endsection
