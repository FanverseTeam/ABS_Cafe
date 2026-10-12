@php
    $pendingOrders = \App\Models\Order::where('payment_status', 'menunggu_pembayaran')->take(5)->get();
    $lowStockItems = \App\Models\Menu::whereColumn('stock', '<=', 'min_stock')->take(5)->get();
    $totalNotif = $pendingOrders->count() + $lowStockItems->count();
@endphp

<header class="sticky top-0 z-30 h-20 bg-[#FAFCF3]/90 backdrop-blur-md border-b border-cafe-beige/60 px-4 sm:px-8 flex items-center justify-between gap-4">
    <!-- Left: Hamburger & Title -->
    <div class="flex items-center gap-3 min-w-0">
        <button onclick="toggleSidebar()" class="lg:hidden p-2 rounded-2xl bg-cafe-beige/60 hover:bg-cafe-beige text-cafe-dark transition-colors" title="Buka Menu">
            <i data-lucide="menu" class="w-5 h-5"></i>
        </button>

        <div class="hidden sm:block">
            <h1 class="font-serif text-xl font-bold text-cafe-dark truncate">
                @yield('page_title', 'Dashboard Back-Office')
            </h1>
        </div>
    </div>

    <!-- Center: Search -->
    <div class="flex-1 max-w-md hidden md:block">
        <form action="{{ route('admin.orders.index') }}" method="GET" class="relative">
            <i data-lucide="search" class="w-4 h-4 text-cafe-muted absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none"></i>
            <input type="text" name="search" placeholder="Cari pesanan, menu, atau meja... (tekan Enter)" class="w-full bg-[#E3DEA4]/50 focus:bg-white text-xs text-cafe-text placeholder:text-cafe-muted/70 pl-10 pr-4 py-2.5 rounded-full border border-cafe-beige/80 focus:border-cafe-gold focus:outline-none focus:ring-2 focus:ring-cafe-gold/20 transition-all">
        </form>
    </div>

    <!-- Right: Notification Bell & Profile Dropdown -->
    <div class="flex items-center gap-2 sm:gap-4 shrink-0">
        <!-- Notif Bell -->
        <div class="relative" id="notif-dropdown-wrapper">
            <button onclick="toggleNotifDropdown()" class="relative p-2.5 rounded-full bg-cafe-beige/50 hover:bg-cafe-beige text-cafe-dark transition-colors" title="Notifikasi">
                <i data-lucide="bell" class="w-4 h-4"></i>
                @if($totalNotif > 0)
                    <span class="absolute top-1 right-1 w-4 h-4 rounded-full bg-cafe-danger text-white text-[10px] font-bold flex items-center justify-center animate-pulse">
                        {{ $totalNotif }}
                    </span>
                @endif
            </button>

            <!-- Notif Dropdown Menu -->
            <div id="notif-dropdown" class="hidden absolute right-0 mt-2 w-80 sm:w-96 bg-cafe-card rounded-2xl shadow-soft-lg border border-cafe-beige/60 p-4 z-50 animate-in fade-in">
                <div class="flex items-center justify-between pb-3 border-b border-cafe-beige/50">
                    <h4 class="font-serif font-bold text-sm text-cafe-dark">Pemberitahuan Sistem</h4>
                    <span class="text-[10px] bg-cafe-gold/20 text-cafe-dark font-semibold px-2 py-0.5 rounded-full">
                        {{ $totalNotif }} Mendesak
                    </span>
                </div>

                <div class="divide-y divide-cafe-beige/30 max-h-72 overflow-y-auto my-1">
                    @forelse($pendingOrders as $pOrd)
                        <a href="{{ route('admin.orders.index', ['payment_status' => 'menunggu_pembayaran']) }}" class="p-3 hover:bg-cafe-cream/80 rounded-xl transition-colors flex items-start gap-3 block">
                            <div class="w-8 h-8 rounded-full bg-[#C98A2B]/15 text-[#C98A2B] flex items-center justify-center shrink-0">
                                <i data-lucide="clock" class="w-4 h-4"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-semibold text-cafe-dark truncate">Pesanan {{ $pOrd->order_code }}</p>
                                <p class="text-[11px] text-cafe-muted truncate">{{ $pOrd->customer_name }} &bull; Rp {{ number_format($pOrd->total, 0, ',', '.') }}</p>
                                <p class="text-[10px] text-cafe-gold font-medium mt-0.5">Menunggu Pembayaran</p>
                            </div>
                        </a>
                    @empty
                    @endforelse

                    @forelse($lowStockItems as $lStk)
                        <a href="{{ route('admin.stock.index') }}" class="p-3 hover:bg-cafe-cream/80 rounded-xl transition-colors flex items-start gap-3 block">
                            <div class="w-8 h-8 rounded-full bg-cafe-danger/15 text-cafe-danger flex items-center justify-center shrink-0">
                                <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-semibold text-cafe-dark truncate">Stok {{ $lStk->stock <= 0 ? 'Habis' : 'Menipis' }}: {{ $lStk->name }}</p>
                                <p class="text-[11px] text-cafe-muted">Sisa {{ $lStk->stock }} {{ $lStk->unit }} (Min: {{ $lStk->min_stock }})</p>
                            </div>
                        </a>
                    @empty
                    @endforelse

                    @if($totalNotif === 0)
                        <div class="py-6 text-center text-xs text-cafe-muted">
                            Tidak ada notifikasi mendesak saat ini.
                        </div>
                    @endif
                </div>

                <div class="pt-2 border-t border-cafe-beige/50 text-center">
                    <a href="{{ route('admin.logs.index') }}" class="text-xs font-semibold text-cafe-brown hover:text-cafe-dark transition-colors">
                        Lihat Semua Log Aktivitas &rarr;
                    </a>
                </div>
            </div>
        </div>

        <!-- Profile Dropdown -->
        <div class="relative" id="profile-dropdown-wrapper">
            <button onclick="toggleProfileDropdown()" class="flex items-center gap-2 p-1.5 pr-3 rounded-full bg-cafe-beige/40 hover:bg-cafe-beige/70 transition-all border border-cafe-beige/60">
                <div class="w-8 h-8 rounded-full bg-[#290024] text-[#D4954D] font-serif font-bold text-xs flex items-center justify-center shadow-sm">
                    {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                </div>
                <div class="text-left hidden sm:block">
                    <span class="text-xs font-bold text-cafe-dark block leading-tight">{{ Auth::user()->name ?? 'Admin ABS' }}</span>
                    <span class="text-[10px] text-cafe-muted block">Administrator</span>
                </div>
                <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-cafe-muted ml-0.5"></i>
            </button>

            <!-- Profile Menu -->
            <div id="profile-dropdown" class="hidden absolute right-0 mt-2 w-56 bg-cafe-card rounded-2xl shadow-soft-lg border border-cafe-beige/60 p-2 z-50 animate-in fade-in">
                <div class="px-3 py-2 border-b border-cafe-beige/50">
                    <p class="text-xs font-bold text-cafe-dark truncate">{{ Auth::user()->name ?? 'Admin' }}</p>
                    <p class="text-[10px] text-cafe-muted truncate">{{ Auth::user()->email ?? 'admin@abscafe.com' }}</p>
                </div>

                <div class="py-1 space-y-0.5">
                    <a href="{{ route('admin.settings.index', ['tab' => 'profil']) }}" class="flex items-center gap-2.5 px-3 py-2 text-xs text-cafe-text hover:bg-cafe-cream rounded-xl transition-colors">
                        <i data-lucide="user" class="w-4 h-4 text-cafe-muted"></i>
                        <span>Profil Saya</span>
                    </a>
                    <a href="{{ route('admin.settings.index', ['tab' => 'akun']) }}" class="flex items-center gap-2.5 px-3 py-2 text-xs text-cafe-text hover:bg-cafe-cream rounded-xl transition-colors">
                        <i data-lucide="key-round" class="w-4 h-4 text-cafe-muted"></i>
                        <span>Ganti Password</span>
                    </a>
                    <form action="{{ route('admin.logout') }}" method="POST" data-cafe-confirm class="pt-1 border-t border-cafe-beige/40">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2 text-xs text-cafe-danger hover:bg-cafe-danger/10 rounded-xl transition-colors text-left">
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                            <span>Keluar</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
    function toggleNotifDropdown() {
        const notif = document.getElementById('notif-dropdown');
        notif.classList.toggle('hidden');
        document.getElementById('profile-dropdown').classList.add('hidden');
    }
    function toggleProfileDropdown() {
        const profile = document.getElementById('profile-dropdown');
        profile.classList.toggle('hidden');
        document.getElementById('notif-dropdown').classList.add('hidden');
    }
    // Close on click outside
    document.addEventListener('click', function(e) {
        const notifWrapper = document.getElementById('notif-dropdown-wrapper');
        const profileWrapper = document.getElementById('profile-dropdown-wrapper');
        if (notifWrapper && !notifWrapper.contains(e.target)) {
            document.getElementById('notif-dropdown').classList.add('hidden');
        }
        if (profileWrapper && !profileWrapper.contains(e.target)) {
            document.getElementById('profile-dropdown').classList.add('hidden');
        }
    });
</script>
