@php
    $backUrl = \App\Support\MobileNavigation::backUrlForCurrentRoute();
    $hasActiveShift = \App\Models\CashShift::where('status', 'buka')->exists();
    $newWebOrdersCount = \App\Models\Order::where('source', 'web')->where('order_status', 'baru')->count();
    $unpaidOrdersCount = \App\Models\Order::where('payment_status', 'menunggu_pembayaran')->where('order_status', '!=', 'selesai')->count();
    $totalNotif = $newWebOrdersCount + $unpaidOrdersCount;
    $newWebOrders = \App\Models\Order::where('source', 'web')->where('order_status', 'baru')->take(5)->get();
    $unpaidOrders = \App\Models\Order::unpaid()->where('order_status', '!=', 'selesai')->take(5)->get();
@endphp

<header class="sticky top-0 z-30 min-h-[4rem] sm:min-h-[5rem] bg-[#FAFCF3]/95 backdrop-blur-md border-b border-cafe-beige/60 px-4 sm:px-8 shadow-sm transition-all" style="padding-top: env(safe-area-inset-top, 0px);">
    <div class="h-16 sm:h-20 flex items-center justify-between gap-2.5 sm:gap-4 w-full">
    <!-- Left: Mobile Logo & Title -->
    <div class="flex items-center gap-2 sm:gap-3 min-w-0">
        @if($backUrl)
            <a href="{{ $backUrl }}" class="lg:hidden flex h-10 w-10 -ml-1 shrink-0 items-center justify-center rounded-full text-cafe-dark transition-colors hover:bg-cafe-beige/60 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-cafe-gold/70" aria-label="Kembali">
                <i data-lucide="arrow-left" class="h-5 w-5"></i>
            </a>
        @else
            <a href="{{ route('kasir.dashboard') }}" class="lg:hidden shrink-0" title="ABS Cafe Beranda">
                <x-logo :show-text="false" size="sm" loading="eager" class="shrink-0" />
            </a>
        @endif

        <div class="min-w-0 flex-1 py-1">
            <h1 class="font-serif text-base sm:text-xl font-bold text-cafe-dark truncate leading-snug py-0.5" title="@yield('page_title', 'Portal Kasir')">
                @yield('page_title', 'Portal Kasir')
            </h1>
        </div>
    </div>

    <!-- Shift Status Pill -->
    <a href="{{ $hasActiveShift ? route('kasir.shift.close') : route('kasir.shift.open') }}"
       title="{{ $hasActiveShift ? 'Shift sedang berjalan — buka rekap tutup shift' : 'Shift belum dibuka — klik untuk membuka' }}"
       class="{{ $hasActiveShift ? 'bg-cafe-success/10 text-cafe-success border-cafe-success/30' : 'bg-cafe-warning/10 text-cafe-warning border-cafe-warning/30' }} px-3.5 py-2 rounded-full border text-xs font-semibold flex items-center gap-2 shrink-0">
        <span class="relative flex h-2.5 w-2.5">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-60 {{ $hasActiveShift ? 'bg-cafe-success' : 'bg-cafe-warning' }}"></span>
            <span class="relative inline-flex rounded-full h-2.5 w-2.5 {{ $hasActiveShift ? 'bg-cafe-success' : 'bg-cafe-warning' }}"></span>
        </span>
        <span class="hidden sm:inline">{{ $hasActiveShift ? 'Shift Berjalan' : 'Shift Belum Dibuka' }}</span>
        <span class="text-[10px] sm:hidden">{{ $hasActiveShift ? 'Shft' : 'Buka' }}</span>
    </a>

    <!-- Right: Notif Bell & Profile -->
    <div class="flex items-center gap-2 sm:gap-4 shrink-0">
        <!-- Notif Bell -->
        <div class="relative" id="notif-dropdown-wrapper">
            <button onclick="toggleNotifDropdown()" class="relative p-2.5 rounded-full bg-cafe-beige/50 hover:bg-cafe-beige text-cafe-dark transition-colors" title="Notifikasi Pesanan">
                <i data-lucide="bell" class="w-4 h-4"></i>
                <span id="notif-badge" class="absolute top-1 right-1 w-4 h-4 rounded-full bg-cafe-danger text-white text-[10px] font-bold flex items-center justify-center {{ $totalNotif > 0 ? 'animate-pulse' : 'hidden' }}">
                    {{ $totalNotif > 0 ? $totalNotif : '' }}
                </span>
            </button>

            <div id="notif-dropdown" class="hidden absolute right-0 mt-2 w-80 sm:w-96 bg-cafe-card rounded-2xl shadow-soft-lg border border-cafe-beige/60 p-4 z-50 animate-in fade-in">
                <div class="flex items-center justify-between pb-3 border-b border-cafe-beige/50">
                    <h4 class="font-serif font-bold text-sm text-cafe-dark">Notifikasi Pesanan</h4>
                    <span class="text-[10px] bg-cafe-gold/20 text-cafe-dark font-semibold px-2 py-0.5 rounded-full">
                        {{ $totalNotif }} Perlu Tindakan
                    </span>
                </div>

                <div class="divide-y divide-cafe-beige/30 max-h-72 overflow-y-auto my-1">
                    @forelse($unpaidOrders as $unpaid)
                        <a href="{{ route('kasir.payments.index') }}" class="p-3 hover:bg-cafe-cream/80 rounded-xl transition-colors flex items-start gap-3 block">
                            <div class="w-8 h-8 rounded-full bg-[#C98A2B]/15 text-[#C98A2B] flex items-center justify-center shrink-0">
                                <i data-lucide="clock" class="w-4 h-4"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-semibold text-cafe-dark truncate">{{ $unpaid->order_code }} &bull; Menunggu Bayar</p>
                                <p class="text-[11px] text-cafe-muted truncate">{{ $unpaid->customer_name }} &bull; {{ $unpaid->orderTypeLabel() }} &bull; Rp {{ number_format($unpaid->total, 0, ',', '.') }}</p>
                            </div>
                        </a>
                    @empty
                    @endforelse

                    @forelse($newWebOrders as $web)
                        <a href="{{ route('kasir.orders.index', ['tab' => 'web']) }}" class="p-3 hover:bg-cafe-cream/80 rounded-xl transition-colors flex items-start gap-3 block">
                            <div class="w-8 h-8 rounded-full bg-cafe-info/15 text-cafe-info flex items-center justify-center shrink-0">
                                <i data-lucide="globe" class="w-4 h-4"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-semibold text-cafe-dark truncate">[Web] {{ $web->order_code }}</p>
                                <p class="text-[11px] text-cafe-muted truncate">{{ $web->customer_name }} &bull; {{ $web->orderTypeLabel() }} &bull; Rp {{ number_format($web->total, 0, ',', '.') }}</p>
                            </div>
                        </a>
                    @empty
                    @endforelse

                    @if($totalNotif === 0)
                        <div class="py-6 text-center text-xs text-cafe-muted">
                            Tidak ada pesanan yang memerlukan tindakan.
                        </div>
                    @endif
                </div>

                <div class="pt-2 border-t border-cafe-beige/50 text-center">
                    <a href="{{ route('kasir.orders.index') }}" class="text-xs font-semibold text-cafe-brown hover:text-cafe-dark transition-colors">
                        Lihat Semua Pesanan &rarr;
                    </a>
                </div>
            </div>
        </div>

        <!-- Profile Dropdown -->
        <div class="relative" id="profile-dropdown-wrapper">
            <button type="button"
                    id="profile-dropdown-button"
                    onclick="toggleProfileDropdown()"
                    aria-expanded="false"
                    aria-haspopup="true"
                    aria-controls="profile-dropdown"
                    aria-label="Menu profil {{ Auth::user()->name ?? 'Kasir' }}"
                    class="flex min-h-[44px] min-w-[44px] items-center gap-2 p-1.5 sm:pr-3 rounded-full bg-cafe-beige/40 hover:bg-cafe-beige/70 transition-all border border-cafe-beige/60 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-cafe-gold/70">
                <div class="w-8 h-8 rounded-full bg-[#290024] text-[#D4954D] font-serif font-bold text-xs flex items-center justify-center shadow-sm shrink-0">
                    {{ substr(Auth::user()->name ?? 'K', 0, 1) }}
                </div>
                <div class="text-left hidden sm:block">
                    <span class="text-xs font-bold text-cafe-dark block leading-tight">{{ Auth::user()->name ?? 'Kasir ABS' }}</span>
                    <span class="text-[10px] text-cafe-muted block">Kasir</span>
                </div>
                <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-cafe-muted ml-0.5"></i>
            </button>

            <div id="profile-dropdown" class="hidden absolute right-0 mt-2 w-64 max-w-[calc(100vw-2rem)] bg-cafe-card rounded-2xl shadow-soft-lg border border-cafe-beige/60 p-2 z-50 animate-in fade-in" role="menu" aria-labelledby="profile-dropdown-button">
                <div class="px-3 py-2 border-b border-cafe-beige/50">
                    <p class="text-xs font-bold text-cafe-dark truncate">{{ Auth::user()->name ?? 'Kasir' }}</p>
                    <p class="text-[10px] text-cafe-muted truncate">{{ '@' . strtolower(Auth::user()->username ?? 'kasir') }}</p>
                </div>

                <div class="py-1 space-y-0.5">
                    <button type="button" onclick="alert('Penggantian kata sandi kasir dikelola oleh Administrator melalui menu Manajemen Staf.')" role="menuitem" class="w-full flex min-h-[44px] items-center gap-2.5 px-3 py-2.5 text-xs text-cafe-text hover:bg-cafe-cream rounded-xl transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-cafe-gold/70 text-left">
                        <i data-lucide="key-round" class="w-4 h-4 text-cafe-muted shrink-0"></i>
                        <span>Ganti Password</span>
                    </button>
                    <form action="{{ route('kasir.logout') }}" method="POST" data-cafe-confirm class="pt-1 border-t border-cafe-beige/40">
                        @csrf
                        <button type="submit" role="menuitem" class="w-full flex min-h-[44px] items-center gap-2.5 px-3 py-2.5 text-xs font-semibold text-cafe-danger hover:bg-cafe-danger/10 rounded-xl transition-colors text-left focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-cafe-danger/40">
                            <i data-lucide="log-out" class="w-4 h-4 shrink-0"></i>
                            <span>Keluar</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
</header>

<script>
    function toggleNotifDropdown() {
        const notif = document.getElementById('notif-dropdown');
        if (!notif) return;
        notif.classList.toggle('hidden');
        setProfileOpen(false);
    }

    function setProfileOpen(open) {
        const profile = document.getElementById('profile-dropdown');
        const btn = document.getElementById('profile-dropdown-button');
        if (!profile) return;
        if (open) {
            profile.classList.remove('hidden');
            if (btn) btn.setAttribute('aria-expanded', 'true');
        } else {
            profile.classList.add('hidden');
            if (btn) btn.setAttribute('aria-expanded', 'false');
        }
    }

    function toggleProfileDropdown() {
        const profile = document.getElementById('profile-dropdown');
        if (!profile) return;
        const willOpen = profile.classList.contains('hidden');
        document.getElementById('notif-dropdown')?.classList.add('hidden');
        setProfileOpen(willOpen);
    }

    // Close on click outside & Escape key
    document.addEventListener('click', function(e) {
        const notifWrapper = document.getElementById('notif-dropdown-wrapper');
        const profileWrapper = document.getElementById('profile-dropdown-wrapper');
        if (notifWrapper && !notifWrapper.contains(e.target)) {
            document.getElementById('notif-dropdown')?.classList.add('hidden');
        }
        if (profileWrapper && !profileWrapper.contains(e.target)) {
            setProfileOpen(false);
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' || e.key === 'Esc') {
            const profile = document.getElementById('profile-dropdown');
            if (profile && !profile.classList.contains('hidden')) {
                setProfileOpen(false);
                document.getElementById('profile-dropdown-button')?.focus();
            }
            const notif = document.getElementById('notif-dropdown');
            if (notif && !notif.classList.contains('hidden')) {
                notif.classList.add('hidden');
            }
        }
    });

    // Polling notifikasi pesanan Web baru
    let pollCounter = 0;
    setInterval(() => {
        pollCounter++;
        if (pollCounter % 3 !== 0) return;
        fetch('{{ route('kasir.notifications.pending') }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json())
            .then(d => {
                const badge = document.getElementById('notif-badge');
                const total = (d.web_new || 0) + (d.unpaid || 0);
                if (badge) {
                    if (total > 0) {
                        badge.classList.remove('hidden');
                        badge.classList.add('animate-pulse');
                        badge.textContent = total;
                    } else {
                        badge.classList.add('hidden');
                        badge.textContent = '';
                    }
                }
            })
            .catch(() => {});
    }, 10000);
</script>