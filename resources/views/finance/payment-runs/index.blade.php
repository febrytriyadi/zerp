@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Payment Run</h1>
            <a href="{{ route('finance.payment-runs.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">Buat Payment Run Baru</a>
        </div>
        @if (session('success'))
            <div class="mb-4 px-4 py-3 bg-green-100 text-green-700 rounded-md text-sm">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-4 px-4 py-3 bg-red-100 text-red-700 rounded-md text-sm">{{ session('error') }}</div>
        @endif
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Run Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Suppliers</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Invoices</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($paymentRuns ?? [] as $run)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $run->run_number }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $run->run_date }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $run->total_suppliers }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $run->total_invoices }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">Rp {{ number_format($run->total_amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-sm">
                                @if ($run->status === 'draft') <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Draft</span>
                                @elseif ($run->status === 'posted') <span class="px-2 py-1 text-xs font-medium bg-purple-100 text-purple-700 rounded-full">Posted</span>
                                @elseif ($run->status === 'voided') <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">Voided</span>
                                @else <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">{{ $run->status }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <a href="{{ route('finance.payment-runs.show', $run) }}" class="text-blue-600 hover:text-blue-900 mr-3">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-sm text-gray-500 text-center">Belum ada payment run.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if (isset($paymentRuns) && $paymentRuns instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="mt-4">{{ $paymentRuns->links() }}</div>
        @endif
    </div>
</div>
@endsection
