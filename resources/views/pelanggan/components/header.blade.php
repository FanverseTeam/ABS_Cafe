<header x-data="{ mobileMenuOpen: false, isScrolled: false }"
        x-init="isScrolled = (window.pageYOffset > 20)"
        @scroll.window="isScrolled = (window.pageYOffset > 20)"
        @keydown.escape.window="mobileMenuOpen = false"
        :class="isScrolled ? 'bg-[#290024]/95 backdrop-blur-md border-b border-[#D4954D]/20 shadow-[0_4px_24px_rgba(41,0,36,0.35)] py-3' : 'bg-transparent py-4 sm:py-6'"
        class="fixed top-0 inset-x-0 z-40 transition-all duration-300">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 flex items-center justify-between gap-4">
        <!-- Logo & Branding di Kiri -->
        <a href="{{ route('pelanggan.beranda') }}" class="group flex items-center gap-3 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#D4954D] rounded-xl pr-2" aria-label="Beranda ABS Cafe">
            <x-logo :show-text="false" size="sm" class="shrink-0 group-hover:scale-105 transition-transform" />
            <div class="flex flex-col text-left">
                <span class="font-serif text-base sm:text-lg font-bold tracking-wider text-[#F2F5E2] leading-tight">
                    {{ $landing['cafe']['name'] ?? 'ABS CAFE' }}
                </span>
                <span class="text-[10px] sm:text-[11px] text-[#D4954D] tracking-wider italic font-medium font-serif leading-none mt-0.5">
                    {{ $landing['cafe']['tagline'] ?? 'Good Coffee, Better Moments.' }}
                </span>
            </div>
        </a>

        <!-- Menu Horizontal di Desktop (>= 1024px) -->
        <nav class="hidden lg:flex items-center gap-8" aria-label="Navigasi Utama Pelanggan">
            <a href="{{ route('pelanggan.beranda') }}"
               class="text-xs font-semibold uppercase tracking-wider text-[#F2F5E2] hover:text-[#D4954D] transition-colors py-2 {{ request()->routeIs('pelanggan.beranda') && !request()->has('section') ? 'text-[#D4954D]' : '' }}">
                Beranda
            </a>
            <a href="{{ route('pelanggan.menu') }}"
               class="text-xs font-semibold uppercase tracking-wider text-[#F2F5E2] hover:text-[#D4954D] transition-colors py-2 {{ request()->routeIs('pelanggan.menu') ? 'text-[#D4954D]' : '' }}">
                Menu
            </a>
            <a href="{{ route('pelanggan.reservasi') }}"
               class="text-xs font-semibold uppercase tracking-wider text-[#F2F5E2] hover:text-[#D4954D] transition-colors py-2 {{ request()->routeIs('pelanggan.reservasi') ? 'text-[#D4954D]' : '' }}">
                Reservasi
            </a>
            <a href="{{ route('pelanggan.beranda') }}#tentang-kami"
               class="text-xs font-semibold uppercase tracking-wider text-[#F2F5E2] hover:text-[#D4954D] transition-colors py-2">
                Tentang Kami
            </a>
            <a href="{{ route('pelanggan.beranda') }}#lokasi"
               class="text-xs font-semibold uppercase tracking-wider text-[#F2F5E2] hover:text-[#D4954D] transition-colors py-2">
                Lokasi
            </a>


        </nav>

        <!-- Tombol Hamburger di Mobile & Tablet (< 1024px) -->
        <button type="button"
                @click="mobileMenuOpen = !mobileMenuOpen"
                :aria-expanded="mobileMenuOpen"
                aria-controls="mobile-menu-overlay"
                aria-label="Buka menu navigasi"
                class="lg:hidden flex items-center justify-center min-h-[44px] min-w-[44px] rounded-xl text-[#F2F5E2] hover:text-[#D4954D] hover:bg-[#775533]/20 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#D4954D] transition-colors">
            <svg class="w-6 h-6 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <line x1="4" y1="7" x2="20" y2="7"></line>
                <line x1="4" y1="12" x2="20" y2="12"></line>
                <line x1="4" y1="17" x2="20" y2="17"></line>
            </svg>
        </button>
    </div>

    <!-- Menu Layar Penuh Mobile (< 1024px) -->
    <div id="mobile-menu-overlay"
         x-show="mobileMenuOpen"
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-[-10px]"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-[-10px]"
         role="dialog"
         aria-modal="true"
         aria-label="Menu navigasi layar penuh"
         class="lg:hidden fixed inset-0 z-50 bg-[#290024]/98 backdrop-blur-xl flex flex-col justify-between px-6 pt-6 pb-8 overflow-y-auto">

        <!-- Baris Atas Menu Mobile: Logo & Tombol Tutup -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <x-logo :show-text="false" size="sm" />
                <span class="font-serif font-bold text-[#F2F5E2] text-sm sm:text-base tracking-wider">
                    {{ $landing['cafe']['name'] ?? 'ABS CAFE' }}
                </span>
            </div>

            <button type="button"
                    @click="mobileMenuOpen = false"
                    aria-label="Tutup menu navigasi"
                    class="flex items-center justify-center min-h-[44px] min-w-[44px] rounded-full text-[#F2F5E2] hover:text-[#D4954D] hover:bg-[#775533]/20 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#D4954D] transition-colors">
                <svg class="w-6 h-6 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <!-- Daftar Menu Navigasi Utama Layar Penuh -->
        <nav class="flex flex-col items-center justify-center gap-6 my-auto py-8 text-center" aria-label="Menu Navigasi Mobile">
            <a href="{{ route('pelanggan.beranda') }}"
               @click="mobileMenuOpen = false"
               class="min-h-[44px] flex items-center justify-center font-serif text-2xl sm:text-3xl font-semibold tracking-wide text-[#F2F5E2] hover:text-[#D4954D] transition-colors">
                Beranda
            </a>
            <a href="{{ route('pelanggan.menu') }}"
               @click="mobileMenuOpen = false"
               class="min-h-[44px] flex items-center justify-center font-serif text-2xl sm:text-3xl font-semibold tracking-wide text-[#F2F5E2] hover:text-[#D4954D] transition-colors">
                Menu
            </a>
            <a href="{{ route('pelanggan.reservasi') }}"
               @click="mobileMenuOpen = false"
               class="min-h-[44px] flex items-center justify-center font-serif text-2xl sm:text-3xl font-semibold tracking-wide text-[#F2F5E2] hover:text-[#D4954D] transition-colors">
                Reservasi
            </a>
            <a href="{{ route('pelanggan.beranda') }}#tentang-kami"
               @click="mobileMenuOpen = false"
               class="min-h-[44px] flex items-center justify-center font-serif text-2xl sm:text-3xl font-semibold tracking-wide text-[#F2F5E2] hover:text-[#D4954D] transition-colors">
                Tentang Kami
            </a>
            <a href="{{ route('pelanggan.beranda') }}#lokasi"
               @click="mobileMenuOpen = false"
               class="min-h-[44px] flex items-center justify-center font-serif text-2xl sm:text-3xl font-semibold tracking-wide text-[#F2F5E2] hover:text-[#D4954D] transition-colors">
                Lokasi
            </a>
        </nav>


    </div>
</header>
