@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Bank Statement</h1>
            <div class="flex gap-2">
                <a href="{{ route('finance.bank-statements.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">Buat Baru</a>
                <a href="{{ route('finance.bank-statements.import') }}" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm">Import</a>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Statement</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bank</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Saldo Awal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Saldo Akhir</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($statements ?? [] as $statement)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $statement->statement_number }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $statement->statement_date }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $statement->bankAccount?->bank_name ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">Rp {{ number_format($statement->beginning_balance, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">Rp {{ number_format($statement->ending_balance, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-sm">
                                @if ($statement->status === 'draft') <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Draft</span>
                                @elseif ($statement->status === 'posted') <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Posted</span>
                                @elseif ($statement->status === 'voided') <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">Voided</span>
                                @else <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">{{ $statement->status }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <a href="{{ route('finance.bank-statements.show', $statement) }}" class="text-blue-600 hover:text-blue-900 mr-3">Detail</a>
                                @if ($statement->status === 'draft')
                                    <a href="{{ route('finance.bank-statements.edit', $statement) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                    <form action="{{ route('finance.bank-statements.post', $statement) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-900 mr-3">Post</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-sm text-gray-500 text-center">Tidak ada data bank statement.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if (isset($statements) && $statements instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="mt-4">{{ $statements->links() }}</div>
        @endif
    </div>
</div>
@endsection
