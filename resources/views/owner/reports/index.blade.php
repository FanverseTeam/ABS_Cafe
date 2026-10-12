@extends('owner.layouts.app')

@section('title', 'Laporan')
@section('page_title', 'Laporan Bisnis')

@php
    $rp = fn ($v) => 'Rp ' . number_format((float) $v, 0, ',', '.');
    $tabs = [
        'penjualan' => ['label' => 'Penjualan', 'icon' => 'shopping-cart'],
        'keuangan' => ['label' => 'Keuangan', 'icon' => 'landmark'],
        'stok' => ['label' => 'Stok & Bahan', 'icon' => 'boxes'],
    ];
    $tabQuery = fn ($t) => array_merge($period->queryParams(), ['tab' => $t]);
@endphp

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
            <div>
                <h2 class="font-serif text-2xl sm:text-3xl font-bold text-cafe-dark">Laporan Bisnis</h2>
                <p class="text-xs text-cafe-muted mt-1">Analisis mendalam penjualan, keuangan, dan perputaran stok.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('owner.reports.export', $tabQuery($tab)) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-cafe-card border border-cafe-beige text-cafe-dark text-xs font-semibold hover:bg-cafe-beige/60 transition-colors">
                    <i data-lucide="download" class="w-4 h-4"></i> Ekspor CSV
                </a>
                <a href="{{ route('owner.reports.print', $tabQuery($tab)) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream text-xs font-semibold transition-colors">
                    <i data-lucide="printer" class="w-4 h-4"></i> Cetak
                </a>
            </div>
        </div>

        <!-- Period Filter -->
        <div class="bg-cafe-card rounded-2xl border border-cafe-beige/60 shadow-soft p-4">
            @include('owner.partials.period-filter', ['period' => $period])
        </div>

        <!-- Tabs -->
        <div class="inline-flex items-center gap-1 p-1 rounded-full bg-cafe-beige/50 border border-cafe-beige">
            @foreach($tabs as $key => $meta)
                <a href="{{ route('owner.reports.index', $tabQuery($key)) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-medium transition-all {{ $tab === $key ? 'bg-cafe-dark text-cafe-cream shadow-sm' : 'text-cafe-muted hover:bg-cafe-beige hover:text-cafe-dark' }}">
                    <i data-lucide="{{ $meta['icon'] }}" class="w-3.5 h-3.5"></i> {{ $meta['label'] }}
                </a>
            @endforeach
        </div>

        @if($tab === 'penjualan')
            <!-- Ringkasan -->
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
                @foreach([
                    ['Pesanan', number_format($data['summary']['orders'], 0, ',', '.'), 'shopping-bag', 'info'],
                    ['Item Terjual', number_format($data['summary']['items'], 0, ',', '.'), 'package', 'gold'],
                    ['Penjualan Kotor', $rp($data['summary']['gross']), 'wallet', 'success'],
                    ['Diskon', $rp($data['summary']['discount']), 'badge-percent', 'warning'],
                    ['Pendapatan Bersih', $rp($data['summary']['net']), 'banknote', 'dark'],
                ] as [$label, $value, $icon, $tone])
                    <div class="bg-cafe-card rounded-2xl border border-cafe-beige/60 shadow-soft p-4">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center mb-3
                            {{ $tone === 'success' ? 'bg-cafe-success/15 text-cafe-success' : '' }}
                            {{ $tone === 'warning' ? 'bg-cafe-warning/15 text-cafe-warning' : '' }}
                            {{ $tone === 'info' ? 'bg-cafe-info/15 text-cafe-info' : '' }}
                            {{ $tone === 'gold' ? 'bg-cafe-gold/15 text-cafe-bronze' : '' }}
                            {{ $tone === 'dark' ? 'bg-cafe-dark/10 text-cafe-dark' : '' }}">
                            <i data-lucide="{{ $icon }}" class="w-4 h-4"></i>
                        </div>
                        <p class="text-[10px] uppercase tracking-wide text-cafe-muted font-semibold">{{ $label }}</p>
                        <p class="font-serif text-lg font-bold text-cafe-dark mt-0.5">{{ $value }}</p>
                    </div>
                @endforeach
            </div>

            <!-- Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div class="lg:col-span-2 bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft p-5">
                    <h3 class="font-serif font-bold text-cafe-dark mb-4 flex items-center gap-2"><i data-lucide="bar-chart-3" class="w-4 h-4 text-cafe-gold"></i> Penjualan per Hari</h3>
                    <div class="h-72"><canvas id="dailyChart"></canvas></div>
                </div>
                <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft p-5">
                    <h3 class="font-serif font-bold text-cafe-dark mb-4 flex items-center gap-2"><i data-lucide="clock-4" class="w-4 h-4 text-cafe-gold"></i> Jam Sibuk</h3>
                    <div class="h-72"><canvas id="hourChart"></canvas></div>
                </div>
            </div>

            <!-- Per Menu & Category -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft p-5">
                    <h3 class="font-serif font-bold text-cafe-dark mb-4 flex items-center gap-2"><i data-lucide="coffee" class="w-4 h-4 text-cafe-gold"></i> Penjualan per Menu</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs">
                            <thead>
                                <tr class="text-cafe-muted uppercase text-[10px] tracking-wide border-b border-cafe-beige/60">
                                    <th class="text-left py-2">Menu</th>
                                    <th class="text-right py-2">Qty</th>
                                    <th class="text-right py-2">Pendapatan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-cafe-beige/40">
                                @forelse($data['perMenu']->take(10) as $row)
                                    <tr>
                                        <td class="py-2 text-cafe-text font-medium">{{ $row['name'] }}</td>
                                        <td class="py-2 text-right text-cafe-muted">{{ number_format($row['quantity'], 0, ',', '.') }}</td>
                                        <td class="py-2 text-right font-semibold text-cafe-dark">{{ $rp($row['revenue']) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="py-4 text-center text-cafe-muted">Belum ada data.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft p-5">
                    <h3 class="font-serif font-bold text-cafe-dark mb-4 flex items-center gap-2"><i data-lucide="tags" class="w-4 h-4 text-cafe-gold"></i> Penjualan per Kategori</h3>
                    <div class="space-y-3">
                        @php $maxCat = $data['perCategory']->max('revenue') ?: 1; @endphp
                        @forelse($data['perCategory'] as $cat)
                            <div>
                                <div class="flex items-center justify-between text-xs mb-1">
                                    <span class="font-medium text-cafe-text">{{ $cat['name'] }}</span>
                                    <span class="text-cafe-muted">{{ number_format($cat['quantity'], 0, ',', '.') }} &bull; {{ $rp($cat['revenue']) }}</span>
                                </div>
                                <div class="h-2 rounded-full bg-cafe-beige/60 overflow-hidden">
                                    <div class="h-full rounded-full bg-cafe-gold" style="width: {{ max(3, round($cat['revenue'] / $maxCat * 100)) }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-cafe-muted">Belum ada data.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Detail Pesanan -->
            <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft p-5">
                <h3 class="font-serif font-bold text-cafe-dark mb-4 flex items-center gap-2"><i data-lucide="list" class="w-4 h-4 text-cafe-gold"></i> Detail Pesanan</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="text-cafe-muted uppercase text-[10px] tracking-wide border-b border-cafe-beige/60">
                                <th class="text-left py-2">Kode</th>
                                <th class="text-left py-2">Waktu</th>
                                <th class="text-left py-2">Customer</th>
                                <th class="text-left py-2">Kasir</th>
                                <th class="text-left py-2">Metode</th>
                                <th class="text-right py-2">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-cafe-beige/40">
                            @forelse($data['orders'] as $order)
                                <tr class="hover:bg-cafe-cream/60 transition-colors">
                                    <td class="py-2.5 font-semibold text-cafe-dark">{{ $order->order_code }}</td>
                                    <td class="py-2.5 text-cafe-muted">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="py-2.5 text-cafe-text">{{ $order->customer_name }}</td>
                                    <td class="py-2.5 text-cafe-muted">{{ $order->cashierLabel() }}</td>
                                    <td class="py-2.5 uppercase text-cafe-muted">{{ $order->payment_method }}</td>
                                    <td class="py-2.5 text-right font-semibold text-cafe-dark">{{ $rp($order->total) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="py-6 text-center text-cafe-muted">Belum ada pesanan pada periode ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $data['orders']->links() }}</div>
            </div>
        @elseif($tab === 'keuangan')
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
                @foreach([
                    ['Penjualan Kotor', $rp($finance['gross']), 'wallet'],
                    ['Total Diskon', $rp($finance['discount']), 'badge-percent'],
                    ['Pajak', $rp($finance['tax']), 'landmark'],
                    ['Service Charge', $rp($finance['service']), 'receipt'],
                    ['Pendapatan Bersih', $rp($finance['net']), 'banknote'],
                ] as [$label, $value, $icon])
                    <div class="bg-cafe-card rounded-2xl border border-cafe-beige/60 shadow-soft p-4">
                        <div class="w-9 h-9 rounded-xl bg-cafe-gold/15 text-cafe-bronze flex items-center justify-center mb-3">
                            <i data-lucide="{{ $icon }}" class="w-4 h-4"></i>
                        </div>
                        <p class="text-[10px] uppercase tracking-wide text-cafe-muted font-semibold">{{ $label }}</p>
                        <p class="font-serif text-lg font-bold text-cafe-dark mt-0.5">{{ $value }}</p>
                    </div>
                @endforeach
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft p-5">
                    <h3 class="font-serif font-bold text-cafe-dark mb-4 flex items-center gap-2"><i data-lucide="credit-card" class="w-4 h-4 text-cafe-gold"></i> Rekap Metode Pembayaran</h3>
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="text-cafe-muted uppercase text-[10px] tracking-wide border-b border-cafe-beige/60">
                                <th class="text-left py-2">Metode</th>
                                <th class="text-right py-2">Transaksi</th>
                                <th class="text-right py-2">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-cafe-beige/40">
                            @forelse($finance['byMethod'] as $m)
                                <tr>
                                    <td class="py-2 capitalize font-medium text-cafe-text">{{ $m['method'] }}</td>
                                    <td class="py-2 text-right text-cafe-muted">{{ $m['count'] }}</td>
                                    <td class="py-2 text-right font-semibold text-cafe-dark">{{ $rp($m['total']) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="py-4 text-center text-cafe-muted">Belum ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-4 p-3 rounded-2xl bg-cafe-cream/70 border border-cafe-beige/50 text-xs">
                        <p class="text-cafe-muted">Estimasi laba (margin {{ $finance['margin'] }}%)</p>
                        <p class="font-serif text-lg font-bold text-cafe-dark">{{ $rp($finance['net'] * $finance['margin'] / 100) }}</p>
                    </div>
                </div>

                <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft p-5">
                    <h3 class="font-serif font-bold text-cafe-dark mb-4 flex items-center gap-2"><i data-lucide="coins" class="w-4 h-4 text-cafe-gold"></i> Rekap Kas per Shift</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs">
                            <thead>
                                <tr class="text-cafe-muted uppercase text-[10px] tracking-wide border-b border-cafe-beige/60">
                                    <th class="text-left py-2">Kasir</th>
                                    <th class="text-left py-2">Buka</th>
                                    <th class="text-right py-2">Penjualan</th>
                                    <th class="text-right py-2">Selisih</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-cafe-beige/40">
                                @forelse($finance['shifts'] as $shift)
                                    <tr>
                                        <td class="py-2 font-medium text-cafe-text">{{ $shift->user?->name ?? '-' }}</td>
                                        <td class="py-2 text-cafe-muted">{{ $shift->opened_at?->format('d/m H:i') }}</td>
                                        <td class="py-2 text-right font-semibold text-cafe-dark">{{ $rp($shift->total_sales) }}</td>
                                        <td class="py-2 text-right">
                                            @php $badge = $shift->differenceBadge(); @endphp
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold
                                                {{ $badge['color'] === 'success' ? 'bg-cafe-success/15 text-cafe-success' : '' }}
                                                {{ $badge['color'] === 'warning' ? 'bg-cafe-warning/15 text-cafe-warning' : '' }}
                                                {{ $badge['color'] === 'danger' ? 'bg-cafe-danger/15 text-cafe-danger' : '' }}
                                                {{ $badge['color'] === 'muted' ? 'bg-cafe-beige/60 text-cafe-muted' : '' }}">
                                                {{ $badge['label'] }} {{ $rp($shift->cash_difference) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="py-4 text-center text-cafe-muted">Belum ada data shift.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <p class="text-[11px] text-cafe-muted mt-3">Pesanan dibatalkan: <strong class="text-cafe-danger">{{ $finance['cancelled'] }}</strong></p>
                </div>
            </div>
        @else
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-cafe-card rounded-2xl border border-cafe-beige/60 shadow-soft p-4">
                    <div class="w-9 h-9 rounded-xl bg-cafe-info/15 text-cafe-info flex items-center justify-center mb-3"><i data-lucide="package" class="w-4 h-4"></i></div>
                    <p class="text-[10px] uppercase tracking-wide text-cafe-muted font-semibold">Total Item Menu</p>
                    <p class="font-serif text-lg font-bold text-cafe-dark mt-0.5">{{ number_format($stock['totalItems'], 0, ',', '.') }}</p>
                </div>
                <div class="bg-cafe-card rounded-2xl border border-cafe-beige/60 shadow-soft p-4">
                    <div class="w-9 h-9 rounded-xl bg-cafe-gold/15 text-cafe-bronze flex items-center justify-center mb-3"><i data-lucide="banknote" class="w-4 h-4"></i></div>
                    <p class="text-[10px] uppercase tracking-wide text-cafe-muted font-semibold">Nilai Stok (jual)</p>
                    <p class="font-serif text-lg font-bold text-cafe-dark mt-0.5">{{ $rp($stock['stockValue']) }}</p>
                </div>
                <div class="bg-cafe-card rounded-2xl border border-cafe-beige/60 shadow-soft p-4">
                    <div class="w-9 h-9 rounded-xl bg-cafe-warning/15 text-cafe-warning flex items-center justify-center mb-3"><i data-lucide="alert-triangle" class="w-4 h-4"></i></div>
                    <p class="text-[10px] uppercase tracking-wide text-cafe-muted font-semibold">Stok Menipis</p>
                    <p class="font-serif text-lg font-bold text-cafe-dark mt-0.5">{{ $stock['lowStock']->count() }}</p>
                </div>
                <div class="bg-cafe-card rounded-2xl border border-cafe-beige/60 shadow-soft p-4">
                    <div class="w-9 h-9 rounded-xl bg-cafe-danger/15 text-cafe-danger flex items-center justify-center mb-3"><i data-lucide="x-octagon" class="w-4 h-4"></i></div>
                    <p class="text-[10px] uppercase tracking-wide text-cafe-muted font-semibold">Stok Habis</p>
                    <p class="font-serif text-lg font-bold text-cafe-dark mt-0.5">{{ $stock['outOfStock']->count() }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft p-5">
                    <h3 class="font-serif font-bold text-cafe-dark mb-4 flex items-center gap-2"><i data-lucide="package-search" class="w-4 h-4 text-cafe-gold"></i> Pemakaian vs Stok (Terlaris)</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs">
                            <thead>
                                <tr class="text-cafe-muted uppercase text-[10px] tracking-wide border-b border-cafe-beige/60">
                                    <th class="text-left py-2">Menu</th>
                                    <th class="text-right py-2">Terjual</th>
                                    <th class="text-right py-2">Sisa Stok</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-cafe-beige/40">
                                @forelse($stock['usage']->take(12) as $u)
                                    <tr>
                                        <td class="py-2 font-medium text-cafe-text">{{ $u['name'] }}</td>
                                        <td class="py-2 text-right text-cafe-muted">{{ number_format($u['sold'], 0, ',', '.') }}</td>
                                        <td class="py-2 text-right font-semibold {{ $u['stock'] <= 0 ? 'text-cafe-danger' : 'text-cafe-dark' }}">{{ $u['stock'] }} {{ $u['unit'] }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="py-4 text-center text-cafe-muted">Belum ada data.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft p-5">
                    <h3 class="font-serif font-bold text-cafe-dark mb-4 flex items-center gap-2"><i data-lucide="arrow-right-left" class="w-4 h-4 text-cafe-gold"></i> Riwayat Pergerakan Stok</h3>
                    <div class="overflow-x-auto max-h-96 overflow-y-auto">
                        <table class="w-full text-xs">
                            <thead class="sticky top-0 bg-cafe-card">
                                <tr class="text-cafe-muted uppercase text-[10px] tracking-wide border-b border-cafe-beige/60">
                                    <th class="text-left py-2">Item</th>
                                    <th class="text-left py-2">Tipe</th>
                                    <th class="text-right py-2">Qty</th>
                                    <th class="text-right py-2">Waktu</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-cafe-beige/40">
                                @forelse($stock['movements'] as $mv)
                                    <tr>
                                        <td class="py-2 font-medium text-cafe-text">{{ $mv->item_name }}</td>
                                        <td class="py-2">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold
                                                {{ $mv->type === 'masuk' ? 'bg-cafe-success/15 text-cafe-success' : '' }}
                                                {{ $mv->type === 'keluar' ? 'bg-cafe-warning/15 text-cafe-warning' : '' }}
                                                {{ $mv->type === 'koreksi' ? 'bg-cafe-info/15 text-cafe-info' : '' }}">
                                                {{ ucfirst($mv->type) }}
                                            </span>
                                        </td>
                                        <td class="py-2 text-right font-semibold text-cafe-dark">{{ $mv->quantity }}</td>
                                        <td class="py-2 text-right text-cafe-muted">{{ $mv->created_at->format('d/m H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="py-4 text-center text-cafe-muted">Belum ada pergerakan stok.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
    lucide.createIcons();
    Chart.defaults.font.family = "'Poppins', sans-serif";
    Chart.defaults.color = '#775533';

    @if($tab === 'penjualan')
    const daily = @json($data['daily']);
    new Chart(document.getElementById('dailyChart'), {
        type: 'bar',
        data: {
            labels: daily.map(d => d.label),
            datasets: [
                {
                    label: 'Pendapatan Bersih (Rp)',
                    data: daily.map(d => d.net),
                    backgroundColor: '#D4954D',
                    borderRadius: 6,
                    yAxisID: 'y',
                },
                {
                    label: 'Jumlah Pesanan',
                    data: daily.map(d => d.orders),
                    type: 'line',
                    borderColor: '#290024',
                    backgroundColor: '#290024',
                    tension: 0.4,
                    pointRadius: 2,
                    yAxisID: 'y1',
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 12, boxHeight: 12, usePointStyle: true, font: { size: 11 } } },
                tooltip: { callbacks: { label: (ctx) => ctx.dataset.yAxisID === 'y' ? ' Pendapatan: Rp ' + new Intl.NumberFormat('id-ID').format(ctx.parsed.y) : ' Pesanan: ' + ctx.parsed.y } },
            },
            scales: {
                y: { beginAtZero: true, ticks: { callback: (v) => 'Rp ' + new Intl.NumberFormat('id-ID', { notation: 'compact' }).format(v), font: { size: 10 } }, grid: { color: 'rgba(227, 222, 164, 0.5)' } },
                y1: { beginAtZero: true, position: 'right', ticks: { font: { size: 10 } }, grid: { display: false } },
                x: { grid: { display: false }, ticks: { font: { size: 9 }, maxRotation: 0, autoSkip: true } },
            },
        },
    });

    const hours = @json($data['perHour']);
    new Chart(document.getElementById('hourChart'), {
        type: 'bar',
        data: {
            labels: hours.map(h => h.hour + ':00'),
            datasets: [{ label: 'Pesanan', data: hours.map(h => h.orders), backgroundColor: '#775533', borderRadius: 6 }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { font: { size: 10 } }, grid: { color: 'rgba(227, 222, 164, 0.5)' } }, x: { grid: { display: false }, ticks: { font: { size: 9 } } } },
        },
    });
    @endif
</script>
@endpush
