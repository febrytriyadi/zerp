<table>
    <thead>
        <tr>
            <th>Date</th>
            <th>Transaction #</th>
            <th>Account</th>
            <th>Description</th>
            <th>Debit</th>
            <th>Credit</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($lines as $line)
            <tr>
                <td>{{ $line->journalEntry->transaction_date ?? '' }}</td>
                <td>{{ $line->journalEntry->transaction_number ?? '' }}</td>
                <td>{{ $line->chartOfAccount->name ?? '' }}</td>
                <td>{{ $line->description ?? '' }}</td>
                <td>{{ $line->debit ?? 0 }}</td>
                <td>{{ $line->credit ?? 0 }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
