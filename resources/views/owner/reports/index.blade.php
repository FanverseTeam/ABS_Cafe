@extends('owner.layouts.app')

@section('title', 'Laporan Rekap Penjualan Harian')
@section('page_title', 'Rekap Penjualan Harian')

@php
    $rp = fn ($v) => 'Rp ' . number_format((float) $v, 0, ',', '.');
@endphp

@section('content')
    <div class="space-y-4 lg:space-y-6">
        <!-- Header (Desktop only) -->
        <div class="hidden lg:flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
            <div>
                <h2 class="font-serif text-2xl sm:text-3xl font-bold text-cafe-dark">Rekap Penjualan Harian</h2>
                <p class="text-xs text-cafe-muted mt-1">Lihat ringkasan penjualan setiap hari dan ekspor rentang tanggal pilihan Anda.</p>
            </div>
            <a href="{{ route('owner.reports.print', $period->queryParams()) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream text-xs font-semibold transition-colors">
                <i data-lucide="printer" class="w-4 h-4"></i> Cetak
            </a>
        </div>

        {{-- Mobile FAB for Cetak Laporan --}}
        <x-mobile.fab icon="printer" label="Cetak" href="{{ route('owner.reports.print', $period->queryParams()) }}" target="_blank" />

        <!-- Period Filter -->
        <div class="bg-cafe-card rounded-2xl border border-cafe-beige/60 shadow-soft p-3 sm:p-4">
            @include('owner.partials.period-filter', ['period' => $period])
        </div>

        <!-- Ringkasan Periode -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            @foreach([
                ['Jumlah Pesanan', number_format($dailyRecap['totals']['orders'], 0, ',', '.'), 'shopping-bag', 'info'],
                ['Penjualan Kotor', $rp($dailyRecap['totals']['gross']), 'wallet', 'success'],
                ['Total Diskon', $rp($dailyRecap['totals']['discount']), 'badge-percent', 'warning'],
                ['Total Penjualan', $rp($dailyRecap['totals']['total']), 'banknote', 'gold'],
            ] as [$label, $value, $icon, $tone])
                <div class="bg-cafe-card rounded-2xl border border-cafe-beige/60 shadow-soft p-4">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center mb-3
                        {{ $tone === 'success' ? 'bg-cafe-success/15 text-cafe-success' : '' }}
                        {{ $tone === 'warning' ? 'bg-cafe-warning/15 text-cafe-warning' : '' }}
                        {{ $tone === 'info' ? 'bg-cafe-info/15 text-cafe-info' : '' }}
                        {{ $tone === 'gold' ? 'bg-cafe-gold/15 text-cafe-bronze' : '' }}">
                        <i data-lucide="{{ $icon }}" class="w-4 h-4"></i>
                    </div>
                    <p class="text-[10px] uppercase tracking-wide text-cafe-muted font-semibold">{{ $label }}</p>
                    <p class="font-serif text-lg font-bold text-cafe-dark mt-0.5 truncate">{{ $value }}</p>
                </div>
            @endforeach
        </div>

<<<<<<< HEAD
        <!-- Rekap Penjualan Harian + Form Ekspor Pilih Tanggal Mulai dan Sampai -->
        <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft p-4 sm:p-5 space-y-4">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                <div>
                    <h3 class="font-serif font-bold text-cafe-dark flex items-center gap-2">
                        <i data-lucide="calendar-days" class="w-4 h-4 text-cafe-gold"></i> Rekap Penjualan per Hari
=======
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
                    <div class="h-56 sm:h-72"><canvas id="dailyChart"></canvas></div>
                </div>
                <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft p-5">
                    <h3 class="font-serif font-bold text-cafe-dark mb-4 flex items-center gap-2"><i data-lucide="clock-4" class="w-4 h-4 text-cafe-gold"></i> Jam Sibuk</h3>
                    <div class="h-56 sm:h-72"><canvas id="hourChart"></canvas></div>
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
                                        <td class="py-2 text-cafe-text font-medium">
                                            <div>{{ $row['name'] }}</div>
                                            @if(!empty($row['variants']))
                                                <div class="mt-1 space-y-0.5">
                                                    @foreach($row['variants'] as $variant)
                                                        <div class="text-[10px] text-cafe-muted flex items-center gap-1">
                                                            <span class="w-1 h-1 rounded-full bg-cafe-gold shrink-0"></span>
                                                            <span>{{ $variant['name'] }}</span>
                                                            <span class="text-cafe-muted/70">{{ number_format($variant['quantity'], 0, ',', '.') }}x</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </td>
                                        <td class="py-2 text-right text-cafe-muted align-top">{{ number_format($row['quantity'], 0, ',', '.') }}</td>
                                        <td class="py-2 text-right font-semibold text-cafe-dark align-top">{{ $rp($row['revenue']) }}</td>
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
                <x-responsive-table :paginate="$data['orders']">
                    <x-slot:headers>
                        <th class="text-left py-2">Kode</th>
                        <th class="text-left py-2">Waktu</th>
                        <th class="text-left py-2">Customer</th>
                        <th class="text-left py-2">Kasir</th>
                        <th class="text-left py-2">Metode</th>
                        <th class="text-right py-2">Total</th>
                    </x-slot:headers>

                    @forelse($data['orders'] as $order)
                        <x-responsive-table.row>
                            <x-slot:title>
                                {{ $order->order_code }}
                            </x-slot:title>

                            <x-slot:subtitle>
                                {{ $order->created_at->format('d/m/Y H:i') }}
                            </x-slot:subtitle>

                            <x-slot:badge>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-cafe-beige/60 text-cafe-muted">
                                    {{ $order->payment_method }}
                                </span>
                            </x-slot:badge>

                            <x-slot:fields>
                                <x-responsive-table.field label="Customer" :value="$order->customer_name" />
                                <x-responsive-table.field label="Kasir" :value="$order->cashierLabel()" />
                                <x-responsive-table.field label="Total" :value="$rp($order->total)" is-highlight="true" />
                            </x-slot:fields>

                            <x-slot:desktop>
                                <td class="hidden md:table-cell py-2.5 font-semibold text-cafe-dark">{{ $order->order_code }}</td>
                                <td class="hidden md:table-cell py-2.5 text-cafe-muted">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                <td class="hidden md:table-cell py-2.5 text-cafe-text">{{ $order->customer_name }}</td>
                                <td class="hidden md:table-cell py-2.5 text-cafe-muted">{{ $order->cashierLabel() }}</td>
                                <td class="hidden md:table-cell py-2.5 uppercase text-cafe-muted">{{ $order->payment_method }}</td>
                                <td class="hidden md:table-cell py-2.5 text-right font-semibold text-cafe-dark">{{ $rp($order->total) }}</td>
                            </x-slot:desktop>
                        </x-responsive-table.row>
                    @empty
                        <x-responsive-table.empty col-span="6" message="Belum ada pesanan pada periode ini." />
                    @endforelse
                </x-responsive-table>
            </div>
        @else
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach([
                    ['Penjualan Kotor', $rp($finance['gross']), 'wallet'],
                    ['Total Diskon', $rp($finance['discount']), 'badge-percent'],
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
                    <div class="overflow-x-auto">
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
                    </div>
                    <div class="mt-4 p-3 rounded-2xl bg-cafe-cream/70 border border-cafe-beige/50 text-xs">
                        <p class="text-cafe-muted">Estimasi laba (margin {{ $finance['margin'] }}%)</p>
                        <p class="font-serif text-lg font-bold text-cafe-dark">{{ $rp($finance['net'] * $finance['margin'] / 100) }}</p>
                    </div>
                </div>

                <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft p-5">
                    <h3 class="font-serif font-bold text-cafe-dark mb-4 flex items-center gap-2"><i data-lucide="coins" class="w-4 h-4 text-cafe-gold"></i> Rekap Kas per Shift
                        @if(!$finance['requirePhysical'])
                            <span class="ml-2 text-[10px] bg-cafe-success/15 text-cafe-success font-semibold px-2 py-0.5 rounded-full">Mode Otomatis</span>
                        @endif
>>>>>>> 5d6c777e0ebc0b951290e257619bf1c2303fa220
                    </h3>
                    <p class="text-xs text-cafe-muted mt-0.5">Pilih tanggal mulai dan sampai untuk mengekspor banyak hari sekaligus.</p>
                </div>

                <form action="{{ route('owner.reports.export') }}" method="GET" class="flex flex-wrap sm:flex-nowrap items-center gap-2 text-xs">
                    <div class="flex items-center gap-1.5 bg-cafe-cream border border-cafe-beige rounded-full px-3 py-1.5">
                        <span class="text-[10px] font-semibold text-cafe-muted">Mulai:</span>
                        <input type="date" name="mulai"
                               value="{{ $period->start->toDateString() }}"
                               required
                               class="bg-transparent text-[11px] font-medium text-cafe-text focus:outline-none cursor-pointer">
                    </div>
                    <span class="text-cafe-muted font-bold">&ndash;</span>
                    <div class="flex items-center gap-1.5 bg-cafe-cream border border-cafe-beige rounded-full px-3 py-1.5">
                        <span class="text-[10px] font-semibold text-cafe-muted">Sampai:</span>
                        <input type="date" name="selesai"
                               value="{{ $period->end->toDateString() }}"
                               required
                               class="bg-transparent text-[11px] font-medium text-cafe-text focus:outline-none cursor-pointer">
                    </div>
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream text-xs font-semibold transition-colors shrink-0">
                        <i data-lucide="download" class="w-3.5 h-3.5"></i>
                        <span>Ekspor CSV</span>
                    </button>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="border-b border-cafe-beige/70 text-[11px] uppercase tracking-wide text-cafe-muted text-left">
                            <th class="py-2.5 pr-3 font-semibold">Tanggal</th>
                            <th class="py-2.5 px-3 font-semibold text-right">Pesanan</th>
                            <th class="py-2.5 px-3 font-semibold text-right">Kotor</th>
                            <th class="py-2.5 px-3 font-semibold text-right">Diskon</th>
                            <th class="py-2.5 pl-3 font-semibold text-right">Total Penjualan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-cafe-beige/40">
                        @forelse($dailyRecap['rows'] as $row)
                            <tr class="hover:bg-cafe-beige/20 transition-colors">
                                <td class="py-2.5 pr-3">
                                    <span class="font-semibold text-cafe-dark">{{ $row['label'] }}</span>
                                    <span class="text-[10px] text-cafe-muted ml-1">({{ $row['day_name'] }})</span>
                                </td>
                                <td class="py-2.5 px-3 text-right tabular-nums text-cafe-text">{{ number_format($row['orders'], 0, ',', '.') }}</td>
                                <td class="py-2.5 px-3 text-right tabular-nums text-cafe-muted">{{ $rp($row['gross']) }}</td>
                                <td class="py-2.5 px-3 text-right tabular-nums text-cafe-muted">{{ $rp($row['discount']) }}</td>
                                <td class="py-2.5 pl-3 text-right tabular-nums font-semibold text-cafe-dark">{{ $rp($row['total']) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-cafe-muted">Belum ada data penjualan pada periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if(count($dailyRecap['rows']) > 0)
                        <tfoot>
                            <tr class="border-t-2 border-cafe-beige bg-cafe-cream/60 font-bold text-cafe-dark">
                                <td class="py-2.5 pr-3">Total ({{ count($dailyRecap['rows']) }} hari)</td>
                                <td class="py-2.5 px-3 text-right tabular-nums">{{ number_format($dailyRecap['totals']['orders'], 0, ',', '.') }}</td>
                                <td class="py-2.5 px-3 text-right tabular-nums">{{ $rp($dailyRecap['totals']['gross']) }}</td>
                                <td class="py-2.5 px-3 text-right tabular-nums">{{ $rp($dailyRecap['totals']['discount']) }}</td>
                                <td class="py-2.5 pl-3 text-right tabular-nums text-cafe-bronze">{{ $rp($dailyRecap['totals']['total']) }}</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
