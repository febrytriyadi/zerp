@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('finance.dunning-levels.index') }}" class="text-blue-600 hover:text-blue-900 text-sm">&larr; Kembali ke Level Dunning</a>
        </div>
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Buat Level Dunning Baru</h2>
            </div>
            <form action="{{ route('finance.dunning-levels.store') }}" method="POST" class="p-6">
                @csrf
                <input type="hidden" name="company_id" value="1">
                <input type="hidden" name="branch_id" value="1">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Code <span class="text-red-500">*</span></label>
                        <input type="text" name="code" value="{{ old('code') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                        @error('code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hari Dari <span class="text-red-500">*</span></label>
                        <input type="number" min="0" name="days_from" value="{{ old('days_from', 0) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                        @error('days_from') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hari Sampai <span class="text-red-500">*</span></label>
                        <input type="number" min="0" name="days_to" value="{{ old('days_to', 0) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                        @error('days_to') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Charge Persen (%)</label>
                        <input type="number" step="0.01" min="0" name="charge_percent" value="{{ old('charge_percent', 0) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        @error('charge_percent') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Charge Amount (Rp)</label>
                        <input type="number" step="0.01" min="0" name="charge_amount" value="{{ old('charge_amount', 0) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        @error('charge_amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Akun Charge</label>
                        <select name="charge_account_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <option value="">Pilih Akun</option>
                            @foreach ($accounts ?? [] as $account)
                                <option value="{{ $account->id }}" {{ old('charge_account_id') == $account->id ? 'selected' : '' }}>{{ $account->code }} - {{ $account->name }}</option>
                            @endforeach
                        </select>
                        @error('charge_account_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Aktif</label>
                        <select name="is_active" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Ya</option>
                            <option value="0" {{ old('is_active') === '0' ? 'selected' : '' }}>Tidak</option>
                        </select>
                        @error('is_active') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="flex justify-end mt-6">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
