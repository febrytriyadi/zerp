@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('finance.dunning-runs.index') }}" class="text-blue-600 hover:text-blue-900 text-sm">&larr; Kembali ke Dunning Run</a>
        </div>
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Generate Dunning Run Baru</h2>
            </div>
            <form action="{{ route('finance.dunning-runs.generate') }}" method="POST" class="p-6">
                @csrf
                <input type="hidden" name="company_id" value="1">
                <input type="hidden" name="branch_id" value="1">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Level Dunning <span class="text-red-500">*</span></label>
                        <select name="dunning_level_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                            <option value="">Pilih Level</option>
                            @foreach ($levels ?? [] as $level)
                                <option value="{{ $level->id }}" {{ old('dunning_level_id') == $level->id ? 'selected' : '' }}>{{ $level->name }} ({{ $level->days_from }}-{{ $level->days_to }} hari)</option>
                            @endforeach
                        </select>
                        @error('dunning_level_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pelanggan (opsional)</label>
                        <select name="customer_ids[]" multiple class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" size="5">
                            @foreach ($customers ?? [] as $customer)
                                <option value="{{ $customer->id }}" {{ in_array($customer->id, old('customer_ids', [])) ? 'selected' : '' }}>{{ $customer->code }} - {{ $customer->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Kosongkan untuk semua pelanggan yang overdue.</p>
                        @error('customer_ids') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="flex justify-end mt-6">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">Generate</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
