<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan {{ $startDate }} s.d. {{ $endDate }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #1f2937; padding: 30px; }
        h1 { font-size: 18px; text-align: center; margin-bottom: 4px; }
        h2 { font-size: 14px; text-align: center; color: #6b7280; margin-bottom: 20px; font-weight: normal; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #d1d5db; padding: 6px 8px; text-align: left; }
        th { background: #f3f4f6; font-weight: 600; }
        .text-right { text-align: right; }
        .text-blue { color: #2563eb; }
        .text-red { color: #dc2626; }
        .text-emerald { color: #059669; }
        .summary { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .summary-item { padding: 10px 16px; border: 1px solid #d1d5db; border-radius: 6px; text-align: center; flex: 1; margin: 0 4px; }
        .summary-item .label { font-size: 10px; text-transform: uppercase; color: #6b7280; }
        .summary-item .value { font-size: 16px; font-weight: bold; margin-top: 4px; }
        .page-break { page-break-before: always; }
        .footer { text-align: center; color: #9ca3af; font-size: 10px; margin-top: 30px; }
    </style>
</head>
<body>
    <h1>Laporan Keuangan</h1>
    <h2>{{ $startDate }} s.d. {{ $endDate }}</h2>

    <div class="summary">
        <div class="summary-item">
            <div class="label">Total Pemasukan</div>
            <div class="value text-blue">Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
        </div>
        <div class="summary-item">
            <div class="label">Total Pengeluaran</div>
            <div class="value text-red">Rp {{ number_format($totalExpense, 0, ',', '.') }}</div>
        </div>
        <div class="summary-item">
            <div class="label">Saldo Periode</div>
            <div class="value {{ $balance >= 0 ? 'text-emerald' : 'text-red' }}">Rp {{ number_format($balance, 0, ',', '.') }}</div>
        </div>
    </div>

    <h3 style="margin-bottom:6px;">Rincian Pemasukan</h3>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Jenis</th>
                <th>Sumber</th>
                <th>Metode</th>
                <th class="text-right">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($incomes as $income)
            <tr>
                <td>{{ $income->date->format('d/m/Y') }}</td>
                <td>{{ $income->income_type }}</td>
                <td>{{ $income->source_name ?: '-' }}</td>
                <td>{{ $income->payment_method }}</td>
                <td class="text-right text-blue">Rp {{ number_format($income->amount, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align:center;color:#9ca3af;">Tidak ada pemasukan</td></tr>
            @endforelse
        </tbody>
    </table>

    <h3 style="margin-bottom:6px;">Rincian Pengeluaran</h3>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Kategori</th>
                <th>Dibayar Kepada</th>
                <th>Metode</th>
                <th class="text-right">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($expenses as $expense)
            <tr>
                <td>{{ $expense->date->format('d/m/Y') }}</td>
                <td>{{ $expense->expense_category }}</td>
                <td>{{ $expense->paid_to ?: '-' }}</td>
                <td>{{ $expense->payment_method }}</td>
                <td class="text-right text-red">Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align:center;color:#9ca3af;">Tidak ada pengeluaran</td></tr>
            @endforelse
        </tbody>
    </table>

    @if($expenseByCategory->isNotEmpty())
    <h3 style="margin-bottom:6px;">Rekap Pengeluaran per Kategori</h3>
    <table style="max-width:400px;">
        <thead>
            <tr><th>Kategori</th><th class="text-right">Total</th></tr>
        </thead>
        <tbody>
            @foreach($expenseByCategory as $category => $total)
            <tr>
                <td>{{ $category }}</td>
                <td class="text-right text-red">Rp {{ number_format($total, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="footer">
        Dicetak pada {{ now()->format('d/m/Y H:i') }} | SMA Persis Serang
    </div>
</body>
</html>
