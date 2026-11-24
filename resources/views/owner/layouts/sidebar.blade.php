@php
    $currentRoute = Route::currentRouteName();
    $activePromoCount = \App\Models\Promo::where('is_active', true)
        ->whereDate('start_date', '<=', now())
        ->whereDate('end_date', '>=', now())
        ->count();
    $menuHabisCount = \App\Models\Menu::where('is_available', false)->count();

    $menus = [
        ['route' => 'owner.dashboard', 'label' => 'Dashboard Bisnis', 'icon' => 'layout-dashboard', 'badge' => null, 'match' => 'owner.dashboard'],
        ['route' => 'owner.reports.index', 'label' => 'Laporan', 'icon' => 'bar-chart-3', 'badge' => null, 'match' => 'owner.reports'],
        ['route' => 'owner.staff.index', 'label' => 'Manajemen Staf', 'icon' => 'users', 'badge' => null, 'match' => 'owner.staff'],
        ['route' => 'owner.promos.index', 'label' => 'Promo & Diskon', 'icon' => 'percent', 'badge' => $activePromoCount, 'match' => 'owner.promos'],
    ];
@endphp

<aside id="owner-sidebar" class="hidden lg:flex fixed top-0 bottom-0 left-0 z-50 w-72 bg-gradient-to-b from-[#180f16] via-[#120a10] to-[#0a0508] text-[#F2F5E2] flex-col shadow-2xl border-r border-[#D4954D]/20 overflow-hidden">
    <!-- Subtle Golden Ambient Glow Orbs -->
    <div class="absolute -top-12 -left-12 w-44 h-44 bg-[#D4954D]/15 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-16 -right-16 w-44 h-44 bg-[#D4954D]/10 rounded-full blur-3xl pointer-events-none"></div>

    <!-- Header with Monogram Logo -->
    <div class="h-20 px-6 flex items-center justify-between border-b border-white/[0.08] relative z-10 shrink-0">
        <a href="{{ route('owner.dashboard') }}" class="flex items-center gap-3 group">
            <x-logo on-dark size="md" subtitle="Owner Portal" loading="eager" class="transition-transform group-hover:scale-105" />
        </a>
    </div>

    <!-- Navigation List -->
    <nav class="flex-1 overflow-y-auto px-4 py-5 space-y-1.5 scrollbar-thin scrollbar-thumb-white/10 relative z-10">
        <div class="px-3 pb-2 text-[10px] font-bold tracking-[0.2em] uppercase text-[#D4954D]">
            Analisis Bisnis
        </div>

        @foreach($menus as $item)
            @php
                $isActive = str_starts_with((string) $currentRoute, $item['match']);
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

        <div class="pt-4">
            <div class="px-3 pb-2 text-[10px] font-bold tracking-[0.2em] uppercase text-[#D4954D]">
                Ringkasan
            </div>
            <div class="px-3.5 py-3 rounded-2xl bg-white/[0.04] border border-[#D4954D]/15 text-[11px] space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[#F2F5E2]/75">Promo Aktif</span>
                    <span class="font-bold text-[#D4954D]">{{ $activePromoCount }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-[#F2F5E2]/75">Menu Habis</span>
                    <span class="font-bold text-[#D4954D]">{{ $menuHabisCount }}</span>
                </div>
            </div>
        </div>
    </nav>

    <!-- Footer Profile -->
    <div class="p-3 border-t border-white/[0.08] bg-black/20 relative z-10 shrink-0">
        <div class="flex items-center gap-2.5 p-2 rounded-2xl bg-white/[0.04] border border-[#D4954D]/20 backdrop-blur-sm shadow-xs">
            <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-[#D4954D] to-[#ebd2a9] text-[#1a0e05] font-serif font-black flex items-center justify-center text-xs shrink-0 shadow-md ring-2 ring-[#D4954D]/30">
                {{ Auth::user()->initials() ?? 'O' }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-bold text-[#F2F5E2] truncate">{{ Auth::user()->name ?? 'Owner ABS' }}</p>
                <p class="text-[10px] text-[#D4954D] font-medium tracking-wide">Pemilik Usaha</p>
            </div>
        </div>
    </div>
</aside>
