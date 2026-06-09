@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('finance.accruals.index') }}" class="text-blue-600 hover:text-blue-900 text-sm">&larr; Kembali ke Akrual &amp; Deferral</a>
        </div>
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Buat Akrual / Deferral Baru</h2>
            </div>
            <form action="{{ route('finance.accruals.store') }}" method="POST" class="p-6">
                @csrf
                <input type="hidden" name="company_id" value="1">
                <input type="hidden" name="branch_id" value="1">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Akrual <span class="text-red-500">*</span></label>
                        <select name="accrual_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                            <option value="">Pilih Tipe</option>
                            <option value="accrual" {{ old('accrual_type') == 'accrual' ? 'selected' : '' }}>Akrual</option>
                            <option value="deferral" {{ old('accrual_type') == 'deferral' ? 'selected' : '' }}>Deferral</option>
                        </select>
                        @error('accrual_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                        <select name="category" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                            <option value="">Pilih Kategori</option>
                            <option value="prepaid_expense" {{ old('category') == 'prepaid_expense' ? 'selected' : '' }}>Prepaid Expense</option>
                            <option value="accrued_revenue" {{ old('category') == 'accrued_revenue' ? 'selected' : '' }}>Accrued Revenue</option>
                            <option value="deferred_revenue" {{ old('category') == 'deferred_revenue' ? 'selected' : '' }}>Deferred Revenue</option>
                            <option value="accrued_expense" {{ old('category') == 'accrued_expense' ? 'selected' : '' }}>Accrued Expense</option>
                        </select>
                        @error('category') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi <span class="text-red-500">*</span></label>
                        <input type="text" name="description" value="{{ old('description') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                        @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Total Amount <span class="text-red-500">*</span></label>
                        <input type="number" step="0.01" name="total_amount" id="total_amount" value="{{ old('total_amount', 0) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                        @error('total_amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Periode <span class="text-red-500">*</span></label>
                        <input type="number" name="total_periods" id="total_periods" value="{{ old('total_periods', 1) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                        @error('total_periods') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai <span class="text-red-500">*</span></label>
                        <input type="date" name="start_date" value="{{ old('start_date') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                        @error('start_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai <span class="text-red-500">*</span></label>
                        <input type="date" name="end_date" value="{{ old('end_date') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                        @error('end_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Amount per Periode</label>
                        <input type="number" step="0.01" name="amount_per_period" id="amount_per_period" value="{{ old('amount_per_period', 0) }}" class="w-full rounded-md border-gray-300 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" readonly>
                        @error('amount_per_period') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Akun Debit <span class="text-red-500">*</span></label>
                        <select name="debit_account_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                            <option value="">Pilih Akun</option>
                            @foreach ($accounts ?? [] as $account)
                                <option value="{{ $account->id }}" {{ old('debit_account_id') == $account->id ? 'selected' : '' }}>{{ $account->code }} - {{ $account->name }}</option>
                            @endforeach
                        </select>
                        @error('debit_account_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Akun Kredit <span class="text-red-500">*</span></label>
                        <select name="credit_account_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                            <option value="">Pilih Akun</option>
                            @foreach ($accounts ?? [] as $account)
                                <option value="{{ $account->id }}" {{ old('credit_account_id') == $account->id ? 'selected' : '' }}>{{ $account->code }} - {{ $account->name }}</option>
                            @endforeach
                        </select>
                        @error('credit_account_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                        <textarea name="notes" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">{{ old('notes') }}</textarea>
                        @error('notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="flex justify-end mt-6">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const totalAmount = document.getElementById('total_amount');
        const totalPeriods = document.getElementById('total_periods');
        const amountPerPeriod = document.getElementById('amount_per_period');

        function calculate() {
            const total = parseFloat(totalAmount.value) || 0;
            const periods = parseInt(totalPeriods.value) || 1;
            if (periods > 0) {
                amountPerPeriod.value = (total / periods).toFixed(2);
            }
        }

        totalAmount.addEventListener('input', calculate);
        totalPeriods.addEventListener('input', calculate);
    });
</script>
@endpush
