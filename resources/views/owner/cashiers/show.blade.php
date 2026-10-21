@extends('owner.layouts.app')

@section('title', 'Detail Kinerja Kasir')
@section('page_title', 'Detail Kinerja Kasir')

@php
    $rp = fn ($v) => 'Rp ' . number_format((float) $v, 0, ',', '.');
@endphp

@section('content')
    <div class="space-y-6">
        <!-- Back + Header -->
        <div class="flex items-center gap-3">
            <a href="{{ route('owner.cashiers.index', $period->queryParams()) }}" class="w-9 h-9 rounded-full bg-cafe-card border border-cafe-beige flex items-center justify-center text-cafe-muted hover:text-cafe-dark transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
            </a>
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-cafe-dark text-cafe-gold serif font-bold flex items-center justify-center">{{ $cashier->initials() }}</div>
                <div>
                    <h2 class="font-serif text-xl font-bold text-cafe-dark">{{ $cashier->name }}</h2>
                    <p class="text-xs text-cafe-muted">@ {{ $cashier->username }} &bull; {{ $period->label }}</p>
                </div>
            </div>
        </div>

        <div class="bg-cafe-card rounded-2xl border border-cafe-beige/60 shadow-soft p-4">
            @include('owner.partials.period-filter', ['period' => $period])
        </div>

        <!-- Metric Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft p-5">
                <div class="w-9 h-9 rounded-xl bg-cafe-gold/15 text-cafe-bronze flex items-center justify-center mb-3"><i data-lucide="wallet" class="w-4 h-4"></i></div>
                <p class="text-[10px] uppercase tracking-wide text-cafe-muted font-semibold">Total Penjualan</p>
                <p class="font-serif text-xl font-bold text-cafe-dark mt-0.5">{{ $rp($metrics['sales']) }}</p>
            </div>
            <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft p-5">
                <div class="w-9 h-9 rounded-xl bg-cafe-info/15 text-cafe-info flex items-center justify-center mb-3"><i data-lucide="shopping-bag" class="w-4 h-4"></i></div>
                <p class="text-[10px] uppercase tracking-wide text-cafe-muted font-semibold">Jumlah Pesanan</p>
                <p class="font-serif text-xl font-bold text-cafe-dark mt-0.5">{{ number_format($metrics['orders'], 0, ',', '.') }}</p>
            </div>
            <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft p-5">
                <div class="w-9 h-9 rounded-xl bg-cafe-success/15 text-cafe-success flex items-center justify-center mb-3"><i data-lucide="timer" class="w-4 h-4"></i></div>
                <p class="text-[10px] uppercase tracking-wide text-cafe-muted font-semibold">Kecepatan Layanan</p>
                <p class="font-serif text-xl font-bold text-cafe-dark mt-0.5">{{ $metrics['prep_minutes'] !== null ? '~' . $metrics['prep_minutes'] . ' mnt' : '-' }}</p>
            </div>
            <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft p-5">
                <div class="w-9 h-9 rounded-xl bg-cafe-warning/15 text-cafe-warning flex items-center justify-center mb-3"><i data-lucide="coins" class="w-4 h-4"></i></div>
                <p class="text-[10px] uppercase tracking-wide text-cafe-muted font-semibold">Selisih Kas</p>
                @if(\App\Models\CafeSetting::get('require_physical_cash_count', '0') == '0')
                    <p class="font-serif text-xl font-bold text-cafe-success mt-0.5">–</p>
                    <p class="text-[10px] text-cafe-muted mt-0.5">Mode otomatis</p>
                @else
                    <p class="font-serif text-xl font-bold {{ abs($metrics['cash_difference']) < 1 ? 'text-cafe-success' : 'text-cafe-danger' }} mt-0.5">{{ $rp($metrics['cash_difference']) }}</p>
                @endif
            </div>
        </div>

        <!-- Daily Chart -->
        <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft p-5">
            <h3 class="font-serif font-bold text-cafe-dark mb-4 flex items-center gap-2"><i data-lucide="line-chart" class="w-4 h-4 text-cafe-gold"></i> Penjualan Harian</h3>
            <div class="h-56 sm:h-64"><canvas id="cashierDailyChart"></canvas></div>
        </div>

        <!-- Shifts -->
        <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft p-5">
            <h3 class="font-serif font-bold text-cafe-dark mb-4 flex items-center gap-2"><i data-lucide="clock" class="w-4 h-4 text-cafe-gold"></i> Riwayat Shift</h3>
            <x-responsive-table>
                <x-slot:headers>
                    <th class="text-left py-2">Buka</th>
                    <th class="text-left py-2">Tutup</th>
                    <th class="text-right py-2">Durasi</th>
                    <th class="text-right py-2">Transaksi</th>
                    <th class="text-right py-2">Penjualan</th>
                    <th class="text-right py-2">Selisih</th>
                    <th class="text-center py-2">Status</th>
                </x-slot:headers>

                @forelse($shifts as $shift)
                    @php $status = $shift->statusBadge(); $diff = $shift->differenceBadge(); @endphp
                    <x-responsive-table.row>
                        <x-slot:title>
                            {{ $shift->opened_at?->format('d/m/Y H:i') }}
                        </x-slot:title>

                        <x-slot:subtitle>
                            Tutup: {{ $shift->closed_at?->format('d/m/Y H:i') ?? '-' }}
                        </x-slot:subtitle>

                        <x-slot:badge>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $status['color'] === 'success' ? 'bg-cafe-success/15 text-cafe-success' : 'bg-cafe-beige/60 text-cafe-muted' }}">
                                {{ $status['label'] }}
                            </span>
                        </x-slot:badge>

                        <x-slot:fields>
                            <x-responsive-table.field label="Penjualan" :value="$rp($shift->total_sales)" is-highlight="true" />
                            <x-responsive-table.field label="Transaksi" :value="$shift->total_transactions" />
                            <x-responsive-table.field label="Durasi" :value="$shift->durationLabel()" />
                            <x-responsive-table.field label="Selisih">
                                @if($shift->is_auto_closed)
                                    <span class="font-bold text-cafe-success">Rp 0 (auto)</span>
                                @else
                                    <span class="font-bold {{ $diff['color'] === 'success' ? 'text-cafe-success' : ($diff['color'] === 'warning' ? 'text-cafe-warning' : 'text-cafe-danger') }}">
                                        {{ $rp($shift->cash_difference) }}
                                    </span>
                                @endif
                            </x-responsive-table.field>
                        </x-slot:fields>

                        <x-slot:desktop>
                            <td class="hidden md:table-cell py-2 text-cafe-text">{{ $shift->opened_at?->format('d/m/Y H:i') }}</td>
                            <td class="hidden md:table-cell py-2 text-cafe-muted">{{ $shift->closed_at?->format('d/m/Y H:i') ?? '-' }}</td>
                            <td class="hidden md:table-cell py-2 text-right text-cafe-muted">{{ $shift->durationLabel() }}</td>
                            <td class="hidden md:table-cell py-2 text-right text-cafe-muted">{{ $shift->total_transactions }}</td>
                            <td class="hidden md:table-cell py-2 text-right font-semibold text-cafe-dark">{{ $rp($shift->total_sales) }}</td>
                            <td class="hidden md:table-cell py-2 text-right">
                                @if($shift->is_auto_closed)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-cafe-success/15 text-cafe-success">Rp 0 (auto)</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold
                                        {{ $diff['color'] === 'success' ? 'bg-cafe-success/15 text-cafe-success' : '' }}
                                        {{ $diff['color'] === 'warning' ? 'bg-cafe-warning/15 text-cafe-warning' : '' }}
                                        {{ $diff['color'] === 'danger' ? 'bg-cafe-danger/15 text-cafe-danger' : '' }}">
                                        {{ $rp($shift->cash_difference) }}
                                    </span>
                                @endif
                            </td>
                            <td class="hidden md:table-cell py-2 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $status['color'] === 'success' ? 'bg-cafe-success/15 text-cafe-success' : 'bg-cafe-beige/60 text-cafe-muted' }}">
                                    {{ $status['label'] }}
                                </span>
                            </td>
                        </x-slot:desktop>
                    </x-responsive-table.row>
                @empty
                    <x-responsive-table.empty col-span="7" message="Belum ada data shift pada periode ini." />
                @endforelse
            </x-responsive-table>
        </div>

        <!-- Orders -->
        <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft p-5">
            <h3 class="font-serif font-bold text-cafe-dark mb-4 flex items-center gap-2"><i data-lucide="receipt" class="w-4 h-4 text-cafe-gold"></i> Pesanan Ditangani</h3>
            <x-responsive-table :paginate="$orders">
                <x-slot:headers>
                    <th class="text-left py-2">Kode</th>
                    <th class="text-left py-2">Waktu</th>
                    <th class="text-left py-2">Customer</th>
                    <th class="text-left py-2">Metode</th>
                    <th class="text-left py-2">Status</th>
                    <th class="text-right py-2">Total</th>
                </x-slot:headers>

                @forelse($orders as $order)
                    <x-responsive-table.row>
                        <x-slot:title>
                            {{ $order->order_code }}
                        </x-slot:title>

                        <x-slot:subtitle>
                            {{ $order->created_at->format('d/m/Y H:i') }}
                        </x-slot:subtitle>

                        <x-slot:badge>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold
                                {{ $order->payment_status === 'lunas' ? 'bg-cafe-success/15 text-cafe-success' : '' }}
                                {{ $order->payment_status === 'menunggu_pembayaran' ? 'bg-cafe-warning/15 text-cafe-warning' : '' }}
                                {{ $order->payment_status === 'dibatalkan' ? 'bg-cafe-danger/15 text-cafe-danger' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}
                            </span>
                        </x-slot:badge>

                        <x-slot:fields>
                            <x-responsive-table.field label="Customer" :value="$order->customer_name" />
                            <x-responsive-table.field label="Metode" :value="strtoupper($order->payment_method)" />
                            <x-responsive-table.field label="Total" :value="$rp($order->total)" is-highlight="true" />
                        </x-slot:fields>

                        <x-slot:desktop>
                            <td class="hidden md:table-cell py-2.5 font-semibold text-cafe-dark">{{ $order->order_code }}</td>
                            <td class="hidden md:table-cell py-2.5 text-cafe-muted">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td class="hidden md:table-cell py-2.5 text-cafe-text">{{ $order->customer_name }}</td>
                            <td class="hidden md:table-cell py-2.5 uppercase text-cafe-muted">{{ $order->payment_method }}</td>
                            <td class="hidden md:table-cell py-2.5">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold
                                    {{ $order->payment_status === 'lunas' ? 'bg-cafe-success/15 text-cafe-success' : '' }}
                                    {{ $order->payment_status === 'menunggu_pembayaran' ? 'bg-cafe-warning/15 text-cafe-warning' : '' }}
                                    {{ $order->payment_status === 'dibatalkan' ? 'bg-cafe-danger/15 text-cafe-danger' : '' }}">
                                    {{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}
                                </span>
                            </td>
                            <td class="hidden md:table-cell py-2.5 text-right font-semibold text-cafe-dark">{{ $rp($order->total) }}</td>
                        </x-slot:desktop>
                    </x-responsive-table.row>
                @empty
                    <x-responsive-table.empty col-span="6" message="Belum ada pesanan pada periode ini." />
                @endforelse
            </x-responsive-table>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    lucide.createIcons();
    Chart.defaults.font.family = "'Poppins', sans-serif";
    Chart.defaults.color = '#775533';

    const daily = @json($daily);
    new Chart(document.getElementById('cashierDailyChart'), {
        type: 'line',
        data: {
            labels: daily.map(d => {
                const parts = d.date.split('-');
                return parts[2] + '/' + parts[1];
            }),
            datasets: [{
                label: 'Penjualan (Rp)',
                data: daily.map(d => d.sales),
                borderColor: '#D4954D',
                backgroundColor: 'rgba(212, 149, 77, 0.15)',
                fill: true,
                tension: 0.4,
                borderWidth: 2.5,
                pointBackgroundColor: '#290024',
                pointRadius: 3,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: (ctx) => ' Penjualan: Rp ' + new Intl.NumberFormat('id-ID').format(ctx.parsed.y) } },
            },
            scales: {
                y: { beginAtZero: true, ticks: { callback: (v) => 'Rp ' + new Intl.NumberFormat('id-ID', { notation: 'compact' }).format(v), font: { size: 10 } }, grid: { color: 'rgba(227, 222, 164, 0.5)' } },
                x: { grid: { display: false }, ticks: { font: { size: 10 } } },
            },
        },
    });
</script>
@endpush
