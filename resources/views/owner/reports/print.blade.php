<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan {{ ucfirst($tab) }} - {{ $cafe['name'] }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #290024; font-size: 12px; margin: 0; padding: 12px; }
        @media (min-width: 600px) {
            body { padding: 24px; }
        }
        .header { text-align: center; border-bottom: 2px solid #290024; padding-bottom: 12px; margin-bottom: 16px; }
        .header h1 { margin: 0; font-size: 18px; color: #290024; }
        @media (min-width: 600px) {
            .header h1 { font-size: 20px; }
        }
        .header p { margin: 2px 0; color: #775533; font-size: 11px; }
        .meta { display: flex; flex-direction: column; gap: 4px; margin-bottom: 16px; font-size: 11px; color: #290024; }
        @media (min-width: 600px) {
            .meta { flex-direction: row; justify-content: space-between; }
        }
        .cards { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 18px; }
        .card { flex: 1 1 calc(50% - 10px); min-width: 130px; border: 1px solid #E3DEA4; border-radius: 8px; padding: 10px; }
        @media (min-width: 600px) {
            .card { flex: 1 1 150px; }
        }
        .card .label { font-size: 10px; text-transform: uppercase; color: #775533; letter-spacing: .5px; }
        .card .value { font-size: 14px; font-weight: bold; color: #290024; margin-top: 4px; }
        @media (min-width: 600px) {
            .card .value { font-size: 15px; }
        }
        .table-responsive { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; margin-bottom: 18px; }
        table { width: 100%; border-collapse: collapse; min-width: 480px; }
        th, td { border: 1px solid #E3DEA4; padding: 6px 8px; text-align: left; font-size: 11px; }
        th { background: #F2F5E2; font-size: 11px; text-transform: uppercase; color: #775533; }
        td.num, th.num { text-align: right; }
        h2 { font-size: 13px; color: #290024; margin: 18px 0 8px; border-left: 4px solid #D4954D; padding-left: 8px; }
        @media (min-width: 600px) {
            h2 { font-size: 14px; }
        }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #888; border-top: 1px solid #ddd; padding-top: 10px; }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0 !important; }
            table { min-width: 100% !important; }
            .table-responsive { overflow: visible !important; }
        }
        .no-print { display: flex; justify-content: flex-end; margin-bottom: 12px; }
        .no-print button { background: #290024; color: #F2F5E2; border: 0; padding: 10px 18px; border-radius: 20px; cursor: pointer; font-size: 12px; font-weight: 600; width: 100%; }
        @media (min-width: 480px) {
            .no-print button { width: auto; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print">
        <button onclick="window.print()">Cetak / Simpan PDF</button>
    </div>

    <div class="header">
        <div style="display:flex;justify-content:center;margin-bottom:8px;">
            <x-logo :show-text="false" size="md" thermal loading="eager" />
        </div>
        <h1>{{ $cafe['name'] }}</h1>
        <p>{{ $cafe['tagline'] }}</p>
        <p>{{ $cafe['address'] }} @if($cafe['phone']) &bull; {{ $cafe['phone'] }} @endif</p>
    </div>

    <div class="meta">
        <div><strong>Jenis Laporan:</strong> {{ ucfirst($tab) }}</div>
        <div><strong>Periode:</strong> {{ $period->label }}</div>
        <div><strong>Dicetak:</strong> {{ now()->translatedFormat('d F Y, H:i') }} WIB</div>
    </div>

    @if($tab === 'penjualan')
        <div class="cards">
            <div class="card"><div class="label">Pesanan</div><div class="value">{{ number_format($report['summary']['orders'], 0, ',', '.') }}</div></div>
            <div class="card"><div class="label">Item Terjual</div><div class="value">{{ number_format($report['summary']['items'], 0, ',', '.') }}</div></div>
            <div class="card"><div class="label">Penjualan Kotor</div><div class="value">Rp {{ number_format($report['summary']['gross'], 0, ',', '.') }}</div></div>
            <div class="card"><div class="label">Diskon</div><div class="value">Rp {{ number_format($report['summary']['discount'], 0, ',', '.') }}</div></div>
            <div class="card"><div class="label">Pendapatan Bersih</div><div class="value">Rp {{ number_format($report['summary']['net'], 0, ',', '.') }}</div></div>
        </div>

        <h2>Rekap Harian</h2>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr><th>Tanggal</th><th class="num">Pesanan</th><th class="num">Kotor</th><th class="num">Diskon</th><th class="num">Bersih</th><th class="num">Rata-rata</th></tr>
                </thead>
                <tbody>
                    @foreach($report['daily'] as $day)
                        <tr>
                            <td>{{ $day['date']->translatedFormat('d M Y') }}</td>
                            <td class="num">{{ $day['orders'] }}</td>
                            <td class="num">Rp {{ number_format($day['gross'], 0, ',', '.') }}</td>
                            <td class="num">Rp {{ number_format($day['discount'], 0, ',', '.') }}</td>
                            <td class="num">Rp {{ number_format($day['net'], 0, ',', '.') }}</td>
                            <td class="num">Rp {{ number_format($day['avg'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <h2>Penjualan per Menu</h2>
        <div class="table-responsive">
            <table>
                <thead><tr><th>Menu</th><th class="num">Qty</th><th class="num">Pendapatan</th></tr></thead>
                <tbody>
                    @foreach($report['perMenu'] as $row)
                        <tr>
                            <td>
                                {{ $row['name'] }}
                                @if(!empty($row['variants']))
                                    <div class="muted">
                                        @foreach($row['variants'] as $variant)
                                            {{ $variant['name'] }} ({{ number_format($variant['quantity'], 0, ',', '.') }}x)@if(!$loop->last), @endif
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td class="num">{{ number_format($row['quantity'], 0, ',', '.') }}</td>
                            <td class="num">Rp {{ number_format($row['revenue'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <h2>Penjualan per Kategori</h2>
        <div class="table-responsive">
            <table>
                <thead><tr><th>Kategori</th><th class="num">Qty</th><th class="num">Pendapatan</th></tr></thead>
                <tbody>
                    @foreach($report['perCategory'] as $row)
                        <tr><td>{{ $row['name'] }}</td><td class="num">{{ number_format($row['quantity'], 0, ',', '.') }}</td><td class="num">Rp {{ number_format($row['revenue'], 0, ',', '.') }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="cards">
            <div class="card"><div class="label">Penjualan Kotor</div><div class="value">Rp {{ number_format($report['finance']['gross'], 0, ',', '.') }}</div></div>
            <div class="card"><div class="label">Total Diskon</div><div class="value">Rp {{ number_format($report['finance']['discount'], 0, ',', '.') }}</div></div>
            <div class="card"><div class="label">Service Charge</div><div class="value">Rp {{ number_format($report['finance']['service'], 0, ',', '.') }}</div></div>
            <div class="card"><div class="label">Pendapatan Bersih</div><div class="value">Rp {{ number_format($report['finance']['net'], 0, ',', '.') }}</div></div>
        </div>

        <h2>Rekap Metode Pembayaran</h2>
        <div class="table-responsive">
            <table>
                <thead><tr><th>Metode</th><th class="num">Transaksi</th><th class="num">Total</th></tr></thead>
                <tbody>
                    @foreach($report['perMethod'] as $m)
                        <tr><td style="text-transform: capitalize">{{ $m['method'] }}</td><td class="num">{{ $m['count'] }}</td><td class="num">Rp {{ number_format($m['total'], 0, ',', '.') }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="footer">
        Laporan dibuat otomatis oleh Sistem ABS Cafe &bull; {{ $cafe['name'] }}
    </div>
</body>
</html>
