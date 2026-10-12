@extends('owner.layouts.app')

@section('title', 'Kinerja Kasir')
@section('page_title', 'Kinerja Kasir')

@php
    $rp = fn ($v) => 'Rp ' . number_format((float) $v, 0, ',', '.');
    $topSales = $cashiers->max('sales') ?: 1;
@endphp

@section('content')
    <div class="space-y-6">
        <div>
            <h2 class="font-serif text-2xl sm:text-3xl font-bold text-cafe-dark">Kinerja Kasir</h2>
            <p class="text-xs text-cafe-muted mt-1">Bandingkan performa penjualan, jumlah transaksi, dan kecepatan layanan tiap kasir.</p>
        </div>

        <div class="bg-cafe-card rounded-2xl border border-cafe-beige/60 shadow-soft p-4">
            @include('owner.partials.period-filter', ['period' => $period])
        </div>

        @if($cashiers->isEmpty())
            <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft p-10 text-center">
                <i data-lucide="users" class="w-10 h-10 text-cafe-beige mx-auto mb-3"></i>
                <p class="text-sm text-cafe-muted">Belum ada akun kasir terdaftar.</p>
            </div>
        @else
            <!-- Chart -->
            <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft p-5">
                <h3 class="font-serif font-bold text-cafe-dark mb-4 flex items-center gap-2"><i data-lucide="bar-chart-3" class="w-4 h-4 text-cafe-gold"></i> Perbandingan Penjualan</h3>
                <div class="h-72"><canvas id="cashierChart"></canvas></div>
            </div>

            <!-- Table -->
            <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft p-5">
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="text-cafe-muted uppercase text-[10px] tracking-wide border-b border-cafe-beige/60">
                                <th class="text-left py-3">Kasir</th>
                                <th class="text-right py-3">Pesanan</th>
                                <th class="text-right py-3">Total Penjualan</th>
                                <th class="text-right py-3">Rata-rata</th>
                                <th class="text-right py-3">Shift</th>
                                <th class="text-right py-3">Kecepatan</th>
                                <th class="text-right py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-cafe-beige/40">
                            @foreach($cashiers as $c)
                                <tr class="hover:bg-cafe-cream/60 transition-colors">
                                    <td class="py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-full bg-cafe-dark text-cafe-gold text-xs font-bold flex items-center justify-center shrink-0">{{ $c['initials'] }}</div>
                                            <div class="min-w-0">
                                                <p class="font-semibold text-cafe-dark truncate">{{ $c['name'] }}</p>
                                                <p class="text-[10px] text-cafe-muted">@ {{ $c['username'] }}</p>
                                                <div class="mt-1 h-1.5 w-28 rounded-full bg-cafe-beige/60 overflow-hidden">
                                                    <div class="h-full rounded-full bg-cafe-gold" style="width: {{ max(3, round($c['sales'] / $topSales * 100)) }}%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 text-right font-medium text-cafe-text">{{ number_format($c['orders'], 0, ',', '.') }}</td>
                                    <td class="py-3 text-right font-semibold text-cafe-dark">{{ $rp($c['sales']) }}</td>
                                    <td class="py-3 text-right text-cafe-muted">{{ $rp($c['avg']) }}</td>
                                    <td class="py-3 text-right text-cafe-muted">{{ $c['shifts'] }}</td>
                                    <td class="py-3 text-right">
                                        @if($c['prep_minutes'] !== null)
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $c['prep_minutes'] <= 12 ? 'bg-cafe-success/15 text-cafe-success' : 'bg-cafe-warning/15 text-cafe-warning' }}">
                                                ~{{ $c['prep_minutes'] }} mnt
                                            </span>
                                        @else
                                            <span class="text-cafe-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="py-3 text-right">
                                        <a href="{{ route('owner.cashiers.show', array_merge(['id' => $c['id']], $period->queryParams())) }}"
                                           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-cafe-beige/60 hover:bg-cafe-beige text-cafe-dark font-medium transition-colors">
                                            Detail <i data-lucide="arrow-right" class="w-3 h-3"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
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

    @if($cashiers->isNotEmpty())
    new Chart(document.getElementById('cashierChart'), {
        type: 'bar',
        data: {
            labels: @json($chart['labels']),
            datasets: [
                {
                    label: 'Total Penjualan (Rp)',
                    data: @json($chart['sales']),
                    backgroundColor: '#D4954D',
                    borderRadius: 8,
                    yAxisID: 'y',
                },
                {
                    label: 'Jumlah Pesanan',
                    data: @json($chart['orders']),
                    type: 'line',
                    borderColor: '#290024',
                    backgroundColor: '#290024',
                    tension: 0.4,
                    pointRadius: 3,
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
                tooltip: { callbacks: { label: (ctx) => ctx.dataset.yAxisID === 'y' ? ' Penjualan: Rp ' + new Intl.NumberFormat('id-ID').format(ctx.parsed.y) : ' Pesanan: ' + ctx.parsed.y } },
            },
            scales: {
                y: { beginAtZero: true, ticks: { callback: (v) => 'Rp ' + new Intl.NumberFormat('id-ID', { notation: 'compact' }).format(v), font: { size: 10 } }, grid: { color: 'rgba(227, 222, 164, 0.5)' } },
                y1: { beginAtZero: true, position: 'right', ticks: { font: { size: 10 } }, grid: { display: false } },
                x: { grid: { display: false }, ticks: { font: { size: 10 } } },
            },
        },
    });
    @endif
</script>
@endpush
