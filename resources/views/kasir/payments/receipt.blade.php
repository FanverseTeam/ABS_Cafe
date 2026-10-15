@extends('kasir.layouts.app')

@section('title', 'Struk '.$order->order_code.' - Kasir')
@section('page_title', 'Struk Pembayaran')

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <!-- Success Hero Card (Persis Layar 8 Referensi) -->
    <div class="bg-[#FAFCF3] rounded-3xl border border-[#E3DEA4]/80 shadow-soft-lg p-6 sm:p-8 text-center flex flex-col items-center">
        <!-- Lingkaran Gelap Centang -->
        <div class="relative mb-4">
            <div class="w-20 h-20 rounded-full bg-[#290024] text-[#D4954D] flex items-center justify-center shadow-soft-lg animate-in zoom-in-75 duration-300">
                <i data-lucide="check" class="w-10 h-10 stroke-[3]"></i>
            </div>
            <span class="absolute -top-1 -right-1 w-3.5 h-3.5 rounded-full bg-[#D4954D] animate-ping opacity-75"></span>
            <span class="absolute -bottom-1 -left-1 w-3 h-3 rounded-full bg-[#E3DEA4]"></span>
        </div>

        <h2 class="font-serif font-bold text-2xl text-[#290024]">Pembayaran Berhasil!</h2>
        <p class="text-xs text-[#775533] mt-1.5 max-w-xs leading-relaxed">
            Terima kasih telah memesan di ABS Cafe. Pesanan sedang diproses operasional.
        </p>

        <!-- Kartu Rincian Kecil (Layar 8 Referensi) -->
        <div class="w-full mt-5 p-4 rounded-2xl bg-[#F2F5E2]/80 border border-[#E3DEA4]/70 text-xs space-y-2 text-left">
            <div class="flex items-center justify-between text-[#775533]">
                <span>No. Pesanan</span>
                <span class="font-bold text-[#290024]">{{ $order->order_code }}</span>
            </div>
            <div class="flex items-center justify-between text-[#775533]">
                <span>Metode Pembayaran</span>
                <span class="font-semibold text-[#290024] uppercase">{{ $order->paymentMethodLabel() }}</span>
            </div>
            <div class="pt-2 border-t border-[#E3DEA4]/60 flex items-center justify-between">
                <span class="font-semibold text-[#290024]">Total Pembayaran</span>
                <span class="font-serif font-bold text-base text-[#290024]">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Tombol Aksi Layar 8 -->
        <div class="w-full mt-6 grid grid-cols-1 sm:grid-cols-2 gap-2.5">
            <button onclick="window.print()" class="w-full py-3.5 px-4 rounded-full bg-[#290024] hover:bg-[#775533] text-[#FAFCF3] font-semibold text-xs flex items-center justify-center gap-2 shadow-sm transition-all active:scale-[0.98]">
                <i data-lucide="printer" class="w-4 h-4 text-[#D4954D]"></i>
                <span>Cetak Struk Fisik</span>
            </button>
            <a href="{{ route('kasir.pos.index') }}" class="w-full py-3.5 px-4 rounded-full bg-[#D4954D] hover:brightness-105 text-[#290024] font-bold text-xs flex items-center justify-center gap-2 shadow-sm transition-all active:scale-[0.98]">
                <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                <span>Pesanan Baru</span>
            </a>
        </div>
        <div class="mt-2.5 w-full">
            <a href="{{ route('kasir.dashboard') }}" class="w-full py-2.5 px-4 rounded-full bg-transparent border border-[#775533]/30 text-[#775533] hover:bg-[#E3DEA4]/40 font-medium text-xs flex items-center justify-center transition-all">
                Kembali ke Beranda Shift
            </a>
        </div>
    </div>

    <!-- Kertas Struk Fisik (80mm) -->
    <div class="bg-white rounded-3xl border border-[#E3DEA4]/80 shadow-soft overflow-hidden p-4 sm:p-6">
        <div class="flex items-center justify-between pb-4 mb-4 border-b border-gray-100">
            <span class="text-xs font-bold text-gray-700">Preview Struk Kasir</span>
            <span class="text-[10px] text-gray-500 font-mono">{{ $order->paid_at?->format('d/m/Y H:i') }}</span>
        </div>

        <div id="printable-receipt" class="bg-white text-black mx-auto rounded-none" style="width:80mm">
            <div class="text-center px-4 pt-4 pb-4 border-b border-black/20">
                @if(\App\Models\CafeSetting::get('receipt_show_logo', '1') == '1')
                    <img src="{{ asset('images/Logo.png') }}" alt="ABS Cafe Logo" class="w-12 h-12 mx-auto mb-2 rounded-full object-cover">
                @endif
                <h1 class="font-serif font-bold text-sm">{{ $setting['name'] }}</h1>
                <p class="text-[10px] mt-1 leading-tight">{{ $setting['address'] }}</p>
                <p class="text-[10px]">{{ $setting['phone'] }}</p>
            </div>

            <div class="px-4 py-3 text-[11px] space-y-1">
                <div class="flex justify-between"><span>No. Pesanan</span><span class="font-bold">{{ $order->order_code }}</span></div>
                <div class="flex justify-between"><span>Tanggal</span><span>{{ $order->paid_at?->format('d/m/Y H:i') }}</span></div>
                <div class="flex justify-between"><span>Tipe</span><span>{{ $order->orderTypeLabel() }}</span></div>
                @if($order->table_number)
                    <div class="flex justify-between"><span>Meja</span><span>{{ $order->table_number }}</span></div>
                @endif
                <div class="flex justify-between"><span>Kasir</span><span>{{ $order->cashier_name }}</span></div>
                <div class="flex justify-between"><span>Pelanggan</span><span>{{ $order->customer_name ?: 'Tamu' }}</span></div>
            </div>

            <div class="px-4 py-3 border-t border-dashed border-black/30">
                <div class="space-y-1.5">
                    @foreach($order->items as $item)
                        <div class="flex justify-between text-[11px]">
                            <span>{{ $item->name }}</span>
                            <span>{{ $item->quantity }}x Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                        </div>
                        @if($item->notes)
                            <p class="text-[9px] text-right text-gray-600">-{{ $item->notes }}</p>
                        @endif
                    @endforeach
                </div>
            </div>

            <div class="px-4 py-3 border-t border-dashed border-black/30 text-[11px] space-y-1">
                <div class="flex justify-between"><span>Subtotal</span><span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span></div>
                <div class="flex justify-between"><span>Pajak</span><span>Rp {{ number_format($order->tax, 0, ',', '.') }}</span></div>
                <div class="flex justify-between"><span>Service</span><span>Rp {{ number_format($order->service_charge, 0, ',', '.') }}</span></div>
                @if((float)$order->discount > 0)
                    <div class="flex justify-between"><span>Diskon{{ $order->promo ? ' ('.$order->promo->code.')' : '' }}</span><span>-Rp {{ number_format($order->discount, 0, ',', '.') }}</span></div>
                @endif
                <div class="flex justify-between font-bold border-t border-black/20 pt-1 text-sm">
                    <span>TOTAL</span><span>Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between border-t border-black/20 pt-1"><span>Bayar ({{ $order->paymentMethodLabel() }})</span><span>Rp {{ number_format($order->paymentCashReceived(), 0, ',', '.') }}</span></div>
                <div class="flex justify-between"><span>Kembalian</span><span>Rp {{ number_format($order->paymentChange(), 0, ',', '.') }}</span></div>
            </div>

            <div class="px-4 py-5 text-center border-t border-black/20 space-y-1">
                <p class="text-[10px] leading-relaxed">{{ $setting['footer'] }}</p>
                <p class="text-[9px] text-gray-500">Dicetak {{ now()->format('d/m/Y H:i') }} &bull; ABS Cafe Boyolangu</p>
            </div>
        </div>
    </div>
</div>
@endsection