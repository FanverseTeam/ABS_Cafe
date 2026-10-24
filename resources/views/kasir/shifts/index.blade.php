@extends('kasir.layouts.app')

@section('title', 'Shift & Rekap Kas - Kasir')
@section('page_title', 'Buka / Tutup Shift & Rekap Kas')

@section('content')
<div class="space-y-4 lg:space-y-6">
    {{-- Mobile FAB when no active shift --}}
    @if(!$activeShift)
        <x-mobile.fab icon="play" label="Buka Shift" href="{{ route('kasir.shift.open') }}" />
    @endif

    <!-- Shift Aktif -->
    @if($activeShift)
        <div class="bg-cafe-card border border-cafe-success/40 rounded-2xl sm:rounded-3xl shadow-soft-lg overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-3 divide-y lg:divide-y-0 lg:divide-x divide-cafe-beige/40">
                <div class="p-5 md:p-6 lg:p-8 flex flex-col justify-center">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-cafe-success/15 text-cafe-success text-xs font-semibold border border-cafe-success/30 w-fit">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-cafe-success opacity-60"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-cafe-success"></span>
                        </span>
                        Shift Sedang Berjalan
                    </div>
                    <h2 class="font-serif text-2xl font-bold text-cafe-dark mt-3">Shift Dibuka Pukul {{ $activeShift->opened_at->format('H:i') }}</h2>
                    <p class="text-xs text-cafe-muted mt-1">{{ $activeShift->opened_at->translatedFormat('l, d M Y') }} &bull; Kas awal Rp {{ number_format($activeShift->opening_cash, 0, ',', '.') }}</p>
                    @if($activeShift->notes)
                        <p class="text-[11px] text-cafe-muted mt-2 italic">&ldquo;{{ $activeShift->notes }}&rdquo;</p>
                    @endif
                </div>

                <div class="p-6 sm:p-8">
                    <p class="text-[11px] font-semibold text-cafe-muted uppercase tracking-wide">Rekap Berjalan</p>
                    <div class="grid grid-cols-2 gap-4 mt-3">
                        <div>
                            <p class="text-lg font-serif font-bold text-cafe-dark">Rp {{ number_format($summary['sales'], 0, ',', '.') }}</p>
                            <p class="text-[10px] text-cafe-muted">Penjualan lunas</p>
                        </div>
                        <div>
                            <p class="text-lg font-serif font-bold text-cafe-dark">{{ $summary['orders_count'] }}</p>
                            <p class="text-[10px] text-cafe-muted">Transaksi</p>
                        </div>
                        <div>
                            <p class="text-lg font-serif font-bold text-cafe-success">Rp {{ number_format($summary['expected_cash'], 0, ',', '.') }}</p>
                            <p class="text-[10px] text-cafe-muted">Kas fisik seharusnya</p>
                        </div>
                        <div>
                            <p class="text-lg font-serif font-bold {{ $summary['unpaid_count'] > 0 ? 'text-cafe-warning' : 'text-cafe-muted' }}">{{ $summary['unpaid_count'] }}</p>
                            <p class="text-[10px] text-cafe-muted">Belum dibayar</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 sm:p-8 flex flex-col justify-center gap-3">
                    <a href="{{ route('kasir.shift.close') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-full bg-cafe-dark text-white text-xs font-bold hover:bg-cafe-brown transition-colors shadow-md">
                        <i data-lucide="square" class="w-4 h-4"></i>
                        Tutup Shift &amp; Rekap Kas
                    </a>
                    <p class="text-[10px] text-cafe-muted text-center">Tutup shift hanya bisa dilakukan bila semua pesanan sudah lunas/selesai.</p>
                </div>
            </div>
        </div>
    @else
        <div class="bg-cafe-card border border-cafe-beige/60 rounded-3xl p-6 sm:p-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-soft">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-cafe-warning/15 text-cafe-warning flex items-center justify-center shrink-0">
                    <i data-lucide="wallet" class="w-5 h-5"></i>
                </div>
                <div>
                    <h2 class="font-serif text-lg font-bold text-cafe-dark">Belum ada shift aktif hari ini</h2>
                    <p class="text-xs text-cafe-muted mt-0.5">Buka shift untuk mulai menerima transaksi POS & pembayaran.</p>
                </div>
            </div>
            <a href="{{ route('kasir.shift.open') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-cafe-gold text-cafe-dark font-bold text-xs hover:bg-[#B87C3C] transition-colors shadow-md shrink-0">
                <i data-lucide="play" class="w-4 h-4"></i>
                Buka Shift
            </a>
        </div>
    @endif

    <!-- Riwayat Shift -->
    <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft overflow-hidden">
        <div class="px-6 py-4 border-b border-cafe-beige/50 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-2xl bg-cafe-info/15 text-cafe-info flex items-center justify-center"><i data-lucide="history" class="w-4 h-4"></i></div>
                <div>
                    <h3 class="font-serif font-bold text-sm text-cafe-dark">Riwayat Shift Saya</h3>
                    <p class="text-[10px] text-cafe-muted">rekap kas dari shift yang telah ditutup</p>
                </div>
            </div>
        </div>

        <div class="p-5">
            <x-responsive-table :paginate="$history">
                <x-slot:headers>
                    <th class="py-2.5 pr-4 font-semibold text-left">Dibuka</th>
                    <th class="py-2.5 pr-4 font-semibold text-left">Ditutup</th>
                    <th class="py-2.5 pr-4 font-semibold text-right">Kas Awal</th>
                    <th class="py-2.5 pr-4 font-semibold text-right">Penjualan</th>
                    <th class="py-2.5 pr-4 font-semibold text-right">Kas Akhir Diharapkan</th>
                    <th class="py-2.5 pr-4 font-semibold text-right">Kas Fisik</th>
                    <th class="py-2.5 pr-4 font-semibold text-right">Selisih</th>
                    <th class="py-2.5 font-semibold text-right">Aksi</th>
                </x-slot:headers>

                @forelse($history as $s)
                    @php $selisih = (float)$s->cash_difference; @endphp
                    <x-responsive-table.row>
                        <x-slot:title>
                            {{ $s->opened_at->format('d/m/y H:i') }}
                        </x-slot:title>

                        <x-slot:subtitle>
                            Ditutup: {{ $s->closed_at?->format('d/m/y H:i') ?? '-' }}
                        </x-slot:subtitle>

                        <x-slot:badge>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $selisih == 0 ? 'bg-cafe-success/15 text-cafe-success' : ($selisih < 0 ? 'bg-cafe-danger/15 text-cafe-danger' : 'bg-cafe-warning/15 text-cafe-warning') }}">
                                Selisih: {{ $selisih > 0 ? '+' : '' }}{{ number_format($selisih, 0, ',', '.') }}
                            </span>
                        </x-slot:badge>

                        <x-slot:fields>
                            <x-responsive-table.field label="Penjualan" :value="'Rp ' . number_format($s->total_sales, 0, ',', '.')" is-highlight="true" />
                            <x-responsive-table.field label="Kas Awal" :value="'Rp ' . number_format($s->opening_cash, 0, ',', '.')" />
                            <x-responsive-table.field label="Kas Fisik" :value="'Rp ' . number_format($s->closing_cash, 0, ',', '.')" />
                            <x-responsive-table.field label="Diharapkan" :value="'Rp ' . number_format($s->expected_cash, 0, ',', '.')" />
                        </x-slot:fields>

                        <x-slot:actions>
                            <a href="{{ route('kasir.shift.show', $s) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-cafe-beige/60 hover:bg-cafe-beige text-cafe-dark text-xs font-semibold transition-colors">
                                <i data-lucide="file-text" class="w-3.5 h-3.5"></i> Detail Shift
                            </a>
                        </x-slot:actions>

                        <x-slot:desktop>
                            <td class="hidden md:table-cell py-3 pr-4 text-cafe-dark font-medium">{{ $s->opened_at->format('d/m/y H:i') }}</td>
                            <td class="hidden md:table-cell py-3 pr-4 text-cafe-muted">{{ $s->closed_at?->format('d/m/y H:i') ?? '-' }}</td>
                            <td class="hidden md:table-cell py-3 pr-4 text-right">{{ number_format($s->opening_cash, 0, ',', '.') }}</td>
                            <td class="hidden md:table-cell py-3 pr-4 text-right font-serif font-bold text-cafe-dark">{{ number_format($s->total_sales, 0, ',', '.') }}</td>
                            <td class="hidden md:table-cell py-3 pr-4 text-right">{{ number_format($s->expected_cash, 0, ',', '.') }}</td>
                            <td class="hidden md:table-cell py-3 pr-4 text-right">{{ number_format($s->closing_cash, 0, ',', '.') }}</td>
                            <td class="hidden md:table-cell py-3 pr-4 text-right font-bold {{ $selisih == 0 ? 'text-cafe-success' : ($selisih < 0 ? 'text-cafe-danger' : 'text-cafe-warning') }}">
                                {{ $selisih > 0 ? '+' : '' }}{{ number_format($selisih, 0, ',', '.') }}
                            </td>
                            <td class="hidden md:table-cell py-3 text-right">
                                <a href="{{ route('kasir.shift.show', $s) }}" class="inline-flex items-center gap-1 text-xs font-semibold text-cafe-brown hover:text-cafe-dark">
                                    <i data-lucide="file-text" class="w-3.5 h-3.5"></i> Detail
                                </a>
                            </td>
                        </x-slot:desktop>
                    </x-responsive-table.row>
                @empty
                    <x-responsive-table.empty col-span="8" message="Belum ada riwayat shift yang ditutup." />
                @endforelse
            </x-responsive-table>
        </div>
    </div>
</div>
@endsection