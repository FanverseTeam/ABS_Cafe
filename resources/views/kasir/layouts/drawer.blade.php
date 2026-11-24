@php
<<<<<<< HEAD
    $newWebOrdersCount = \App\Models\Order::forKasir()->today()->where('source', 'web')->where('order_status', 'diproses')->count();
    $unpaidOrdersCount = \App\Models\Order::forKasir()->today()->where('payment_status', 'menunggu_pembayaran')->where('order_status', '!=', 'selesai')->count();
    $todayReservationCount = \App\Models\Reservation::whereDate('reservation_date', today())->count();

    $drawerMenus = [
        ['route' => 'kasir.dashboard', 'label' => 'Dashboard Kasir', 'icon' => 'layout-dashboard', 'badge' => null],
=======
    $activeCashierName = session('active_cashier_name', Auth::user()->name ?? 'Kasir');
    $staffCashierList = \App\Models\User::where('status', 'aktif')->whereIn('role', ['kasir', 'staf', 'admin'])->orderBy('name')->pluck('name');
    $newWebOrdersCount = \App\Models\Order::forKasir()->where('source', 'web')->where('order_status', 'diproses')->count();
    $unpaidOrdersCount = \App\Models\Order::forKasir()->where('payment_status', 'menunggu_pembayaran')->where('order_status', '!=', 'selesai')->count();
    $todayReservationCount = \App\Models\Reservation::whereDate('reservation_date', today())->count();

    $drawerMenus = [
        ['route' => 'kasir.dashboard', 'label' => 'Dashboard', 'icon' => 'layout-dashboard', 'badge' => null],
>>>>>>> 6ac93a624e631e5773e51745fdfc766f062753de
        ['route' => 'kasir.pos.index', 'label' => 'POS / Input Pesanan', 'icon' => 'calculator', 'badge' => null],
        ['route' => 'kasir.orders.index', 'label' => 'Pesanan Masuk', 'icon' => 'inbox', 'badge' => $newWebOrdersCount],
        ['route' => 'kasir.payments.index', 'label' => 'Pembayaran & Struk', 'icon' => 'receipt', 'badge' => $unpaidOrdersCount],
        ['route' => 'kasir.reservations.index', 'label' => 'Reservasi & Meja', 'icon' => 'calendar-days', 'badge' => $todayReservationCount],
<<<<<<< HEAD
=======
        ['route' => 'kasir.shift.index', 'label' => 'Rekap Shift & Kas', 'icon' => 'wallet', 'badge' => null],
>>>>>>> 6ac93a624e631e5773e51745fdfc766f062753de
    ];
@endphp

<!-- Mobile & Tablet Navigation Drawer (Slide-over) -->
<div id="kasir-drawer" class="fixed inset-0 z-50 pointer-events-none lg:hidden transition-all duration-300" aria-hidden="true">
    <!-- Backdrop -->
    <div id="kasir-drawer-backdrop"
         onclick="closeKasirDrawer()"
         class="fixed inset-0 bg-[#290024]/60 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-300"></div>

    <!-- Slide Panel -->
    <div id="kasir-drawer-panel"
         class="fixed top-0 bottom-0 left-0 w-80 max-w-[85vw] bg-[#290024] text-[#F2F5E2] shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-in-out pointer-events-auto">
        <!-- Header -->
        <div class="h-16 px-5 flex items-center justify-between border-b border-beige/10 shrink-0">
            <a href="{{ route('kasir.dashboard') }}" class="flex items-center gap-2.5">
                <x-logo on-dark size="sm" subtitle="Kasir Portal" loading="eager" />
            </a>
            <button type="button"
                    onclick="closeKasirDrawer()"
                    aria-label="Tutup menu navigasi"
                    class="flex h-10 w-10 items-center justify-center rounded-xl text-[#F2F5E2]/80 hover:text-[#F2F5E2] hover:bg-beige/10 transition-colors">
                <i data-lucide="x" class="h-5 w-5"></i>
            </button>
        </div>

<<<<<<< HEAD
=======
        <!-- Kasir Bertugas Widget -->
        <div class="p-3 mx-4 mt-3 rounded-2xl border bg-cafe-success/15 border-cafe-success/30 text-cafe-success">
            <div class="flex items-center gap-2 min-w-0 mb-2">
                <span class="relative flex h-2.5 w-2.5 shrink-0">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-60 bg-cafe-success"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-cafe-success"></span>
                </span>
                <span class="text-xs font-semibold">Kasir: {{ $activeCashierName }}</span>
            </div>
            <p class="text-[10px] text-[#F2F5E2]/70 mb-2">Ganti kasir bertugas:</p>
            <form method="POST" action="{{ route('kasir.switch_cashier') }}" class="space-y-1 max-h-40 overflow-y-auto">
                @csrf
                @foreach($staffCashierList as $staffName)
                    <button type="submit" name="cashier_name" value="{{ $staffName }}"
                            class="w-full text-left px-3 py-2 rounded-xl text-[11px] transition-colors flex items-center gap-2 {{ $activeCashierName === $staffName ? 'bg-cafe-success/30 text-[#F2F5E2] font-semibold' : 'text-[#F2F5E2]/80 hover:bg-beige/10' }}">
                        @if($activeCashierName === $staffName)
                            <i data-lucide="check-circle" class="w-3.5 h-3.5 shrink-0"></i>
                        @else
                            <i data-lucide="user" class="w-3.5 h-3.5 shrink-0 opacity-60"></i>
                        @endif
                        {{ $staffName }}
                    </button>
                @endforeach
            </form>
        </div>

>>>>>>> 6ac93a624e631e5773e51745fdfc766f062753de
        <!-- Navigation Menu -->
        <nav class="flex-1 overflow-y-auto px-4 py-3 space-y-1">
            <div class="px-3 py-1.5 text-[10px] font-semibold tracking-wider uppercase text-[#D4954D]/80">
                Menu Operasional Kasir
            </div>

            @foreach($drawerMenus as $item)
                @php
                    $isActive = request()->routeIs($item['route'].'*');
                @endphp
                <a href="{{ route($item['route']) }}"
                   class="group flex items-center justify-between px-3.5 py-3 rounded-2xl text-xs font-medium transition-all duration-200 {{ $isActive ? 'bg-[#D4954D] text-[#290024] font-semibold shadow-md' : 'text-[#F2F5E2]/85 hover:bg-beige/10 hover:text-[#F2F5E2]' }}">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 {{ $isActive ? 'bg-[#290024]/15 text-[#290024]' : 'text-[#D4954D] group-hover:scale-110' }}">
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

        @php $drawerPenjaga = session('active_penjaga_kasir') ?: \App\Models\CafeSetting::get('active_penjaga_kasir'); @endphp
        <!-- Footer Profile -->
        <div class="p-4 border-t border-beige/10 bg-[#290024]/50 shrink-0">
            <div class="flex items-center gap-2.5 p-2 rounded-2xl bg-beige/5">
                <div class="w-9 h-9 rounded-full bg-[#D4954D] text-[#290024] font-serif font-bold flex items-center justify-center text-xs shrink-0 shadow">
<<<<<<< HEAD
                    {{ substr($drawerPenjaga ?: (Auth::user()->name ?? 'K'), 0, 1) }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold text-[#F2F5E2] truncate">{{ $drawerPenjaga ?: (Auth::user()->name ?? 'Kasir ABS') }}</p>
                    <p class="text-[10px] text-[#D4954D]">{{ $drawerPenjaga ? 'Penjaga Kasir' : ucfirst(Auth::user()->role ?? 'Kasir') }}</p>
=======
                    {{ substr($activeCashierName, 0, 1) }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold text-[#F2F5E2] truncate">{{ $activeCashierName }}</p>
                    <p class="text-[10px] text-[#D4954D]">Kasir Bertugas</p>
>>>>>>> 6ac93a624e631e5773e51745fdfc766f062753de
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleKasirDrawer() {
        const panel = document.getElementById('kasir-drawer-panel');
        if (!panel) return;
        if (panel.classList.contains('translate-x-0')) {
            closeKasirDrawer();
        } else {
            openKasirDrawer();
        }
    }

    function openKasirDrawer() {
        const drawer = document.getElementById('kasir-drawer');
        const backdrop = document.getElementById('kasir-drawer-backdrop');
        const panel = document.getElementById('kasir-drawer-panel');
        if (!drawer || !panel) return;

        drawer.classList.remove('pointer-events-none');
        backdrop.classList.remove('opacity-0', 'pointer-events-none');
        backdrop.classList.add('opacity-100', 'pointer-events-auto');
        panel.classList.remove('-translate-x-full');
        panel.classList.add('translate-x-0');
        drawer.setAttribute('aria-hidden', 'false');
    }

    function closeKasirDrawer() {
        const drawer = document.getElementById('kasir-drawer');
        const backdrop = document.getElementById('kasir-drawer-backdrop');
        const panel = document.getElementById('kasir-drawer-panel');
        if (!drawer || !panel) return;

        backdrop.classList.remove('opacity-100', 'pointer-events-auto');
        backdrop.classList.add('opacity-0', 'pointer-events-none');
        panel.classList.remove('translate-x-0');
        panel.classList.add('-translate-x-full');
        drawer.setAttribute('aria-hidden', 'true');
        setTimeout(() => {
            drawer.classList.add('pointer-events-none');
        }, 300);
    }
</script>
