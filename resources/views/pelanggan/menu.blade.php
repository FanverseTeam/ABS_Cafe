@extends('pelanggan.layouts.app')

@section('title', 'Menu - ' . ($landing['cafe']['name'] ?? 'ABS Cafe'))
@section('meta_description', 'Jelajahi menu kopi pilihan, minuman segar, dan makanan lezat di ' . ($landing['cafe']['name'] ?? 'ABS Cafe') . ' Tulungagung.')

@section('body_class', 'bg-white text-[#290024]')
@section('body_style', 'background-color: #ffffff !important; color: #290024 !important;')
@section('main_class', 'bg-white')

{{-- Header Khusus Halaman Menu: Logo + "ABS Cafe" di Kiri, Ikon Cari di Kanan --}}
@section('header')
<header x-data="{ isScrolled: false }"
        x-init="isScrolled = (window.pageYOffset > 10)"
        @scroll.window="isScrolled = (window.pageYOffset > 10)"
        :class="isScrolled ? 'bg-white/95 backdrop-blur-md border-b border-[#EDE8E0] shadow-xs' : 'bg-white border-b border-[#F0ECE4]'"
        style="background-color: #ffffff !important;"
        class="sticky top-0 inset-x-0 z-30 transition-all duration-200">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 h-16 sm:h-20 flex items-center justify-between gap-4">
        <!-- Logo & Branding di Kiri -->
        <a href="{{ route('pelanggan.beranda') }}"
           class="group flex items-center gap-3 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#290024] rounded-xl pr-2"
           aria-label="Kembali ke Beranda ABS Cafe">
            <x-logo :show-text="false" size="sm" class="shrink-0 group-hover:scale-105 transition-transform" />
            <span class="font-serif text-lg sm:text-xl font-bold tracking-tight text-[#290024] leading-tight">
                {{ $landing['cafe']['name'] ?? 'ABS Cafe' }}
            </span>
        </a>

        <!-- Ikon Cari di Kanan (Memicu Bar Pencarian) -->
        <div class="flex items-center gap-2">
            <button type="button"
                    @click="$dispatch('toggle-menu-search')"
                    class="flex items-center justify-center w-10 h-10 rounded-full text-[#290024] hover:bg-[#F5F2EB] hover:text-[#775533] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#290024] transition-colors"
                    aria-label="Buka pencarian menu">
                <svg class="w-5 h-5 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </button>
        </div>
    </div>
</header>
@endsection

@section('content')
<div x-data="menuPage()"
     x-init="initPage()"
     @toggle-menu-search.window="toggleSearch()"
     style="background-color: #ffffff !important;"
     class="min-h-screen bg-white pb-28 pt-2 sm:pt-4">

    <!-- Kontainer Utama (±1200px pada Desktop >= 1024px) -->
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 space-y-6 sm:space-y-8">

        <!-- Bar Pencarian (Selalu Tampil Bersih Sesuai Figma) -->
        <div class="relative bg-white border border-[#EDE8E0] rounded-2xl sm:rounded-full p-2 sm:p-2.5 shadow-xs">
            <form action="{{ route('pelanggan.menu') }}" method="GET" class="flex items-center gap-2">
                @if(request('kategori') && request('kategori') !== 'semua')
                    <input type="hidden" name="kategori" value="{{ request('kategori') }}">
                @endif
                @if($tableNumber)
                    <input type="hidden" name="meja" value="{{ $tableNumber }}">
                @endif
                <div class="pl-2.5 text-[#775533]">
                    <svg class="w-5 h-5 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </div>
                <input type="text"
                       name="q"
                       x-ref="searchInput"
                       value="{{ $searchQuery }}"
                       placeholder="Cari menu kesukaan..."
                       class="flex-1 bg-transparent text-xs sm:text-sm text-[#290024] placeholder-[#775533]/50 focus:outline-none py-1.5 px-2">
                @if($searchQuery !== '')
                    <a href="{{ route('pelanggan.menu', array_filter(['kategori' => request('kategori'), 'meja' => $tableNumber])) }}"
                       class="text-xs text-[#775533] hover:text-[#290024] px-2.5 py-1 rounded-full hover:bg-stone-100">
                        Batal
                    </a>
                @endif
                <button type="submit"
                        class="px-4 py-2 rounded-xl sm:rounded-full bg-[#290024] hover:bg-[#D4954D] hover:text-[#290024] text-white text-xs font-semibold tracking-wide transition-colors">
                    Cari
                </button>
            </form>
        </div>

        {{-- ============================================================= --}}
        {{-- BANNER INFORMASI MEJA / METODE PEMESANAN                     --}}
        {{-- ============================================================= --}}
        @if($tableNumber)
            <!-- Alur 1: Pelanggan Memindai QR di Meja (Nomor Meja Terkunci) -->
            <div class="bg-gradient-to-r from-[#290024] via-[#3B0034] to-[#290024] text-[#E3DEA4] rounded-2xl p-4 sm:p-5 shadow-sm border border-[#D4954D]/30 flex flex-col sm:flex-row sm:items-center justify-between gap-3.5">
                <div class="flex items-start sm:items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl bg-[#D4954D] text-[#290024] flex items-center justify-center font-black text-lg shadow-inner shrink-0">
                        {{ $tableNumber }}
                    </div>
                    <div class="space-y-0.5">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-[#E3DEA4] uppercase tracking-wider">Pesan Dari Meja</span>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                Meja #{{ $tableNumber }} Terhubung
                            </span>
                        </div>
                        <div class="text-xs sm:text-sm text-white font-semibold flex items-center gap-2">
                            <span>Area: {{ $activeTable?->location_notes ?? 'Area Utama Kafe' }}</span>
                            <span class="text-white/40">•</span>
                            <span class="text-[11px] text-[#E3DEA4]/80">Kapasitas: {{ $activeTable?->capacity ?? 4 }} Orang</span>
                        </div>
                        <p class="text-[11px] text-white/70 leading-snug">
                            Pesanan Anda akan langsung disiapkan barista dan diantar ke Meja #{{ $tableNumber }}.
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-2 sm:pt-0 border-t sm:border-t-0 border-white/10 shrink-0">
                    <button type="button"
                            @click="openTableModal = true"
                            class="px-3.5 py-1.5 rounded-full bg-white/10 hover:bg-white/20 text-white text-xs font-semibold transition-colors flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                        </svg>
                        <span>Ganti Meja</span>
                    </button>
                    <a href="{{ route('pelanggan.menu') }}"
                       class="px-3.5 py-1.5 rounded-full bg-[#D4954D]/20 hover:bg-[#D4954D]/30 text-[#E3DEA4] text-xs font-semibold transition-colors">
                        Bawa Pulang
                    </a>
                </div>
            </div>
        @else
            <!-- Alur 2: Pelanggan Masuk Lewat Landing Page ("Pesan Sekarang") -->
            <div class="bg-[#FAF7F2] rounded-2xl p-4 sm:p-5 border border-[#EDE8E0] shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3.5">
                <div class="flex items-start sm:items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#290024] text-[#E3DEA4] flex items-center justify-center font-bold text-sm shadow-sm shrink-0">
                        <svg class="w-5 h-5 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div class="space-y-0.5">
                        <div class="text-[11px] font-bold text-[#775533] uppercase tracking-wider">Metode Pemesanan</div>
                        <h2 class="text-xs sm:text-sm font-bold text-[#290024]">
                            Sudah di kafe atau pesan bawa pulang?
                        </h2>
                        <p class="text-[11px] text-[#775533] leading-snug">
                            Pilih nomor meja Anda untuk santap di tempat, atau pesan langsung untuk dibawa pulang (takeaway).
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-2 sm:pt-0 border-t sm:border-t-0 border-[#EDE8E0] shrink-0">
                    <button type="button"
                            @click="openTableModal = true"
                            class="px-4 py-2 rounded-full bg-[#290024] hover:bg-[#D4954D] hover:text-[#290024] text-white text-xs font-semibold tracking-wide transition-all shadow-xs flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        </svg>
                        <span>Pilih Nomor Meja</span>
                    </button>
                    <span class="px-3 py-1.5 rounded-full bg-white text-[#775533] text-xs font-medium border border-[#EDE8E0]">
                        Bawa Pulang
                    </span>
                </div>
            </div>
        @endif

        <!-- Judul Besar & Subjudul -->
        <div class="space-y-1">
            <h1 class="font-serif text-2xl sm:text-3xl lg:text-4xl font-bold text-[#290024] tracking-tight">
                Menu
            </h1>
            <p class="font-sans text-xs sm:text-sm text-[#775533] font-medium tracking-wide">
                Rasa Yang Selalu Bikin Rindu
            </p>
        </div>

        <!-- Pill Kategori Scroll Horizontal (Sesuai Desain Figma) -->
        <div class="relative -mx-4 px-4 sm:mx-0 sm:px-0">
            <div class="flex items-center gap-2 sm:gap-2.5 overflow-x-auto no-scrollbar scroll-smooth py-1"
                 role="tablist"
                 aria-label="Kategori Menu">
                <!-- Pill: Semua -->
                @php
                    $isSemuaActive = empty($selectedCategorySlug) || $selectedCategorySlug === 'semua';
                @endphp
                <a href="{{ route('pelanggan.menu', array_filter(['q' => $searchQuery, 'meja' => $tableNumber])) }}"
                   class="category-pill shrink-0 inline-flex items-center justify-center px-5 sm:px-6 py-2 rounded-full text-xs sm:text-sm transition-all shadow-xs {{ $isSemuaActive ? 'category-pill-active bg-[#290024] text-white font-semibold border border-[#290024]' : 'bg-white hover:bg-[#F5F2EB] text-[#5C4033] font-medium border border-[#EDE8E0]' }}"
                   role="tab"
                   aria-selected="{{ $isSemuaActive ? 'true' : 'false' }}">
                    Semua
                </a>

                <!-- Pill Tiap Kategori dari DB -->
                @foreach($categories as $category)
                    @php
                        $isCatActive = ($selectedCategorySlug === $category->slug || (string)$selectedCategorySlug === (string)$category->id);
                    @endphp
                    <a href="{{ route('pelanggan.menu', array_filter(['kategori' => $category->slug, 'q' => $searchQuery, 'meja' => $tableNumber])) }}"
                       class="category-pill shrink-0 inline-flex items-center justify-center px-5 sm:px-6 py-2 rounded-full text-xs sm:text-sm transition-all shadow-xs {{ $isCatActive ? 'category-pill-active bg-[#290024] text-white font-semibold border border-[#290024]' : 'bg-white hover:bg-[#F5F2EB] text-[#5C4033] font-medium border border-[#EDE8E0]' }}"
                       role="tab"
                       aria-selected="{{ $isCatActive ? 'true' : 'false' }}">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- ============================================================= --}}
        {{-- KONTEN MENU BERDASARKAN MODE                                 --}}
        {{-- ============================================================= --}}

        {{-- 1. MODE PENCARIAN --}}
        @if($mode === 'search')
            <section class="space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="font-serif font-bold text-lg sm:text-xl text-[#290024]">
                        Hasil Pencarian "{{ $searchQuery }}"
                    </h2>
                    <span class="text-xs text-[#775533] font-medium">
                        {{ $menus->total() }} menu ditemukan
                    </span>
                </div>

                @if($menus->isEmpty())
                    <!-- State Kosong Ramah -->
                    <div class="bg-white border border-[#EDE8E0] rounded-3xl p-8 sm:p-12 text-center max-w-md mx-auto my-8 shadow-xs">
                        <div class="w-12 h-12 rounded-full bg-[#F5F2EB] flex items-center justify-center text-[#775533] mx-auto mb-4">
                            <svg class="w-6 h-6 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                        </div>
                        <h3 class="font-serif font-bold text-base sm:text-lg text-[#290024] mb-1.5">
                            Menu tidak ditemukan
                        </h3>
                        <p class="text-xs sm:text-sm text-[#775533] leading-relaxed mb-6">
                            Maaf, menu dengan kata kunci "{{ $searchQuery }}" belum tersedia atau sedang tidak aktif. Coba kata kunci lain atau lihat menu rekomendasi kami.
                        </p>
                        <a href="{{ route('pelanggan.menu') }}"
                           class="inline-flex items-center justify-center px-6 py-2.5 rounded-full bg-[#290024] hover:bg-[#D4954D] hover:text-[#290024] text-white text-xs font-semibold tracking-wide transition-colors">
                            Lihat Semua Menu
                        </a>
                    </div>
                @else
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-5">
                        @foreach($menus as $menu)
                            @include('pelanggan.partials.menu-card', ['menu' => $menu])
                        @endforeach
                    </div>

                    @if($menus->hasPages())
                        <div class="pt-6 flex justify-center">
                            {{ $menus->links() }}
                        </div>
                    @endif
                @endif
            </section>

        {{-- 2. MODE FILTER KATEGORI ATAU REKOMENDASI LENGKAP --}}
        @elseif($mode === 'category' || $mode === 'rekomendasi')
            <section class="space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="font-serif font-bold text-lg sm:text-xl text-[#290024]">
                        {{ $mode === 'rekomendasi' ? 'Rekomendasi' : ($selectedCategory->name ?? 'Menu') }}
                    </h2>
                    <span class="text-xs text-[#775533] font-medium">
                        {{ $menus->total() }} menu
                    </span>
                </div>

                @if($menus->isEmpty())
                    <div class="bg-white border border-[#EDE8E0] rounded-3xl p-8 text-center max-w-md mx-auto my-8 shadow-xs">
                        <p class="text-xs sm:text-sm text-[#775533] mb-4">Belum ada menu yang tersedia untuk kategori ini.</p>
                        <a href="{{ route('pelanggan.menu') }}" class="inline-flex px-5 py-2 rounded-full bg-[#290024] hover:bg-[#D4954D] hover:text-[#290024] text-white text-xs font-semibold">
                            Kembali ke Semua Menu
                        </a>
                    </div>
                @else
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-5">
                        @foreach($menus as $menu)
                            @include('pelanggan.partials.menu-card', ['menu' => $menu])
                        @endforeach
                    </div>

                    @if($menus->hasPages())
                        <div class="pt-6 flex justify-center">
                            {{ $menus->links() }}
                        </div>
                    @endif
                @endif
            </section>

        {{-- 3. MODE DEFAULT: SEMUA KATEGORI (REKOMENDASI + SATU BAGIAN PER KATEGORI MAKS 4 MENU) --}}
        @else
            <!-- Bagian 1: Rekomendasi (Menu Best Seller) -->
            @if(!empty($recommendedMenus) && $recommendedMenus->isNotEmpty())
                <section class="space-y-3.5">
                    <div class="flex items-center justify-between">
                        <h2 class="font-serif font-bold text-lg sm:text-xl text-[#290024]">
                            Rekomendasi
                        </h2>
                        <a href="{{ route('pelanggan.menu', ['kategori' => 'rekomendasi']) }}"
                           class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#775533] hover:text-[#D4954D] transition-colors py-1 group">
                            <span>Lihat semua</span>
                            <svg class="w-3.5 h-3.5 stroke-current group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </a>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-5">
                        @foreach($recommendedMenus as $menu)
                            @include('pelanggan.partials.menu-card', ['menu' => $menu])
                        @endforeach
                    </div>
                </section>
            @endif

            <!-- Bagian 2..N: Satu Bagian Per Kategori (Maksimal 4 Menu Per Kategori) -->
            @foreach($categoriesWithMenus as $category)
                <section class="space-y-3.5 pt-2">
                    <div class="flex items-center justify-between">
                        <h2 class="font-serif font-bold text-lg sm:text-xl text-[#290024]">
                            {{ $category->name }}
                        </h2>
                        <a href="{{ route('pelanggan.menu', ['kategori' => $category->slug]) }}"
                           class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#775533] hover:text-[#D4954D] transition-colors py-1 group">
                            <span>Lihat semua</span>
                            <svg class="w-3.5 h-3.5 stroke-current group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </a>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-5">
                        @foreach($category->preview_menus as $menu)
                            @include('pelanggan.partials.menu-card', ['menu' => $menu])
                        @endforeach
                    </div>
                </section>
            @endforeach
        @endif

    </div>

    {{-- ============================================================= --}}
    {{-- BOTTOM SHEET (MOBILE) / KARTU TENGAH (DESKTOP) MODAL         --}}
    {{-- ============================================================= --}}
    <div x-show="sheetOpen"
         x-cloak
         role="dialog"
         aria-modal="true"
         :aria-labelledby="activeMenu ? 'sheet-title-' + activeMenu.id : ''"
         @keydown.escape.window="closeBottomSheet()"
         class="fixed inset-0 z-50 overflow-y-auto">

        <!-- Latar Gelap / Backdrop -->
        <div x-show="sheetOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="closeBottomSheet()"
             class="fixed inset-0 bg-[#290024]/60 backdrop-blur-xs"></div>

        <!-- Wadah Posisi: Bawah di Mobile, Tengah di Tablet/Desktop -->
        <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 pointer-events-none">
            <div x-show="sheetOpen"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="translate-y-full sm:translate-y-4 sm:opacity-0 sm:scale-95"
                 x-transition:enter-end="translate-y-0 sm:translate-y-0 sm:opacity-100 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="translate-y-0 sm:translate-y-0 sm:opacity-100 sm:scale-100"
                 x-transition:leave-end="translate-y-full sm:translate-y-4 sm:opacity-0 sm:scale-95"
                 class="pointer-events-auto w-full sm:max-w-[560px] max-h-[90vh] bg-white rounded-t-3xl sm:rounded-3xl border border-[#EDE8E0] shadow-2xl flex flex-col overflow-hidden">

                <!-- Header Modal: Tombol Tutup -->
                <div class="p-4 sm:p-5 pb-2 flex items-center justify-between border-b border-[#F0ECE4]">
                    <span class="text-xs font-semibold text-[#775533] tracking-wide uppercase">
                        Detail Pesanan
                    </span>
                    <button type="button"
                            @click="closeBottomSheet()"
                            class="w-8 h-8 rounded-full flex items-center justify-center text-[#290024] hover:bg-stone-100 transition-colors"
                            aria-label="Tutup jendela">
                        <svg class="w-5 h-5 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>

                <!-- Isi Modal (Scrollable) -->
                <div class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-5">
                    <template x-if="activeMenu">
                        <div class="space-y-4">
                            <!-- Foto Menu -->
                            <div class="relative w-full aspect-[16/9] sm:aspect-[2/1] rounded-2xl overflow-hidden bg-[#F5F2EB] border border-[#EDE8E0] flex items-center justify-center">
                                <template x-if="activeMenu.image">
                                    <img :src="activeMenu.image"
                                         :alt="activeMenu.name"
                                         class="w-full h-full object-cover">
                                </template>
                                <template x-if="!activeMenu.image">
                                    <div class="w-12 h-12 rounded-full bg-[#EAE4D9]/60 flex items-center justify-center text-[#775533]">
                                        <svg class="w-6 h-6 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <path d="M18 8h1a4 4 0 0 1 0 8h-1"></path>
                                            <path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"></path>
                                            <line x1="6" y1="1" x2="6" y2="4"></line>
                                            <line x1="10" y1="1" x2="10" y2="4"></line>
                                            <line x1="14" y1="1" x2="14" y2="4"></line>
                                        </svg>
                                    </div>
                                </template>
                            </div>

                            <!-- Judul & Deskripsi -->
                            <div>
                                <h3 :id="'sheet-title-' + activeMenu.id"
                                    class="font-serif font-bold text-lg sm:text-xl text-[#290024] leading-tight"
                                    x-text="activeMenu.name"></h3>
                                <p class="text-xs sm:text-sm text-[#775533] mt-1 leading-relaxed"
                                   x-text="activeMenu.description || 'Pilihan lezat racikan ABS Cafe.'"></p>
                            </div>

                            <!-- Pilihan Varian (Radio-Cards) -->
                            <template x-if="activeMenu.variants && activeMenu.variants.length > 0">
                                <div class="space-y-2 pt-2">
                                    <label class="block text-xs font-bold text-[#290024] uppercase tracking-wide">
                                        Pilih Varian
                                    </label>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                        <template x-for="variant in activeMenu.variants" :key="variant.id">
                                            <label class="relative flex items-center justify-between p-3 rounded-xl border transition-all cursor-pointer"
                                                   :class="{
                                                       'border-[#290024] bg-[#F5F2EB] ring-2 ring-[#290024]/10 shadow-xs': selectedVariantId === variant.id && !variant.is_habis,
                                                       'border-[#EDE8E0] bg-white hover:bg-[#FAF8F5]': selectedVariantId !== variant.id && !variant.is_habis,
                                                       'opacity-50 cursor-not-allowed border-[#EDE8E0] bg-stone-50': variant.is_habis
                                                   }">
                                                <div class="flex items-center gap-3">
                                                    <input type="radio"
                                                           name="modal_variant"
                                                           :value="variant.id"
                                                           :disabled="variant.is_habis"
                                                           x-model="selectedVariantId"
                                                           class="w-4 h-4 text-[#290024] focus:ring-[#290024] border-stone-300">
                                                    <div class="text-left">
                                                        <span class="block text-xs sm:text-sm font-semibold text-[#290024]"
                                                              x-text="variant.display_name"></span>
                                                        <template x-if="variant.is_habis">
                                                            <span class="text-[10px] font-bold text-rose-600 uppercase tracking-wider">
                                                                Habis
                                                            </span>
                                                        </template>
                                                    </div>
                                                </div>
                                                <span class="text-xs font-semibold text-[#775533]"
                                                      x-text="variant.price_label"></span>
                                            </label>
                                        </template>
                                    </div>
                                </div>
                            </template>

                            <!-- Pemilih Jumlah & Catatan -->
                            <div class="space-y-4 pt-2">
                                <!-- Pemilih Jumlah -->
                                <div class="flex items-center justify-between">
                                    <label class="text-xs font-bold text-[#290024] uppercase tracking-wide">
                                        Jumlah
                                    </label>
                                    <div class="flex items-center gap-3 bg-[#F5F2EB] border border-[#EDE8E0] rounded-full p-1 shadow-xs">
                                        <button type="button"
                                                @click="quantity > 1 ? quantity-- : null"
                                                :disabled="quantity <= 1"
                                                class="w-8 h-8 rounded-full flex items-center justify-center text-[#290024] hover:bg-white disabled:opacity-40 transition-colors"
                                                aria-label="Kurangi jumlah">
                                            <svg class="w-4 h-4 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                            </svg>
                                        </button>
                                        <span class="w-8 text-center font-semibold text-sm text-[#290024]"
                                              x-text="quantity"></span>
                                        <button type="button"
                                                @click="quantity++"
                                                class="w-8 h-8 rounded-full flex items-center justify-center text-[#290024] hover:bg-white transition-colors"
                                                aria-label="Tambah jumlah">
                                            <svg class="w-4 h-4 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Catatan Opsional -->
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-bold text-[#290024] uppercase tracking-wide">
                                        Catatan (Opsional)
                                    </label>
                                    <textarea x-model="notes"
                                              maxlength="150"
                                              rows="2"
                                              placeholder="Contoh: es sedikit, pisahkan sambal..."
                                              class="w-full text-xs sm:text-sm p-3 rounded-xl bg-[#FAF8F5] border border-[#EDE8E0] focus:border-[#290024] focus:outline-none placeholder-[#775533]/50 text-[#290024] resize-none"></textarea>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Bagian Bawah Modal: Total Harga & Tombol Tambah ke Keranjang -->
                <div class="p-4 sm:p-5 pt-3 bg-[#FAF8F5] border-t border-[#EDE8E0] flex items-center justify-between gap-4">
                    <div>
                        <span class="block text-[11px] text-[#775533]">Total Harga</span>
                        <span class="font-serif font-bold text-base sm:text-lg text-[#290024]"
                              x-text="formattedTotalPrice()"></span>
                    </div>

                    <button type="button"
                            @click="addToCart()"
                            class="flex-1 max-w-[240px] inline-flex items-center justify-center gap-2 min-h-[44px] px-6 py-2.5 rounded-full bg-[#290024] hover:bg-[#D4954D] hover:text-[#290024] text-white font-semibold text-xs sm:text-sm tracking-wide transition-all shadow-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#290024]">
                        <span>Tambah ke Keranjang</span>
                    </button>
                </div>

            </div>
        </div>
    </div>

    {{-- ============================================================= --}}
    {{-- BAR KERANJANG MELAYANG (FLOATING CART BAR)                    --}}
    {{-- ============================================================= --}}
    <div x-show="cartTotalItems > 0"
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-4"
         class="fixed bottom-4 inset-x-4 max-w-lg mx-auto sm:max-w-xl z-40 bg-[#290024] text-[#E3DEA4] rounded-2xl p-3 sm:p-3.5 border border-[#D4954D]/40 shadow-[0_12px_40px_rgba(41,0,36,0.45)] flex items-center justify-between gap-3">

        <!-- Info Jumlah Item & Perkiraan Total -->
        <div class="flex items-center gap-3 pl-1">
            <div class="relative w-10 h-10 rounded-xl bg-[#D4954D] text-[#290024] flex items-center justify-center shrink-0 shadow-sm font-bold">
                <svg class="w-5 h-5 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <path d="M16 10a4 4 0 0 1-8 0"></path>
                </svg>
            </div>
            <div class="text-left">
                <div class="text-xs sm:text-sm font-bold text-[#E3DEA4] flex items-center gap-1.5">
                    @if($tableNumber)
                        <span class="bg-[#D4954D]/30 px-1.5 py-0.5 rounded text-[11px] text-[#E3DEA4]">Meja #{{ $tableNumber }}</span>
                    @else
                        <span class="bg-white/20 px-1.5 py-0.5 rounded text-[11px] text-white">Bawa Pulang</span>
                    @endif
                    <span>•</span>
                    <span><span x-text="cartTotalItems"></span> Item</span>
                </div>
                <div class="text-[11px] sm:text-xs text-[#E3DEA4] font-medium"
                     x-text="formattedCartTotal()"></div>
            </div>
        </div>

        <!-- Tombol Menuju Halaman Keranjang -->
        <a href="{{ route('pelanggan.keranjang', array_filter(['meja' => $tableNumber])) }}"
           class="inline-flex items-center justify-center gap-2 min-h-[40px] px-5 sm:px-6 py-2 rounded-full bg-[#D4954D] hover:bg-[#D4954D] text-[#290024] font-bold text-xs sm:text-sm tracking-wide transition-all shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#E3DEA4]">
            <span>Lihat Keranjang</span>
            <svg class="w-4 h-4 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <line x1="5" y1="12" x2="19" y2="12"></line>
                <polyline points="12 5 19 12 12 19"></polyline>
            </svg>
        </a>
    </div>

    <!-- Toast Notifikasi Ringkas Saat Ditambahkan -->
    <div x-show="toastShow"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="fixed top-20 left-1/2 -translate-x-1/2 z-50 px-4 py-2 rounded-full bg-[#290024] text-[#E3DEA4] text-xs font-semibold shadow-lg border border-[#D4954D]/30 flex items-center gap-2 pointer-events-none">
        <svg class="w-4 h-4 stroke-[#D4954D]" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <polyline points="20 6 9 17 4 12"></polyline>
        </svg>
        <span x-text="toastMessage"></span>
    </div>

    {{-- ============================================================= --}}
    {{-- MODAL PILIH NOMOR MEJA KAFE                                   --}}
    {{-- ============================================================= --}}
    <div x-show="openTableModal"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 overflow-y-auto">
        <div class="fixed inset-0 bg-[#290024]/50 backdrop-blur-xs" @click="openTableModal = false"></div>

        <div class="relative w-full max-w-md bg-white rounded-3xl shadow-xl border border-[#EDE8E0] p-6 z-10 space-y-4 my-8">
            <div class="flex items-center justify-between pb-3 border-b border-[#EDE8E0]">
                <div>
                    <h3 class="font-serif text-lg font-bold text-[#290024]">Pilih Nomor Meja</h3>
                    <p class="text-xs text-[#775533]">Pilih nomor meja tempat Anda duduk saat ini</p>
                </div>
                <button type="button" @click="openTableModal = false" class="w-8 h-8 rounded-full bg-stone-100 hover:bg-stone-200 text-stone-500 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <div class="grid grid-cols-3 gap-2.5 max-h-72 overflow-y-auto py-1">
                @foreach($allTables as $t)
                    @php
                        $isSelected = $tableNumber == $t->number;
                    @endphp
                    <a href="{{ route('pelanggan.menu', array_filter(['meja' => $t->number, 'kategori' => request('kategori'), 'q' => $searchQuery])) }}"
                       class="p-3 rounded-2xl text-center border transition-all flex flex-col items-center justify-center gap-1 {{ $isSelected ? 'bg-[#290024] text-[#E3DEA4] border-[#290024] shadow-sm' : 'bg-[#FAF7F2] hover:bg-[#F5F0E6] text-[#290024] border-[#EDE8E0]' }}">
                        <span class="text-[9px] uppercase tracking-wider font-bold {{ $isSelected ? 'text-[#E3DEA4]/80' : 'text-[#775533]' }}">Meja</span>
                        <span class="font-serif font-black text-xl leading-none">{{ $t->number }}</span>
                        <span class="text-[10px] {{ $isSelected ? 'text-[#E3DEA4]/80' : 'text-stone-400' }}">{{ $t->capacity }} Kursi</span>
                    </a>
                @endforeach
            </div>

            <div class="pt-2 border-t border-[#EDE8E0] flex items-center justify-between gap-2">
                <a href="{{ route('pelanggan.menu', array_filter(['kategori' => request('kategori'), 'q' => $searchQuery])) }}"
                   class="flex-1 py-2.5 px-4 rounded-full bg-stone-100 hover:bg-stone-200 text-[#290024] text-xs font-semibold text-center transition-colors">
                    Pesan Bawa Pulang (Tanpa Meja)
                </a>
                <button type="button" @click="openTableModal = false" class="py-2.5 px-4 rounded-full text-xs font-semibold text-[#775533] hover:text-[#290024]">
                    Tutup
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

@section('footer')
<footer class="bg-white border-t border-[#EDE8E0] py-6 sm:py-8 text-center text-xs text-[#775533]" style="background-color: #ffffff !important;">
    <div class="max-w-4xl mx-auto px-4 space-y-1.5">
        <p class="font-serif font-bold text-[#290024]">{{ $landing['cafe']['name'] ?? 'ABS Cafe' }}</p>
        <p class="text-[11px] text-[#775533]/80">{{ $landing['cafe']['tagline'] ?? 'Good Coffee, Better Moments.' }}</p>
        <p class="text-[10px] text-stone-400">&copy; {{ date('Y') }} Hak cipta dilindungi.</p>
    </div>
</footer>
@endsection

@push('scripts')
<script>
function menuPage() {
    return {
        searchOpen: {{ !empty($searchQuery) ? 'true' : 'false' }},
        openTableModal: false,
        tableNumber: {{ $tableNumber ? $tableNumber : 'null' }},
        sheetOpen: false,
        activeMenu: null,
        selectedVariantId: null,
        quantity: 1,
        notes: '',
        cart: [],
        toastShow: false,
        toastMessage: '',

        initPage() {
            this.loadCart();

            if (this.tableNumber) {
                try {
                    localStorage.setItem('abscafe_table_number', this.tableNumber);
                } catch(e) {}
            }

            // Auto-scroll pill aktif agar selalu terlihat pada scroll horizontal
            this.$nextTick(() => {
                const activePill = document.querySelector('.category-pill-active');
                if (activePill) {
                    activePill.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                }
            });

            // Sinkronisasi bila tab lain memodifikasi localStorage
            window.addEventListener('storage', (e) => {
                if (e.key === 'abscafe_cart') {
                    this.loadCart();
                }
            });
        },

        toggleSearch() {
            this.searchOpen = !this.searchOpen;
            if (this.searchOpen) {
                this.$nextTick(() => {
                    this.$refs.searchInput?.focus();
                });
            }
        },

        loadCart() {
            try {
                const raw = localStorage.getItem('abscafe_cart');
                this.cart = raw ? JSON.parse(raw) : [];
                if (!Array.isArray(this.cart)) {
                    this.cart = [];
                }
            } catch (e) {
                this.cart = [];
            }
        },

        saveCart() {
            try {
                localStorage.setItem('abscafe_cart', JSON.stringify(this.cart));
            } catch (e) {
                // Storage quota exceeded or disabled
            }
        },

        get cartTotalItems() {
            return this.cart.reduce((sum, item) => sum + (parseInt(item.jumlah) || 0), 0);
        },

        get cartTotalPrice() {
            return this.cart.reduce((sum, item) => {
                const price = parseFloat(item.harga) || 0;
                const qty = parseInt(item.jumlah) || 0;
                return sum + (price * qty);
            }, 0);
        },

        formattedCartTotal() {
            return 'Rp ' + this.cartTotalPrice.toLocaleString('id-ID');
        },

        openBottomSheet(menu) {
            if (menu.is_habis) {
                return;
            }

            this.activeMenu = menu;
            this.quantity = 1;
            this.notes = '';

            // Tentukan varian default
            if (menu.variants && menu.variants.length > 0) {
                const availableVariants = menu.variants.filter(v => !v.is_habis);
                const defaultVariant = availableVariants.find(v => v.is_default) || availableVariants[0];
                this.selectedVariantId = defaultVariant ? defaultVariant.id : menu.variants[0].id;
            } else {
                this.selectedVariantId = null;
            }

            this.sheetOpen = true;
        },

        closeBottomSheet() {
            this.sheetOpen = false;
            this.activeMenu = null;
        },

        getSelectedVariant() {
            if (!this.activeMenu || !this.activeMenu.variants) {
                return null;
            }
            return this.activeMenu.variants.find(v => v.id === this.selectedVariantId) || null;
        },

        getUnitPrice() {
            const variant = this.getSelectedVariant();
            if (variant) {
                return parseFloat(variant.price) || 0;
            }
            return parseFloat(this.activeMenu?.price) || 0;
        },

        formattedTotalPrice() {
            const total = this.getUnitPrice() * this.quantity;
            return 'Rp ' + total.toLocaleString('id-ID');
        },

        addToCart() {
            if (!this.activeMenu) {
                return;
            }

            const variant = this.getSelectedVariant();
            const unitPrice = this.getUnitPrice();

            const cartItem = {
                menu_id: this.activeMenu.id,
                varian_id: variant ? variant.id : null,
                jumlah: this.quantity,
                catatan: this.notes ? this.notes.trim() : '',
                nama: this.activeMenu.name,
                varian_nama: variant ? variant.display_name : null,
                harga: unitPrice,
                foto: this.activeMenu.image || null
            };

            // Gabungkan jika menu, varian, dan catatan persis sama
            const existingIndex = this.cart.findIndex(item =>
                item.menu_id === cartItem.menu_id &&
                item.varian_id === cartItem.varian_id &&
                (item.catatan || '') === (cartItem.catatan || '')
            );

            if (existingIndex > -1) {
                this.cart[existingIndex].jumlah += cartItem.jumlah;
            } else {
                this.cart.push(cartItem);
            }

            this.saveCart();
            this.showToast('Pesanan ditambahkan ke keranjang');
            this.closeBottomSheet();
        },

        showToast(msg) {
            this.toastMessage = msg;
            this.toastShow = true;
            setTimeout(() => {
                this.toastShow = false;
            }, 2500);
        }
    };
}
</script>
@endpush
