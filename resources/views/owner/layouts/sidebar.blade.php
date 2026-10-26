@php
    $currentRoute = Route::currentRouteName();
    $activePromoCount = \App\Models\Promo::where('is_active', true)
        ->whereDate('start_date', '<=', now())
        ->whereDate('end_date', '>=', now())
        ->count();
    $lowStockCount = \App\Models\Menu::whereNotNull('min_stock')->whereColumn('stock', '<=', 'min_stock')->count();

    $menus = [
        ['route' => 'owner.dashboard', 'label' => 'Dashboard Bisnis', 'icon' => 'layout-dashboard', 'badge' => null, 'match' => 'owner.dashboard'],
        ['route' => 'owner.reports.index', 'label' => 'Laporan', 'icon' => 'bar-chart-3', 'badge' => null, 'match' => 'owner.reports'],
        ['route' => 'owner.cashiers.index', 'label' => 'Kinerja Kasir', 'icon' => 'users', 'badge' => null, 'match' => 'owner.cashiers'],
        ['route' => 'owner.promos.index', 'label' => 'Promo & Diskon', 'icon' => 'percent', 'badge' => $activePromoCount, 'match' => 'owner.promos'],
        ['route' => 'owner.settings.index', 'label' => 'Pengaturan Bisnis', 'icon' => 'briefcase', 'badge' => null, 'match' => 'owner.settings'],
    ];
@endphp

<aside id="owner-sidebar" class="hidden lg:flex fixed top-0 bottom-0 left-0 z-50 w-72 bg-[#290024] text-[#F2F5E2] flex-col shadow-2xl">
    <!-- Header with Monogram Logo -->
    <div class="h-20 px-6 flex items-center justify-between border-b border-beige/10 shrink-0">
        <a href="{{ route('owner.dashboard') }}" class="flex items-center gap-3 group">
            <x-logo on-dark size="md" subtitle="Owner Portal" loading="eager" class="transition-transform group-hover:scale-105" />
        </a>
    </div>

    <!-- Navigation List -->
    <nav class="flex-1 overflow-y-auto px-4 py-5 space-y-1.5">
        <div class="px-3 pb-2 text-[10px] font-semibold tracking-wider uppercase text-[#D4954D]/80">
            Analisis Bisnis
        </div>

        @foreach($menus as $item)
            @php
                $isActive = str_starts_with((string) $currentRoute, $item['match']);
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

        <div class="pt-4">
            <div class="px-3 pb-2 text-[10px] font-semibold tracking-wider uppercase text-[#D4954D]/80">
                Ringkasan
            </div>
            <div class="px-3.5 py-3 rounded-2xl bg-beige/5 text-[11px] space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[#F2F5E2]/70">Promo Aktif</span>
                    <span class="font-bold text-[#D4954D]">{{ $activePromoCount }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-[#F2F5E2]/70">Stok Menipis</span>
                    <span class="font-bold {{ $lowStockCount > 0 ? 'text-[#D4954D]' : 'text-[#D4954D]' }}">{{ $lowStockCount }}</span>
                </div>
            </div>
        </div>
    </nav>

    <!-- Footer Profile -->
    <div class="p-4 border-t border-beige/10 bg-[#290024]/50 shrink-0">
        <div class="flex items-center gap-2.5 p-2 rounded-2xl bg-beige/5">
            <div class="w-9 h-9 rounded-full bg-[#D4954D] text-[#290024] font-serif font-bold flex items-center justify-center text-xs shrink-0 shadow">
                {{ Auth::user()->initials() ?? 'O' }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-semibold text-[#F2F5E2] truncate">{{ Auth::user()->name ?? 'Owner ABS' }}</p>
                <p class="text-[10px] text-[#D4954D]">Pemilik Usaha</p>
            </div>
        </div>
    </div>
</aside>
