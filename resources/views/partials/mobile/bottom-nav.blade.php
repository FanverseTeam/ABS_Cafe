@php
    $role = $role ?? 'admin';
    $currentRoute = Route::currentRouteName();

    if ($role === 'admin') {
        // ACTIVE ADMIN: Dashboard is "Beranda", followed by the active modules.
        $navItems = [
            [
                'label' => 'Beranda',
                'route' => 'admin.dashboard',
                'icon'  => 'layout-dashboard',
                'match' => 'admin.dashboard',
                'badge' => null,
            ],
            [
                'label' => 'Staf',
                'route' => 'admin.staff.index',
                'icon'  => 'users',
                'match' => 'admin.staff',
                'badge' => null,
            ],
            [
                'label' => 'Menu',
                'route' => 'admin.catalog.index',
                'icon'  => 'coffee',
                'match' => 'admin.catalog',
                'badge' => null,
            ],
            [
                'label' => 'Meja',
                'route' => 'admin.operations.index',
                'icon'  => 'grid-3x3',
                'match' => 'admin.operations',
                'badge' => null,
            ],
            [
                'label' => 'Promo',
                'route' => 'admin.vouchers.index',
                'icon'  => 'tag',
                'match' => 'admin.vouchers',
                'badge' => null,
            ],
            [
                'label' => 'Lainnya',
                'action' => 'toggleAdminMore()',
                'icon'  => 'menu',
                'match' => null,
                'badge' => null,
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

    $navColumns = count($navItems) === 6 ? 'grid-cols-6' : 'grid-cols-5';
@endphp

<!-- Mobile Bottom Navigation Bar (Hidden on desktop md/lg) -->
<nav id="mobile-bottom-nav" class="lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-[#FAFCF3]/95 backdrop-blur-lg border-t border-[#E3DEA4] shadow-[0_-4px_20px_rgba(41,0,36,0.06)] px-2 pt-1.5 pb-[max(0.5rem,env(safe-area-inset-bottom))] transition-transform duration-300">
    <div class="max-w-md mx-auto grid {{ $navColumns }} items-center gap-1">
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

@if($role === 'admin')
    <!-- Active admin "Lainnya" sheet (website settings, activity log, profile, logout) -->
    <div id="admin-more-backdrop" onclick="toggleAdminMore()" class="fixed inset-0 z-40 bg-[#290024]/50 backdrop-blur-sm hidden lg:hidden"></div>

    <div id="admin-more-sheet" class="fixed left-0 right-0 bottom-0 z-50 hidden lg:hidden rounded-t-[2rem] bg-[#FAFCF3] border-t border-[#E3DEA4] shadow-[0_-12px_40px_rgba(41,0,36,0.18)] px-4 pt-3 pb-[max(1rem,env(safe-area-inset-bottom))]">
        <div class="mx-auto mb-3 h-1.5 w-12 rounded-full bg-[#775533]/25"></div>

        <div class="max-w-md mx-auto space-y-1">
            <a href="{{ route('admin.website.index') }}" class="flex items-center gap-3 px-3 py-3 rounded-2xl text-xs font-semibold text-[#290024] hover:bg-[#E3DEA4]/50 transition-colors">
                <span class="w-9 h-9 rounded-xl bg-[#E3DEA4]/70 text-[#775533] flex items-center justify-center">
                    <i data-lucide="settings" class="w-4 h-4"></i>
                </span>
                <span>Pengaturan Website</span>
            </a>

            <a href="{{ route('admin.activity.index') }}" class="flex items-center gap-3 px-3 py-3 rounded-2xl text-xs font-semibold text-[#290024] hover:bg-[#E3DEA4]/50 transition-colors">
                <span class="w-9 h-9 rounded-xl bg-[#E3DEA4]/70 text-[#775533] flex items-center justify-center">
                    <i data-lucide="history" class="w-4 h-4"></i>
                </span>
                <span>Log Aktivitas</span>
            </a>

            <a href="{{ route('admin.website.index', ['tab' => 'profil']) }}" class="flex items-center gap-3 px-3 py-3 rounded-2xl text-xs font-semibold text-[#290024] hover:bg-[#E3DEA4]/50 transition-colors">
                <span class="w-9 h-9 rounded-xl bg-[#E3DEA4]/70 text-[#775533] flex items-center justify-center">
                    <i data-lucide="user" class="w-4 h-4"></i>
                </span>
                <span>Profil</span>
            </a>

            <form action="{{ route('admin.logout') }}" method="POST" data-cafe-confirm>
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-3 py-3 rounded-2xl text-xs font-semibold text-[#B03A2E] hover:bg-[#B03A2E]/10 transition-colors text-left">
                    <span class="w-9 h-9 rounded-xl bg-[#B03A2E]/10 text-[#B03A2E] flex items-center justify-center">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                    </span>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </div>

    <script>
        function toggleAdminMore() {
            const backdrop = document.getElementById('admin-more-backdrop');
            const sheet = document.getElementById('admin-more-sheet');
            if (!backdrop || !sheet) return;

            const isHidden = sheet.classList.contains('hidden');
            backdrop.classList.toggle('hidden', !isHidden);
            sheet.classList.toggle('hidden', !isHidden);
        }
    </script>
@endif
