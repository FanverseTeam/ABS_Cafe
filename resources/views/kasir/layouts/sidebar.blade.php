@php
    $currentRoute = Route::currentRouteName();
    $newWebOrdersCount = \App\Models\Order::forKasir()->today()->where('source', 'web')->where('order_status', 'diproses')->count();
    $unpaidOrdersCount = \App\Models\Order::forKasir()->today()->where('payment_status', 'menunggu_pembayaran')->where('order_status', '!=', 'selesai')->count();
    $todayReservationCount = \App\Models\Reservation::whereDate('reservation_date', today())->count();
    $activeCashierName = session('active_cashier_name', Auth::user()->name ?? 'Kasir');

    $menus = [
        ['route' => 'kasir.dashboard', 'label' => 'Dashboard Kasir', 'icon' => 'layout-dashboard', 'badge' => $newWebOrdersCount],
        ['route' => 'kasir.pos.index', 'label' => 'POS / Input Pesanan', 'icon' => 'calculator', 'badge' => null],
        ['route' => 'kasir.orders.index', 'label' => 'Pesanan Masuk', 'icon' => 'inbox', 'badge' => $newWebOrdersCount],
        ['route' => 'kasir.payments.index', 'label' => 'Pembayaran & Struk', 'icon' => 'receipt', 'badge' => $unpaidOrdersCount],
        ['route' => 'kasir.reservations.index', 'label' => 'Reservasi & Meja', 'icon' => 'calendar-days', 'badge' => $todayReservationCount],
    ];
@endphp

<aside id="kasir-sidebar" class="hidden lg:flex fixed top-0 bottom-0 left-0 z-50 w-72 bg-gradient-to-b from-[#180f16] via-[#120a10] to-[#0a0508] text-[#F2F5E2] flex-col shadow-2xl border-r border-[#D4954D]/20 overflow-hidden">
    <!-- Subtle Golden Ambient Glow Orbs -->
    <div class="absolute -top-12 -left-12 w-44 h-44 bg-[#D4954D]/15 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-16 -right-16 w-44 h-44 bg-[#D4954D]/10 rounded-full blur-3xl pointer-events-none"></div>

    <!-- Header -->
    <div class="h-20 px-6 flex items-center justify-between relative z-10 shrink-0">
        <a href="{{ route('kasir.dashboard') }}" class="flex items-center gap-3 group">
            <x-logo on-dark size="md" subtitle="Kasir Portal" loading="eager" class="transition-transform group-hover:scale-105" />
        </a>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto px-4 py-5 space-y-1.5 scrollbar-thin scrollbar-thumb-white/10 relative z-10">
        <div class="px-3 pb-2 text-[10px] font-bold tracking-[0.2em] uppercase text-[#D4954D]">
            Menu Operasional
        </div>

        @foreach($menus as $item)
            @php
                $isActive = request()->routeIs($item['route']);
            @endphp
            <a href="{{ route($item['route']) }}" class="group flex items-center justify-between px-3.5 py-3 rounded-2xl text-xs font-medium transition-all duration-200 {{ $isActive ? 'bg-gradient-to-r from-[#D4954D] via-[#dfa55d] to-[#c7883e] text-[#1a0e05] font-bold shadow-[0_4px_20px_rgba(212,149,77,0.35)] scale-[1.02] translate-x-1' : 'text-[#F2F5E2]/85 hover:text-[#F2F5E2] hover:bg-white/[0.08] hover:translate-x-1' }}">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 transition-all {{ $isActive ? 'bg-white/30 text-[#1a0e05] shadow-xs' : 'text-[#D4954D] group-hover:scale-110' }}">
                        <i data-lucide="{{ $item['icon'] }}" class="w-4 h-4 stroke-[2.2]"></i>
                    </div>
                    <span class="truncate">{{ $item['label'] }}</span>
                </div>

                <div class="flex items-center gap-1.5 shrink-0">
                    @if($item['badge'] && $item['badge'] > 0)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold shadow-sm {{ $isActive ? 'bg-[#1a0e05] text-[#D4954D]' : 'bg-[#D4954D] text-[#1a0e05]' }}">
                            {{ $item['badge'] }}
                        </span>
                    @endif
                    @if($isActive)
                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-[#1a0e05]/70"></i>
                    @endif
                </div>
            </a>
        @endforeach
    </nav>

    @php $sidebarPenjaga = session('active_penjaga_kasir') ?: session('active_cashier_name') ?: \App\Models\CafeSetting::get('active_penjaga_kasir') ?: (Auth::user()->name ?? 'Kasir'); @endphp
    <!-- Footer Profile -->
    <div class="p-3 bg-black/20 relative z-10 shrink-0 space-y-2">
        <div class="flex items-center gap-2.5 p-2 rounded-2xl bg-white/[0.04] border border-[#D4954D]/20 backdrop-blur-sm shadow-xs">
            <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-[#D4954D] to-[#ebd2a9] text-[#1a0e05] font-serif font-black flex items-center justify-center text-xs shrink-0 shadow-md ring-2 ring-[#D4954D]/30">
                {{ substr($sidebarPenjaga, 0, 1) }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-bold text-[#F2F5E2] truncate">{{ $sidebarPenjaga }}</p>
                <p class="text-[10px] text-[#D4954D] font-medium tracking-wide">Kasir Bertugas</p>
            </div>
        </div>

        <!-- Ganti Kasir Dropdown -->
        <div class="relative" id="sidebar-cashier-wrapper">
            <button type="button"
                    onclick="toggleSidebarCashier()"
                    class="w-full flex items-center justify-between gap-2 px-3 py-1.5 rounded-xl text-[11px] text-[#F2F5E2]/70 hover:bg-white/[0.08] transition-colors">
                <span class="flex items-center gap-1.5"><i data-lucide="repeat" class="w-3.5 h-3.5 text-[#D4954D]"></i> Ganti Kasir</span>
                <i data-lucide="chevron-up" class="w-3 h-3 hidden" id="sidebar-cashier-chevron-up"></i>
                <i data-lucide="chevron-down" class="w-3 h-3" id="sidebar-cashier-chevron-down"></i>
            </button>
            <div id="sidebar-cashier-list" class="hidden mt-1 space-y-0.5 max-h-44 overflow-y-auto">
                <form method="POST" action="{{ route('kasir.switch_cashier') }}">
                    @csrf
                    @php
                        $sidebarCashiers = \Illuminate\Support\Facades\Schema::hasTable('penjaga_kasir')
                            ? \App\Models\PenjagaKasir::activeKasir()->pluck('name')
                            : \App\Models\User::where('status', 'aktif')->whereIn('role', ['kasir'])->orderBy('name')->pluck('name');
                    @endphp
                    @foreach($sidebarCashiers as $staffName)
                        <button type="submit" name="cashier_name" value="{{ $staffName }}"
                                class="w-full text-left px-3 py-2 rounded-xl text-[11px] transition-colors flex items-center gap-2 {{ $sidebarPenjaga === $staffName ? 'bg-[#D4954D]/30 text-[#F2F5E2] font-semibold' : 'text-[#F2F5E2]/70 hover:bg-white/[0.08]' }}">
                            @if($sidebarPenjaga === $staffName)
                                <i data-lucide="check" class="w-3.5 h-3.5 shrink-0 text-[#D4954D]"></i>
                            @else
                                <i data-lucide="user" class="w-3.5 h-3.5 shrink-0 opacity-50"></i>
                            @endif
                            {{ $staffName }}
                        </button>
                    @endforeach
                </form>
            </div>
        </div>
    </div>
<<<<<<< HEAD
=======
            </div>
        </div>
    </div>
>>>>>>> 537d7c749fd51ff20601c8adc6bf6b4196adfaac
</aside>

<script>
function toggleSidebarCashier() {
    const list = document.getElementById('sidebar-cashier-list');
    const up = document.getElementById('sidebar-cashier-chevron-up');
    const down = document.getElementById('sidebar-cashier-chevron-down');
    if (!list) return;
    const isHidden = list.classList.contains('hidden');
    list.classList.toggle('hidden');
    up?.classList.toggle('hidden', !isHidden);
    down?.classList.toggle('hidden', isHidden);
}
</script>