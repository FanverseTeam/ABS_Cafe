@php
    $backUrl = \App\Support\MobileNavigation::backUrlForCurrentRoute();
    $hasActiveShift = \App\Models\CashShift::where('status', 'buka')->exists();
    $newWebOrdersCount = \App\Models\Order::where('source', 'web')->where('order_status', 'baru')->count();
    $unpaidOrdersCount = \App\Models\Order::where('payment_status', 'menunggu_pembayaran')->where('order_status', '!=', 'selesai')->count();
    $totalNotif = $newWebOrdersCount + $unpaidOrdersCount;
    $newWebOrders = \App\Models\Order::where('source', 'web')->where('order_status', 'baru')->take(5)->get();
    $unpaidOrders = \App\Models\Order::unpaid()->where('order_status', '!=', 'selesai')->take(5)->get();

@endphp

<header class="sticky top-0 z-30 bg-[#FAFCF3]/95 backdrop-blur-md border-b border-cafe-beige/60 shadow-sm transition-all" style="padding-top: env(safe-area-inset-top, 0px);">
    <div class="h-14 sm:h-16 landscape:h-12 sm:landscape:h-14 flex items-center justify-between gap-3 px-3 sm:px-6 lg:px-8 w-full">

        {{-- ═══ LEFT: Logo / Back + Page Title ═══ --}}
        <div class="flex items-center gap-2.5 min-w-0 flex-1">
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
        <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">

            {{-- Notification Bell --}}
            <div class="relative" id="notif-dropdown-wrapper">
                <button onclick="toggleNotifDropdown()"
                        class="relative flex items-center justify-center h-8 w-8 sm:h-9 sm:w-9 rounded-xl bg-cafe-beige/40 hover:bg-cafe-beige/70 text-cafe-dark transition-all active:scale-95"
                        title="Notifikasi Pesanan">
                    <i data-lucide="bell" class="w-[18px] h-[18px]"></i>
                    <span id="notif-badge"
                          class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] rounded-full bg-cafe-danger text-white text-[10px] font-bold flex items-center justify-center px-1 ring-2 ring-[#FAFCF3]
                                 {{ $totalNotif > 0 ? 'animate-pulse' : 'hidden' }}">
                        {{ $totalNotif > 0 ? $totalNotif : '' }}
                    </span>
                </button>

                {{-- Notification Dropdown --}}
                <div id="notif-dropdown" class="hidden absolute right-0 mt-2.5 w-80 sm:w-96 bg-cafe-card rounded-2xl shadow-soft-lg border border-cafe-beige/60 z-50 overflow-hidden">
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
                                <div class="w-8 h-8 rounded-xl bg-[#C98A2B]/15 text-[#C98A2B] flex items-center justify-center shrink-0">
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

            {{-- Profile Avatar & Dropdown --}}
            <div class="relative" id="profile-dropdown-wrapper">
                <button onclick="toggleProfileDropdown()"
                        class="flex items-center gap-2 h-8 sm:h-9 pl-1 pr-1 sm:pr-2.5 rounded-xl bg-cafe-beige/40 hover:bg-cafe-beige/70 transition-all border border-cafe-beige/50 active:scale-95">
                    <div class="w-7 h-7 sm:w-7 sm:h-7 rounded-lg bg-cafe-dark text-cafe-gold font-serif font-bold text-[11px] flex items-center justify-center">
                        {{ substr(Auth::user()->name ?? 'K', 0, 1) }}
                    </div>
                    <div class="text-left hidden sm:block">
                        <span class="text-[11px] font-semibold text-cafe-dark block leading-tight truncate max-w-[7rem]">{{ Auth::user()->name ?? 'Kasir ABS' }}</span>
                    </div>
                    <i data-lucide="chevron-down" class="w-3 h-3 text-cafe-muted hidden sm:block"></i>
                </button>

                {{-- Profile Dropdown --}}
                <div id="profile-dropdown" class="hidden absolute right-0 mt-2.5 w-56 bg-cafe-card rounded-2xl shadow-soft-lg border border-cafe-beige/60 z-50 overflow-hidden">
                    <div class="px-4 py-3 bg-gradient-to-r from-cafe-dark/[0.03] to-transparent border-b border-cafe-beige/40">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-cafe-dark text-cafe-gold font-serif font-bold text-sm flex items-center justify-center">
                                {{ substr(Auth::user()->name ?? 'K', 0, 1) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-bold text-cafe-dark truncate">{{ Auth::user()->name ?? 'Kasir' }}</p>
                                <p class="text-[10px] text-cafe-muted truncate">{{ Auth::user()->email }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-1.5">
                        <a href="{{ route('kasir.shift.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 text-xs text-cafe-dark hover:bg-cafe-beige/40 rounded-xl transition-colors">
                            <i data-lucide="wallet" class="w-4 h-4 text-cafe-muted"></i>
                            <span>Riwayat Shift</span>
                        </a>
                        <form action="{{ route('kasir.logout') }}" method="POST" data-cafe-confirm>
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2.5 text-xs text-cafe-danger hover:bg-cafe-danger/8 rounded-xl transition-colors text-left">
                                <i data-lucide="log-out" class="w-4 h-4"></i>
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
        document.getElementById('notif-dropdown').classList.toggle('hidden');
        document.getElementById('profile-dropdown').classList.add('hidden');
    }
    function toggleProfileDropdown() {
        document.getElementById('profile-dropdown').classList.toggle('hidden');
        document.getElementById('notif-dropdown').classList.add('hidden');
    }
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
