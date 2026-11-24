@php
    $backUrl = \App\Support\MobileNavigation::backUrlForCurrentRoute();
    $activePenjagaName = session('active_penjaga_kasir') ?: session('active_cashier_name') ?: \App\Models\CafeSetting::get('active_penjaga_kasir');
    $activeCashierName = $activePenjagaName ?: (Auth::user()->name ?? 'Kasir');
    $newWebOrdersCount = \App\Models\Order::forKasir()->today()->where('source', 'web')->where('order_status', 'diproses')->count();
    $unpaidOrdersCount = \App\Models\Order::forKasir()->today()->where('payment_status', 'menunggu_pembayaran')->where('order_status', '!=', 'selesai')->count();
    $totalNotif = $newWebOrdersCount + $unpaidOrdersCount;
    $newWebOrders = \App\Models\Order::forKasir()->today()->where('source', 'web')->where('order_status', 'diproses')->take(5)->get();
    $unpaidOrders = \App\Models\Order::forKasir()->today()->unpaid()->where('order_status', '!=', 'selesai')->take(5)->get();
    $staffCashierList = \App\Models\User::where('status', 'aktif')->whereIn('role', ['kasir', 'staf', 'admin'])->orderBy('name')->pluck('name');
@endphp

<header class="sticky top-0 z-30 bg-[#F2F5E2]/95 backdrop-blur-md border-b border-cafe-beige/60 shadow-sm transition-all" style="padding-top: env(safe-area-inset-top, 0px);">
    <div class="h-14 sm:h-16 landscape:h-12 sm:landscape:h-14 flex items-center justify-between gap-2 sm:gap-4 px-3 sm:px-6 lg:px-8 w-full">

        {{-- ═══ LEFT: Back / Logo + Page Title ═══ --}}
        <div class="flex items-center gap-2 sm:gap-2.5 min-w-0 flex-1">
            @if($backUrl)
                <a href="{{ $backUrl }}"
                   class="lg:hidden flex h-8 w-8 sm:h-9 sm:w-9 shrink-0 items-center justify-center rounded-xl text-cafe-dark transition-colors hover:bg-cafe-beige/60 active:scale-95"
                   aria-label="Kembali">
                    <i data-lucide="arrow-left" class="h-[18px] w-[18px]"></i>
                </a>
            @else
                <a href="{{ route('kasir.dashboard') }}" class="lg:hidden shrink-0" title="ABS Cafe Beranda">
                    <x-logo :show-text="false" size="sm" loading="eager" class="shrink-0" />
                </a>
            @endif

            <div class="min-w-0">
                <h1 class="font-serif text-sm sm:text-base landscape:text-sm font-bold text-cafe-dark truncate leading-tight">
                    @yield('page_title', 'Portal Kasir')
                </h1>
            </div>
        </div>

        {{-- ═══ RIGHT: Action Pills & Icons ═══ --}}
        <div class="flex items-center gap-1.5 sm:gap-3 shrink-0">

            {{-- Realtime Clock (Sesuai Komputer Pengguna) --}}
            <div class="hidden sm:flex items-center gap-2 px-3 sm:px-3.5 py-1.5 rounded-full bg-[#E3DEA4]/70 border border-[#D4954D]/40 text-[#290024] shadow-xs text-xs font-semibold select-none" title="Waktu Komputer Lokal Realtime">
                <i data-lucide="clock" class="w-3.5 h-3.5 text-[#D4954D] shrink-0 animate-pulse"></i>
                <span class="realtime-clock-display font-mono tracking-wide font-bold">--:--:--</span>

            {{-- Kasir Bertugas Widget --}}
            <div class="relative" id="cashier-switch-wrapper">
                <button type="button"
                        onclick="toggleCashierDropdown()"
                        title="Kasir bertugas: {{ $activeCashierName }} — klik untuk ganti"
                        class="flex items-center gap-1.5 px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-full border bg-cafe-success/15 text-cafe-success border-cafe-success/30 text-[11px] sm:text-xs font-semibold transition-all hover:scale-105 active:scale-95 shrink-0">
                    <span class="relative flex h-2 w-2 sm:h-2.5 sm:w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-60 bg-cafe-success"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 sm:h-2.5 sm:w-2.5 bg-cafe-success"></span>
                    </span>
                    <span class="hidden sm:inline max-w-[90px] truncate">{{ $activeCashierName }}</span>
                    <span class="text-[10px] sm:hidden">Kasir</span>
                    <i data-lucide="chevron-down" class="w-3 h-3 hidden sm:block"></i>
                </button>

                {{-- Dropdown Ganti Kasir --}}
                <div id="cashier-switch-dropdown"
                     class="hidden absolute right-0 mt-2 w-64 bg-[#F2F5E2] rounded-2xl shadow-soft-lg border border-[#E3DEA4] z-50 overflow-hidden">
                    <div class="px-4 py-3 border-b border-cafe-beige/40 bg-gradient-to-r from-cafe-dark/[0.03] to-transparent">
                        <p class="text-xs font-bold text-cafe-dark flex items-center gap-2">
                            <i data-lucide="user-check" class="w-4 h-4 text-cafe-gold"></i>
                            Ganti Kasir Bertugas
                        </p>
                        <p class="text-[10px] text-cafe-muted mt-0.5">Pilih nama, tanpa perlu logout</p>
                    </div>
                    <form method="POST" action="{{ route('kasir.switch_cashier') }}" class="p-2 space-y-1 max-h-56 overflow-y-auto">
                        @csrf
                        @foreach($staffCashierList as $staffName)
                            <button type="submit" name="cashier_name" value="{{ $staffName }}"
                                    class="w-full text-left px-3 py-2.5 rounded-xl text-xs text-cafe-text hover:bg-cafe-cream transition-colors flex items-center gap-2 {{ $activeCashierName === $staffName ? 'bg-cafe-success/15 font-semibold text-cafe-success' : '' }}">
                                @if($activeCashierName === $staffName)
                                    <i data-lucide="check-circle" class="w-4 h-4 shrink-0"></i>
                                @else
                                    <i data-lucide="user" class="w-4 h-4 shrink-0 text-cafe-muted"></i>
                                @endif
                                {{ $staffName }}
                            </button>
                        @endforeach
                        @if($staffCashierList->isEmpty())
                            <p class="text-xs text-cafe-muted px-3 py-4 text-center">Belum ada staf aktif. Tambah di Admin → Manajemen Staf.</p>
                        @endif
                    </form>
                </div>
            </div>

            {{-- Notification Bell --}}
            <div class="relative" id="notif-dropdown-wrapper">
                <button onclick="toggleNotifDropdown()"
                        class="relative flex items-center justify-center h-8 w-8 sm:h-9 sm:w-9 rounded-xl bg-cafe-beige/40 hover:bg-cafe-beige/70 text-cafe-dark transition-all active:scale-95"
                        title="Notifikasi Pesanan">
                    <i data-lucide="bell" class="w-[18px] h-[18px]"></i>
                    <span id="notif-badge"
                          class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] rounded-full bg-cafe-danger text-beige text-[10px] font-bold flex items-center justify-center px-1 ring-2 ring-[#F2F5E2]
                                 {{ $totalNotif > 0 ? 'animate-pulse' : 'hidden' }}">
                        {{ $totalNotif > 0 ? $totalNotif : '' }}
                    </span>
                </button>

                {{-- Notification Dropdown --}}
                <div id="notif-dropdown" class="hidden absolute right-0 mt-2.5 w-80 sm:w-96 max-w-[calc(100vw-2rem)] bg-[#F2F5E2] rounded-2xl shadow-soft-lg border border-[#E3DEA4] z-50 overflow-hidden">
                    <div class="flex items-center justify-between px-4 py-3 bg-gradient-to-r from-cafe-dark/[0.03] to-transparent border-b border-cafe-beige/40">
                        <h4 class="font-serif font-bold text-sm text-cafe-dark flex items-center gap-2">
                            <i data-lucide="bell-ring" class="w-4 h-4 text-cafe-gold"></i>
                            Notifikasi
                        </h4>
                        <span class="text-[10px] bg-cafe-gold/20 text-cafe-dark font-semibold px-2 py-0.5 rounded-full">
                            {{ $totalNotif }} aktif
                        </span>
                    </div>

                    <div class="divide-y divide-cafe-beige/30 max-h-72 overflow-y-auto">
                        @forelse($unpaidOrders as $unpaid)
                            <a href="{{ route('kasir.payments.index') }}" class="p-3 hover:bg-cafe-cream/80 transition-colors flex items-start gap-3 block">
                                <div class="w-8 h-8 rounded-xl bg-[#D4954D]/15 text-[#D4954D] flex items-center justify-center shrink-0">
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
                            <a href="{{ route('kasir.orders.index', ['tab' => 'web']) }}" class="p-3 hover:bg-cafe-cream/80 transition-colors flex items-start gap-3 block">
                                <div class="w-8 h-8 rounded-xl bg-cafe-info/15 text-cafe-info flex items-center justify-center shrink-0">
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
                            <div class="py-8 text-center">
                                <i data-lucide="inbox" class="w-8 h-8 text-cafe-beige mx-auto mb-2"></i>
                                <p class="text-xs text-cafe-muted">Tidak ada notifikasi baru</p>
                            </div>
                        @endif
                    </div>

                    <div class="px-4 py-2.5 border-t border-cafe-beige/40 bg-gradient-to-r from-cafe-dark/[0.02] to-transparent">
                        <a href="{{ route('kasir.orders.index') }}" class="flex items-center justify-center gap-1.5 text-xs font-semibold text-cafe-brown hover:text-cafe-dark transition-colors">
                            Lihat Semua Pesanan
                            <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
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
                        <span class="text-xs font-bold text-cafe-dark block leading-tight">{{ $activeCashierName }}</span>
                        <span class="text-[10px] text-cafe-muted block">Kasir Bertugas</span>
                    </div>
                    <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-cafe-muted ml-0.5"></i>
                </button>

                <div id="profile-dropdown" class="hidden absolute right-0 mt-2 w-64 max-w-[calc(100vw-2rem)] bg-[#F2F5E2] rounded-2xl shadow-soft-lg border border-[#E3DEA4] p-2 z-50 animate-in fade-in" role="menu" aria-labelledby="profile-dropdown-button">
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
        setCashierSwitchOpen(false);
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

    function setCashierSwitchOpen(open) {
        const dd = document.getElementById('cashier-switch-dropdown');
        if (!dd) return;
        if (open) { dd.classList.remove('hidden'); } else { dd.classList.add('hidden'); }
    }

    function toggleProfileDropdown() {
        const profile = document.getElementById('profile-dropdown');
        if (!profile) return;
        const willOpen = profile.classList.contains('hidden');
        document.getElementById('notif-dropdown')?.classList.add('hidden');
        setCashierSwitchOpen(false);
        setProfileOpen(willOpen);
    }

    function toggleCashierDropdown() {
        const dd = document.getElementById('cashier-switch-dropdown');
        if (!dd) return;
        const willOpen = dd.classList.contains('hidden');
        document.getElementById('notif-dropdown')?.classList.add('hidden');
        setProfileOpen(false);
        setCashierSwitchOpen(willOpen);
    }

    // Close on click outside & Escape key
    document.addEventListener('click', function(e) {
        const notifWrapper = document.getElementById('notif-dropdown-wrapper');
        const profileWrapper = document.getElementById('profile-dropdown-wrapper');
        const cashierWrapper = document.getElementById('cashier-switch-wrapper');
        if (notifWrapper && !notifWrapper.contains(e.target)) {
            document.getElementById('notif-dropdown')?.classList.add('hidden');
        }
        if (profileWrapper && !profileWrapper.contains(e.target)) {
            setProfileOpen(false);
        }
        if (cashierWrapper && !cashierWrapper.contains(e.target)) {
            setCashierSwitchOpen(false);
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
            setCashierSwitchOpen(false);
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

    // Realtime Computer Clock Script
    (function () {
        function updateClock() {
            const now = new Date();
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            const dayName = days[now.getDay()];
            const date = String(now.getDate()).padStart(2, '0');
            const monthName = months[now.getMonth()];
            const year = now.getFullYear();
            const h = String(now.getHours()).padStart(2, '0');
            const m = String(now.getMinutes()).padStart(2, '0');
            const s = String(now.getSeconds()).padStart(2, '0');
            const str = `${dayName}, ${date} ${monthName} ${year} • ${h}:${m}:${s}`;
            document.querySelectorAll('.realtime-clock-display').forEach(function (el) {
                el.textContent = str;
            });
        }
        updateClock();
        setInterval(updateClock, 1000);
    })();
</script>
