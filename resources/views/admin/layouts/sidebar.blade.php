@php
    $currentRoute = Route::currentRouteName();
    $pendingOrdersCount = \App\Models\Order::where('payment_status', 'menunggu_pembayaran')->count();
    $lowStockCount = \App\Models\Menu::whereColumn('stock', '<=', 'min_stock')->count();

    $menus = [
        ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'layout-dashboard', 'badge' => null],
        ['route' => 'admin.orders.index', 'label' => 'Pesanan', 'icon' => 'shopping-cart', 'badge' => $pendingOrdersCount],
        ['route' => 'admin.menu.index', 'label' => 'Menu', 'icon' => 'coffee', 'badge' => null],
        ['route' => 'admin.category.index', 'label' => 'Kategori', 'icon' => 'tags', 'badge' => null],
        ['route' => 'admin.stock.index', 'label' => 'Stok', 'icon' => 'boxes', 'badge' => $lowStockCount],
        ['route' => 'admin.promo.index', 'label' => 'Promo', 'icon' => 'percent', 'badge' => null],
        ['route' => 'admin.users.index', 'label' => 'Pengguna (Staf)', 'icon' => 'users', 'badge' => null],
        ['route' => 'admin.tables.index', 'label' => 'Meja', 'icon' => 'grid-3x3', 'badge' => null],
        ['route' => 'admin.settings.index', 'label' => 'Pengaturan Cafe', 'icon' => 'settings', 'badge' => null],
        ['route' => 'admin.logs.index', 'label' => 'Log Aktivitas', 'icon' => 'history', 'badge' => null],
    ];
@endphp

<aside id="admin-sidebar" class="fixed top-0 bottom-0 left-0 z-50 w-72 bg-[#4A1F14] text-[#F6EDE0] flex flex-col transition-transform duration-300 ease-in-out shadow-2xl lg:shadow-none -translate-x-full lg:translate-x-0">
    <!-- Header with Monogram Logo -->
    <div class="h-20 px-6 flex items-center justify-between border-b border-white/10 shrink-0">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 group">
            <!-- Monogram circle -->
            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#D4A76A] via-[#C59253] to-[#7A4A2B] p-0.5 shadow-sm shrink-0">
                <div class="w-full h-full rounded-full bg-[#4A1F14] flex items-center justify-center border border-[#D4A76A]/40 group-hover:scale-105 transition-transform">
                    <span class="font-serif font-black tracking-tighter text-xs text-[#D4A76A]">ABS</span>
                </div>
            </div>
            <!-- Title and Tagline -->
            <div class="flex flex-col">
                <span class="font-serif font-bold text-lg tracking-tight leading-none text-[#F6EDE0] group-hover:text-cafe-gold transition-colors">ABS Cafe</span>
                <span class="text-[10px] tracking-widest uppercase font-medium text-[#D4A76A] mt-0.5">Accessories & Cafe</span>
            </div>
        </a>

        <button onclick="toggleSidebar()" class="lg:hidden w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-[#F6EDE0] flex items-center justify-center transition-colors">
            <i data-lucide="x" class="w-5 h-5"></i>
        </button>
    </div>

    <!-- Navigation List (10 Menu Navigasi) -->
    <nav class="flex-1 overflow-y-auto px-4 py-5 space-y-1.5 scrollbar-thin scrollbar-thumb-white/10">
        <div class="px-3 pb-2 text-[10px] font-semibold tracking-wider uppercase text-[#D4A76A]/80">
            Menu Utama
        </div>

        @foreach($menus as $item)
            @php
                $isActive = request()->routeIs($item['route']) || (str_contains($item['route'], 'index') && str_contains($currentRoute, explode('.', $item['route'])[1]));
            @endphp
            <a href="{{ route($item['route']) }}" class="group flex items-center justify-between px-3.5 py-3 rounded-2xl text-xs font-medium transition-all duration-200 {{ $isActive ? 'bg-[#D4A76A] text-[#4A1F14] font-semibold shadow-md translate-x-1' : 'text-[#F6EDE0]/85 hover:bg-white/10 hover:text-[#F6EDE0]' }}">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 transition-colors {{ $isActive ? 'bg-[#4A1F14]/15 text-[#4A1F14]' : 'text-[#D4A76A] group-hover:scale-110' }}">
                        <i data-lucide="{{ $item['icon'] }}" class="w-4 h-4 stroke-[2.2]"></i>
                    </div>
                    <span class="truncate">{{ $item['label'] }}</span>
                </div>

                <div class="flex items-center gap-1.5 shrink-0">
                    @if($item['badge'] && $item['badge'] > 0)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold shadow-sm {{ $isActive ? 'bg-[#4A1F14] text-[#D4A76A]' : 'bg-[#D4A76A] text-[#4A1F14]' }}">
                            {{ $item['badge'] }}
                        </span>
                    @endif
                    @if($isActive)
                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-[#4A1F14]/70"></i>
                    @endif
                </div>
            </a>
        @endforeach
    </nav>

    <!-- Footer Profile & Logout -->
    <div class="p-4 border-t border-white/10 bg-[#3B1E14]/50 shrink-0">
        <div class="flex items-center justify-between p-2 rounded-2xl bg-white/5">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-9 h-9 rounded-full bg-[#D4A76A] text-[#4A1F14] font-serif font-bold flex items-center justify-center text-xs shrink-0 shadow">
                    {{ substr(Auth::user()->name ?? 'Admin', 0, 1) }}
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-[#F6EDE0] truncate">{{ Auth::user()->name ?? 'Admin ABS' }}</p>
                    <p class="text-[10px] text-[#D4A76A] capitalize">{{ Auth::user()->role ?? 'Administrator' }}</p>
                </div>
            </div>

            <form action="{{ route('admin.logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" title="Keluar" class="p-2 rounded-xl text-white/70 hover:text-white hover:bg-white/10 transition-colors">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                </button>
            </form>
        </div>
    </div>
</aside>
