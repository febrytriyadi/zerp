@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('finance.check-books.index') }}" class="text-blue-600 hover:text-blue-900 text-sm">&larr; Kembali ke Buku Cek</a>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-lg font-medium text-gray-900">Detail Buku Cek</h2>
                        <span class="text-sm text-gray-500">{{ $checkBook->check_book_number }}</span>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Check Book Number</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $checkBook->check_book_number }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Bank Account</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $checkBook->bankAccount?->bank_name ?? '-' }} ({{ $checkBook->bankAccount?->account_number ?? '-' }})</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Start Number</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $checkBook->start_number }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">End Number</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $checkBook->end_number }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Current Number</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $checkBook->current_number }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Issued Date</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $checkBook->issued_date }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Status</dt>
                                <dd class="mt-1">
                                    @if ($checkBook->status === 'active') <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Active</span>
                                    @elseif ($checkBook->status === 'used') <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">Used</span>
                                    @elseif ($checkBook->status === 'voided') <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-700 rounded-full">Voided</span>
                                    @else <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">{{ $checkBook->status }}</span>
                                    @endif
                                </dd>
                            </div>
                            @if ($checkBook->notes)
                            <div class="sm:col-span-2">
                                <dt class="text-xs font-medium text-gray-500 uppercase">Notes</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $checkBook->notes }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                </div>
            </div>
            <div class="space-y-6">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-md font-medium text-gray-900">Informasi</h3>
                    </div>
                    <div class="p-6">
                        <dt class="text-xs font-medium text-gray-500 uppercase">Dibuat Oleh</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $checkBook->createdBy?->name ?? '-' }}</dd>
                    </div>
                </div>
                @if ($checkBook->status === 'active')
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-md font-medium text-gray-900">Aksi</h3>
                    </div>
                    <div class="p-6">
                        <a href="{{ route('finance.check-books.edit', $checkBook) }}" class="block w-full text-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm">Edit</a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
