@php
    $currentRoute = Route::currentRouteName();
    $newWebOrdersCount = \App\Models\Order::where('source', 'web')->where('order_status', 'baru')->count();
    $unpaidOrdersCount = \App\Models\Order::where('payment_status', 'menunggu_pembayaran')->where('order_status', '!=', 'selesai')->count();
    $todayReservationCount = \App\Models\Reservation::whereDate('reservation_date', today())->count();

    $menus = [
        ['route' => 'kasir.dashboard', 'label' => 'Dashboard Shift', 'icon' => 'layout-dashboard', 'badge' => $newWebOrdersCount],
        ['route' => 'kasir.pos.index', 'label' => 'POS / Input Pesanan', 'icon' => 'calculator', 'badge' => null],
        ['route' => 'kasir.orders.index', 'label' => 'Pesanan Masuk', 'icon' => 'inbox', 'badge' => $newWebOrdersCount],
        ['route' => 'kasir.payments.index', 'label' => 'Pembayaran & Struk', 'icon' => 'receipt', 'badge' => $unpaidOrdersCount],
        ['route' => 'kasir.reservations.index', 'label' => 'Reservasi & Meja', 'icon' => 'calendar-days', 'badge' => $todayReservationCount],
        ['route' => 'kasir.shift.index', 'label' => 'Buka/Tutup Shift & Rekap', 'icon' => 'wallet', 'badge' => null],
    ];
@endphp

<aside id="kasir-sidebar" class="fixed top-0 bottom-0 left-0 z-50 w-72 bg-[#290024] text-[#F2F5E2] flex flex-col transition-transform duration-300 ease-in-out shadow-2xl lg:shadow-none -translate-x-full lg:translate-x-0">
    <!-- Header -->
    <div class="h-20 px-6 flex items-center justify-between border-b border-white/10 shrink-0">
        <a href="{{ route('kasir.dashboard') }}" class="flex items-center gap-3 group">
            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#D4954D] via-[#B87C3C] to-[#775533] p-0.5 shadow-sm shrink-0">
                <div class="w-full h-full rounded-full bg-[#290024] flex items-center justify-center border border-[#D4954D]/40 group-hover:scale-105 transition-transform">
                    <span class="font-serif font-black tracking-tighter text-xs text-[#D4954D]">ABS</span>
                </div>
            </div>
            <div class="flex flex-col">
                <span class="font-serif font-bold text-lg tracking-tight leading-none text-[#F2F5E2] group-hover:text-cafe-gold transition-colors">ABS Cafe</span>
                <span class="text-[10px] tracking-widest uppercase font-medium text-[#D4954D] mt-0.5">Kasir Portal</span>
            </div>
        </a>

        <button onclick="toggleSidebar()" class="lg:hidden w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-[#F2F5E2] flex items-center justify-center transition-colors">
            <i data-lucide="x" class="w-5 h-5"></i>
        </button>
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
            <a href="{{ route($item['route']) }}" class="group flex items-center justify-between px-3.5 py-3 rounded-2xl text-xs font-medium transition-all duration-200 {{ $isActive ? 'bg-[#D4954D] text-[#290024] font-semibold shadow-md translate-x-1' : 'text-[#F2F5E2]/85 hover:bg-white/10 hover:text-[#F2F5E2]' }}">
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

        <div class="px-3 pt-4 pb-2 text-[10px] font-semibold tracking-wider uppercase text-[#D4954D]/80">
            Akun
        </div>
        <a href="{{ route('kasir.profile.index') }}" class="group flex items-center justify-between px-3.5 py-3 rounded-2xl text-xs font-medium transition-all duration-200 {{ request()->routeIs('kasir.profile.*') ? 'bg-[#D4954D] text-[#290024] font-semibold shadow-md translate-x-1' : 'text-[#F2F5E2]/85 hover:bg-white/10 hover:text-[#F2F5E2]' }}">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 transition-colors {{ request()->routeIs('kasir.profile.*') ? 'bg-[#290024]/15 text-[#290024]' : 'text-[#D4954D]' }}">
                    <i data-lucide="user" class="w-4 h-4 stroke-[2.2]"></i>
                </div>
                <span class="truncate">Profil Saya</span>
            </div>
        </a>
    </nav>

    <!-- Footer -->
    <div class="p-4 border-t border-white/10 bg-[#290024]/50 shrink-0">
        <div class="flex items-center justify-between p-2 rounded-2xl bg-white/5">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-9 h-9 rounded-full bg-[#D4954D] text-[#290024] font-serif font-bold flex items-center justify-center text-xs shrink-0 shadow">
                    {{ substr(Auth::user()->name ?? 'K', 0, 1) }}
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-[#F2F5E2] truncate">{{ Auth::user()->name ?? 'Kasir ABS' }}</p>
                    <p class="text-[10px] text-[#D4954D] capitalize">{{ Auth::user()->role ?? 'Kasir' }}</p>
                </div>
            </div>

            <form action="{{ route('kasir.logout') }}" method="POST" data-cafe-confirm class="inline">
                @csrf
                <button type="submit" title="Keluar" class="p-2 rounded-xl text-white/70 hover:text-white hover:bg-white/10 transition-colors">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                </button>
            </form>
        </div>
    </div>
</aside>