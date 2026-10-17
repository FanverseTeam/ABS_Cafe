@extends('kasir.layouts.app')

@section('title', 'Tutup Shift & Rekap Kas - Kasir')
@section('page_title', 'Tutup Shift & Rekap Kas')

@section('content')
<div class="space-y-6">
    <!-- Header Shift -->
    <div class="flex flex-wrap items-center justify-between gap-4 bg-cafe-card rounded-3xl border border-cafe-success/40 p-5 shadow-soft">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-cafe-success/15 text-cafe-success flex items-center justify-center">
                <i data-lucide="clock" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-[11px] text-cafe-muted">Shift dibuka</p>
                <p class="text-sm font-serif font-bold text-cafe-dark">{{ $shift->opened_at->translatedFormat('l, d M Y • H:i') }} WIB</p>
            </div>
        </div>
        <div class="text-right">
            <p class="text-[11px] text-cafe-muted">Penjualan shift berjalan</p>
            <p class="text-xl font-serif font-bold text-cafe-success">Rp {{ number_format($summary['sales'], 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Alert Blokir Pesanan Belum Selesai -->
    @if(isset($pendingOrders) && $pendingOrders->isNotEmpty())
        <div class="p-5 rounded-3xl bg-[#B03A2E]/10 border border-[#B03A2E]/30 text-[#B03A2E] space-y-3 shadow-soft">
            <div class="flex items-start gap-3">
                <i data-lucide="alert-octagon" class="w-5 h-5 shrink-0 mt-0.5 text-cafe-danger"></i>
                <div class="space-y-1">
                    <p class="text-xs font-bold uppercase tracking-wider text-cafe-danger">Shift Tidak Dapat Ditutup</p>
                    <p class="text-xs text-cafe-dark leading-relaxed">
                        Masih terdapat <b>{{ $pendingOrders->count() }}</b> pesanan aktif atau belum lunas. Selesaikan, lunasi, atau batalkan pesanan berikut sebelum menutup shift:
                    </p>
                </div>
            </div>
            <div class="divide-y divide-cafe-danger/20 border-t border-cafe-danger/20 pt-2">
                @foreach($pendingOrders as $order)
                    <div class="py-2 flex items-center justify-between text-xs gap-3">
                        <div class="flex items-center gap-2">
                            <span class="font-mono font-bold text-cafe-dark">{{ $order->order_code }}</span>
                            <span class="text-cafe-muted">&bull; {{ $order->customer_name }}</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-cafe-beige/60 text-cafe-brown capitalize">
                                {{ str_replace('_', ' ', $order->payment_status) }} ({{ $order->order_status }})
                            </span>
                        </div>
                        <a href="{{ route('kasir.orders.show', $order->id) }}" class="inline-flex items-center gap-1 font-bold text-cafe-brown hover:text-cafe-dark underline text-xs shrink-0">
                            <span>Buka Pesanan</span>
                            <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Rekap per metode pembayaran -->
    <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft overflow-hidden">
        <div class="px-6 py-4 border-b border-cafe-beige/50 flex items-center justify-between">
            <h3 class="font-serif font-bold text-sm text-cafe-dark flex items-center gap-2">
                <i data-lucide="wallet" class="w-4 h-4 text-cafe-gold"></i>
                Rekap Pembayaran per Metode
            </h3>
            <span class="text-xs text-cafe-muted">{{ $summary['orders_count'] }} pesanan lunas</span>
        </div>
        <div class="p-5">
            @if(count($summary['methods']) > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($summary['methods'] as $m)
                        <div class="p-4 rounded-2xl bg-cafe-cream/70 border border-cafe-beige/50">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold text-cafe-dark">{{ $m['label'] }}</span>
                                <span class="text-[10px] bg-cafe-beige/60 text-cafe-muted font-semibold px-2 py-0.5 rounded-full">{{ $m['count'] }}x</span>
                            </div>
                            <p class="font-serif font-bold text-cafe-dark mt-1.5">Rp {{ number_format($m['total'], 0, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-cafe-muted py-3 text-center">Belum ada transaksi lunas pada shift ini.</p>
            @endif

            <!-- Ringkasan Arus Kas Laci -->
            <div class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div class="p-3 rounded-2xl bg-cafe-cream border border-cafe-beige/60">
                    <p class="text-[10px] font-semibold text-cafe-muted uppercase">Kas Awal</p>
                    <p class="text-sm font-serif font-bold text-cafe-dark mt-0.5">Rp {{ number_format($shift->opening_cash, 0, ',', '.') }}</p>
                </div>
                <div class="p-3 rounded-2xl bg-cafe-cream border border-cafe-beige/60">
                    <p class="text-[10px] font-semibold text-cafe-muted uppercase">Penjualan Tunai</p>
                    <p class="text-sm font-serif font-bold text-cafe-dark mt-0.5">Rp {{ number_format($summary['cash_sales'], 0, ',', '.') }}</p>
                </div>
                <div class="p-3 rounded-2xl bg-cafe-cream border border-cafe-beige/60">
                    <p class="text-[10px] font-semibold text-cafe-muted uppercase">Kas Masuk Manual</p>
                    <p class="text-sm font-serif font-bold text-cafe-info mt-0.5">+Rp {{ number_format($summary['cash_in'], 0, ',', '.') }}</p>
                </div>
                <div class="p-3 rounded-2xl bg-cafe-cream border border-cafe-beige/60">
                    <p class="text-[10px] font-semibold text-cafe-muted uppercase">Kas Keluar Manual</p>
                    <p class="text-sm font-serif font-bold text-cafe-danger mt-0.5">-Rp {{ number_format($summary['cash_out'], 0, ',', '.') }}</p>
                </div>
            </div>

            @if($summary['discount'] > 0)
                <div class="mt-3 p-2.5 rounded-xl bg-cafe-beige/30 border border-cafe-beige/50 flex items-center justify-between text-xs text-cafe-muted">
                    <span>Total Diskon &amp; Promo Shift:</span>
                    <span class="font-bold text-cafe-dark">Rp {{ number_format($summary['discount'], 0, ',', '.') }}</span>
                </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Catat Kas Masuk/Keluar -->
        <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft overflow-hidden">
            <div class="px-6 py-4 border-b border-cafe-beige/50">
                <h3 class="font-serif font-bold text-sm text-cafe-dark">Catat Kas Masuk / Keluar</h3>
                <p class="text-[11px] text-cafe-muted mt-0.5">Catatan ini langsung memengaruhi kas sistem di laci</p>
            </div>
            <div class="p-5 space-y-5">
                <form action="{{ route('kasir.shift.movement') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @csrf
                    <div class="sm:col-span-2">
                        <select name="type" class="w-full bg-[#E3DEA4]/50 text-xs text-cafe-text px-4 py-3 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none focus:ring-2 focus:ring-cafe-gold/20">
                            <option value="masuk">Kas Masuk (tambahan modal / pengembalian)</option>
                            <option value="keluar">Kas Keluar (belanja bahan / operasional kasir)</option>
                        </select>
                    </div>
                    <div>
                        <input type="number" name="amount" min="1" required placeholder="Jumlah (Rp)" class="w-full bg-[#E3DEA4]/50 focus:bg-white text-xs text-cafe-dark placeholder:text-cafe-muted/60 px-4 py-3 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none focus:ring-2 focus:ring-cafe-gold/20">
                    </div>
                    <div>
                        <input type="text" name="reason" required maxlength="200" placeholder="Alasan wajib (mis. beli es batu)" class="w-full bg-[#E3DEA4]/50 focus:bg-white text-xs text-cafe-dark placeholder:text-cafe-muted/60 px-4 py-3 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none focus:ring-2 focus:ring-cafe-gold/20">
                    </div>
                    <div class="sm:col-span-2">
                        <button class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-full bg-cafe-dark text-white text-xs font-semibold hover:bg-cafe-brown transition-colors">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            Tambahkan Catatan Kas
                        </button>
                    </div>
                </form>

                @if($movements->isNotEmpty())
                    <div class="divide-y divide-cafe-beige/30 max-h-56 overflow-y-auto border-t border-cafe-beige/50 pt-2">
                        @foreach($movements as $rec)
                            <div class="py-2.5 flex items-center justify-between gap-3">
                                <div class="min-w-0 flex items-start gap-2.5">
                                    <i data-lucide="{{ $rec->type === 'masuk' ? 'arrow-down-left' : 'arrow-up-right' }}" class="w-4 h-4 mt-0.5 {{ $rec->type === 'masuk' ? 'text-cafe-info' : 'text-cafe-warning' }}"></i>
                                    <div class="min-w-0">
                                        <p class="text-xs font-semibold text-cafe-dark truncate">{{ $rec->reason }}</p>
                                        <p class="text-[10px] text-cafe-muted">{{ $rec->user?->name ?? 'Kasir' }} &bull; {{ $rec->created_at->format('d/m/y H:i') }}</p>
                                    </div>
                                </div>
                                <span class="text-xs font-bold shrink-0 {{ $rec->type === 'masuk' ? 'text-cafe-info' : 'text-cafe-warning' }}">
                                    {{ $rec->type === 'masuk' ? '+' : '-' }}Rp {{ number_format($rec->amount, 0, ',', '.') }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Tutup Shift -->
        <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft overflow-hidden">
            <div class="px-6 py-4 border-b border-cafe-beige/50">
                <h3 class="font-serif font-bold text-sm text-cafe-dark">Akhiri Shift</h3>
            </div>
            <div class="p-5 space-y-5">
                <div class="p-5 rounded-2xl bg-cafe-cream/80 border border-cafe-beige/70 space-y-1">
                    <p class="text-xs font-semibold text-cafe-muted uppercase tracking-wider">Kas Akhir Sistem di Laci</p>
                    <p class="font-serif text-3xl font-bold text-cafe-success">Rp {{ number_format($summary['expected_cash'], 0, ',', '.') }}</p>
                    <p class="text-[11px] text-cafe-muted pt-1">
                        (= Kas awal + penjualan tunai + kas masuk − kas keluar)
                    </p>
                    @if(! $requirePhysical)
                        <div class="mt-2 text-[11px] text-cafe-brown bg-cafe-beige/30 p-2.5 rounded-xl border border-cafe-beige/50 flex items-center gap-2">
                            <i data-lucide="check-circle" class="w-4 h-4 text-cafe-success shrink-0"></i>
                            <span>Mode otomatis: kas akhir di atas otomatis menjadi kas awal shift berikutnya (selisih: Rp 0).</span>
                        </div>
                    @endif
                </div>

                <form action="{{ route('kasir.shift.close.store') }}" method="POST" class="space-y-4">
                    @csrf

                    @if($requirePhysical)
                        <!-- Form hitung kas fisik (bila opsi di Pengaturan Bisnis diaktifkan oleh Owner) -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-xs font-semibold text-cafe-text">Jumlah Kas Fisik di Laci (Rp)</label>
                                <span class="text-[10px] text-cafe-warning font-semibold">Mode hitung fisik aktif</span>
                            </div>
                            <input type="number" name="closing_cash" id="closing_cash" value="{{ old('closing_cash', (int)$summary['expected_cash']) }}" step="1000" min="0" required
                                class="w-full bg-[#E3DEA4]/50 focus:bg-white text-lg font-serif font-bold text-cafe-dark px-4 py-3.5 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none focus:ring-2 focus:ring-cafe-gold/20 transition-all">
                            @error('closing_cash')<p class="text-[11px] text-cafe-danger mt-1.5">{{ $message }}</p>@enderror
                        </div>
                    @endif

                    <div>
                        <label class="block text-xs font-semibold text-cafe-text mb-2">Catatan Penutupan (opsional)</label>
                        <input type="text" name="notes" maxlength="500" placeholder="Misal: laci rapi, stok cup aman" class="w-full bg-[#E3DEA4]/50 focus:bg-white text-xs text-cafe-dark placeholder:text-cafe-muted/60 px-4 py-3 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none focus:ring-2 focus:ring-cafe-gold/20">
                    </div>

                    @php $hasBlockedOrders = isset($pendingOrders) && $pendingOrders->isNotEmpty(); @endphp
                    <button type="submit" {{ $hasBlockedOrders ? 'disabled' : '' }}
                        class="w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-full {{ $hasBlockedOrders ? 'bg-cafe-muted/50 cursor-not-allowed text-white/70' : 'bg-[#290024] hover:bg-cafe-brown text-[#F2F5E2]' }} text-xs sm:text-sm font-bold shadow-md transition-colors">
                        <i data-lucide="power" class="w-4 h-4"></i>
                        {{ $requirePhysical ? 'Tutup Shift & Rekap Kas' : 'Akhiri Shift' }}
                    </button>
                    @if($hasBlockedOrders)
                        <p class="text-[11px] text-center text-cafe-danger">Selesaikan seluruh pesanan aktif sebelum menekan tombol di atas.</p>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>
@endsection