@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('finance.payment-runs.index') }}" class="text-blue-600 hover:text-blue-900 text-sm">&larr; Kembali ke Payment Run</a>
        </div>
        @if (session('success'))
            <div class="mb-4 px-4 py-3 bg-green-100 text-green-700 rounded-md text-sm">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-4 px-4 py-3 bg-red-100 text-red-700 rounded-md text-sm">{{ session('error') }}</div>
        @endif
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-lg font-medium text-gray-900">Detail Payment Run</h2>
                        <span class="text-sm text-gray-500">{{ $paymentRun->run_number }}</span>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Run Number</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $paymentRun->run_number }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Run Date</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $paymentRun->run_date }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Payment Method</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ Str::title(str_replace('_', ' ', $paymentRun->payment_method)) }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Bank Account</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $paymentRun->bankAccount->bank_name ?? '-' }} {{ $paymentRun->bankAccount ? '- ' . $paymentRun->bankAccount->account_number : '' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Total Suppliers</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $paymentRun->total_suppliers }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Total Invoices</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $paymentRun->total_invoices }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Total Amount</dt>
                                <dd class="mt-1 text-sm text-gray-900">Rp {{ number_format($paymentRun->total_amount, 2, ',', '.') }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Status</dt>
                                <dd class="mt-1">
                                    @if ($paymentRun->status === 'draft') <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Draft</span>
                                    @elseif ($paymentRun->status === 'posted') <span class="px-2 py-1 text-xs font-medium bg-purple-100 text-purple-700 rounded-full">Posted</span>
                                    @elseif ($paymentRun->status === 'voided') <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">Voided</span>
                                    @else <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">{{ $paymentRun->status }}</span>
                                    @endif
                                </dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-xs font-medium text-gray-500 uppercase">Notes</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $paymentRun->notes ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Created By</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $paymentRun->createdBy->name ?? '-' }}</dd>
                            </div>
                            @if ($paymentRun->postedBy)
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Posted By</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $paymentRun->postedBy->name }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-md font-medium text-gray-900">Invoice Items ({{ $paymentRun->items->count() }})</h3>
                    </div>
                    <div class="p-6">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Supplier</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Invoice</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Due Date</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Outstanding</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Discount</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Net Payment</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($paymentRun->items as $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2 text-sm text-gray-900">{{ $item->supplier->name ?? '-' }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-600">{{ $item->invoice_number }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-600">{{ $item->due_date }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-600 text-right">Rp {{ number_format($item->outstanding_amount, 0, ',', '.') }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-600 text-right">Rp {{ number_format($item->discount_amount, 0, ',', '.') }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-900 font-medium text-right">Rp {{ number_format($item->net_payment, 0, ',', '.') }}</td>
                                        <td class="px-4 py-2 text-sm">
                                            @if ($item->status === 'pending') <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Pending</span>
                                            @elseif ($item->status === 'posted') <span class="px-2 py-1 text-xs font-medium bg-purple-100 text-purple-700 rounded-full">Posted</span>
                                            @elseif ($item->status === 'voided') <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">Voided</span>
                                            @else <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">{{ $item->status }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-4 py-6 text-sm text-gray-500 text-center">Tidak ada item invoice.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="bg-gray-50 font-semibold">
                                <tr>
                                    <td colspan="3" class="px-4 py-2 text-sm text-gray-900">Total</td>
                                    <td class="px-4 py-2 text-sm text-gray-900 text-right">Rp {{ number_format($paymentRun->items->sum('outstanding_amount'), 0, ',', '.') }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900 text-right">Rp {{ number_format($paymentRun->items->sum('discount_amount'), 0, ',', '.') }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900 text-right">Rp {{ number_format($paymentRun->items->sum('net_payment'), 0, ',', '.') }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="space-y-6">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-md font-medium text-gray-900">Actions</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        @if ($paymentRun->status === 'draft')
                            <form action="{{ route('finance.payment-runs.post', $paymentRun) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 text-sm">Post</button>
                            </form>
                            <form action="{{ route('finance.payment-runs.void', $paymentRun) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 text-sm" onclick="return confirm('Yakin ingin void payment run ini?')">Void</button>
                            </form>
                        @endif
                        @if ($paymentRun->status === 'posted')
                            <form action="{{ route('finance.payment-runs.void', $paymentRun) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 text-sm" onclick="return confirm('Yakin ingin void payment run ini?')">Void</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
