@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Level Dunning</h1>
            <a href="{{ route('finance.dunning-levels.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">Create New</a>
        </div>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Hari Dari</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Hari Sampai</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Charge %</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Charge Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aktif</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($dunningLevels ?? [] as $level)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $level->code }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $level->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600 text-right">{{ $level->days_from }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600 text-right">{{ $level->days_to }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600 text-right">{{ number_format($level->charge_percent, 2) }}%</td>
                            <td class="px-6 py-4 text-sm text-gray-600 text-right">Rp {{ number_format($level->charge_amount, 2, ',', '.') }}</td>
                            <td class="px-6 py-4 text-sm">
                                @if ($level->is_active) <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Yes</span>
                                @else <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-700 rounded-full">No</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <a href="{{ route('finance.dunning-levels.edit', $level) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                <form action="{{ route('finance.dunning-levels.destroy', $level) }}" method="POST" class="inline" onsubmit="return confirm('Hapus level dunning ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-sm text-gray-500 text-center">Tidak ada level dunning.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if (isset($dunningLevels) && $dunningLevels instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="mt-4">{{ $dunningLevels->links() }}</div>
        @endif
    </div>
</div>
@endsection
