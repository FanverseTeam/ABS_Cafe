@extends('kasir.layouts.app')

@section('title', 'Tutup Shift & Rekap Kas - Kasir')
@section('page_title', 'Tutup Shift & Rekap Kas')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between bg-cafe-card rounded-3xl border border-cafe-success/40 p-5 shadow-soft">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-cafe-success/15 text-cafe-success flex items-center justify-center"><i data-lucide="clock" class="w-5 h-5"></i></div>
            <div>
                <p class="text-[11px] text-cafe-muted">Shift dibuka</p>
                <p class="text-sm font-serif font-bold text-cafe-dark">{{ $shift->opened_at->translatedFormat('l, d M Y • H:i') }}</p>
            </div>
        </div>
        <div class="text-right">
            <p class="text-[11px] text-cafe-muted">Penjualan shift berjalan</p>
            <p class="text-xl font-serif font-bold text-cafe-success">Rp {{ number_format($summary['sales'], 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Rekap per metode -->
    <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft overflow-hidden">
        <div class="px-6 py-4 border-b border-cafe-beige/50">
            <h3 class="font-serif font-bold text-sm text-cafe-dark">Rekap Pembayaran per Metode</h3>
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
                <p class="text-xs text-cafe-muted py-4 text-center">Belum ada transaksi lunas pada shift ini.</p>
            @endif

            <div class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div class="p-3 rounded-2xl bg-cafe-warning/10 border border-cafe-warning/30">
                    <p class="text-[10px] text-cafe-muted">Kas Awal</p>
                    <p class="text-sm font-serif font-bold text-cafe-dark">Rp {{ number_format($shift->opening_cash, 0, ',', '.') }}</p>
                </div>
                <div class="p-3 rounded-2xl bg-cafe-info/10 border border-cafe-info/30">
                    <p class="text-[10px] text-cafe-muted">Kas Masuk</p>
                    <p class="text-sm font-serif font-bold text-cafe-dark">Rp {{ number_format($summary['cash_in'], 0, ',', '.') }}</p>
                </div>
                <div class="p-3 rounded-2xl bg-cafe-danger/10 border border-cafe-danger/30">
                    <p class="text-[10px] text-cafe-muted">Kas Keluar</p>
                    <p class="text-sm font-serif font-bold text-cafe-dark">Rp {{ number_format($summary['cash_out'], 0, ',', '.') }}</p>
                </div>
                <div class="p-3 rounded-2xl bg-cafe-success/10 border border-cafe-success/30">
                    <p class="text-[10px] text-cafe-muted">Kas Diharapkan</p>
                    <p class="text-sm font-serif font-bold text-cafe-success">Rp {{ number_format($summary['expected_cash'], 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Catat Kas Masuk/Keluar -->
        <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft overflow-hidden">
            <div class="px-6 py-4 border-b border-cafe-beige/50">
                <h3 class="font-serif font-bold text-sm text-cafe-dark">Catat Kas Masuk / Keluar</h3>
            </div>
            <div class="p-5 space-y-5">
                <form action="{{ route('kasir.shift.movement') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @csrf
                    <div class="sm:col-span-2">
                        <select name="type" class="w-full bg-[#E3DEA4]/50 text-xs text-cafe-text px-4 py-3 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none focus:ring-2 focus:ring-cafe-gold/20">
                            <option value="masuk">Kas Masuk (tambahan modal / pengembalian)</option>
                            <option value="keluar">Kas Keluar (belanja operasional / selisih)</option>
                        </select>
                    </div>
                    <div>
                        <input type="number" name="amount" min="1" required placeholder="Jumlah (Rp)" class="w-full bg-[#E3DEA4]/50 focus:bg-white text-xs text-cafe-dark placeholder:text-cafe-muted/60 px-4 py-3 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none focus:ring-2 focus:ring-cafe-gold/20">
                    </div>
                    <div>
                        <input type="text" name="reason" required maxlength="200" placeholder="Alasan (mis. beli gula)" class="w-full bg-[#E3DEA4]/50 focus:bg-white text-xs text-cafe-dark placeholder:text-cafe-muted/60 px-4 py-3 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none focus:ring-2 focus:ring-cafe-gold/20">
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
                                <i data-lucide="{{ $rec->type === 'masuk' ? 'download' : 'upload' }}" class="w-4 h-4 mt-0.5 {{ $rec->type === 'masuk' ? 'text-cafe-info' : 'text-cafe-warning' }}"></i>
                                <div class="min-w-0">
                                    <p class="text-xs font-semibold text-cafe-dark">{{ $rec->reason }}</p>
                                    <p class="text-[10px] text-cafe-muted">{{ $rec->user->name }} &bull; {{ $rec->created_at->format('d/m/y H:i') }}</p>
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
                <h3 class="font-serif font-bold text-sm text-cafe-dark">Tutup Shift</h3>
            </div>
            <div class="p-5 space-y-5">
                @if($summary['unpaid_count'] > 0 || $summary['active_count'] > 0)
                    <div class="p-4 rounded-2xl bg-cafe-warning/10 border border-cafe-warning/30 text-[11px] text-cafe-warning leading-relaxed flex items-start gap-2.5">
                        <i data-lucide="alert-triangle" class="w-4 h-4 shrink-0 mt-0.5"></i>
                        <span>
                            Masih ada <b>{{ $summary['unpaid_count'] }}</b> pesanan belum dibayar dan
                            <b>{{ $summary['active_count'] }}</b> pesanan aktif. Selesaikan/lunasi dahulu sebelum menutup shift.
                        </span>
                    </div>
                @endif

                <div class="p-4 rounded-2xl bg-cafe-cream border border-cafe-beige/60">
                    <p class="text-xs font-semibold text-cafe-muted">Kas yang diharapkan di laci</p>
                    <p class="font-serif text-2xl font-bold text-cafe-success mt-1">Rp {{ number_format($summary['expected_cash'], 0, ',', '.') }}</p>
                    <p class="text-[10px] text-cafe-muted mt-0.5">(= kas awal + penjualan tunai + kas masuk − kas keluar)</p>
                </div>

                <form action="{{ route('kasir.shift.close.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-cafe-text mb-2">Jumlah Kas Fisik di Laci (Rp)</label>
                        <input type="number" name="closing_cash" id="closing_cash" value="{{ old('closing_cash', (int)$summary['expected_cash']) }}" step="1000" min="0" required
                            class="w-full bg-[#E3DEA4]/50 focus:bg-white text-lg font-serif font-bold text-cafe-dark px-4 py-3.5 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none focus:ring-2 focus:ring-cafe-gold/20 transition-all">
                        @error('closing_cash')<p class="text-[11px] text-cafe-danger mt-1.5">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-cafe-text mb-2">Catatan (opsional)</label>
                        <input type="text" name="notes" maxlength="500" placeholder="Misal: kelebihan Rp 5.000 di laci, lembur barista" class="w-full bg-[#E3DEA4]/50 focus:bg-white text-xs text-cafe-dark placeholder:text-cafe-muted/60 px-4 py-3 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none focus:ring-2 focus:ring-cafe-gold/20">
                    </div>

                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-full bg-cafe-danger text-white text-xs font-bold shadow-md hover:bg-cafe-danger/90 transition-colors">
                        <i data-lucide="square" class="w-4 h-4"></i>
                        Tutup Shift &amp; Cetak Rekap Kas
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection