@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('finance.dunning-levels.index') }}" class="text-blue-600 hover:text-blue-900 text-sm">&larr; Kembali ke Level Dunning</a>
        </div>
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-lg font-medium text-gray-900">Detail Level Dunning</h2>
                <span class="text-sm text-gray-500">{{ $dunningLevel->code }}</span>
            </div>
            <div class="p-6">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Code</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $dunningLevel->code }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Nama</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $dunningLevel->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Hari Dari</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $dunningLevel->days_from }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Hari Sampai</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $dunningLevel->days_to }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Charge Persen</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ number_format($dunningLevel->charge_percent, 2) }}%</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Charge Amount</dt>
                        <dd class="mt-1 text-sm text-gray-900">Rp {{ number_format($dunningLevel->charge_amount, 2, ',', '.') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Akun Charge</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $dunningLevel->chargeAccount?->name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Aktif</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $dunningLevel->is_active ? 'Ya' : 'Tidak' }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
