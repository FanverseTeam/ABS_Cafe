@php
    $currentRoute = Route::currentRouteName();

    // ACTIVE ADMIN: Dashboard is always first, followed by the six active modules.
    $menus = [
        ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'layout-dashboard', 'badge' => null],
        ['route' => 'admin.staff.index', 'label' => 'Manajemen User Staf & Hak Akses', 'icon' => 'users', 'badge' => null],
        ['route' => 'admin.catalog.index', 'label' => 'Manajemen Menu', 'icon' => 'coffee', 'badge' => null],
        ['route' => 'admin.operations.index', 'label' => 'Meja', 'icon' => 'grid-3x3', 'badge' => null],
        ['route' => 'admin.vouchers.index', 'label' => 'Promo / Voucher', 'icon' => 'percent', 'badge' => null],
        ['route' => 'admin.website.index', 'label' => 'Pengaturan Website', 'icon' => 'settings', 'badge' => null],
        ['route' => 'admin.activity.index', 'label' => 'Log Aktivitas', 'icon' => 'history', 'badge' => null],
    ];

    $homeRoute = 'admin.dashboard';
@endphp

<aside id="admin-sidebar" class="hidden lg:flex fixed top-0 bottom-0 left-0 z-50 w-72 bg-[#290024] text-[#F2F5E2] flex-col shadow-2xl">
    <!-- Header with Monogram Logo -->
    <div class="h-20 px-6 flex items-center justify-between border-b border-beige/10 shrink-0">
        <a href="{{ route($homeRoute) }}" class="flex items-center gap-3 group">
            <x-logo on-dark size="md" loading="eager" class="transition-transform group-hover:scale-105" />
        </a>
    </div>

    <!-- Navigation List -->
    <nav class="flex-1 overflow-y-auto px-4 py-5 space-y-1.5 scrollbar-thin scrollbar-thumb-white/10">
        <div class="px-3 pb-2 text-[10px] font-semibold tracking-wider uppercase text-[#D4954D]/80">
            Menu Utama
        </div>

        @foreach($menus as $item)
            @php
                $routePrefix = explode('.', $item['route'])[1] ?? '';
                $routeName = (string) Route::currentRouteName();
                $isActive = request()->routeIs($item['route'])
                    || ($routePrefix !== '' && str_starts_with($routeName, 'admin.'.$routePrefix.'.'));
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

    <!-- Footer Profile -->
    <div class="p-4 border-t border-beige/10 bg-[#290024]/50 shrink-0">
        <div class="flex items-center gap-2.5 p-2 rounded-2xl bg-beige/5">
            <div class="w-9 h-9 rounded-full bg-[#D4954D] text-[#290024] font-serif font-bold flex items-center justify-center text-xs shrink-0 shadow">
                {{ substr(Auth::user()->name ?? 'Admin', 0, 1) }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-semibold text-[#F2F5E2] truncate">{{ Auth::user()->name ?? 'Admin ABS' }}</p>
                <p class="text-[10px] text-[#D4954D] capitalize">{{ Auth::user()->role ?? 'Administrator' }}</p>
            </div>
        </div>
    </div>
</aside>
