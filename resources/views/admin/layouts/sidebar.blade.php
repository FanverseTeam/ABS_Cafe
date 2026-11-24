@php
    $currentRoute = Route::currentRouteName();

    // ACTIVE ADMIN: Dashboard is always first, followed by the six active modules.
    $menus = [
        ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'layout-dashboard', 'badge' => null],
        ['route' => 'admin.staff.index', 'label' => 'Manajemen Staf', 'icon' => 'users', 'badge' => null],
        ['route' => 'admin.catalog.index', 'label' => 'Manajemen Menu', 'icon' => 'coffee', 'badge' => null],
        ['route' => 'admin.operations.index', 'label' => 'Meja', 'icon' => 'grid-3x3', 'badge' => null],
        ['route' => 'admin.vouchers.index', 'label' => 'Promo / Voucher', 'icon' => 'percent', 'badge' => null],
        ['route' => 'admin.website.index', 'label' => 'Pengaturan Website', 'icon' => 'settings', 'badge' => null],
        ['route' => 'admin.activity.index', 'label' => 'Log Aktivitas', 'icon' => 'history', 'badge' => null],
    ];

    $homeRoute = 'admin.dashboard';
@endphp

<aside id="admin-sidebar" class="hidden lg:flex fixed top-0 bottom-0 left-0 z-50 w-72 bg-gradient-to-b from-[#180f16] via-[#120a10] to-[#0a0508] text-[#F2F5E2] flex-col shadow-2xl border-r border-[#D4954D]/20 overflow-hidden">
    <!-- Subtle Golden Ambient Glow Orbs -->
    <div class="absolute -top-12 -left-12 w-44 h-44 bg-[#D4954D]/15 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-16 -right-16 w-44 h-44 bg-[#D4954D]/10 rounded-full blur-3xl pointer-events-none"></div>

    <!-- Header with Monogram Logo -->
    <div class="h-20 px-6 flex items-center justify-between border-b border-white/[0.08] relative z-10 shrink-0">
        <a href="{{ route($homeRoute) }}" class="flex items-center gap-3 group">
            <x-logo on-dark size="md" loading="eager" class="transition-transform group-hover:scale-105" />
        </a>
    </div>

    <!-- Navigation List -->
    <nav class="flex-1 overflow-y-auto px-4 py-5 space-y-1.5 scrollbar-thin scrollbar-thumb-white/10 relative z-10">
        <div class="px-3 pb-2 text-[10px] font-bold tracking-[0.2em] uppercase text-[#D4954D]">
            Menu Utama
        </div>

        @foreach($menus as $item)
            @php
                $routePrefix = explode('.', $item['route'])[1] ?? '';
                $routeName = (string) Route::currentRouteName();
                $isActive = request()->routeIs($item['route'])
                    || ($routePrefix !== '' && str_starts_with($routeName, 'admin.'.$routePrefix.'.'));
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

    <!-- Footer Profile -->
    <div class="p-3 border-t border-white/[0.08] bg-black/20 relative z-10 shrink-0">
        <div class="flex items-center gap-2.5 p-2 rounded-2xl bg-white/[0.04] border border-[#D4954D]/20 backdrop-blur-sm shadow-xs">
            <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-[#D4954D] to-[#ebd2a9] text-[#1a0e05] font-serif font-black flex items-center justify-center text-xs shrink-0 shadow-md ring-2 ring-[#D4954D]/30">
                {{ substr(Auth::user()->name ?? 'Admin', 0, 1) }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-bold text-[#F2F5E2] truncate">{{ Auth::user()->name ?? 'Admin ABS' }}</p>
                <p class="text-[10px] text-[#D4954D] font-medium tracking-wide capitalize">{{ Auth::user()->role ?? 'Administrator' }}</p>
            </div>
        </div>
    </div>
</aside>
