@extends('pelanggan.layouts.app')

@section('title', 'Pesanan Berhasil - ' . $order->order_code . ' - ' . ($landing['cafe']['name'] ?? 'ABS Cafe'))

@section('body_class', 'bg-white text-[#290024]')
@section('body_style', 'background-color: #ffffff !important; color: #290024 !important;')
@section('main_class', 'bg-white')

@section('header')
<header class="sticky top-0 inset-x-0 z-30 bg-white border-b border-[#F0ECE4]" style="background-color: #ffffff !important;">
    <div class="mx-auto max-w-2xl px-4 sm:px-6 h-16 flex items-center justify-between gap-4">
        <a href="{{ route('pelanggan.beranda') }}" class="flex items-center gap-2">
            <x-logo :show-text="false" size="sm" />
            <span class="font-serif font-bold text-base text-[#290024]">{{ $landing['cafe']['name'] ?? 'ABS Cafe' }}</span>
        </a>

        <a href="{{ route('pelanggan.menu') }}" class="text-xs font-semibold text-[#775533] hover:text-[#290024] px-3 py-1.5 rounded-full hover:bg-stone-100 transition-colors">
            Pesan Lagi
        </a>
    </div>
</header>
@endsection

@section('content')
<section class="min-h-screen bg-white pt-6 pb-24 px-4 sm:px-6" style="background-color: #ffffff !important;">
    <div class="max-w-xl mx-auto space-y-6">

        <!-- Status Card Konfirmasi -->
        <div class="text-center p-6 sm:p-8 rounded-3xl bg-white border border-[#EDE8E0] shadow-xs space-y-4">
            <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto shadow-sm animate-bounce">
                <svg class="w-8 h-8 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>

            <div class="space-y-1">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold tracking-widest uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">
                    Pesanan Berhasil Dikirim
                </span>
                <h1 class="font-serif text-2xl sm:text-3xl font-bold text-[#290024]">
                    Terima Kasih, {{ $order->customer_name }}!
                </h1>
                <p class="text-xs sm:text-sm text-[#775533]">
                    Pesanan Anda telah masuk ke sistem antrean kasir &amp; barista kami.
                </p>
            </div>

            <!-- Kode Pesanan & Badge Meja -->
            <div class="p-4 rounded-2xl bg-[#FAF7F2] border border-[#EDE8E0] space-y-2">
                <div class="text-[11px] font-bold uppercase tracking-wider text-[#775533]">Kode Pesanan</div>
                <div class="font-mono text-2xl sm:text-3xl font-black text-[#290024] tracking-wider select-all">
                    {{ $order->order_code }}
                </div>

                <div class="pt-2 flex items-center justify-center gap-2">
                    @if($order->order_type === 'dine_in' && $order->table_number)
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-[#290024] text-[#E3DEA4]">
                            Meja #{{ $order->table_number }}
                        </span>
                    @else
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-[#775533] text-white">
                            Bawa Pulang (Takeaway)
                        </span>
                    @endif

                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                        {{ ucfirst($order->order_status) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Rincian Item Pesanan -->
        <div class="p-6 rounded-3xl bg-white border border-[#EDE8E0] shadow-xs space-y-4">
            <h2 class="font-serif text-base sm:text-lg font-bold text-[#290024] pb-2 border-b border-[#EDE8E0]">
                Rincian Pesanan
            </h2>

            <div class="divide-y divide-[#EDE8E0]/70 text-xs text-[#290024]">
                @foreach($order->items as $item)
                    <div class="py-2.5 flex items-start justify-between gap-3">
                        <div class="space-y-0.5">
                            <div class="font-semibold text-xs sm:text-sm">
                                {{ $item->quantity }}x {{ $item->name }}
                            </div>
                            @if($item->notes)
                                <div class="text-[11px] text-[#775533] italic">Catatan: {{ $item->notes }}</div>
                            @endif
                        </div>
                        <div class="font-serif font-bold text-xs sm:text-sm shrink-0">
                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Ringkasan Total -->
            <div class="pt-3 border-t border-[#EDE8E0] space-y-1.5 text-xs text-[#775533]">
                <div class="flex items-center justify-between">
                    <span>Subtotal</span>
                    <span class="font-semibold text-[#290024]">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span>Pajak (PB1)</span>
                    <span class="font-semibold text-[#290024]">Rp {{ number_format($order->tax, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between pt-2 border-t border-[#EDE8E0] text-sm">
                    <span class="font-bold text-[#290024]">Total Pembayaran</span>
                    <span class="font-serif font-black text-base text-[#290024]">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="p-3 rounded-xl bg-amber-50 border border-amber-200/70 text-[11px] text-amber-900 leading-relaxed">
                <span class="font-bold">Info Pembayaran:</span> Pembayaran dapat diselesaikan langsung di kasir melalui Tunai, QRIS, atau Kartu Debit dengan menyebutkan kode pesanan Anda.
            </div>
        </div>

        <!-- Tombol Aksi Bawah -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
            <a href="{{ route('pelanggan.menu', array_filter(['meja' => $order->table_number])) }}"
               class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-full bg-[#290024] hover:bg-[#D4954D] hover:text-[#290024] text-white text-xs sm:text-sm font-semibold transition-all shadow-md">
                <span>Pesan Menu Lainnya</span>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
            </a>

            <a href="{{ route('pelanggan.beranda') }}"
               class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 rounded-full bg-stone-100 hover:bg-stone-200 text-[#290024] text-xs sm:text-sm font-semibold transition-colors">
                Kembali ke Beranda
            </a>
        </div>

    </div>
</section>
@endsection
