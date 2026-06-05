@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('sales.invoices.index') }}" class="text-blue-600 hover:text-blue-900 text-sm">&larr; Back to Invoices</a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-lg font-medium text-gray-900">Invoice Detail</h2>
                        <span class="text-sm text-gray-500">#{{ $invoice->invoice_number }}</span>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Invoice Number</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $invoice->invoice_number }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Invoice Date</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $invoice->invoice_date }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Customer</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $invoice->customer->name ?? $invoice->customer_id }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Status</dt>
                                <dd class="mt-1">
                                    @if ($invoice->status === 'draft') <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Draft</span>
                                    @elseif ($invoice->status === 'submitted') <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">Submitted</span>
                                    @elseif ($invoice->status === 'approved') <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Approved</span>
                                    @elseif ($invoice->status === 'rejected') <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-700 rounded-full">Rejected</span>
                                    @elseif ($invoice->status === 'posted') <span class="px-2 py-1 text-xs font-medium bg-purple-100 text-purple-700 rounded-full">Posted</span>
                                    @elseif ($invoice->status === 'voided') <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">Voided</span>
                                    @else <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">{{ $invoice->status }}</span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                        @if ($invoice->customer)
                            <div class="mt-4 p-4 bg-gray-50 rounded-md">
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Customer Info</h4>
                                <p class="text-sm text-gray-600">{{ $invoice->customer->name }}</p>
                                <p class="text-sm text-gray-600">{{ $invoice->customer->address ?? '-' }}</p>
                                <p class="text-sm text-gray-600">{{ $invoice->customer->phone ?? '-' }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-md font-medium text-gray-900">Items</h3>
                    </div>
                    <div class="p-6">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Qty</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Price</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($invoice->items ?? [] as $item)
                                    <tr>
                                        <td class="px-3 py-2 text-sm text-gray-900">{{ $item->product->name ?? $item->product_id }}</td>
                                        <td class="px-3 py-2 text-sm text-gray-600 text-right">{{ $item->quantity }}</td>
                                        <td class="px-3 py-2 text-sm text-gray-600 text-right">{{ number_format($item->price, 0, ',', '.') }}</td>
                                        <td class="px-3 py-2 text-sm text-gray-900 text-right">{{ number_format($item->total, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-3 py-4 text-sm text-gray-500 text-center">No items</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="px-3 py-2 text-sm font-medium text-gray-900 text-right">Total</td>
                                    <td class="px-3 py-2 text-sm font-medium text-gray-900 text-right">{{ number_format($invoice->total, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                @if (($invoice->payments ?? collect())->count() > 0)
                    <div class="bg-white rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-md font-medium text-gray-900">Payment History</h3>
                        </div>
                        <div class="p-6">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Payment #</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($invoice->payments as $payment)
                                        <tr>
                                            <td class="px-3 py-2 text-sm text-gray-900">{{ $payment->payment_number }}</td>
                                            <td class="px-3 py-2 text-sm text-gray-600">{{ $payment->payment_date }}</td>
                                            <td class="px-3 py-2 text-sm text-gray-600 text-right">{{ number_format($payment->amount, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-md font-medium text-gray-900">Status Timeline</h3>
                    </div>
                    <div class="p-6">
                        <ol class="relative border-l border-gray-200">
                            <li class="mb-4 ml-4">
                                <div class="absolute w-3 h-3 bg-{{ in_array($invoice->status, ['draft','submitted','approved','posted']) ? 'yellow' : 'gray' }}-200 rounded-full mt-1.5 -left-1.5 border border-white"></div>
                                <time class="mb-1 text-xs font-normal text-gray-400">Draft</time>
                            </li>
                            <li class="mb-4 ml-4">
                                <div class="absolute w-3 h-3 bg-{{ in_array($invoice->status, ['submitted','approved','posted']) ? 'blue' : 'gray' }}-200 rounded-full mt-1.5 -left-1.5 border border-white"></div>
                                <time class="mb-1 text-xs font-normal text-gray-400">Submitted</time>
                            </li>
                            <li class="mb-4 ml-4">
                                <div class="absolute w-3 h-3 bg-{{ in_array($invoice->status, ['approved','posted']) ? 'green' : 'gray' }}-200 rounded-full mt-1.5 -left-1.5 border border-white"></div>
                                <time class="mb-1 text-xs font-normal text-gray-400">Approved</time>
                            </li>
                            <li class="ml-4">
                                <div class="absolute w-3 h-3 bg-{{ $invoice->status === 'posted' ? 'purple' : 'gray' }}-200 rounded-full mt-1.5 -left-1.5 border border-white"></div>
                                <time class="mb-1 text-xs font-normal text-gray-400">Posted</time>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-md font-medium text-gray-900">Totals</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Total</span>
                            <span class="font-medium">Rp {{ number_format($invoice->total, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Outstanding</span>
                            <span class="font-medium text-red-600">Rp {{ number_format($invoice->outstanding, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-md font-medium text-gray-900">Actions</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <a href="{{ route('sales.invoices.print', $invoice) }}" class="block w-full px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-md hover:bg-gray-50 text-sm text-center">Print Invoice</a>
                        @if ($invoice->status === 'draft')
                            <form action="{{ route('sales.invoices.submit', $invoice) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">Submit</button>
                            </form>
                        @endif
                        @if ($invoice->status === 'submitted')
                            <form action="{{ route('sales.invoices.approve', $invoice) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm">Approve</button>
                            </form>
                        @endif
                        @if ($invoice->status === 'approved')
                            <form action="{{ route('sales.invoices.post', $invoice) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 text-sm">Post</button>
                            </form>
                            <form action="{{ route('sales.invoices.void', $invoice) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 text-sm">Void</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection