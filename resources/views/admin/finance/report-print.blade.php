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
            <div class="label">Kas Masuk Keuangan</div>
            <div class="value text-blue">Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
            <div style="font-size:9px;color:#6b7280;margin-top:2px;">Eksternal: Rp {{ number_format($externalIncomesTotal, 0, ',', '.') }} · Mutasi: Rp {{ number_format($internalTransferIncomesTotal, 0, ',', '.') }}</div>
        </div>
        <div class="summary-item">
            <div class="label">Total Pengeluaran</div>
            <div class="value text-red">Rp {{ number_format($totalExpense, 0, ',', '.') }}</div>
        </div>
        <div class="summary-item">
            <div class="label">Saldo Kas Periode</div>
            <div class="value {{ $balance >= 0 ? 'text-emerald' : 'text-red' }}">Rp {{ number_format($balance, 0, ',', '.') }}</div>
        </div>
    </div>

    {{-- KONSOLIDASI ORGANISASI --}}
    <h3 style="margin-bottom:6px;color:#4c1d95;">Laporan Konsolidasi Pendapatan Organisasi (Non-Double Counting)</h3>
    <table style="margin-bottom:15px;background:#fdf4ff;">
        <thead>
            <tr style="background:#f5d0fe;">
                <th>Komponen</th>
                <th>Keterangan</th>
                <th class="text-right">Nominal</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>1. Donasi Masuk Valid</strong></td>
                <td>Pendapatan eksternal donasi ({{ $consolidated['donations_count'] }} transaksi)</td>
                <td class="text-right">Rp {{ number_format($consolidated['total_donation'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>2. Pemasukan Eksternal Keuangan</strong></td>
                <td>Pendapatan eksternal non-donasi ({{ $consolidated['external_incomes_count'] }} transaksi)</td>
                <td class="text-right">Rp {{ number_format($consolidated['total_external_income'], 0, ',', '.') }}</td>
            </tr>
            <tr style="background:#fae8ff;font-weight:bold;">
                <td>TOTAL PENDAPATAN MURNI ORGANISASI (1 + 2)</td>
                <td>Pendapatan riil baru organisasi (Tanpa double-count)</td>
                <td class="text-right text-emerald">Rp {{ number_format($consolidated['total_organization_revenue'], 0, ',', '.') }}</td>
            </tr>
            <tr style="color:#4b5563;font-style:italic;">
                <td><em>Transfer Internal (Donasi &rarr; Keuangan)</em></td>
                <td><em>Perpindahan saldo kas internal (Bukan pendapatan baru)</em></td>
                <td class="text-right"><em>Rp {{ number_format($consolidated['total_internal_transfer'], 0, ',', '.') }}</em></td>
            </tr>
        </tbody>
    </table>

    <h3 style="margin-bottom:6px;">Rincian Pemasukan Keuangan</h3>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Jenis / Tipe</th>
                <th>Sumber</th>
                <th>Metode</th>
                <th class="text-right">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($incomes as $income)
            <tr>
                <td>{{ $income->date->format('d/m/Y') }}</td>
                <td>
                    {{ $income->income_type }}
                    @if($income->isInternalTransfer())
                        <span style="font-size:9px;background:#dbeafe;color:#1e40af;padding:1px 4px;border-radius:3px;">[Transfer Internal]</span>
                    @endif
                </td>
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
