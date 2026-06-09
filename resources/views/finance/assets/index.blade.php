@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Aset Tetap</h1>
            <a href="{{ route('finance.assets.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">Buat Baru</a>
        </div>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Asset #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Harga Perolehan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Akum. Penyusutan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nilai Buku</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($assets ?? [] as $asset)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $asset->asset_number }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $asset->asset_name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $asset->asset_category }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">Rp {{ number_format($asset->purchase_cost, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">Rp {{ number_format($asset->accumulated_depreciation, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">Rp {{ number_format($asset->book_value, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-sm">
                                @if ($asset->status === 'active') <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">Active</span>
                                @elseif ($asset->status === 'depreciating') <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Disusutkan</span>
                                @elseif ($asset->status === 'fully_depreciated') <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Tersusutkan</span>
                                @elseif ($asset->status === 'sold') <span class="px-2 py-1 text-xs font-medium bg-purple-100 text-purple-700 rounded-full">Terjual</span>
                                @elseif ($asset->status === 'retired') <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-700 rounded-full">Retire</span>
                                @else <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">{{ $asset->status }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <a href="{{ route('finance.assets.show', $asset) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                @if (in_array($asset->status, ['active', 'depreciating']))
                                    <a href="{{ route('finance.assets.edit', $asset) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-sm text-gray-500 text-center">Tidak ada aset tetap.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if (isset($assets) && $assets instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="mt-4">{{ $assets->links() }}</div>
        @endif
    </div>
</div>
@endsection
