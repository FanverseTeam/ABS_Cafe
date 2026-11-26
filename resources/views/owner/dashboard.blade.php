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
    $chartQueryBase = collect(request()->query())->except(['chart_range'])->all();
@endphp

@section('content')
    <div class="space-y-4 lg:space-y-6">
        <!-- Header (Desktop only) -->
        <div class="hidden lg:flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
            <div>
                <h2 class="font-serif text-2xl sm:text-3xl font-bold text-cafe-dark">Ringkasan Performa Bisnis</h2>
                <p class="text-xs text-cafe-muted mt-1">Pantau penjualan, laba, dan operasional ABS Cafe secara menyeluruh.</p>
            </div>
            <a href="{{ route('owner.reports.index', $period->queryParams()) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream text-xs font-semibold transition-colors">
                <i data-lucide="file-bar-chart" class="w-4 h-4"></i>
                <span>Lihat Laporan Lengkap</span>
            </a>
        </div>

        {{-- Mobile FAB for Laporan Lengkap --}}
        <x-mobile.fab icon="file-bar-chart" label="Laporan" href="{{ route('owner.reports.index', $period->queryParams()) }}" />

        <!-- Period Filter -->
        <div class="bg-cafe-card rounded-2xl border border-cafe-beige/60 shadow-soft p-4">
            @include('owner.partials.period-filter', ['period' => $period])
        </div>

        <!-- Summary Cards (2 Kolom) -->
        <div class="grid grid-cols-2 gap-3 sm:gap-4">
            <div class="bg-cafe-card rounded-2xl sm:rounded-3xl border border-cafe-beige/60 shadow-soft p-4 sm:p-5 relative overflow-hidden flex flex-col justify-between">
                <div class="absolute -right-6 -top-6 w-20 h-20 rounded-full bg-cafe-gold/10 pointer-events-none"></div>
                <div class="relative">
                    <div class="flex items-center justify-between gap-1">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-cafe-gold/15 text-cafe-bronze flex items-center justify-center">
                            <i data-lucide="wallet" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                        </div>
                        {!! $changeBadge($summary['sales']['change']) !!}
                    </div>
                    <p class="text-[10px] sm:text-[11px] uppercase tracking-wide text-cafe-muted font-semibold mt-3">Total Penjualan</p>
                    <p class="font-serif text-base sm:text-2xl font-bold text-cafe-dark mt-0.5 truncate">{{ $rp($summary['sales']['value']) }}</p>
                </div>
            </div>

            <div class="bg-cafe-card rounded-2xl sm:rounded-3xl border border-cafe-beige/60 shadow-soft p-4 sm:p-5 relative overflow-hidden flex flex-col justify-between">
                <div class="absolute -right-6 -top-6 w-20 h-20 rounded-full bg-cafe-success/10 pointer-events-none"></div>
                <div class="relative">
                    <div class="flex items-center justify-between gap-1">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-cafe-success/15 text-cafe-success flex items-center justify-center">
                            <i data-lucide="shopping-bag" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                        </div>
                        {!! $changeBadge($summary['orders']['change']) !!}
                    </div>
                    <p class="text-[10px] sm:text-[11px] uppercase tracking-wide text-cafe-muted font-semibold mt-3">Jumlah Pesanan</p>
                    <p class="font-serif text-base sm:text-2xl font-bold text-cafe-dark mt-0.5 truncate">{{ number_format($summary['orders']['value'], 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <!-- Total Penjualan Hari Ini + Promo Diberikan -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="lg:col-span-2 bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft p-5 flex flex-col justify-between">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h3 class="font-serif font-bold text-cafe-dark flex items-center gap-2">
                            <i data-lucide="calendar-check" class="w-4 h-4 text-cafe-gold"></i> Total Penjualan Hari Ini
                        </h3>
                        <p class="text-xs text-cafe-muted mt-1">{{ now()->translatedFormat('l, d F Y') }}</p>
                    </div>
                    <span class="px-3 py-1 rounded-full bg-cafe-gold/15 text-cafe-bronze text-[11px] font-bold">
                        {{ number_format($todaySales['orders'], 0, ',', '.') }} transaksi lunas
                    </span>
                </div>
                <div class="my-4">
                    <p class="font-serif text-3xl sm:text-4xl font-bold text-cafe-dark">{{ $rp($todaySales['total']) }}</p>
                    <p class="text-xs text-cafe-muted mt-1">Akumulasi seluruh pesanan berstatus lunas hari ini.</p>
                </div>
                <a href="{{ route('owner.reports.index') }}" class="text-xs font-semibold text-cafe-brown hover:text-cafe-dark transition-colors inline-flex items-center gap-1">
                    Lihat Rekap Harian &amp; Ekspor <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </a>
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
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
                    <div>
                        <h3 class="font-serif font-bold text-cafe-dark flex items-center gap-2">
                            <i data-lucide="line-chart" class="w-4 h-4 text-cafe-gold"></i> Tren Persentase Penjualan
                        </h3>
                        <p class="text-[11px] text-cafe-muted mt-0.5">
                            Distribusi persentase penjualan per {{ $chart['range'] }}
                        </p>
                    </div>
                    <div class="inline-flex items-center bg-cafe-beige/50 border border-cafe-beige rounded-full p-1 text-xs self-start sm:self-auto">
                        @foreach(['hari' => 'Hari', 'bulan' => 'Bulan', 'tahun' => 'Tahun'] as $rangeKey => $rangeLabel)
                            <a href="{{ route('owner.dashboard', array_merge($chartQueryBase, ['chart_range' => $rangeKey])) }}"
                               class="px-3 py-1 rounded-full font-semibold transition-all {{ $chart['range'] === $rangeKey ? 'bg-cafe-dark text-cafe-cream shadow-2xs' : 'text-cafe-muted hover:text-cafe-dark' }}">
                                {{ $rangeLabel }}
                            </a>
                        @endforeach
                    </div>
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

        <!-- Top Menus + Active Promos -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft p-5">
                <h3 class="font-serif font-bold text-cafe-dark flex items-center gap-2 mb-4">
                    <i data-lucide="flame" class="w-4 h-4 text-cafe-gold"></i> Menu Terlaris
                </h3>
                <div class="space-y-3">
                    @forelse($topMenus as $i => $menu)
                        <div class="flex items-center gap-3 p-2 rounded-2xl hover:bg-cafe-beige/30 transition-colors">
                            <span class="w-6 h-6 rounded-lg bg-cafe-beige/60 text-cafe-dark text-[11px] font-bold flex items-center justify-center shrink-0">#{{ $i + 1 }}</span>
                            <div class="w-10 h-10 rounded-xl bg-cafe-beige overflow-hidden shrink-0 flex items-center justify-center border border-cafe-beige">
                                @if(!empty($menu['image']))
                                    <img src="{{ $menu['image'] }}" alt="{{ $menu['name'] }}" class="w-full h-full object-cover">
                                @else
                                    <i data-lucide="coffee" class="w-5 h-5 text-cafe-gold"></i>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-semibold text-cafe-dark truncate">{{ $menu['name'] }}</p>
                                <p class="text-[10px] text-cafe-muted">{{ number_format($menu['quantity'], 0, ',', '.') }} terjual</p>
                                @if(!empty($menu['variants']))
                                    <p class="text-[9px] text-cafe-muted/80 truncate">
                                        {{ collect($menu['variants'])->map(fn ($variant) => $variant['name'].' '.$variant['quantity'].'x')->implode(' / ') }}
                                    </p>
                                @endif
                            </div>
                            <span class="text-xs font-serif font-bold text-cafe-dark whitespace-nowrap">{{ $rp($menu['revenue']) }}</span>
                        </div>
                    @empty
                        <p class="text-xs text-cafe-muted">Belum ada data penjualan.</p>
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
        </div>
    </div>
@endsection

@push('scripts')
<script>
    lucide.createIcons();
    Chart.defaults.font.family = "'Poppins', sans-serif";
    Chart.defaults.color = '#775533';

    const salesCtx = document.getElementById('salesTrendChart');
    const chartSalesValues = @json($chart['sales']);
    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: @json($chart['labels']),
            datasets: [
                {
                    label: 'Persentase Penjualan (%)',
                    data: @json($chart['percentages']),
                    borderColor: '#D4954D',
                    backgroundColor: 'rgba(212, 149, 77, 0.18)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2.5,
                    pointBackgroundColor: '#290024',
                    pointRadius: 3.5,
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
                        label: (ctx) => {
                            const amount = chartSalesValues[ctx.dataIndex] || 0;
                            return ' ' + ctx.parsed.y + '% (Rp ' + new Intl.NumberFormat('id-ID').format(amount) + ')';
                        },
                    },
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    suggestedMax: 100,
                    ticks: { callback: (v) => v + '%', font: { size: 10 } },
                    grid: { color: 'rgba(227, 222, 164, 0.5)' },
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
                backgroundColor: ['#D4954D', '#290024', '#775533', '#E3DEA4', '#F2F5E2'],
                borderColor: '#F2F5E2',
                borderWidth: 2,
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
