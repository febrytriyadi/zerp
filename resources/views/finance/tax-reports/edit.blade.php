@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('finance.tax-reports.index') }}" class="text-blue-600 hover:text-blue-900 text-sm">&larr; Back to Laporan Pajak</a>
        </div>
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Edit Laporan Pajak</h2>
            </div>
            <form action="{{ route('finance.tax-reports.update', $taxReport) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Report Type <span class="text-red-500">*</span></label>
                        <select name="report_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                            <option value="">Select Type</option>
                            <option value="ppn_1111" {{ old('report_type', $taxReport->report_type) == 'ppn_1111' ? 'selected' : '' }}>SPT Masa PPN 1111</option>
                            <option value="pph_23" {{ old('report_type', $taxReport->report_type) == 'pph_23' ? 'selected' : '' }}>SPT Masa PPh 23</option>
                            <option value="pph_42" {{ old('report_type', $taxReport->report_type) == 'pph_42' ? 'selected' : '' }}>SPT Masa PPh 4(2)</option>
                            <option value="pph_21" {{ old('report_type', $taxReport->report_type) == 'pph_21' ? 'selected' : '' }}>SPT Masa PPh 21</option>
                        </select>
                        @error('report_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Period Code <span class="text-red-500">*</span></label>
                        <input type="text" name="period_code" value="{{ old('period_code', $taxReport->period_code) }}" placeholder="e.g. 2026-01" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                        @error('period_code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Period Start <span class="text-red-500">*</span></label>
                        <input type="date" name="period_start" value="{{ old('period_start', $taxReport->period_start) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                        @error('period_start') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Period End <span class="text-red-500">*</span></label>
                        <input type="date" name="period_end" value="{{ old('period_end', $taxReport->period_end) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                        @error('period_end') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea name="notes" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">{{ old('notes', $taxReport->notes) }}</textarea>
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
