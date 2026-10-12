@extends('kasir.layouts.app')

@section('title', 'Struk '.$order->order_code.' - Kasir')
@section('page_title', 'Struk Pembayaran')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft p-5 mb-6">
        <div>
            <h3 class="font-serif font-bold text-cafe-dark flex items-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4 text-cafe-success"></i>
                Pembayaran Berhasil
            </h3>
            <p class="text-[11px] text-cafe-muted mt-0.5">{{ $order->order_code }} &bull; {{ $order->payMethodReceipt() }}</p>
        </div>
        <button onclick="window.print()" class="inline-flex items-center gap-2 px-5 py-3 rounded-full bg-cafe-dark text-white text-xs font-semibold hover:bg-cafe-brown transition-colors">
            <i data-lucide="printer" class="w-4 h-4"></i>
            Cetak Struk
        </button>
    </div>

    <div id="printable-receipt" class="bg-white text-black mx-auto rounded-none" style="width:80mm">
        <div class="text-center px-4 pt-6 pb-4 border-b border-black/20">
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
            <div class="flex justify-between"><span>Pelanggan</span><span>{{ $order->customer_name }}</span></div>
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

        <div class="px-4 py-6 text-center border-t border-black/20 space-y-1">
            <p class="text-[10px] leading-relaxed">{{ $setting['footer'] }}</p>
            <p class="text-[9px] text-gray-500">Dicetak {{ now()->format('d/m/Y H:i') }} &bull; ABS Cafe Boyolangu</p>
        </div>
    </div>

    <div class="mt-6 flex flex-col sm:flex-row gap-3">
        <a href="{{ route('kasir.pos.index') }}" class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-full bg-cafe-gold text-cafe-dark text-xs font-bold hover:bg-[#B87C3C] transition-colors shadow-md">
            <i data-lucide="shopping-bag" class="w-4 h-4"></i>
            Pesanan Baru
        </a>
        <a href="{{ route('kasir.payments.index') }}" class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-full bg-cafe-dark text-white text-xs font-bold hover:bg-cafe-brown transition-colors">
            <i data-lucide="receipt" class="w-4 h-4"></i>
            Riwayat Hari Ini
        </a>
    </div>
</div>
@endsection