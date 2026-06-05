@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('accounting.journal-entries.index') }}" class="text-blue-600 hover:text-blue-900 text-sm">&larr; Back to Journal Entries</a>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-lg font-medium text-gray-900">Journal Entry Detail</h2>
                        <span class="text-sm text-gray-500">#{{ $journalEntry->journal_number }}</span>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Journal Number</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $journalEntry->journal_number }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Transaction Date</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $journalEntry->transaction_date }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Journal Type</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ Str::title(str_replace('_', ' ', $journalEntry->journal_type)) }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Status</dt>
                                <dd class="mt-1">
                                    @if ($journalEntry->status === 'draft') <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Draft</span>
                                    @elseif ($journalEntry->status === 'submitted') <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">Submitted</span>
                                    @elseif ($journalEntry->status === 'approved') <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Approved</span>
                                    @elseif ($journalEntry->status === 'rejected') <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-700 rounded-full">Rejected</span>
                                    @elseif ($journalEntry->status === 'posted') <span class="px-2 py-1 text-xs font-medium bg-purple-100 text-purple-700 rounded-full">Posted</span>
                                    @elseif ($journalEntry->status === 'voided') <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">Voided</span>
                                    @else <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">{{ $journalEntry->status }}</span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                        @if ($journalEntry->description)
                        <div class="mt-4">
                            <dt class="text-xs font-medium text-gray-500 uppercase">Description</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $journalEntry->description }}</dd>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-md font-medium text-gray-900">Journal Lines</h3>
                    </div>
                    <div class="p-6">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Account</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Debit</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Credit</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($journalEntry->lines ?? [] as $line)
                                    <tr>
                                        <td class="px-3 py-2 text-sm text-gray-900">{{ $line->account->code ?? $line->account_id }} - {{ $line->account->name ?? '' }}</td>
                                        <td class="px-3 py-2 text-sm text-gray-600">{{ $line->description ?? '-' }}</td>
                                        <td class="px-3 py-2 text-sm text-gray-600 text-right">{{ $line->debit ? number_format($line->debit, 0, ',', '.') : '-' }}</td>
                                        <td class="px-3 py-2 text-sm text-gray-600 text-right">{{ $line->credit ? number_format($line->credit, 0, ',', '.') : '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-3 py-4 text-sm text-gray-500 text-center">No lines</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2" class="px-3 py-2 text-sm font-medium text-gray-900 text-right">Total</td>
                                    <td class="px-3 py-2 text-sm font-medium text-gray-900 text-right">{{ number_format($journalEntry->total_debit, 0, ',', '.') }}</td>
                                    <td class="px-3 py-2 text-sm font-medium text-gray-900 text-right">{{ number_format($journalEntry->total_credit, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
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
                            <span class="text-gray-500">Total Debit</span>
                            <span class="font-medium">Rp {{ number_format($journalEntry->total_debit, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Total Credit</span>
                            <span class="font-medium">Rp {{ number_format($journalEntry->total_credit, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-md font-medium text-gray-900">Actions</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        @if ($journalEntry->status === 'draft')
                            <form action="{{ route('accounting.journal-entries.submit', $journalEntry) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">Submit</button>
                            </form>
                        @endif
                        @if ($journalEntry->status === 'submitted')
                            <form action="{{ route('accounting.journal-entries.approve', $journalEntry) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm">Approve</button>
                            </form>
                            <form action="{{ route('accounting.journal-entries.reject', $journalEntry) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm">Reject</button>
                            </form>
                        @endif
                        @if ($journalEntry->status === 'approved')
                            <form action="{{ route('accounting.journal-entries.post', $journalEntry) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 text-sm">Post</button>
                            </form>
                            <form action="{{ route('accounting.journal-entries.void', $journalEntry) }}" method="POST">
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
