@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('finance.closing-journals.index') }}" class="text-blue-600 hover:text-blue-900 text-sm">&larr; Back to Jurnal Penutup</a>
        </div>
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Buat Jurnal Penutup Baru</h2>
            </div>
            <form action="{{ route('finance.closing-journals.store') }}" method="POST" class="p-6">
                @csrf
                <input type="hidden" name="company_id" value="1">
                <input type="hidden" name="branch_id" value="1">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Penutup <span class="text-red-500">*</span></label>
                        <select name="closing_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                            <option value="">Select Type</option>
                            <option value="month_end" {{ old('closing_type') == 'month_end' ? 'selected' : '' }}>Akhir Bulan</option>
                            <option value="year_end" {{ old('closing_type') == 'year_end' ? 'selected' : '' }}>Akhir Tahun</option>
                            <option value="adjustment" {{ old('closing_type') == 'adjustment' ? 'selected' : '' }}>Penyesuaian</option>
                        </select>
                        @error('closing_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Periode Fiskal <span class="text-red-500">*</span></label>
                        <select name="fiscal_period_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                            <option value="">Select Period</option>
                            @foreach ($fiscalPeriods ?? [] as $period)
                                <option value="{{ $period->id }}" {{ old('fiscal_period_id') == $period->id ? 'selected' : '' }}>{{ $period->name }}</option>
                            @endforeach
                        </select>
                        @error('fiscal_period_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi <span class="text-red-500">*</span></label>
                        <textarea name="description" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>{{ old('description') }}</textarea>
                        @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-6">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="text-md font-medium text-gray-900">Items Jurnal</h3>
                        <button type="button" id="addRow" class="px-3 py-1 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm">Tambah Baris</button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 border rounded-md" id="itemsTable">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Akun</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Debit</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Kredit</th>
                                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr id="rowTemplate" class="hidden">
                                    <td class="px-4 py-2">
                                        <select name="items[INDEX][account_id]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                            <option value="">Pilih Akun</option>
                                            @foreach ($accounts ?? [] as $account)
                                                <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-4 py-2">
                                        <input type="number" step="0.01" min="0" name="items[INDEX][debit]" value="0" class="debit w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    </td>
                                    <td class="px-4 py-2">
                                        <input type="number" step="0.01" min="0" name="items[INDEX][credit]" value="0" class="credit w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        <button type="button" class="removeRow px-2 py-1 bg-red-600 text-white rounded-md hover:bg-red-700 text-xs">Hapus</button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td class="px-4 py-2 text-right text-sm font-medium text-gray-700">Total</td>
                                    <td class="px-4 py-2 text-sm font-medium text-gray-900">
                                        <span id="totalDebit">Rp 0,00</span>
                                    </td>
                                    <td class="px-4 py-2 text-sm font-medium text-gray-900">
                                        <span id="totalCredit">Rp 0,00</span>
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @error('items') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-end mt-6">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let rowIndex = 0;
    const tbody = document.querySelector('#itemsTable tbody');
    const template = document.getElementById('rowTemplate');

    function addRow() {
        const clone = template.cloneNode(true);
        clone.id = '';
        clone.classList.remove('hidden');

        clone.querySelectorAll('[name]').forEach(el => {
            el.name = el.name.replace('INDEX', rowIndex);
        });

        clone.querySelector('.removeRow').addEventListener('click', function () {
            clone.remove();
            updateTotals();
        });

        clone.querySelectorAll('.debit, .credit').forEach(el => {
            el.addEventListener('input', updateTotals);
        });

        tbody.appendChild(clone);
        rowIndex++;
        updateTotals();
    }

    function updateTotals() {
        let totalDebit = 0;
        let totalCredit = 0;

        tbody.querySelectorAll('tr:not(.hidden)').forEach(row => {
            const debit = parseFloat(row.querySelector('.debit')?.value) || 0;
            const credit = parseFloat(row.querySelector('.credit')?.value) || 0;
            totalDebit += debit;
            totalCredit += credit;
        });

        document.getElementById('totalDebit').textContent = 'Rp ' + totalDebit.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('totalCredit').textContent = 'Rp ' + totalCredit.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    document.getElementById('addRow').addEventListener('click', addRow);

    addRow();
});
</script>
@endpush
