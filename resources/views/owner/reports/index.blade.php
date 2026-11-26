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

        <!-- Rekap Penjualan Harian + Form Ekspor Pilih Tanggal Mulai dan Sampai -->
        <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft p-4 sm:p-5 space-y-4">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                <div>
                    <h3 class="font-serif font-bold text-cafe-dark flex items-center gap-2">
                        <i data-lucide="calendar-days" class="w-4 h-4 text-cafe-gold"></i> Rekap Penjualan per Hari
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
