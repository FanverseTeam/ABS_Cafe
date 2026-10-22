@extends('kasir.layouts.app')

@section('title', 'Pesanan Masuk - Kasir')
@section('page_title', 'Pesanan Masuk')

@section('content')
<div class="space-y-4 lg:space-y-5">
    {{-- Mobile FAB for Pesanan Baru --}}
    <x-mobile.fab icon="plus" label="Pesanan Baru" href="{{ route('kasir.pos.index') }}" />

    <!-- Tabs & Filter -->
    <div class="flex items-center justify-between gap-3">
        @php
            $currentTab = $tab ?? 'aktif';
            $tabDefs = [
                'aktif' => ['label' => 'Aktif', 'badge' => $counts['aktif']],
                'web' => ['label' => 'Web', 'badge' => $counts['web']],
                'belum_bayar' => ['label' => 'Belum Bayar', 'badge' => $counts['belum_bayar']],
                'selesai' => ['label' => 'Hari Ini', 'badge' => null],
            ];
            $orderChips = [];
            foreach($tabDefs as $key => $tabItem) {
                $orderChips[] = [
                    'label'  => $tabItem['label'] . ($tabItem['badge'] ? ' (' . $tabItem['badge'] . ')' : ''),
                    'href'   => route('kasir.orders.index', ['tab' => $key]),
                    'active' => $currentTab == $key,
                ];
            }
        @endphp

        {{-- Mobile/Tablet: Chip Scroller with fade edges --}}
        <div class="lg:hidden flex-1 min-w-0">
            <x-mobile.chip-scroller :chips="$orderChips" />
        </div>

        {{-- Desktop tabs --}}
        <div class="hidden lg:flex gap-2 overflow-x-auto no-scrollbar py-1">
            @foreach($tabDefs as $key => $tabItem)
                <a href="{{ route('kasir.orders.index', ['tab' => $key]) }}"
                   class="shrink-0 px-4 py-2 rounded-full text-xs font-semibold transition-all flex items-center gap-1.5 {{ $currentTab == $key ? 'bg-[#290024] text-[#F2F5E2] shadow-sm' : 'bg-[#FAFCF3] text-[#775533] border border-[#E3DEA4] hover:bg-[#E3DEA4]/60' }}">
                    {{ $tabItem['label'] }}
                    @if($tabItem['badge'])
                        <span class="px-1.5 py-0.5 rounded-full text-[9px] font-bold {{ $currentTab == $key ? 'bg-[#D4954D] text-[#290024]' : 'bg-[#290024] text-white' }}">{{ $tabItem['badge'] }}</span>
                    @endif
                </a>
            @endforeach
        </div>

        <!-- Desktop Action Button -->
        <a href="{{ route('kasir.pos.index') }}" class="hidden lg:inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-cafe-gold text-cafe-dark text-xs font-bold hover:bg-[#B87C3C] transition-colors shadow-md shrink-0">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Pesanan Baru
        </a>
    </div>

    <!-- List Pesanan -->
    @if($orders->count() === 0)
        <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft p-10 text-center">
            <div class="w-16 h-16 mx-auto rounded-full bg-cafe-cream flex items-center justify-center text-cafe-muted">
                <i data-lucide="inbox" class="w-7 h-7"></i>
            </div>
            <p class="text-xs text-cafe-muted mt-4">Tidak ada pesanan pada tab ini.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($orders as $order)
                @php $statusBadge = $order->orderStatusBadge(); $payBadge = $order->paymentStatusBadge(); @endphp
                <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft overflow-hidden flex flex-col">
                    <div class="px-5 py-4 border-b border-cafe-beige/50 flex items-center justify-between gap-2 bg-cafe-cream/50">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="font-serif font-bold text-sm text-cafe-dark">{{ $order->order_code }}</span>
                                @if($order->source === 'web')
                                    <span class="text-[9px] px-2 py-0.5 rounded-full bg-cafe-info/15 text-cafe-info font-bold">WEB</span>
                                @endif
                            </div>
                            <p class="text-[10px] text-cafe-muted truncate">{{ $order->customer_name }} &bull; {{ $order->orderTypeLabel() }} {{ $order->table_number ? '&bull; Meja '.$order->table_number : '' }} &bull; {{ $order->created_at->format('H:i') }}</p>
                        </div>
                        <button type="button" onclick="showOrder({{ $order->id }})" class="p-2 rounded-full bg-cafe-beige/60 text-cafe-brown hover:bg-cafe-gold/30 transition-colors" title="Detail">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </button>
                    </div>

                    <div class="px-5 py-3 flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-cafe-{{ $statusBadge['color'] }}/15 text-cafe-{{ $statusBadge['color'] }} border border-cafe-{{ $statusBadge['color'] }}/30">
                                {{ $statusBadge['label'] }}
                            </span>
                            <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-cafe-{{ $payBadge['color'] }}/15 text-cafe-{{ $payBadge['color'] }} border border-cafe-{{ $payBadge['color'] }}/30">
                                {{ $payBadge['label'] }}
                            </span>
                        </div>

                        <div class="mt-3 space-y-1">
                            @foreach($order->items->take(3) as $item)
                                <p class="text-[11px] text-cafe-muted truncate">
                                    <b class="text-cafe-dark">{{ $item->quantity }}&times;</b> {{ $item->name }}
                                    @if($item->notes)<span class="text-cafe-info">({{ $item->notes }})</span>@endif
                                </p>
                            @endforeach
                            @if($order->items->count() > 3)
                                <p class="text-[10px] text-cafe-muted">+{{ $order->items->count() - 3 }} item lainnya</p>
                            @endif
                        </div>
                    </div>

                    <div class="px-5 pb-4">
                        <div class="flex items-center justify-between py-2 border-t border-cafe-beige/40">
                            <span class="text-[10px] text-cafe-muted">{{ $order->paymentMethodLabel() }}</span>
                            <span class="font-serif font-bold text-cafe-dark">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                        </div>

                        @php
                            $flow = $order->source === 'web'
                                ? ['baru', 'diterima', 'diproses', 'siap', 'selesai']
                                : ['diproses', 'siap', 'selesai'];
                            $idx = array_search($order->order_status, $flow, true);
                            $nextStatus = ($idx !== false && isset($flow[$idx + 1])) ? $flow[$idx + 1] : null;
                        @endphp
                        <div class="grid grid-cols-2 gap-2 mt-1">
                            @if($nextStatus)
                                <form action="{{ route('kasir.orders.status', $order) }}" method="POST">
                                    @csrf
                                    <button class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-full bg-cafe-dark text-white text-[11px] font-bold hover:bg-cafe-brown transition-colors">
                                        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                        {{ match ($nextStatus) { 'diterima' => 'Terima', 'diproses' => 'Proses', 'siap' => 'Siap', 'selesai' => 'Selesai' } }}
                                    </button>
                                </form>
                            @else
                                <span class="inline-flex items-center justify-center px-3 py-2.5 rounded-full bg-cafe-beige/50 text-cafe-muted text-[11px] font-semibold">Selesai</span>
                            @endif

                            @if(! $order->isPaid() && ! $order->isCancelled())
                                <a href="{{ route('kasir.payments.checkout', $order) }}" class="inline-flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-full bg-cafe-gold text-cafe-dark text-[11px] font-bold hover:bg-[#B87C3C] transition-colors">
                                    <i data-lucide="wallet" class="w-3.5 h-3.5"></i>
                                    Bayar
                                </a>
                            @elseif(! $order->isCancelled())
                                <button onclick="showOrder({{ $order->id }})" class="inline-flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-full bg-cafe-beige/60 text-cafe-brown text-[11px] font-bold hover:bg-cafe-beige transition-colors">
                                    <i data-lucide="receipt" class="w-3.5 h-3.5"></i>
                                    Struk
                                </button>
                            @endif
                        </div>

                        @if(! $order->isCancelled())
                            <form action="{{ route('kasir.orders.cancel', $order) }}" method="POST" onsubmit="return confirmCancel(event)">
                                @csrf
                                <input type="text" name="cancel_reason" placeholder="Alasan pembatalan (wajib)" required class="mt-2 w-full text-[10px] bg-[#E3DEA4]/40 border border-cafe-danger/30 text-cafe-text placeholder:text-cafe-muted/50 px-3 py-2 rounded-xl focus:border-cafe-danger focus:outline-none">
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="pt-2">
            {{ $orders->links() }}
        </div>
    @endif
</div>

<!-- Modal Detail (Bottom Sheet on Mobile, Centered Modal on Tablet/Desktop) -->
<div id="order-modal" class="hidden fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4">
    <div class="fixed inset-0 bg-[#290024]/60 backdrop-blur-sm" onclick="closeOrderModal()"></div>
    <div class="relative bg-[#FAFCF3] rounded-t-[2rem] sm:rounded-3xl shadow-soft-lg w-full max-w-lg border-t sm:border border-[#E3DEA4] z-10 max-h-[90vh] overflow-y-auto pb-[max(1rem,env(safe-area-inset-bottom))]">
        <!-- Handle for mobile -->
        <div class="sm:hidden w-full pt-3 pb-1 flex justify-center cursor-pointer" onclick="closeOrderModal()">
            <div class="w-12 h-1 bg-[#775533]/30 rounded-full"></div>
        </div>

        <div class="px-6 py-4 border-b border-[#E3DEA4]/60 flex items-center justify-between sticky top-0 bg-[#FAFCF3] z-10">
            <div>
                <h3 class="font-serif font-bold text-base text-[#290024]" id="om-code">Pesanan</h3>
                <p class="text-[10px] text-[#775533]" id="om-meta"></p>
            </div>
            <button onclick="closeOrderModal()" class="w-8 h-8 rounded-full bg-[#E3DEA4]/50 hover:bg-[#E3DEA4] text-[#290024] flex items-center justify-center transition-colors"><i data-lucide="x" class="w-4 h-4"></i></button>
        </div>
        <div class="p-6 space-y-5" id="om-body"></div>
    </div>
</div>

<script>
function showOrder(id) {
    fetch(`{{ route('kasir.orders.data', 'X') }}`.replace('X', id))
        .then(r => r.json())
        .then(d => {
            const o = d.order;
            document.getElementById('om-code').textContent = o.code;
            document.getElementById('om-meta').textContent = o.customer + ' • ' + o.type + (o.table ? ' • Meja ' + o.table : '') + ' • ' + o.created;

            const badge = o.status;
            document.getElementById('om-body').innerHTML = `
                <div class="flex flex-wrap gap-2">
                    <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-cafe-cream border border-cafe-beige">Status: ${o.status}</span>
                    <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-cafe-cream border border-cafe-beige">Bayar: ${o.payment}</span>
                </div>

                <div class="space-y-2">
                    ${d.items.map(i => `
                        <div class="flex items-start justify-between gap-3 p-3 rounded-2xl bg-cafe-cream/70 border border-cafe-beige/40">
                            <div class="min-w-0">
                                <p class="text-xs font-semibold text-cafe-dark">${i.qty}× ${i.name}</p>
                                ${i.notes ? `<p class="text-[10px] text-cafe-info">${i.notes}</p>` : ''}
                            </div>
                            <p class="text-xs font-serif font-bold text-cafe-dark shrink-0">Rp ${Number(i.subtotal).toLocaleString('id-ID')}</p>
                        </div>
                    `).join('')}
                </div>

                <div class="rounded-2xl bg-cafe-card border border-cafe-beige/50 p-4 space-y-1.5 text-[11px]">
                    <div class="flex justify-between"><span class="text-cafe-muted">Subtotal</span><span>Rp ${Number(o.subtotal).toLocaleString('id-ID')}</span></div>
                    <div class="flex justify-between"><span class="text-cafe-muted">Pajak</span><span>Rp ${Number(o.tax).toLocaleString('id-ID')}</span></div>
                    <div class="flex justify-between"><span class="text-cafe-muted">Service</span><span>Rp ${Number(o.service).toLocaleString('id-ID')}</span></div>
                    <div class="flex justify-between"><span class="text-cafe-muted">Diskon</span><span class="text-cafe-danger">-Rp ${Number(o.discount).toLocaleString('id-ID')}</span></div>
                    <div class="flex justify-between border-t border-cafe-beige/50 pt-1.5"><span class="font-bold text-cafe-dark">Total</span><span class="font-serif font-bold text-cafe-dark">Rp ${Number(o.total).toLocaleString('id-ID')}</span></div>
                </div>

                ${d.timelines.length ? `
                    <div>
                        <p class="text-[10px] font-semibold text-cafe-muted uppercase tracking-wide mb-2">Riwayat</p>
                        <div class="space-y-1.5">
                            ${d.timelines.map(t => `
                                <div class="flex items-start gap-2 text-[11px]">
                                    <i data-lucide="circle-check" class="w-3.5 h-3.5 text-cafe-success mt-0.5 shrink-0"></i>
                                    <div class="min-w-0">
                                        <p class="text-cafe-dark font-medium">${t.description}</p>
                                        <p class="text-[10px] text-cafe-muted">${t.status} • ${t.actor} • ${t.time}</p>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                ` : ''}
            `;
            lucide.createIcons();
            document.getElementById('order-modal').classList.remove('hidden');
        });
}

function closeOrderModal() {
    document.getElementById('order-modal').classList.add('hidden');
}

function confirmCancel(event) {
    const reason = event.target.querySelector('input[name="cancel_reason"]').value.trim();
    if (! reason) return false;
    if (! confirm('Yakin membatalkan pesanan ini? Alasan: ' + reason)) {
        event.preventDefault();
        return false;
    }
    return true;
}
</script>
@endsection