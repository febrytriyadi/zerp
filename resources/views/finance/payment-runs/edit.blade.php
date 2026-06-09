@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('finance.payment-runs.show', $paymentRun) }}" class="text-blue-600 hover:text-blue-900 text-sm">&larr; Kembali ke Detail</a>
        </div>
        @if (session('error'))
            <div class="mb-4 px-4 py-3 bg-red-100 text-red-700 rounded-md text-sm">{{ session('error') }}</div>
        @endif
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Edit Payment Run: {{ $paymentRun->run_number }}</h2>
            </div>
            <form action="{{ route('finance.payment-runs.update', $paymentRun) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Run Date <span class="text-red-500">*</span></label>
                        <input type="date" name="run_date" value="{{ old('run_date', $paymentRun->run_date) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                        @error('run_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method <span class="text-red-500">*</span></label>
                        <select name="payment_method" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                            <option value="bank_transfer" {{ old('payment_method', $paymentRun->payment_method) == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="cash" {{ old('payment_method', $paymentRun->payment_method) == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="cheque" {{ old('payment_method', $paymentRun->payment_method) == 'cheque' ? 'selected' : '' }}>Cheque</option>
                            <option value="giro" {{ old('payment_method', $paymentRun->payment_method) == 'giro' ? 'selected' : '' }}>Giro</option>
                        </select>
                        @error('payment_method') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bank Account</label>
                        <select name="bank_account_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <option value="">Pilih Bank Account</option>
                            @foreach ($bankAccounts ?? [] as $account)
                                <option value="{{ $account->id }}" {{ old('bank_account_id', $paymentRun->bank_account_id) == $account->id ? 'selected' : '' }}>{{ $account->bank_name }} - {{ $account->account_number }}</option>
                            @endforeach
                        </select>
                        @error('bank_account_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea name="notes" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">{{ old('notes', $paymentRun->notes) }}</textarea>
                        @error('notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
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
