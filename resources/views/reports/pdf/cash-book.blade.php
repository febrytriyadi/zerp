<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cash Book Report</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f4f4f4; }
        .text-right { text-align: right; }
        .header { margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; }
        .header p { margin: 2px 0; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Cash Book Report</h1>
        <p>Period: {{ request('start_date', 'All') }} - {{ request('end_date', 'All') }}</p>
        <p>Generated: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Transaction #</th>
                <th>Description</th>
                <th class="text-right">Debit</th>
                <th class="text-right">Credit</th>
                <th class="text-right">Balance</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($lines ?? [] as $line)
                <tr>
                    <td>{{ $line->journalEntry->transaction_date ?? '' }}</td>
                    <td>{{ $line->journalEntry->transaction_number ?? '' }}</td>
                    <td>{{ $line->chartOfAccount->name ?? '' }} - {{ $line->description ?? '' }}</td>
                    <td class="text-right">{{ $line->debit ? number_format($line->debit, 2) : '-' }}</td>
                    <td class="text-right">{{ $line->credit ? number_format($line->credit, 2) : '-' }}</td>
                    <td class="text-right">-</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">No transactions found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
