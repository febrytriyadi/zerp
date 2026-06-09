@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('finance.assets.index') }}" class="text-blue-600 hover:text-blue-900 text-sm">&larr; Kembali ke Aset Tetap</a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-lg font-medium text-gray-900">Detail Aset Tetap</h2>
                        <span class="text-sm text-gray-500">#{{ $asset->asset_number }}</span>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Nomor Aset</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $asset->asset_number }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Nama Aset</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $asset->asset_name }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Kategori</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $asset->asset_category }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Status</dt>
                                <dd class="mt-1">
                                    @if ($asset->status === 'active') <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">Active</span>
                                    @elseif ($asset->status === 'depreciating') <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Disusutkan</span>
                                    @elseif ($asset->status === 'fully_depreciated') <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Tersusutkan</span>
                                    @elseif ($asset->status === 'sold') <span class="px-2 py-1 text-xs font-medium bg-purple-100 text-purple-700 rounded-full">Terjual</span>
                                    @elseif ($asset->status === 'retired') <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-700 rounded-full">Retire</span>
                                    @else <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">{{ $asset->status }}</span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Tanggal Perolehan</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $asset->purchase_date }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Harga Perolehan</dt>
                                <dd class="mt-1 text-sm text-gray-900">Rp {{ number_format($asset->purchase_cost, 0, ',', '.') }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Nilai Residu</dt>
                                <dd class="mt-1 text-sm text-gray-900">Rp {{ number_format($asset->salvage_value, 0, ',', '.') }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Masa Manfaat</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $asset->useful_life_years }} tahun</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Metode Penyusutan</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $asset->depreciation_method === 'straight_line' ? 'Garis Lurus' : 'Saldo Menurun Ganda' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Lokasi</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $asset->location ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Akumulasi Penyusutan</dt>
                                <dd class="mt-1 text-sm text-gray-900">Rp {{ number_format($asset->accumulated_depreciation, 0, ',', '.') }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Nilai Buku</dt>
                                <dd class="mt-1 text-sm font-semibold text-gray-900">Rp {{ number_format($asset->book_value, 0, ',', '.') }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Penyusutan Terakhir</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $asset->last_depreciation_date ?? '-' }}</dd>
                            </div>
                        </dl>
                        @if ($asset->description)
                            <div class="mt-4">
                                <dt class="text-xs font-medium text-gray-500 uppercase">Keterangan</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $asset->description }}</dd>
                            </div>
                        @endif
                    </div>
                </div>

                @if ($asset->depreciations->count() > 0)
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-md font-medium text-gray-900">Riwayat Penyusutan</h3>
                    </div>
                    <div class="p-6">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Periode</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nilai Penyusutan</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Akumulasi</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nilai Buku</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($asset->depreciations as $depr)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 text-sm text-gray-600">{{ $depr->period_date }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-600">Rp {{ number_format($depr->depreciation_amount, 0, ',', '.') }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-600">Rp {{ number_format($depr->accumulated_after, 0, ',', '.') }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-600">Rp {{ number_format($depr->book_value_after, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                @if ($asset->transactions->count() > 0)
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-md font-medium text-gray-900">Riwayat Transaksi</h3>
                    </div>
                    <div class="p-6">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($asset->transactions as $txn)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 text-sm text-gray-600">{{ $txn->transaction_date }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-600">{{ $txn->transaction_type }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-600">Rp {{ number_format($txn->amount, 0, ',', '.') }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-600">{{ $txn->description ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>

            <div class="space-y-6">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-md font-medium text-gray-900">Aksi</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        @if (in_array($asset->status, ['active', 'depreciating']))
                            <form action="{{ route('finance.assets.calculate-depreciation', $asset) }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="period_date" value="{{ date('Y-m-d') }}">
                                <button type="submit" class="w-full px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 text-sm">Hitung Penyusutan</button>
                            </form>
                        @endif
                        @if (in_array($asset->status, ['active', 'depreciating']))
                            <a href="{{ route('finance.assets.edit', $asset) }}" class="block w-full text-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm">Edit Aset</a>
                        @endif
                    </div>
                </div>

                @if (in_array($asset->status, ['active', 'depreciating']))
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-md font-medium text-gray-900">Penjualan Aset</h3>
                    </div>
                    <div class="p-6">
                        <form action="{{ route('finance.assets.sell', $asset) }}" method="POST">
                            @csrf
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Jual</label>
                                    <input type="date" name="sale_date" value="{{ date('Y-m-d') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Nilai Jual</label>
                                    <input type="number" step="0.01" name="sale_amount" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Keterangan</label>
                                    <textarea name="description" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"></textarea>
                                </div>
                                <button type="submit" class="w-full px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 text-sm">Jual Aset</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-md font-medium text-gray-900">Retiremen Aset</h3>
                    </div>
                    <div class="p-6">
                        <form action="{{ route('finance.assets.retire', $asset) }}" method="POST">
                            @csrf
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Retire</label>
                                    <input type="date" name="retire_date" value="{{ date('Y-m-d') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Keterangan</label>
                                    <textarea name="description" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"></textarea>
                                </div>
                                <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm">Retire Aset</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-md font-medium text-gray-900">Revaluasi Aset</h3>
                    </div>
                    <div class="p-6">
                        <form action="{{ route('finance.assets.revalue', $asset) }}" method="POST">
                            @csrf
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Nilai Baru</label>
                                    <input type="number" step="0.01" name="new_value" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Revaluasi</label>
                                    <input type="date" name="revalue_date" value="{{ date('Y-m-d') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Keterangan</label>
                                    <textarea name="description" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"></textarea>
                                </div>
                                <button type="submit" class="w-full px-4 py-2 bg-teal-600 text-white rounded-md hover:bg-teal-700 text-sm">Revaluasi</button>
                            </div>
                        </form>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
