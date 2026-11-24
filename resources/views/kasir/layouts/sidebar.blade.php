@php
    $currentRoute = Route::currentRouteName();
    $newWebOrdersCount = \App\Models\Order::forKasir()->where('source', 'web')->where('order_status', 'diproses')->count();
    $unpaidOrdersCount = \App\Models\Order::forKasir()->where('payment_status', 'menunggu_pembayaran')->where('order_status', '!=', 'selesai')->count();
    $todayReservationCount = \App\Models\Reservation::whereDate('reservation_date', today())->count();
    $activeCashierName = session('active_cashier_name', Auth::user()->name ?? 'Kasir');

    $menus = [
        ['route' => 'kasir.dashboard', 'label' => 'Dashboard', 'icon' => 'layout-dashboard', 'badge' => $newWebOrdersCount],
        ['route' => 'kasir.pos.index', 'label' => 'POS / Input Pesanan', 'icon' => 'calculator', 'badge' => null],
        ['route' => 'kasir.orders.index', 'label' => 'Pesanan Masuk', 'icon' => 'inbox', 'badge' => $newWebOrdersCount],
        ['route' => 'kasir.payments.index', 'label' => 'Pembayaran & Struk', 'icon' => 'receipt', 'badge' => $unpaidOrdersCount],
        ['route' => 'kasir.reservations.index', 'label' => 'Reservasi & Meja', 'icon' => 'calendar-days', 'badge' => $todayReservationCount],
        ['route' => 'kasir.shift.index', 'label' => 'Rekap Shift & Kas', 'icon' => 'wallet', 'badge' => null],
    ];
@endphp

<aside id="kasir-sidebar" class="hidden lg:flex fixed top-0 bottom-0 left-0 z-50 w-72 bg-[#290024] text-[#F2F5E2] flex-col shadow-2xl">
    <!-- Header -->
    <div class="h-20 px-6 flex items-center justify-between border-b border-beige/10 shrink-0">
        <a href="{{ route('kasir.dashboard') }}" class="flex items-center gap-3 group">
            <x-logo on-dark size="md" subtitle="Kasir Portal" loading="eager" class="transition-transform group-hover:scale-105" />
        </a>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto px-4 py-5 space-y-1.5">
        <div class="px-3 pb-2 text-[10px] font-semibold tracking-wider uppercase text-[#D4954D]/80">
            Menu Operasional
        </div>

        @foreach($menus as $item)
            @php
                $isActive = request()->routeIs($item['route']);
            @endphp
            <a href="{{ route($item['route']) }}" class="group flex items-center justify-between px-3.5 py-3 rounded-2xl text-xs font-medium transition-all duration-200 {{ $isActive ? 'bg-[#D4954D] text-[#290024] font-semibold shadow-md translate-x-1' : 'text-[#F2F5E2]/85 hover:bg-beige/10 hover:text-[#F2F5E2]' }}">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 transition-colors {{ $isActive ? 'bg-[#290024]/15 text-[#290024]' : 'text-[#D4954D] group-hover:scale-110' }}">
                        <i data-lucide="{{ $item['icon'] }}" class="w-4 h-4 stroke-[2.2]"></i>
                    </div>
                    <span class="truncate">{{ $item['label'] }}</span>
                </div>

                <div class="flex items-center gap-1.5 shrink-0">
                    @if($item['badge'] && $item['badge'] > 0)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold shadow-sm {{ $isActive ? 'bg-[#290024] text-[#D4954D]' : 'bg-[#D4954D] text-[#290024]' }}">
                            {{ $item['badge'] }}
                        </span>
                    @endif
                    @if($isActive)
                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-[#290024]/70"></i>
                    @endif
                </div>
            </a>
        @endforeach
    </nav>

    <!-- Footer Profile & Kasir Switch -->
    <div class="p-4 border-t border-beige/10 bg-[#290024]/50 shrink-0 space-y-2">
        <!-- Kasir Bertugas -->
        <div class="flex items-center gap-2.5 p-2 rounded-2xl bg-beige/5">
            <div class="w-9 h-9 rounded-full bg-[#D4954D] text-[#290024] font-serif font-bold flex items-center justify-center text-xs shrink-0 shadow">
                {{ substr($activeCashierName, 0, 1) }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-semibold text-[#F2F5E2] truncate">{{ $activeCashierName }}</p>
                <p class="text-[10px] text-[#D4954D]">Kasir Bertugas</p>
            </div>
        </div>

        <!-- Ganti Kasir Dropdown -->
        <div class="relative" id="sidebar-cashier-wrapper">
            <button type="button"
                    onclick="toggleSidebarCashier()"
                    class="w-full flex items-center justify-between gap-2 px-3 py-2 rounded-xl text-[11px] text-[#F2F5E2]/70 hover:bg-beige/10 transition-colors">
                <span class="flex items-center gap-1.5"><i data-lucide="repeat" class="w-3.5 h-3.5"></i> Ganti Kasir</span>
                <i data-lucide="chevron-up" class="w-3 h-3 hidden" id="sidebar-cashier-chevron-up"></i>
                <i data-lucide="chevron-down" class="w-3 h-3" id="sidebar-cashier-chevron-down"></i>
            </button>
            <div id="sidebar-cashier-list" class="hidden mt-1 space-y-0.5 max-h-44 overflow-y-auto">
                <form method="POST" action="{{ route('kasir.switch_cashier') }}">
                    @csrf
                    @foreach(\App\Models\User::where('status', 'aktif')->whereIn('role', ['kasir', 'staf', 'admin'])->orderBy('name')->pluck('name') as $staffName)
                        <button type="submit" name="cashier_name" value="{{ $staffName }}"
                                class="w-full text-left px-3 py-2 rounded-xl text-[11px] transition-colors flex items-center gap-2 {{ $activeCashierName === $staffName ? 'bg-[#D4954D]/30 text-[#F2F5E2] font-semibold' : 'text-[#F2F5E2]/70 hover:bg-beige/10' }}">
                            @if($activeCashierName === $staffName)
                                <i data-lucide="check" class="w-3.5 h-3.5 shrink-0"></i>
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