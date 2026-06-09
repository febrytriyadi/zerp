@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Akrual &amp; Deferral</h1>
            <a href="{{ route('finance.accruals.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">Buat Baru</a>
        </div>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Akrual</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tersisa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($accruals ?? [] as $accrual)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $accrual->accrual_number }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ ucfirst($accrual->accrual_type) }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ str_replace('_', ' ', ucfirst($accrual->category)) }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ Str::limit($accrual->description, 40) }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">Rp {{ number_format($accrual->total_amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">Rp {{ number_format($accrual->remaining_amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-sm">
                                @if ($accrual->status === 'active') <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">Active</span>
                                @elseif ($accrual->status === 'fully_recognized') <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Fully Recognized</span>
                                @elseif ($accrual->status === 'voided') <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-700 rounded-full">Voided</span>
                                @else <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">{{ $accrual->status }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <a href="{{ route('finance.accruals.show', $accrual) }}" class="text-blue-600 hover:text-blue-900 mr-3">Detail</a>
                                @if ($accrual->status !== 'voided')
                                    <a href="{{ route('finance.accruals.edit', $accrual) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                    <form action="{{ route('finance.accruals.destroy', $accrual) }}" method="POST" class="inline" onsubmit="return confirm('Hapus akrual ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-sm text-gray-500 text-center">Tidak ada data akrual / deferral.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if (isset($accruals) && $accruals instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="mt-4">{{ $accruals->links() }}</div>
        @endif
    </div>
</div>
@endsection
