@extends('owner.layouts.app')

@section('title', 'Dashboard Bisnis')
@section('page_title', 'Dashboard Bisnis')

@php
    $rp = fn ($v) => 'Rp ' . number_format((float) $v, 0, ',', '.');
    $changeBadge = function ($change) {
        if ($change === null) {
            return '<span class="text-[10px] font-semibold text-cafe-muted bg-cafe-beige/60 px-2 py-0.5 rounded-full">Baru</span>';
        }
        $up = $change >= 0;
        $color = $up ? 'text-cafe-success bg-cafe-success/10' : 'text-cafe-danger bg-cafe-danger/10';
        $icon = $up ? 'trending-up' : 'trending-down';
        return '<span class="inline-flex items-center gap-0.5 text-[10px] font-semibold ' . $color . ' px-2 py-0.5 rounded-full"><i data-lucide="' . $icon . '" class="w-3 h-3"></i>' . ($up ? '+' : '') . $change . '%</span>';
    };
@endphp

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
            <div>
                <h2 class="font-serif text-2xl sm:text-3xl font-bold text-cafe-dark">Ringkasan Performa Bisnis</h2>
                <p class="text-xs text-cafe-muted mt-1">Pantau penjualan, laba, dan operasional ABS Cafe secara menyeluruh.</p>
            </div>
            <a href="{{ route('owner.reports.index', $period->queryParams()) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream text-xs font-semibold transition-colors">
                <i data-lucide="file-bar-chart" class="w-4 h-4"></i>
                <span>Lihat Laporan Lengkap</span>
            </a>
        </div>

        <!-- Period Filter -->
        <div class="bg-cafe-card rounded-2xl border border-cafe-beige/60 shadow-soft p-4">
            @include('owner.partials.period-filter', ['period' => $period])
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft p-5 relative overflow-hidden">
                <div class="absolute -right-6 -top-6 w-24 h-24 rounded-full bg-cafe-gold/10"></div>
                <div class="relative">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-2xl bg-cafe-gold/15 text-cafe-bronze flex items-center justify-center">
                            <i data-lucide="wallet" class="w-5 h-5"></i>
                        </div>
                        {!! $changeBadge($summary['sales']['change']) !!}
                    </div>
                    <p class="text-[11px] uppercase tracking-wide text-cafe-muted font-semibold mt-4">Total Penjualan</p>
                    <p class="font-serif text-2xl font-bold text-cafe-dark mt-1">{{ $rp($summary['sales']['value']) }}</p>
                </div>
            </div>

            <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft p-5 relative overflow-hidden">
                <div class="absolute -right-6 -top-6 w-24 h-24 rounded-full bg-cafe-success/10"></div>
                <div class="relative">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-2xl bg-cafe-success/15 text-cafe-success flex items-center justify-center">
                            <i data-lucide="shopping-bag" class="w-5 h-5"></i>
                        </div>
                        {!! $changeBadge($summary['orders']['change']) !!}
                    </div>
                    <p class="text-[11px] uppercase tracking-wide text-cafe-muted font-semibold mt-4">Jumlah Pesanan</p>
                    <p class="font-serif text-2xl font-bold text-cafe-dark mt-1">{{ number_format($summary['orders']['value'], 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft p-5 relative overflow-hidden">
                <div class="absolute -right-6 -top-6 w-24 h-24 rounded-full bg-cafe-info/10"></div>
                <div class="relative">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-2xl bg-cafe-info/15 text-cafe-info flex items-center justify-center">
                            <i data-lucide="receipt" class="w-5 h-5"></i>
                        </div>
                        {!! $changeBadge($summary['avg']['change']) !!}
                    </div>
                    <p class="text-[11px] uppercase tracking-wide text-cafe-muted font-semibold mt-4">Rata-rata / Pesanan</p>
                    <p class="font-serif text-2xl font-bold text-cafe-dark mt-1">{{ $rp($summary['avg']['value']) }}</p>
                </div>
            </div>

            <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft p-5 relative overflow-hidden">
                <div class="absolute -right-6 -top-6 w-24 h-24 rounded-full bg-cafe-warning/10"></div>
                <div class="relative">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-2xl bg-cafe-warning/15 text-cafe-warning flex items-center justify-center">
                            <i data-lucide="trending-up" class="w-5 h-5"></i>
                        </div>
                        {!! $changeBadge($summary['profit']['change']) !!}
                    </div>
                    <p class="text-[11px] uppercase tracking-wide text-cafe-muted font-semibold mt-4">Estimasi Laba ({{ $summary['margin'] }}%)</p>
                    <p class="font-serif text-2xl font-bold text-cafe-dark mt-1">{{ $rp($summary['profit']['value']) }}</p>
                </div>
            </div>
        </div>

        <!-- Target + Discount -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="lg:col-span-2 bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft p-5 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-serif font-bold text-cafe-dark flex items-center gap-2">
                        <i data-lucide="target" class="w-4 h-4 text-cafe-gold"></i> Pencapaian Target
                    </h3>
                </div>

                @foreach($targets as $key => $target)
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1.5">
                            <span class="font-semibold text-cafe-text">{{ $target['label'] }}</span>
                            <span class="text-cafe-muted">{{ $rp($target['actual']) }} / <strong class="text-cafe-dark">{{ $rp($target['target']) }}</strong></span>
                        </div>
                        <div class="h-2.5 rounded-full bg-cafe-beige/60 overflow-hidden">
                            <div class="h-full rounded-full {{ $target['percent'] >= 100 ? 'bg-cafe-success' : ($target['percent'] >= 60 ? 'bg-cafe-gold' : 'bg-cafe-bronze') }} transition-all"
                                 style="width: {{ max(2, $target['percent']) }}%"></div>
                        </div>
                        <p class="text-[10px] text-cafe-muted mt-1">{{ $target['percent'] }}% tercapai</p>
                    </div>
                @endforeach
            </div>

            <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft p-5 flex flex-col justify-between">
                <div>
                    <h3 class="font-serif font-bold text-cafe-dark flex items-center gap-2">
                        <i data-lucide="badge-percent" class="w-4 h-4 text-cafe-gold"></i> Promo Diberikan
                    </h3>
                    <p class="text-xs text-cafe-muted mt-1">Total diskon pada periode ini</p>
                </div>
                <div class="my-4">
                    <p class="font-serif text-3xl font-bold text-cafe-dark">{{ $rp($summary['discount']['value']) }}</p>
                    <div class="mt-2">{!! $changeBadge($summary['discount']['change']) !!}</div>
                </div>
                <a href="{{ route('owner.promos.index') }}" class="text-xs font-semibold text-cafe-brown hover:text-cafe-dark transition-colors inline-flex items-center gap-1">
                    Kelola Promo <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>
        </div>

        <!-- Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="lg:col-span-2 bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-serif font-bold text-cafe-dark flex items-center gap-2">
                        <i data-lucide="line-chart" class="w-4 h-4 text-cafe-gold"></i> Tren Penjualan
                    </h3>
                    <span class="text-[10px] uppercase tracking-wide text-cafe-muted font-semibold">Per {{ $chart['group'] }}</span>
                </div>
                <div class="h-72">
                    <canvas id="salesTrendChart"></canvas>
                </div>
            </div>

            <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft p-5">
                <h3 class="font-serif font-bold text-cafe-dark flex items-center gap-2 mb-4">
                    <i data-lucide="pie-chart" class="w-4 h-4 text-cafe-gold"></i> Metode Pembayaran
                </h3>
                <div class="h-56">
                    <canvas id="paymentChart"></canvas>
                </div>
                <div class="mt-4 space-y-2">
                    @foreach($paymentComposition as $pc)
                        <div class="flex items-center justify-between text-xs">
                            <span class="capitalize text-cafe-muted">{{ $pc['method'] }} ({{ $pc['count'] }})</span>
                            <span class="font-semibold text-cafe-dark">{{ $rp($pc['total']) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Top Menus / Categories / Cashier -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft p-5">
                <h3 class="font-serif font-bold text-cafe-dark flex items-center gap-2 mb-4">
                    <i data-lucide="flame" class="w-4 h-4 text-cafe-gold"></i> Menu Terlaris
                </h3>
                <div class="space-y-3">
                    @forelse($topMenus as $i => $menu)
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-lg bg-cafe-beige/60 text-cafe-dark text-[11px] font-bold flex items-center justify-center shrink-0">{{ $i + 1 }}</span>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-semibold text-cafe-dark truncate">{{ $menu['name'] }}</p>
                                <p class="text-[10px] text-cafe-muted">{{ number_format($menu['quantity'], 0, ',', '.') }} terjual</p>
                            </div>
                            <span class="text-xs font-semibold text-cafe-bronze whitespace-nowrap">{{ $rp($menu['revenue']) }}</span>
                        </div>
                    @empty
                        <p class="text-xs text-cafe-muted">Belum ada data penjualan.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft p-5">
                <h3 class="font-serif font-bold text-cafe-dark flex items-center gap-2 mb-4">
                    <i data-lucide="tags" class="w-4 h-4 text-cafe-gold"></i> Kategori Teratas
                </h3>
                <div class="space-y-3">
                    @forelse($topCategories as $cat)
                        @php $max = $topCategories->max('revenue') ?: 1; @endphp
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="font-medium text-cafe-text truncate">{{ $cat['name'] }}</span>
                                <span class="text-cafe-muted">{{ $rp($cat['revenue']) }}</span>
                            </div>
                            <div class="h-2 rounded-full bg-cafe-beige/60 overflow-hidden">
                                <div class="h-full rounded-full bg-cafe-gold" style="width: {{ max(3, round($cat['revenue'] / $max * 100)) }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-cafe-muted">Belum ada data.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft p-5">
                <h3 class="font-serif font-bold text-cafe-dark flex items-center gap-2 mb-4">
                    <i data-lucide="users" class="w-4 h-4 text-cafe-gold"></i> Kinerja Kasir
                </h3>
                <div class="space-y-3">
                    @forelse($cashierPerformance as $cs)
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-cafe-dark text-cafe-gold text-[11px] font-bold flex items-center justify-center shrink-0">
                                {{ mb_substr($cs['name'], 0, 1) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-semibold text-cafe-dark truncate">{{ $cs['name'] }}</p>
                                <p class="text-[10px] text-cafe-muted">{{ $cs['orders'] }} pesanan</p>
                            </div>
                            <span class="text-xs font-semibold text-cafe-bronze whitespace-nowrap">{{ $rp($cs['sales']) }}</span>
                        </div>
                    @empty
                        <p class="text-xs text-cafe-muted">Belum ada aktivitas kasir.</p>
                    @endforelse
                </div>
                <a href="{{ route('owner.cashiers.index', $period->queryParams()) }}" class="mt-4 inline-flex items-center gap-1 text-xs font-semibold text-cafe-brown hover:text-cafe-dark transition-colors">
                    Detail Kinerja <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>
        </div>

        <!-- Low Stock + Active Promos + Recent Orders -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft p-5">
                <h3 class="font-serif font-bold text-cafe-dark flex items-center gap-2 mb-4">
                    <i data-lucide="package-search" class="w-4 h-4 text-cafe-danger"></i> Stok Menipis
                </h3>
                <div class="space-y-2.5">
                    @forelse($lowStockMenus as $menu)
                        <div class="flex items-center justify-between gap-2">
                            <div class="min-w-0">
                                <p class="text-xs font-semibold text-cafe-dark truncate">{{ $menu->name }}</p>
                                <p class="text-[10px] text-cafe-muted">{{ $menu->category?->name ?? '-' }}</p>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $menu->stock <= 0 ? 'bg-cafe-danger/15 text-cafe-danger' : 'bg-cafe-warning/15 text-cafe-warning' }}">
                                {{ $menu->stock }} {{ $menu->unit ?? 'pcs' }}
                            </span>
                        </div>
                    @empty
                        <p class="text-xs text-cafe-muted">Semua stok dalam kondisi aman.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft p-5">
                <h3 class="font-serif font-bold text-cafe-dark flex items-center gap-2 mb-4">
                    <i data-lucide="gift" class="w-4 h-4 text-cafe-gold"></i> Promo Aktif
                </h3>
                <div class="space-y-2.5">
                    @forelse($activePromos as $promo)
                        <div class="p-2.5 rounded-2xl bg-cafe-cream/70 border border-cafe-beige/50">
                            <p class="text-xs font-semibold text-cafe-dark truncate">{{ $promo->title }}</p>
                            <p class="text-[10px] text-cafe-muted">s/d {{ \Carbon\Carbon::parse($promo->end_date)->translatedFormat('d M Y') }}</p>
                        </div>
                    @empty
                        <p class="text-xs text-cafe-muted">Tidak ada promo aktif.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft p-5">
                <h3 class="font-serif font-bold text-cafe-dark flex items-center gap-2 mb-4">
                    <i data-lucide="clock" class="w-4 h-4 text-cafe-gold"></i> Pesanan Terbaru
                </h3>
                <div class="space-y-2.5">
                    @forelse($recentOrders as $order)
                        <div class="flex items-center justify-between gap-2">
                            <div class="min-w-0">
                                <p class="text-xs font-semibold text-cafe-dark truncate">{{ $order->order_code }}</p>
                                <p class="text-[10px] text-cafe-muted">{{ $order->created_at->translatedFormat('d M, H:i') }} &bull; {{ $order->customer_name }}</p>
                            </div>
                            <span class="text-xs font-semibold text-cafe-bronze whitespace-nowrap">{{ $rp($order->total) }}</span>
                        </div>
                    @empty
                        <p class="text-xs text-cafe-muted">Belum ada pesanan pada periode ini.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    lucide.createIcons();
    Chart.defaults.font.family = "'Poppins', sans-serif";
    Chart.defaults.color = '#775533';

    const salesCtx = document.getElementById('salesTrendChart');
    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: @json($chart['labels']),
            datasets: [
                {
                    label: 'Penjualan (Rp)',
                    data: @json($chart['sales']),
                    borderColor: '#D4954D',
                    backgroundColor: 'rgba(212, 149, 77, 0.15)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2.5,
                    pointBackgroundColor: '#290024',
                    pointRadius: 3,
                    yAxisID: 'y',
                },
                {
                    label: 'Jumlah Pesanan',
                    data: @json($chart['orders']),
                    borderColor: '#775533',
                    borderDash: [5, 4],
                    tension: 0.4,
                    borderWidth: 2,
                    pointRadius: 0,
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
                tooltip: {
                    callbacks: {
                        label: (ctx) => ctx.dataset.yAxisID === 'y'
                            ? ' ' + ctx.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(ctx.parsed.y)
                            : ' ' + ctx.dataset.label + ': ' + ctx.parsed.y,
                    },
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { callback: (v) => 'Rp ' + new Intl.NumberFormat('id-ID').format(v), font: { size: 10 } },
                    grid: { color: 'rgba(227, 222, 164, 0.5)' },
                },
                y1: {
                    beginAtZero: true,
                    position: 'right',
                    ticks: { font: { size: 10 } },
                    grid: { display: false },
                },
                x: { grid: { display: false }, ticks: { font: { size: 10 } } },
            },
        },
    });

    const paymentData = @json($paymentComposition);
    new Chart(document.getElementById('paymentChart'), {
        type: 'doughnut',
        data: {
            labels: paymentData.map(p => p.method.charAt(0).toUpperCase() + p.method.slice(1)),
            datasets: [{
                data: paymentData.map(p => p.total),
                backgroundColor: ['#D4954D', '#290024', '#775533', '#B87C3C', '#5B7C99'],
                borderWidth: 0,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '62%',
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 10, boxHeight: 10, usePointStyle: true, font: { size: 10 } } },
                tooltip: {
                    callbacks: {
                        label: (ctx) => ' ' + ctx.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(ctx.parsed),
                    },
                },
            },
        },
    });
</script>
@endpush
