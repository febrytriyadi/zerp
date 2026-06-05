@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Supplier Payments</h1>
            <a href="{{ route('purchasing.supplier-payments.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">Create New</a>
        </div>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supplier</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($supplierPayments ?? [] as $payment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $payment->payment_number }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $payment->payment_date }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $payment->supplier->name ?? $payment->supplier_id }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ number_format($payment->amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-sm">
                                @if ($payment->status === 'draft') <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Draft</span>
                                @elseif ($payment->status === 'submitted') <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">Submitted</span>
                                @elseif ($payment->status === 'approved') <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Approved</span>
                                @elseif ($payment->status === 'rejected') <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-700 rounded-full">Rejected</span>
                                @elseif ($payment->status === 'posted') <span class="px-2 py-1 text-xs font-medium bg-purple-100 text-purple-700 rounded-full">Posted</span>
                                @elseif ($payment->status === 'voided') <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">Voided</span>
                                @else <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">{{ $payment->status }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <a href="{{ route('purchasing.supplier-payments.show', $payment) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                @if ($payment->status === 'draft')
                                    <a href="{{ route('purchasing.supplier-payments.edit', $payment) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-sm text-gray-500 text-center">No supplier payments found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if (isset($supplierPayments) && $supplierPayments instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="mt-4">{{ $supplierPayments->links() }}</div>
        @endif
    </div>
</div>
@endsection
