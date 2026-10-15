@php
    $role = $role ?? 'admin';
    $currentRoute = Route::currentRouteName();

    if ($role === 'admin') {
        $pendingOrders = \App\Models\Order::where('payment_status', 'menunggu_pembayaran')->count();
        $navItems = [
            [
                'label' => 'Beranda',
                'route' => 'admin.dashboard',
                'icon'  => 'layout-dashboard',
                'match' => 'admin.dashboard',
                'badge' => null,
            ],
            [
                'label' => 'Menu',
                'route' => 'admin.menu.index',
                'icon'  => 'coffee',
                'match' => 'admin.menu',
                'badge' => null,
            ],
            [
                'label' => 'Staf',
                'route' => 'admin.users.index',
                'icon'  => 'users',
                'match' => 'admin.users',
                'badge' => null,
            ],
            [
                'label' => 'Promo',
                'route' => 'admin.promo.index',
                'icon'  => 'tag',
                'match' => 'admin.promo',
                'badge' => null,
            ],
            [
                'label' => 'Lainnya',
                'action' => 'toggleSidebar()',
                'icon'  => 'menu',
                'match' => null,
                'badge' => $pendingOrders > 0 ? $pendingOrders : null,
            ],
        ];
    } elseif ($role === 'owner') {
        $activePromoCount = \App\Models\Promo::where('is_active', true)->whereDate('start_date', '<=', now())->whereDate('end_date', '>=', now())->count();
        $navItems = [
            [
                'label' => 'Beranda',
                'route' => 'owner.dashboard',
                'icon'  => 'layout-dashboard',
                'match' => 'owner.dashboard',
                'badge' => null,
            ],
            [
                'label' => 'Laporan',
                'route' => 'owner.reports.index',
                'icon'  => 'bar-chart-3',
                'match' => 'owner.reports',
                'badge' => null,
            ],
            [
                'label' => 'Kasir',
                'route' => 'owner.cashiers.index',
                'icon'  => 'users',
                'match' => 'owner.cashiers',
                'badge' => null,
            ],
            [
                'label' => 'Promo',
                'route' => 'owner.promos.index',
                'icon'  => 'tag',
                'match' => 'owner.promos',
                'badge' => $activePromoCount > 0 ? $activePromoCount : null,
            ],
            [
                'label' => 'Lainnya',
                'action' => 'toggleSidebar()',
                'icon'  => 'menu',
                'match' => null,
                'badge' => null,
            ],
        ];
    } else { // kasir
        $newWebOrders = \App\Models\Order::where('source', 'web')->where('order_status', 'baru')->count();
        $unpaidOrders = \App\Models\Order::where('payment_status', 'menunggu_pembayaran')->where('order_status', '!=', 'selesai')->count();
        $navItems = [
            [
                'label' => 'Beranda',
                'route' => 'kasir.dashboard',
                'icon'  => 'layout-dashboard',
                'match' => 'kasir.dashboard',
                'badge' => null,
            ],
            [
                'label' => 'POS',
                'route' => 'kasir.pos.index',
                'icon'  => 'calculator',
                'match' => 'kasir.pos',
                'badge' => null,
            ],
            [
                'label' => 'Pesanan',
                'route' => 'kasir.orders.index',
                'icon'  => 'clipboard-list',
                'match' => 'kasir.orders',
                'badge' => $newWebOrders > 0 ? $newWebOrders : null,
            ],
            [
                'label' => 'Meja',
                'route' => 'kasir.reservations.index',
                'icon'  => 'calendar',
                'match' => 'kasir.reservations',
                'badge' => null,
            ],
            [
                'label' => 'Lainnya',
                'action' => 'toggleSidebar()',
                'icon'  => 'menu',
                'match' => null,
                'badge' => $unpaidOrders > 0 ? $unpaidOrders : null,
            ],
        ];
    }
@endphp

<!-- Mobile Bottom Navigation Bar (Hidden on desktop md/lg) -->
<nav id="mobile-bottom-nav" class="lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-[#FAFCF3]/95 backdrop-blur-lg border-t border-[#E3DEA4] shadow-[0_-4px_20px_rgba(41,0,36,0.06)] px-2 pt-1.5 pb-[max(0.5rem,env(safe-area-inset-bottom))] transition-transform duration-300">
    <div class="max-w-md mx-auto grid grid-cols-5 items-center gap-1">
        @foreach($navItems as $item)
            @php
                $isActive = false;
                if (!empty($item['match'])) {
                    $isActive = str_starts_with((string)$currentRoute, $item['match']);
                }
            @endphp

            @if(isset($item['route']))
                <a href="{{ route($item['route']) }}" 
                   class="group relative flex flex-col items-center justify-center py-1.5 px-1 rounded-2xl min-h-[48px] transition-all duration-200 {{ $isActive ? 'text-[#290024] font-semibold' : 'text-[#775533]/80 hover:text-[#290024]' }}">
                    <div class="relative flex items-center justify-center w-10 h-7 rounded-full transition-all duration-200 {{ $isActive ? 'bg-[#290024] text-[#F2F5E2] shadow-sm' : 'group-hover:bg-[#E3DEA4]/40' }}">
                        <i data-lucide="{{ $item['icon'] }}" class="w-4 h-4 {{ $isActive ? 'text-[#F2F5E2]' : 'text-[#775533]' }}"></i>
                        @if(!empty($item['badge']))
                            <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-[#B03A2E] text-white text-[9px] font-bold flex items-center justify-center shadow-sm">
                                {{ $item['badge'] }}
                            </span>
                        @endif
                    </div>
                    <span class="text-[10px] mt-0.5 tracking-tight leading-none {{ $isActive ? 'text-[#290024] font-bold' : 'text-[#775533]' }}">
                        {{ $item['label'] }}
                    </span>
                    @if($isActive)
                        <span class="w-1 h-1 rounded-full bg-[#D4954D] mt-0.5"></span>
                    @endif
                </a>
            @else
                <button type="button" 
                        onclick="{{ $item['action'] }}" 
                        class="group relative flex flex-col items-center justify-center py-1.5 px-1 rounded-2xl min-h-[48px] text-[#775533]/80 hover:text-[#290024] transition-all duration-200">
                    <div class="relative flex items-center justify-center w-10 h-7 rounded-full group-hover:bg-[#E3DEA4]/40 transition-colors">
                        <i data-lucide="{{ $item['icon'] }}" class="w-4 h-4 text-[#775533]"></i>
                        @if(!empty($item['badge']))
                            <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-[#D4954D] text-[#290024] text-[9px] font-bold flex items-center justify-center shadow-sm">
                                {{ $item['badge'] }}
                            </span>
                        @endif
                    </div>
                    <span class="text-[10px] mt-0.5 tracking-tight leading-none text-[#775533]">
                        {{ $item['label'] }}
                    </span>
                </button>
            @endif
        @endforeach
    </div>
</nav>
