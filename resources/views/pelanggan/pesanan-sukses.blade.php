@extends('pelanggan.layouts.app')

@section('title', 'Nota Pesanan ' . $order->order_code . ' - ' . ($landing['cafe']['name'] ?? 'ABS Cafe'))

@section('body_class', 'bg-[#F2F5E2] text-[#290024]')
@section('body_style', 'background-color: #F2F5E2 !important; color: #290024 !important;')
@section('main_class', 'bg-[#F2F5E2]')

@section('header')
<header class="sticky top-0 inset-x-0 z-30 bg-[#F2F5E2] border-b border-[#E3DEA4]/80" style="background-color: #F2F5E2 !important;">
    <div class="mx-auto max-w-2xl px-4 sm:px-6 h-16 flex items-center justify-between gap-4">
        <a href="{{ route('pelanggan.beranda') }}" class="flex items-center gap-2">
            <x-logo :show-text="false" size="sm" />
            <span class="font-serif font-bold text-base text-[#290024]">{{ $landing['cafe']['name'] ?? 'ABS Cafe' }}</span>
        </a>

        <a href="{{ route('pelanggan.menu') }}" class="text-xs font-semibold text-[#775533] hover:text-[#290024] px-3 py-1.5 rounded-full hover:bg-[#E3DEA4]/40 transition-colors">
            Pesan Menu Lain
        </a>
    </div>
</header>
@endsection

@section('content')
<section class="min-h-screen bg-[#F2F5E2] pt-6 pb-24 px-4 sm:px-6"
         style="background-color: #F2F5E2 !important;"
         x-data="customerOrderWatcher('{{ $order->order_code }}', '{{ $order->payment_status }}', '{{ $order->order_status }}')"
         x-init="startPolling()">
    <div class="max-w-md mx-auto space-y-5">

        {{-- ========================================================================= --}}
        {{-- BANNER STATUS 1: JIKA BELUM LUNAS (SILAKAN BAYAR DI KASIR)                 --}}
        {{-- ========================================================================= --}}
        <div x-show="paymentStatus !== 'lunas'"
             class="text-center p-6 rounded-3xl bg-[#F2F5E2] border-2 border-[#D4954D]/60 shadow-soft space-y-3">
            <div class="w-16 h-16 rounded-full bg-[#D4954D]/20 text-[#D4954D] flex items-center justify-center mx-auto shadow-inner animate-pulse">
                <svg class="w-8 h-8 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="5" width="20" height="14" rx="2"></rect>
                    <line x1="2" y1="10" x2="22" y2="10"></line>
                </svg>
            </div>

            <div class="space-y-1">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black tracking-wider uppercase bg-[#D4954D]/15 text-[#290024] border border-[#D4954D]/40">
                    <span class="w-2 h-2 rounded-full bg-[#D4954D] animate-ping"></span>
                    Menunggu Pembayaran
                </span>
                <h1 class="font-serif text-2xl sm:text-3xl font-black text-[#290024] pt-1">
                    Silakan Bayar di Kasir
                </h1>
                <p class="text-xs sm:text-sm text-[#775533] leading-relaxed max-w-sm mx-auto">
                    Pesanan Anda telah masuk di antrean kasir. Tunjukkan kode pesanan di bawah ini ke kasir untuk menyelesaikan pembayaran.
                </p>
            </div>

            <!-- Kode Pesanan Box -->
            <div class="p-3.5 rounded-2xl bg-[#E3DEA4]/30 border border-[#E3DEA4] space-y-1">
                <div class="text-[10px] font-bold uppercase tracking-wider text-[#775533]">Kode Pesanan Anda</div>
                <div class="font-mono text-3xl font-black text-[#290024] tracking-widest select-all">
                    {{ $order->order_code }}
                </div>
                <p class="text-[10px] text-[#775533] font-medium">
                    (Sebutkan kode ini di meja kasir)
                </p>
            </div>
        </div>

        {{-- ========================================================================= --}}
        {{-- BANNER STATUS 2: JIKA SUDAH LUNAS (LUNAS & PESANAN DIPROSES)             --}}
        {{-- ========================================================================= --}}
        <div x-show="paymentStatus === 'lunas'"
             x-cloak
             class="text-center p-6 rounded-3xl bg-[#290024] border-2 border-[#D4954D] shadow-soft space-y-3">
            <div class="w-16 h-16 rounded-full bg-[#D4954D] text-[#290024] flex items-center justify-center mx-auto shadow-md">
                <svg class="w-9 h-9 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>

            <div class="space-y-1">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black tracking-wider uppercase bg-[#D4954D] text-[#290024] shadow-xs">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    <span>Lunas</span>
                </div>
                <h1 class="font-serif text-2xl sm:text-3xl font-black text-[#F2F5E2] pt-1">
                    Pesanan Sedang Diproses
                </h1>
                <p class="text-xs sm:text-sm text-[#E3DEA4] leading-relaxed max-w-sm mx-auto">
                    Terima kasih! Pembayaran Anda telah dikonfirmasi oleh kasir. Nota fisik telah dicetak dan pesanan Anda sedang disiapkan oleh tim dapur / barista.
                </p>
            </div>

            <div class="p-3 rounded-2xl bg-[#F2F5E2]/10 border border-[#E3DEA4]/30 text-xs text-[#E3DEA4] space-y-1">
                <div class="flex items-center justify-between">
                    <span class="text-[#E3DEA4]/70">Kode Pesanan:</span>
                    <span class="font-mono font-bold text-[#F2F5E2]">{{ $order->order_code }}</span>
                </div>
                <template x-if="paidAt">
                    <div class="flex items-center justify-between">
                        <span class="text-[#E3DEA4]/70">Waktu Bayar:</span>
                        <span class="font-medium" x-text="paidAt"></span>
                    </div>
                </template>
                <template x-if="cashierName">
                    <div class="flex items-center justify-between">
                        <span class="text-[#E3DEA4]/70">Kasir:</span>
                        <span class="font-medium" x-text="cashierName"></span>
                    </div>
                </template>
            </div>
        </div>

        {{-- ========================================================================= --}}
        {{-- KARTU NOTA / STRUK RINCIAN PESANAN CUSTOMER                              --}}
        {{-- ========================================================================= --}}
        <div class="bg-[#F2F5E2] rounded-3xl border border-[#E3DEA4]/80 shadow-soft overflow-hidden">
            <!-- Header Nota -->
            <div class="px-5 py-4 border-b border-dashed border-[#D4954D]/40 bg-[#E3DEA4]/30 text-center space-y-1">
                <h2 class="font-serif font-black text-base text-[#290024] tracking-wide uppercase">
                    Nota Pesanan Pelanggan
                </h2>
                <p class="text-[11px] text-[#775533] font-medium">
                    {{ $landing['cafe']['name'] ?? 'ABS Cafe' }} &bull; {{ $order->created_at->format('d/m/Y H:i') }} WIB
                </p>
            </div>

            <!-- Identitas Pesanan -->
            <div class="px-5 py-3.5 bg-[#E3DEA4]/20 border-b border-[#E3DEA4]/60 text-xs space-y-1.5 text-[#775533]">
                <div class="flex justify-between items-center">
                    <span>Nama Pelanggan:</span>
                    <span class="font-bold text-[#290024]">{{ $order->customer_name }}</span>
                </div>
                @if($order->customer_phone)
                    <div class="flex justify-between items-center">
                        <span>No. HP / WhatsApp:</span>
                        <span class="font-medium text-[#290024]">{{ $order->customer_phone }}</span>
                    </div>
                @endif
                <div class="flex justify-between items-center">
                    <span>Tipe Pemesanan:</span>
                    @if($order->order_type === 'dine_in')
                        <span class="font-bold text-[#290024] px-2 py-0.5 rounded-md bg-[#D4954D]/15 border border-[#D4954D]/30">
                            Santap di Tempat (Dine In){{ $order->table_number ? ' &bull; Meja #' . $order->table_number : '' }}
                        </span>
                    @else
                        <span class="font-bold text-[#290024] px-2 py-0.5 rounded-md bg-[#E3DEA4] border border-[#E3DEA4]">
                            Bawa Pulang (Takeaway)
                        </span>
                    @endif
                </div>
                <div class="flex justify-between items-center pt-1 border-t border-[#E3DEA4]/70">
                    <span>Status Pembayaran:</span>
                    <template x-if="paymentStatus === 'lunas'">
                        <span class="font-bold text-[#F2F5E2] uppercase bg-[#290024] px-2 py-0.5 rounded">Lunas</span>
                    </template>
                    <template x-if="paymentStatus !== 'lunas'">
                        <span class="font-bold text-[#290024] uppercase bg-[#D4954D]/20 border border-[#D4954D]/40 px-2 py-0.5 rounded">Belum Bayar</span>
                    </template>
                </div>
            </div>

            <!-- Daftar Menu yang Dipesan -->
            <div class="px-5 py-4 space-y-3">
                <div class="text-[11px] font-bold uppercase tracking-wider text-[#775533] pb-1 border-b border-[#E3DEA4]">
                    Daftar Item
                </div>

                <div class="divide-y divide-[#E3DEA4]/70">
                    @foreach($order->items as $item)
                        <div class="py-2.5 flex items-start justify-between gap-3 text-xs">
                            <div class="space-y-0.5 min-w-0">
                                <div class="font-bold text-[#290024]">
                                    <span class="text-[#D4954D] font-black mr-1">{{ $item->quantity }}x</span>
                                    {{ $item->name }}
                                </div>
                                @if($item->notes)
                                    <div class="text-[11px] text-[#775533] italic pl-4">
                                        Catatan: {{ $item->notes }}
                                    </div>
                                @endif
                                <div class="text-[10px] text-[#775533]/60 pl-4">
                                    @ Rp {{ number_format($item->price, 0, ',', '.') }}
                                </div>
                            </div>
                            <div class="font-serif font-bold text-xs shrink-0 text-[#290024] pt-0.5">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Total Perhitungan Biaya -->
            <div class="px-5 py-4 bg-[#E3DEA4]/25 border-t border-[#E3DEA4] space-y-1.5 text-xs text-[#775533]">
                <div class="flex items-center justify-between">
                    <span>Subtotal Menu</span>
                    <span class="font-semibold text-[#290024]">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span>Pajak (PB1 10%)</span>
                    <span class="font-semibold text-[#290024]">Rp {{ number_format($order->tax, 0, ',', '.') }}</span>
                </div>
                @if($order->discount > 0)
                    <div class="flex items-center justify-between text-[#D4954D]">
                        <span>Diskon</span>
                        <span class="font-semibold">- Rp {{ number_format($order->discount, 0, ',', '.') }}</span>
                    </div>
                @endif
                <div class="flex items-center justify-between pt-2.5 border-t border-[#E3DEA4] text-sm">
                    <span class="font-bold text-[#290024]">Total Tagihan</span>
                    <span class="font-serif font-black text-lg text-[#290024]">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Footer Nota -->
            <div class="p-4 bg-[#F2F5E2] border-t border-[#E3DEA4]/60 text-center text-[11px] text-[#775533] space-y-1">
                <template x-if="paymentStatus !== 'lunas'">
                    <p class="font-semibold text-[#775533]">
                        Silakan lakukan pembayaran ke kasir. Halaman ini akan otomatis terbarui begitu pembayaran dikonfirmasi kasir.
                    </p>
                </template>
                <template x-if="paymentStatus === 'lunas'">
                    <p class="font-bold text-[#290024]">
                        Pembayaran Lunas &bull; Terima kasih atas kunjungan Anda di {{ $landing['cafe']['name'] ?? 'ABS Cafe' }}!
                    </p>
                </template>
            </div>
        </div>

        <!-- Tombol Aksi Bawah -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
            <a href="{{ route('pelanggan.menu', array_filter(['meja' => $order->table_number])) }}"
               class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-full bg-[#290024] hover:bg-[#D4954D] hover:text-[#290024] text-[#F2F5E2] text-xs sm:text-sm font-semibold transition-all shadow-md">
                <span>Pesan Menu Lainnya</span>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
            </a>

            <a href="{{ route('pelanggan.beranda') }}"
               class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 rounded-full bg-[#F2F5E2] hover:bg-[#E3DEA4]/40 text-[#290024] border border-[#E3DEA4] text-xs sm:text-sm font-semibold transition-colors">
                Kembali ke Beranda
            </a>
        </div>

    </div>
</section>

<script>
function customerOrderWatcher(orderCode, initialPaymentStatus, initialOrderStatus) {
    return {
        orderCode: orderCode,
        paymentStatus: initialPaymentStatus,
        orderStatus: initialOrderStatus,
        paidAt: null,
        cashierName: null,
        pollTimer: null,

        startPolling() {
            if (this.paymentStatus === 'lunas') {
                return;
            }

            this.pollTimer = setInterval(() => {
                this.checkStatus();
            }, 3000);
        },

        async checkStatus() {
            try {
                const response = await fetch(`/pesanan/status/${encodeURIComponent(this.orderCode)}`, {
                    headers: { 'Accept': 'application/json' }
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        this.paymentStatus = data.payment_status;
                        this.orderStatus = data.order_status;
                        this.paidAt = data.paid_at;
                        this.cashierName = data.cashier_name;

                        if (this.paymentStatus === 'lunas') {
                            clearInterval(this.pollTimer);
                            this.pollTimer = null;
                        }
                    }
                }
            } catch (e) {
                // Ignore transient network errors
            }
        }
    };
}
</script>
@endsection
