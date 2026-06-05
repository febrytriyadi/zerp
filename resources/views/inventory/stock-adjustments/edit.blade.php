@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('inventory.stock-adjustments.index') }}" class="text-blue-600 hover:text-blue-900 text-sm">&larr; Back to Stock Adjustments</a>
        </div>
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Edit Stock Adjustment</h2>
            </div>
            <form action="{{ route('inventory.stock-adjustments.update', $stockAdjustment) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Adjustment Date <span class="text-red-500">*</span></label>
                        <input type="date" name="adjustment_date" value="{{ old('adjustment_date', $stockAdjustment->adjustment_date) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                        @error('adjustment_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Warehouse <span class="text-red-500">*</span></label>
                        <select name="warehouse_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                            <option value="">Select Warehouse</option>
                            @foreach ($warehouses ?? [] as $warehouse)
                                <option value="{{ $warehouse->id }}" {{ old('warehouse_id', $stockAdjustment->warehouse_id) == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                            @endforeach
                        </select>
                        @error('warehouse_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Adjustment Type <span class="text-red-500">*</span></label>
                        <select name="adjustment_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                            <option value="addition" {{ old('adjustment_type', $stockAdjustment->adjustment_type) === 'addition' ? 'selected' : '' }}>Addition</option>
                            <option value="subtraction" {{ old('adjustment_type', $stockAdjustment->adjustment_type) === 'subtraction' ? 'selected' : '' }}>Subtraction</option>
                            <option value="correction" {{ old('adjustment_type', $stockAdjustment->adjustment_type) === 'correction' ? 'selected' : '' }}>Correction</option>
                        </select>
                        @error('adjustment_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">{{ old('description', $stockAdjustment->description) }}</textarea>
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
