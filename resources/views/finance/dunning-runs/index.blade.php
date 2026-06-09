@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Dunning Run</h1>
            <div class="flex gap-2">
                <a href="{{ route('finance.dunning-runs.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">Buat Baru</a>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Run</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Level Dunning</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Pelanggan</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Invoice</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($dunningRuns ?? [] as $run)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $run->run_number }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $run->run_date }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $run->dunningLevel?->name ?? $run->dunning_level_id }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600 text-right">{{ number_format($run->total_customers, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600 text-right">{{ number_format($run->total_invoices, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600 text-right">Rp {{ number_format($run->total_amount, 2, ',', '.') }}</td>
                            <td class="px-6 py-4 text-sm">
                                @if ($run->status === 'draft') <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Draft</span>
                                @elseif ($run->status === 'posted') <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Posted</span>
                                @else <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">{{ $run->status }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <a href="{{ route('finance.dunning-runs.show', $run) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-sm text-gray-500 text-center">Tidak ada dunning run.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if (isset($dunningRuns) && $dunningRuns instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="mt-4">{{ $dunningRuns->links() }}</div>
        @endif
    </div>
</div>
@endsection
