@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('finance.accruals.index') }}" class="text-blue-600 hover:text-blue-900 text-sm">&larr; Kembali ke Akrual &amp; Deferral</a>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-lg font-medium text-gray-900">Detail Akrual</h2>
                        <span class="text-sm text-gray-500">{{ $accrual->accrual_number }}</span>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">No. Akrual</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $accrual->accrual_number }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Tipe</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($accrual->accrual_type) }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Kategori</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ str_replace('_', ' ', ucfirst($accrual->category)) }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Status</dt>
                                <dd class="mt-1">
                                    @if ($accrual->status === 'active') <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">Active</span>
                                    @elseif ($accrual->status === 'fully_recognized') <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Fully Recognized</span>
                                    @elseif ($accrual->status === 'voided') <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-700 rounded-full">Voided</span>
                                    @else <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">{{ $accrual->status }}</span>
                                    @endif
                                </dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-xs font-medium text-gray-500 uppercase">Deskripsi</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $accrual->description }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Total Amount</dt>
                                <dd class="mt-1 text-sm text-gray-900">Rp {{ number_format($accrual->total_amount, 0, ',', '.') }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Amount per Periode</dt>
                                <dd class="mt-1 text-sm text-gray-900">Rp {{ number_format($accrual->amount_per_period, 0, ',', '.') }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Sudah Direkognisi</dt>
                                <dd class="mt-1 text-sm text-gray-900">Rp {{ number_format($accrual->recognized_amount, 0, ',', '.') }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Sisa</dt>
                                <dd class="mt-1 text-sm text-gray-900">Rp {{ number_format($accrual->remaining_amount, 0, ',', '.') }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Tanggal Mulai</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $accrual->start_date?->format('d M Y') }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Tanggal Selesai</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $accrual->end_date?->format('d M Y') }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Total Periode</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $accrual->total_periods }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Periode Terekognisi</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $accrual->recognized_periods ?? 0 }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Akun Debit</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $accrual->debitAccount?->code }} - {{ $accrual->debitAccount?->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Akun Kredit</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $accrual->creditAccount?->code }} - {{ $accrual->creditAccount?->name }}</dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-xs font-medium text-gray-500 uppercase">Catatan</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $accrual->notes ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Dibuat Oleh</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $accrual->createdBy?->name ?? '-' }}</dd>
                            </div>
                            @if ($accrual->voided_at)
                                <div>
                                    <dt class="text-xs font-medium text-gray-500 uppercase">Dibatalkan Oleh</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $accrual->voidedBy?->name ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-medium text-gray-500 uppercase">Tanggal Dibatalkan</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $accrual->voided_at?->format('d M Y H:i') }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                </div>
            </div>
            <div class="space-y-6">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-md font-medium text-gray-900">Aksi</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        @if ($accrual->status === 'active')
                            <form action="{{ route('finance.accruals.recognize', $accrual) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm">Recognize</button>
                            </form>
                        @endif
                        @if ($accrual->status !== 'voided')
                            <form action="{{ route('finance.accruals.void', $accrual) }}" method="POST" onsubmit="return confirm('Batalkan akrual ini?')">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm">Void</button>
                            </form>
                        @endif
                        <a href="{{ route('finance.accruals.edit', $accrual) }}" class="w-full block text-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm">Edit</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
