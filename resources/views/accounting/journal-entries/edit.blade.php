@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('accounting.journal-entries.index') }}" class="text-blue-600 hover:text-blue-900 text-sm">&larr; Back to Journal Entries</a>
        </div>
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Edit Journal Entry</h2>
            </div>
            <form action="{{ route('accounting.journal-entries.update', $journalEntry) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Transaction Date</label>
                        <input type="date" name="transaction_date" value="{{ old('transaction_date', $journalEntry->transaction_date) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        @error('transaction_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Journal Type</label>
                        <select name="journal_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <option value="general" {{ old('journal_type', $journalEntry->journal_type) === 'general' ? 'selected' : '' }}>General</option>
                            <option value="sales" {{ old('journal_type', $journalEntry->journal_type) === 'sales' ? 'selected' : '' }}>Sales</option>
                            <option value="purchasing" {{ old('journal_type', $journalEntry->journal_type) === 'purchasing' ? 'selected' : '' }}>Purchasing</option>
                            <option value="cash_receipt" {{ old('journal_type', $journalEntry->journal_type) === 'cash_receipt' ? 'selected' : '' }}>Cash Receipt</option>
                            <option value="cash_disbursement" {{ old('journal_type', $journalEntry->journal_type) === 'cash_disbursement' ? 'selected' : '' }}>Cash Disbursement</option>
                        </select>
                        @error('journal_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">{{ old('description', $journalEntry->description) }}</textarea>
                        @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="flex justify-end mt-6">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
