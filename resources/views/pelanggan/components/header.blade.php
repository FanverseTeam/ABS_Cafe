<header x-data="{ mobileMenuOpen: false, isScrolled: false }"
        x-init="isScrolled = (window.pageYOffset > 20); $watch('mobileMenuOpen', val => document.body.style.overflow = val ? 'hidden' : '')"
        @scroll.window="isScrolled = (window.pageYOffset > 20)"
        @keydown.escape.window="mobileMenuOpen = false"
        :class="isScrolled ? 'bg-[#000000]/95 backdrop-blur-md border-b border-[#D4954D]/20 shadow-[0_4px_24px_rgba(0,0,0,0.5)] py-3' : 'bg-transparent py-4 sm:py-6'"
        class="fixed top-0 inset-x-0 z-40 transition-all duration-300">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 flex items-center justify-between gap-4">
        <!-- Logo & Branding di Kiri -->
        <a href="{{ route('pelanggan.beranda') }}" class="group flex items-center gap-3 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#D4954D] rounded-xl pr-2" aria-label="Beranda ABS Cafe">
            <x-logo on-dark :show-text="false" size="sm" class="shrink-0 group-hover:scale-105 transition-transform" />
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
            <a href="{{ route('pelanggan.beranda') }}#suasana"
               class="text-xs font-semibold uppercase tracking-wider text-[#F2F5E2] hover:text-[#D4954D] transition-colors py-2">
                Galeri
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

    <!-- Menu Mobile: Side Drawer Rapi di Pinggir Kanan (< 1024px) -->
    <div id="mobile-menu-overlay"
         x-show="mobileMenuOpen"
         x-cloak
         class="lg:hidden fixed inset-0 z-50 overflow-hidden"
         role="dialog"
         aria-modal="true"
         aria-label="Menu navigasi">

        <!-- Backdrop Gelap (Klik di luar menu untuk menutup) -->
        <div x-show="mobileMenuOpen"
             x-transition:enter="transition-opacity ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="mobileMenuOpen = false"
             class="fixed inset-0 bg-black/70 backdrop-blur-xs transition-opacity"
             aria-hidden="true"></div>

        <!-- Panel Menu di Pinggir Kanan (Kompak & Rapi) -->
        <div class="fixed inset-y-0 right-0 w-64 sm:w-72 max-w-[78vw] bg-[#0d0a08]/98 border-l border-[#D4954D]/25 shadow-2xl flex flex-col justify-between z-10"
             x-show="mobileMenuOpen"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full">

            <!-- Header Menu Samping: Brand & Tombol Tutup -->
            <div class="px-4 py-3.5 border-b border-[#775533]/25 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <x-logo on-dark :show-text="false" size="sm" class="shrink-0 w-5 h-5" />
                    <span class="font-serif font-bold text-[#F2F5E2] text-xs sm:text-sm tracking-wider">
                        {{ $landing['cafe']['name'] ?? 'ABS CAFE' }}
                    </span>
                </div>

                <button type="button"
                        @click="mobileMenuOpen = false"
                        aria-label="Tutup menu"
                        class="w-7 h-7 flex items-center justify-center rounded-full bg-white/5 text-[#E3DEA4] hover:text-[#D4954D] hover:bg-white/10 transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-[#D4954D]">
                    <svg class="w-3.5 h-3.5 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <!-- List Menu Rapi & Minimalis -->
            <nav class="flex-1 py-3 px-2.5 space-y-1 overflow-y-auto" aria-label="Navigasi Mobile">
                <!-- Beranda -->
                <a href="{{ route('pelanggan.beranda') }}"
                   @click="mobileMenuOpen = false"
                   class="flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium transition-all {{ request()->routeIs('pelanggan.beranda') && !request()->has('section') ? 'bg-[#D4954D]/15 text-[#D4954D] font-semibold border-l-2 border-[#D4954D]' : 'text-[#F2F5E2]/85 hover:text-[#D4954D] hover:bg-white/5' }}">
                    <span>Beranda</span>
                    <svg class="w-3.5 h-3.5 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </a>

                <!-- Menu -->
                <a href="{{ route('pelanggan.menu') }}"
                   @click="mobileMenuOpen = false"
                   class="flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium transition-all {{ request()->routeIs('pelanggan.menu') ? 'bg-[#D4954D]/15 text-[#D4954D] font-semibold border-l-2 border-[#D4954D]' : 'text-[#F2F5E2]/85 hover:text-[#D4954D] hover:bg-white/5' }}">
                    <span>Menu</span>
                    <svg class="w-3.5 h-3.5 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </a>

                <!-- Reservasi -->
                <a href="{{ route('pelanggan.reservasi') }}"
                   @click="mobileMenuOpen = false"
                   class="flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium transition-all {{ request()->routeIs('pelanggan.reservasi') ? 'bg-[#D4954D]/15 text-[#D4954D] font-semibold border-l-2 border-[#D4954D]' : 'text-[#F2F5E2]/85 hover:text-[#D4954D] hover:bg-white/5' }}">
                    <span>Reservasi</span>
                    <svg class="w-3.5 h-3.5 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </a>

                <div class="my-1.5 border-t border-[#775533]/20"></div>

                <!-- Tentang Kami -->
                <a href="{{ route('pelanggan.beranda') }}#tentang-kami"
                   @click="mobileMenuOpen = false"
                   class="flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium text-[#F2F5E2]/85 hover:text-[#D4954D] hover:bg-white/5 transition-all">
                    <span>Tentang Kami</span>
                    <svg class="w-3.5 h-3.5 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </a>

                <!-- Galeri -->
                <a href="{{ route('pelanggan.beranda') }}#suasana"
                   @click="mobileMenuOpen = false"
                   class="flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium text-[#F2F5E2]/85 hover:text-[#D4954D] hover:bg-white/5 transition-all">
                    <span>Galeri</span>
                    <svg class="w-3.5 h-3.5 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </a>

                <!-- Lokasi -->
                <a href="{{ route('pelanggan.beranda') }}#lokasi"
                   @click="mobileMenuOpen = false"
                   class="flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium text-[#F2F5E2]/85 hover:text-[#D4954D] hover:bg-white/5 transition-all">
                    <span>Lokasi</span>
                    <svg class="w-3.5 h-3.5 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </nav>

            <!-- Bagian Bawah Menu: Jam & Watermark Rapi -->
            <div class="p-3 border-t border-[#775533]/25 bg-black/40 space-y-1.5">
                <p class="text-[10px] text-[#E3DEA4]/70 text-center tracking-wide">
                    Buka Setiap Hari &bull; <span class="text-[#D4954D] font-medium">09:00 - 23:00</span>
                </p>
                <x-team-watermark theme="dark" class="!py-0 scale-90" />
            </div>
        </div>
    </div>
</header>
