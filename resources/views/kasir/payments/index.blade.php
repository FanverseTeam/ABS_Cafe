@extends('kasir.layouts.app')

@section('title', 'Pembayaran & Cetak Struk - Kasir')
@section('page_title', 'Pembayaran & Cetak Struk')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="font-serif font-bold text-cafe-dark">Tagihan Menunggu Pembayaran</h3>
            <p class="text-xs text-cafe-muted mt-0.5">Proses pembayaran lalu cetak struk untuk pelanggan.</p>
        </div>
        <div class="bg-cafe-card rounded-2xl border border-cafe-beige/60 px-4 py-2.5 text-right shadow-soft">
            <p class="text-[10px] text-cafe-muted">Pembayaran Hari Ini</p>
            <p class="font-serif font-bold text-cafe-success">Rp {{ number_format($paidTotal, 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Belum dibayar -->
    @if($unpaid->count() === 0)
        <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft p-10 text-center">
            <div class="w-16 h-16 mx-auto rounded-full bg-cafe-cream flex items-center justify-center text-cafe-muted">
                <i data-lucide="receipt" class="w-7 h-7"></i>
            </div>
            <p class="text-xs text-cafe-muted mt-4">Tidak ada tagihan menunggu pembayaran. Semua pesanan sudah lunas!</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($unpaid as $order)
                <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft overflow-hidden flex flex-col">
                    <div class="px-5 py-4 border-b border-cafe-beige/50 flex items-center justify-between bg-cafe-cream/50">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-serif font-bold text-sm text-cafe-dark">{{ $order->order_code }}</span>
                                <span class="text-[9px] px-2 py-0.5 rounded-full bg-cafe-warning/15 text-cafe-warning font-bold">BELUM BAYAR</span>
                            </div>
                            <p class="text-[10px] text-cafe-muted truncate">{{ $order->customer_name }} &bull; {{ $order->orderTypeLabel() }} {{ $order->table_number ? '&bull; Meja '.$order->table_number : '' }}</p>
                        </div>
                        <div class="w-9 h-9 rounded-2xl bg-cafe-warning/15 text-cafe-warning flex items-center justify-center">
                            <i data-lucide="hourglass" class="w-4 h-4"></i>
                        </div>
                    </div>

                    <div class="px-5 py-3 flex-1">
                        @foreach($order->items->take(4) as $item)
                            <p class="text-[11px] text-cafe-muted truncate"><b class="text-cafe-dark">{{ $item->quantity }}&times;</b> {{ $item->name }}</p>
                        @endforeach
                        @if($order->items->count() > 4)
                            <p class="text-[10px] text-cafe-muted">+{{ $order->items->count() - 4 }} item lainnya</p>
                        @endif
                    </div>

                    <div class="px-5 pb-4">
                        <div class="flex items-center justify-between py-2 border-t border-cafe-beige/40">
                            <span class="text-[10px] text-cafe-muted">{{ $order->created_at->format('H:i') }} WIB</span>
                            <span class="font-serif font-bold text-cafe-dark text-lg">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                        </div>
                        <a href="{{ route('kasir.payments.checkout', $order) }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-full bg-cafe-gold text-cafe-dark text-xs font-bold hover:bg-[#B87C3C] transition-colors shadow-md">
                            <i data-lucide="credit-card" class="w-4 h-4"></i>
                            Proses Pembayaran
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Pembayaran hari ini -->
    <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft overflow-hidden">
        <div class="px-6 py-4 border-b border-cafe-beige/50 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-2xl bg-cafe-success/15 text-cafe-success flex items-center justify-center"><i data-lucide="check-circle" class="w-4 h-4"></i></div>
                <div>
                    <h3 class="font-serif font-bold text-sm text-cafe-dark">Pembayaran Hari Ini</h3>
                    <p class="text-[10px] text-cafe-muted">cetak ulang struk bila diperlukan</p>
                </div>
            </div>
        </div>

        <div class="p-5">
            @if($paidToday->count() === 0)
                <p class="text-xs text-cafe-muted py-6 text-center">Belum ada pembayaran tercatat hari ini.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-cafe-beige/50 text-[10px] uppercase tracking-wide text-cafe-muted">
                                <th class="py-2.5 pr-4 font-semibold">Kode</th>
                                <th class="py-2.5 pr-4 font-semibold">Pelanggan</th>
                                <th class="py-2.5 pr-4 font-semibold">Metode</th>
                                <th class="py-2.5 pr-4 font-semibold">Waktu</th>
                                <th class="py-2.5 pr-4 font-semibold text-right">Total</th>
                                <th class="py-2.5 font-semibold text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-cafe-beige/30">
                            @foreach($paidToday as $order)
                                <tr class="hover:bg-cafe-cream/60 transition-colors">
                                    <td class="py-3 pr-4 font-semibold text-cafe-dark">{{ $order->order_code }}</td>
                                    <td class="py-3 pr-4 text-cafe-muted">{{ $order->customer_name }}</td>
                                    <td class="py-3 pr-4">{{ $order->paymentMethodLabel() }}</td>
                                    <td class="py-3 pr-4 text-cafe-muted">{{ $order->paid_at?->format('H:i') }}</td>
                                    <td class="py-3 pr-4 text-right font-serif font-bold text-cafe-dark">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                                    <td class="py-3 text-right">
                                        <a href="{{ route('kasir.payments.receipt', $order) }}" class="inline-flex items-center gap-1 text-xs font-semibold text-cafe-brown hover:text-cafe-dark">
                                            <i data-lucide="printer" class="w-3.5 h-3.5"></i> Cetak Ulang
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $paidToday->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection