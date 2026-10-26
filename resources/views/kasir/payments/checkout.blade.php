@extends('kasir.layouts.app')

@section('title', 'Pembayaran '.$order->order_code.' - Kasir')
@section('page_title', 'Proses Pembayaran')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-cafe-beige/50 bg-cafe-cream/50 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-2xl bg-cafe-warning/15 text-cafe-warning flex items-center justify-center"><i data-lucide="credit-card" class="w-5 h-5"></i></div>
                <div>
                    <h3 class="font-serif font-bold text-sm text-cafe-dark">{{ $order->order_code }}</h3>
                    <p class="text-[10px] text-cafe-muted">{{ $order->customer_name }} &bull; {{ $order->orderTypeLabel() }} {{ $order->table_number ? '&bull; Meja '.$order->table_number : '' }}</p>
                </div>
            </div>
            <a href="{{ route('kasir.orders.index') }}" class="text-[11px] text-cafe-muted hover:text-cafe-dark flex items-center gap-1">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Kembali
            </a>
        </div>

        <div class="p-4 sm:p-6 lg:p-8 grid grid-cols-1 landscape:grid-cols-2 lg:grid-cols-2 gap-4 sm:gap-6 lg:gap-8">
            <!-- Rincian Pesanan -->
            <div>
                <h4 class="text-[10px] font-semibold text-cafe-muted uppercase tracking-wide mb-3">Rincian Pesanan</h4>
                <div class="space-y-2">
                    @foreach($order->items as $item)
                        <div class="flex items-start justify-between gap-3 p-3 rounded-2xl bg-cafe-cream/70 border border-cafe-beige/40">
                            <div class="min-w-0">
                                <p class="text-xs font-semibold text-cafe-dark">{{ $item->quantity }}&times; {{ $item->name }}</p>
                                @if($item->notes)<p class="text-[10px] text-cafe-info">{{ $item->notes }}</p>@endif
                            </div>
                            <p class="text-xs font-serif font-bold text-cafe-dark shrink-0">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 rounded-2xl bg-cafe-card border border-cafe-beige/50 p-4 space-y-1.5 text-xs">
                    <div class="flex justify-between text-cafe-muted"><span>Subtotal</span><span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between text-cafe-muted"><span>Pajak</span><span>Rp {{ number_format($order->tax, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between text-cafe-muted"><span>Service Charge</span><span>Rp {{ number_format($order->service_charge, 0, ',', '.') }}</span></div>
                    @if((float)$order->discount > 0)
                        <div class="flex justify-between text-cafe-danger">
                            <span>Diskon{{ $order->promo ? ' ('.$order->promo->title.')' : '' }}</span>
                            <span>-Rp {{ number_format($order->discount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between border-t border-cafe-beige/50 pt-2">
                        <span class="font-bold text-cafe-dark">Total Tagihan</span>
                        <span class="font-serif font-bold text-cafe-gold text-lg">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Form Pembayaran -->
            <div>
                <h4 class="text-[10px] font-semibold text-cafe-muted uppercase tracking-wide mb-3">Metode Pembayaran</h4>
                <form action="{{ route('kasir.payments.pay', $order) }}" method="POST" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-2 gap-2">
                        @foreach($paymentMethods as $method)
                            <button type="button" data-method="{{ $method->code }}" onclick="selectMethod('{{ $method->code }}', this)"
                                class="method-btn p-3.5 rounded-2xl border text-[11px] font-semibold transition-all flex flex-col items-center gap-1.5 {{ $method->code === 'tunai' ? 'bg-cafe-dark text-beige border-cafe-dark shadow-md' : 'bg-cafe-beige/30 text-cafe-brown border-cafe-beige hover:bg-cafe-beige' }}">
                                <i data-lucide="{{ $method->icon() }}" class="w-5 h-5"></i>
                                <span>{{ $method->name }}</span>
                            </button>
                        @endforeach
                    </div>
                    <input type="hidden" name="method" id="pay-method" value="tunai">

                    <div id="cash-block" class="space-y-2.5">
                        <label class="block text-[10px] font-semibold text-cafe-muted uppercase tracking-wide">Uang Diterima (Rp)</label>
                        <input type="number" name="cash_received" id="pay-cash" value="{{ ceil($order->total / 1000) * 1000 }}" step="1000" min="0" class="w-full bg-[#E3DEA4]/50 focus:bg-beige text-lg font-serif font-bold text-cafe-text px-4 py-3 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none focus:ring-2 focus:ring-cafe-gold/20">
                        <div class="flex flex-wrap gap-1.5">
                            @foreach([20000, 50000, 100000, 150000, 200000] as $amount)
                                <button type="button" onclick="document.getElementById('pay-cash').value = {{ $amount }}; calcChange()" class="px-3 py-1.5 rounded-full bg-cafe-beige/60 text-cafe-brown text-[10px] font-bold hover:bg-cafe-gold/30">{{ number_format($amount, 0, ',', '.') }}</button>
                            @endforeach
                        </div>
                        <div class="flex items-center justify-between bg-cafe-success/10 border border-cafe-success/30 rounded-2xl px-4 py-3">
                            <span class="text-xs font-semibold text-cafe-muted">Kembalian</span>
                            <span id="pay-change" class="font-serif font-bold text-cafe-success text-lg">Rp 0</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-semibold text-cafe-muted uppercase tracking-wide mb-1.5">Referensi (opsional)</label>
                        <input type="text" name="reference" placeholder="No. invoice / referensi transfer" class="w-full bg-[#E3DEA4]/50 focus:bg-beige text-xs text-cafe-text placeholder:text-cafe-muted/60 px-4 py-3 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none focus:ring-2 focus:ring-cafe-gold/20">
                    </div>

                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-4 rounded-full bg-cafe-success text-beige text-xs font-bold shadow-md hover:bg-cafe-success/90 transition-colors">
                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                        Terima Pembayaran &amp; Cetak Struk
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
const total = {{ $order->total }};

function selectMethod(code, btn) {
    document.getElementById('pay-method').value = code;
    document.querySelectorAll('.method-btn').forEach(b => {
        b.className = 'method-btn p-3.5 rounded-2xl border text-[11px] font-semibold transition-all flex flex-col items-center gap-1.5 bg-cafe-beige/30 text-cafe-brown border-cafe-beige hover:bg-cafe-beige';
    });
    btn.className = 'method-btn p-3.5 rounded-2xl border text-[11px] font-semibold transition-all flex flex-col items-center gap-1.5 bg-cafe-dark text-beige border-cafe-dark shadow-md';
    lucide.createIcons();

    if (code === 'tunai') {
        document.getElementById('cash-block').style.display = '';
        document.getElementById('pay-cash').value = Math.ceil(total / 1000) * 1000;
    } else {
        document.getElementById('cash-block').style.display = 'none';
        document.getElementById('pay-cash').value = total;
    }
    calcChange();
}

function calcChange() {
    const cash = parseFloat(document.getElementById('pay-cash').value) || 0;
    const chg = Math.max(0, cash - total);
    document.getElementById('pay-change').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(chg);
}

document.getElementById('pay-cash').addEventListener('input', calcChange);
calcChange();
</script>
@endsection