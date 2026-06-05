@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('accounting.journal-entries.index') }}" class="text-blue-600 hover:text-blue-900 text-sm">&larr; Back to Journal Entries</a>
        </div>
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Create Journal Entry</h2>
            </div>
            <form action="{{ route('accounting.journal-entries.store') }}" method="POST" class="p-6">
                @csrf
                <input type="hidden" name="company_id" value="1">
                <input type="hidden" name="branch_id" value="1">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Transaction Date <span class="text-red-500">*</span></label>
                        <input type="date" name="transaction_date" value="{{ old('transaction_date', date('Y-m-d')) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                        @error('transaction_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">{{ old('description') }}</textarea>
                        @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="mt-4">
                    <h3 class="text-md font-medium text-gray-700 mb-2">Journal Lines <span class="text-red-500">*</span> <span class="text-xs text-gray-500">(min 2 lines, debits must equal credits)</span></h3>
                    <div id="journal-lines">
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-2 line-item">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Account</label>
                                <select name="lines[0][chart_of_account_id]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                                    <option value="">Select Account</option>
                                    @foreach ($chartOfAccounts ?? [] as $account)
                                        <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Debit</label>
                                <input type="number" step="0.01" name="lines[0][debit]" value="0" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Credit</label>
                                <input type="number" step="0.01" name="lines[0][credit]" value="0" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Description</label>
                                <input type="text" name="lines[0][description]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-2 line-item">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Account</label>
                                <select name="lines[1][chart_of_account_id]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                                    <option value="">Select Account</option>
                                    @foreach ($chartOfAccounts ?? [] as $account)
                                        <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Debit</label>
                                <input type="number" step="0.01" name="lines[1][debit]" value="0" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Credit</label>
                                <input type="number" step="0.01" name="lines[1][credit]" value="0" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Description</label>
                                <input type="text" name="lines[1][description]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            </div>
                        </div>
                    </div>
                    <button type="button" onclick="addLine()" class="px-3 py-1 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 text-sm">+ Add Line</button>
                </div>
                <div class="flex justify-end mt-6">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

@php
    $accountOptions = '';
    foreach ($chartOfAccounts ?? [] as $account) {
        $accountOptions .= '<option value="' . $account->id . '">' . e($account->code) . ' - ' . e($account->name) . '</option>';
    }
@endphp
<script>
let lineIndex = 2;
const accountOptions = `{!! $accountOptions !!}`;
function addLine() {
    const html = `<div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-2 line-item">
        <div>
            <select name="lines[${lineIndex}][chart_of_account_id]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                <option value="">Select Account</option>
                ${accountOptions}
            </select>
        </div>
        <div>
            <input type="number" step="0.01" name="lines[${lineIndex}][debit]" value="0" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
        </div>
        <div>
            <input type="number" step="0.01" name="lines[${lineIndex}][credit]" value="0" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
        </div>
        <div>
            <input type="text" name="lines[${lineIndex}][description]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
        </div>
    </div>`;
    document.getElementById('journal-lines').insertAdjacentHTML('beforeend', html);
    lineIndex++;
}
</script>
@endsection
