@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('finance.cash-receipts.index') }}" class="text-blue-600 hover:text-blue-900 text-sm">&larr; Back to Cash Receipts</a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-lg font-medium text-gray-900">Cash Receipt Detail</h2>
                        <span class="text-sm text-gray-500">#{{ $cashReceipt->transaction_number }}</span>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Transaction Number</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $cashReceipt->transaction_number }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Date</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $cashReceipt->transaction_date }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Cash Account</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $cashReceipt->cashAccount->name ?? $cashReceipt->cash_account }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Amount</dt>
                                <dd class="mt-1 text-sm text-gray-900">Rp {{ number_format($cashReceipt->amount, 0, ',', '.') }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Contra Account</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $cashReceipt->contraAccount->name ?? ($cashReceipt->contra_account ?? '-') }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Status</dt>
                                <dd class="mt-1">
                                    @if ($cashReceipt->status === 'draft') <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Draft</span>
                                    @elseif ($cashReceipt->status === 'submitted') <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">Submitted</span>
                                    @elseif ($cashReceipt->status === 'approved') <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Approved</span>
                                    @elseif ($cashReceipt->status === 'rejected') <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-700 rounded-full">Rejected</span>
                                    @elseif ($cashReceipt->status === 'posted') <span class="px-2 py-1 text-xs font-medium bg-purple-100 text-purple-700 rounded-full">Posted</span>
                                    @elseif ($cashReceipt->status === 'voided') <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">Voided</span>
                                    @else <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">{{ $cashReceipt->status }}</span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                        <div class="mt-4">
                            <dt class="text-xs font-medium text-gray-500 uppercase">Description</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $cashReceipt->description ?? '-' }}</dd>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-md font-medium text-gray-900">Status Timeline</h3>
                    </div>
                    <div class="p-6">
                        <ol class="relative border-l border-gray-200">
                            <li class="mb-4 ml-4">
                                <div class="absolute w-3 h-3 bg-{{ $cashReceipt->status === 'draft' || in_array($cashReceipt->status, ['submitted','approved','posted']) ? 'yellow' : 'gray' }}-200 rounded-full mt-1.5 -left-1.5 border border-white"></div>
                                <time class="mb-1 text-xs font-normal text-gray-400">Draft</time>
                                <p class="text-sm text-gray-600">Transaction created</p>
                            </li>
                            <li class="mb-4 ml-4">
                                <div class="absolute w-3 h-3 bg-{{ in_array($cashReceipt->status, ['submitted','approved','posted']) ? 'blue' : 'gray' }}-200 rounded-full mt-1.5 -left-1.5 border border-white"></div>
                                <time class="mb-1 text-xs font-normal text-gray-400">Submitted</time>
                                <p class="text-sm text-gray-600">Submitted for approval</p>
                            </li>
                            <li class="mb-4 ml-4">
                                <div class="absolute w-3 h-3 bg-{{ in_array($cashReceipt->status, ['approved','posted']) ? 'green' : 'gray' }}-200 rounded-full mt-1.5 -left-1.5 border border-white"></div>
                                <time class="mb-1 text-xs font-normal text-gray-400">Approved</time>
                                <p class="text-sm text-gray-600">Approved</p>
                            </li>
                            <li class="ml-4">
                                <div class="absolute w-3 h-3 bg-{{ $cashReceipt->status === 'posted' ? 'purple' : 'gray' }}-200 rounded-full mt-1.5 -left-1.5 border border-white"></div>
                                <time class="mb-1 text-xs font-normal text-gray-400">Posted</time>
                                <p class="text-sm text-gray-600">Posted to ledger</p>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-md font-medium text-gray-900">Actions</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        @if ($cashReceipt->status === 'draft')
                            <form action="{{ route('finance.cash-receipts.submit', $cashReceipt) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">Submit</button>
                            </form>
                        @endif
                        @if ($cashReceipt->status === 'submitted')
                            <form action="{{ route('finance.cash-receipts.approve', $cashReceipt) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm">Approve</button>
                            </form>
                            <form action="{{ route('finance.cash-receipts.reject', $cashReceipt) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm">Reject</button>
                            </form>
                        @endif
                        @if ($cashReceipt->status === 'approved')
                            <form action="{{ route('finance.cash-receipts.post', $cashReceipt) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 text-sm">Post</button>
                            </form>
                            <form action="{{ route('finance.cash-receipts.void', $cashReceipt) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 text-sm">Void</button>
                            </form>
                        @endif
                        @if (in_array($cashReceipt->status, ['draft', 'submitted', 'approved']))
                            <form action="{{ route('finance.cash-receipts.cancel', $cashReceipt) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 text-sm">Cancel</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection