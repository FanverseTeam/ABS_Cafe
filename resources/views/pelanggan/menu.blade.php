@extends('pelanggan.layouts.app')

@section('title', 'Menu - ' . ($landing['cafe']['name'] ?? 'ABS Cafe'))
@section('meta_description', 'Jelajahi menu kopi pilihan, minuman segar, dan makanan lezat di ' . ($landing['cafe']['name'] ?? 'ABS Cafe') . ' Tulungagung.')

@section('body_class', 'bg-[#F2F5E2] text-[#290024]')
@section('body_style', 'background-color: #F2F5E2 !important; color: #290024 !important;')
@section('main_class', 'bg-[#F2F5E2]')

{{-- Header Khusus Halaman Menu: Logo + "ABS Cafe" di Kiri --}}
@section('header')
<header x-data="{ isScrolled: false }"
        x-init="isScrolled = (window.pageYOffset > 10)"
        @scroll.window="isScrolled = (window.pageYOffset > 10)"
        :class="isScrolled ? 'bg-[#F2F5E2]/95 backdrop-blur-md border-b border-[#E3DEA4]/80 shadow-xs' : 'bg-[#F2F5E2] border-b border-[#E3DEA4]/60'"
        style="background-color: #F2F5E2 !important;"
        class="sticky top-0 inset-x-0 z-30 transition-all duration-200">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 h-16 sm:h-20 flex items-center gap-4">
        <!-- Logo & Branding di Kiri -->
        <a href="{{ route('pelanggan.beranda') }}"
           class="group flex items-center gap-3 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#290024] rounded-xl pr-2"
           aria-label="Kembali ke Beranda ABS Cafe">
            <x-logo :show-text="false" size="sm" class="shrink-0 group-hover:scale-105 transition-transform" />
            <span class="font-serif text-lg sm:text-xl font-bold tracking-tight text-[#290024] leading-tight">
                {{ $landing['cafe']['name'] ?? 'ABS Cafe' }}
            </span>
        </a>
    </div>
</header>
@endsection

@section('content')
<div x-data="menuPage()"
     x-init="initPage()"
     style="background-color: #F2F5E2 !important;"
     class="min-h-screen bg-[#F2F5E2] pb-28 pt-2 sm:pt-4">

    <!-- Kontainer Utama -->
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 space-y-6 sm:space-y-8">

        <!-- Judul Besar & Subjudul -->
        <div class="space-y-1">
            <div class="flex items-center justify-between gap-3 flex-wrap">
                <h1 class="font-serif text-2xl sm:text-3xl lg:text-4xl font-bold text-[#290024] tracking-tight">
                    Menu
                </h1>
                @if($tableNumber)
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#E3DEA4]/50 border border-[#E3DEA4] text-[#290024] text-xs font-semibold">
                        <span class="w-2 h-2 rounded-full bg-[#D4954D]"></span>
                        <span>Pesan di <strong>Meja #{{ $tableNumber }}</strong></span>
                    </div>
                @endif
            </div>
            <p class="font-sans text-xs sm:text-sm text-[#775533] font-medium tracking-wide">
                Rasa Yang Selalu Bikin Rindu
            </p>
        </div>

        <!-- Bar Pencarian Real-Time (Client-Side Instan) -->
        <div class="relative bg-[#F2F5E2] border border-[#E3DEA4] rounded-2xl sm:rounded-full p-2 sm:p-2.5 shadow-soft">
            <div class="flex items-center gap-2">
                <div class="pl-2.5 text-[#775533]">
                    <svg class="w-5 h-5 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </div>
                <input type="text"
                       x-model="searchQuery"
                       x-ref="searchInput"
                       placeholder="Cari menu kesukaan..."
                       class="flex-1 bg-transparent text-xs sm:text-sm text-[#290024] placeholder-[#775533]/50 focus:outline-none py-1.5 px-2"
                       aria-label="Cari menu">
                <button type="button"
                        x-show="searchQuery.length > 0"
                        x-cloak
                        @click="searchQuery = ''; $refs.searchInput.focus()"
                        class="flex items-center justify-center w-7 h-7 rounded-full text-[#775533] hover:text-[#290024] hover:bg-[#E3DEA4]/50 transition-colors shrink-0"
                        aria-label="Hapus pencarian">
                    <svg class="w-4 h-4 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Pill Kategori Scroll Horizontal (Alpine-driven) -->
        <div class="relative -mx-4 px-4 sm:mx-0 sm:px-0">
            <div class="flex items-center gap-2 sm:gap-2.5 overflow-x-auto no-scrollbar scroll-smooth py-1"
                 role="tablist"
                 aria-label="Kategori Menu"
                 x-ref="categoryScroll">
                <!-- Pill: Semua -->
                <button type="button"
                        @click="setCategory('semua')"
                        class="category-pill shrink-0 inline-flex items-center justify-center px-5 sm:px-6 py-2 rounded-full text-xs sm:text-sm transition-all shadow-xs"
                        :class="activeCategory === 'semua' ? 'category-pill-active bg-[#290024] text-[#F2F5E2] font-semibold border border-[#290024]' : 'bg-[#F2F5E2] hover:bg-[#E3DEA4]/50 text-[#775533] font-medium border border-[#E3DEA4]'"
                        role="tab"
                        :aria-selected="activeCategory === 'semua' ? 'true' : 'false'">
                    Semua
                </button>

                <!-- Pill Tiap Kategori dari DB -->
                @foreach($categories as $category)
                    <button type="button"
                            @click="setCategory('{{ $category->slug }}')"
                            class="category-pill shrink-0 inline-flex items-center justify-center px-5 sm:px-6 py-2 rounded-full text-xs sm:text-sm transition-all shadow-xs"
                            :class="activeCategory === '{{ $category->slug }}' ? 'category-pill-active bg-[#290024] text-[#F2F5E2] font-semibold border border-[#290024]' : 'bg-[#F2F5E2] hover:bg-[#E3DEA4]/50 text-[#775533] font-medium border border-[#E3DEA4]'"
                            role="tab"
                            :aria-selected="activeCategory === '{{ $category->slug }}' ? 'true' : 'false'">
                        {{ $category->name }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- ============================================================= --}}
        {{-- KONTEN MENU: ALPINE REAL-TIME FILTERING                      --}}
        {{-- ============================================================= --}}

        <!-- Hasil Filtering Real-Time (Ditampilkan saat mengetik atau memilih kategori) -->
        <div x-show="isFiltering" x-cloak>
            <!-- Header Hasil -->
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-serif font-bold text-lg sm:text-xl text-[#290024]">
                    <template x-if="searchQuery.length > 0">
                        <span>Hasil Pencarian "<span x-text="searchQuery"></span>"</span>
                    </template>
                    <template x-if="searchQuery.length === 0 && activeCategory !== 'semua'">
                        <span x-text="activeCategoryName"></span>
                    </template>
                </h2>
                <span class="text-xs text-[#775533] font-medium">
                    <span x-text="filteredMenus.length"></span> menu ditemukan
                </span>
            </div>

            <!-- Grid Kartu Menu Dinamis -->
            <template x-if="filteredMenus.length > 0">
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-5">
                    <template x-for="menu in filteredMenus" :key="menu.id">
                        <div class="bg-[#F2F5E2] rounded-2xl border border-[#E3DEA4]/80 p-2.5 sm:p-3 shadow-soft hover:shadow-md hover:border-[#D4954D]/70 transition-all duration-200 flex flex-col justify-between group">
                            <div>
                                <!-- Thumbnail Foto Persegi -->
                                <div class="relative w-full aspect-square rounded-xl overflow-hidden bg-[#E3DEA4]/25 border border-[#E3DEA4]/60 mb-2 sm:mb-2.5 flex items-center justify-center">
                                    <template x-if="menu.image">
                                        <img :src="menu.image"
                                             :alt="menu.name"
                                             loading="lazy"
                                             decoding="async"
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                             :class="menu.is_habis ? 'opacity-45 grayscale-[30%]' : ''">
                                    </template>
                                    <template x-if="!menu.image">
                                        <div class="w-full h-full bg-gradient-to-br from-[#775533]/20 via-[#D4954D]/15 to-[#E3DEA4]/30 flex items-center justify-center text-[#775533]">
                                            <svg class="w-8 h-8 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <path d="M18 8h1a4 4 0 0 1 0 8h-1"></path>
                                                <path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"></path>
                                                <line x1="6" y1="1" x2="6" y2="4"></line>
                                                <line x1="10" y1="1" x2="10" y2="4"></line>
                                                <line x1="14" y1="1" x2="14" y2="4"></line>
                                            </svg>
                                        </div>
                                    </template>

                                    <!-- Badge Habis -->
                                    <template x-if="menu.is_habis">
                                        <span class="absolute top-2 left-2 px-2 py-0.5 rounded-md bg-[#290024]/85 text-[#E3DEA4] text-[9px] sm:text-[10px] font-bold uppercase tracking-wider backdrop-blur-xs shadow-xs">
                                            Habis
                                        </span>
                                    </template>
                                </div>

                                <!-- Nama Menu -->
                                <h3 class="font-serif font-bold text-xs sm:text-sm text-[#290024] line-clamp-1 leading-snug group-hover:text-[#775533] transition-colors"
                                    :title="menu.name"
                                    x-text="menu.name"></h3>
                            </div>

                            <!-- Baris Harga & Tombol Tambah -->
                            <div class="mt-2.5 pt-1.5 flex items-center justify-between gap-1">
                                <span class="font-sans font-semibold text-[11px] sm:text-xs text-[#775533] truncate"
                                      x-text="menu.price_label"></span>

                                <template x-if="menu.is_habis">
                                    <button type="button"
                                            disabled
                                            class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-[#E3DEA4]/30 text-[#775533]/50 flex items-center justify-center cursor-not-allowed opacity-50 shrink-0">
                                        <svg class="w-3.5 h-3.5 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <line x1="12" y1="5" x2="12" y2="19"></line>
                                            <line x1="5" y1="12" x2="19" y2="12"></line>
                                        </svg>
                                    </button>
                                </template>
                                <template x-if="!menu.is_habis">
                                    <button type="button"
                                            @click="openBottomSheet(menu)"
                                            :aria-label="'Pilih varian dan tambah ' + menu.name + ' ke keranjang'"
                                            class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-[#290024] text-[#F2F5E2] hover:bg-[#D4954D] hover:text-[#290024] active:scale-90 flex items-center justify-center shrink-0 transition-all shadow-xs focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#290024]">
                                        <svg class="w-3.5 h-3.5 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <line x1="12" y1="5" x2="12" y2="19"></line>
                                            <line x1="5" y1="12" x2="19" y2="12"></line>
                                        </svg>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            <!-- State Kosong / Tidak Ditemukan -->
            <template x-if="filteredMenus.length === 0">
                <div class="bg-[#F2F5E2] border border-[#E3DEA4] rounded-3xl p-8 sm:p-12 text-center max-w-md mx-auto my-8 shadow-soft">
                    <div class="w-12 h-12 rounded-full bg-[#E3DEA4]/40 flex items-center justify-center text-[#775533] mx-auto mb-4">
                        <svg class="w-6 h-6 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </div>
                    <h3 class="font-serif font-bold text-base sm:text-lg text-[#290024] mb-1.5">
                        Menu tidak ditemukan
                    </h3>
                    <p class="text-xs sm:text-sm text-[#775533] leading-relaxed mb-6">
                        Coba kata kunci lain atau pilih kategori yang berbeda.
                    </p>
                    <button type="button"
                            @click="searchQuery = ''; setCategory('semua')"
                            class="inline-flex items-center justify-center px-6 py-2.5 rounded-full bg-[#290024] hover:bg-[#D4954D] hover:text-[#290024] text-white text-xs font-semibold tracking-wide transition-colors">
                        Lihat Semua Menu
                    </button>
                </div>
            </template>
        </div>

        <!-- Konten Default SSR (Tersembunyi saat filtering aktif) -->
        <div x-show="!isFiltering">
            @if(isset($recommendedMenus) && $recommendedMenus->isNotEmpty())
                <section class="space-y-3.5">
                    <div class="flex items-center justify-between">
                        <h2 class="font-serif font-bold text-lg sm:text-xl text-[#290024]">
                            Rekomendasi
                        </h2>
                        <button type="button"
                                @click="setCategory('rekomendasi')"
                                class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#775533] hover:text-[#D4954D] transition-colors py-1 group">
                            <span>Lihat semua</span>
                            <svg class="w-3.5 h-3.5 stroke-current group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </button>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-5">
                        @foreach($recommendedMenus as $menu)
                            @include('pelanggan.partials.menu-card', ['menu' => $menu])
                        @endforeach
                    </div>
                </section>
            @endif

            @if(isset($categoriesWithMenus))
                @foreach($categoriesWithMenus as $category)
                    <section class="space-y-3.5 pt-2">
                        <div class="flex items-center justify-between">
                            <h2 class="font-serif font-bold text-lg sm:text-xl text-[#290024]">
                                {{ $category->name }}
                            </h2>
                            <button type="button"
                                    @click="setCategory('{{ $category->slug }}')"
                                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#775533] hover:text-[#D4954D] transition-colors py-1 group">
                                <span>Lihat semua</span>
                                <svg class="w-3.5 h-3.5 stroke-current group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                    <polyline points="12 5 19 12 12 19"></polyline>
                                </svg>
                            </button>
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
                 class="pointer-events-auto w-full sm:max-w-[560px] max-h-[90vh] bg-[#F2F5E2] rounded-t-3xl sm:rounded-3xl border border-[#E3DEA4] shadow-2xl flex flex-col overflow-hidden">

                <!-- Header Modal: Tombol Tutup -->
                <div class="p-4 sm:p-5 pb-2 flex items-center justify-between border-b border-[#E3DEA4]/60">
                    <span class="text-xs font-semibold text-[#775533] tracking-wide uppercase">
                        Detail Pesanan
                    </span>
                    <button type="button"
                            @click="closeBottomSheet()"
                            class="w-8 h-8 rounded-full flex items-center justify-center text-[#290024] hover:bg-[#E3DEA4]/40 transition-colors"
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
                            <div class="relative w-full aspect-[16/9] sm:aspect-[2/1] rounded-2xl overflow-hidden bg-[#E3DEA4]/25 border border-[#E3DEA4] flex items-center justify-center">
                                <template x-if="activeMenu.image">
                                    <img :src="activeMenu.image"
                                         :alt="activeMenu.name"
                                         class="w-full h-full object-cover">
                                </template>
                                <template x-if="!activeMenu.image">
                                    <div class="w-12 h-12 rounded-full bg-[#E3DEA4]/40 flex items-center justify-center text-[#775533]">
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
                                                       'border-[#290024] bg-[#E3DEA4]/40 ring-2 ring-[#290024]/10 shadow-xs': selectedVariantId === variant.id && !variant.is_habis,
                                                       'border-[#E3DEA4] bg-[#F2F5E2] hover:bg-[#E3DEA4]/25': selectedVariantId !== variant.id && !variant.is_habis,
                                                       'opacity-50 cursor-not-allowed border-[#E3DEA4]/50 bg-[#E3DEA4]/20': variant.is_habis
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
                                    <div class="flex items-center gap-3 bg-[#E3DEA4]/30 border border-[#E3DEA4] rounded-full p-1 shadow-xs">
                                        <button type="button"
                                                @click="quantity > 1 ? quantity-- : null"
                                                :disabled="quantity <= 1"
                                                class="w-8 h-8 rounded-full flex items-center justify-center text-[#290024] hover:bg-[#F2F5E2] disabled:opacity-40 transition-colors"
                                                aria-label="Kurangi jumlah">
                                            <svg class="w-4 h-4 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                            </svg>
                                        </button>
                                        <span class="w-8 text-center font-semibold text-sm text-[#290024]"
                                              x-text="quantity"></span>
                                        <button type="button"
                                                @click="quantity++"
                                                class="w-8 h-8 rounded-full flex items-center justify-center text-[#290024] hover:bg-[#F2F5E2] transition-colors"
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
                                              class="w-full text-xs sm:text-sm p-3 rounded-xl bg-white/80 border border-[#E3DEA4] focus:border-[#290024] focus:outline-none placeholder-[#775533]/50 text-[#290024] resize-none"></textarea>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Bagian Bawah Modal: Total Harga & Tombol Tambah ke Keranjang -->
                <div class="p-4 sm:p-5 pt-3 bg-[#E3DEA4]/25 border-t border-[#E3DEA4] flex items-center justify-between gap-4">
                    <div>
                        <span class="block text-[11px] text-[#775533]">Total Harga</span>
                        <span class="font-serif font-bold text-base sm:text-lg text-[#290024]"
                              x-text="formattedTotalPrice()"></span>
                    </div>

                    <button type="button"
                            @click="addToCart()"
                            class="flex-1 max-w-[240px] inline-flex items-center justify-center gap-2 min-h-[44px] px-6 py-2.5 rounded-full bg-[#290024] hover:bg-[#D4954D] hover:text-[#290024] text-[#F2F5E2] font-semibold text-xs sm:text-sm tracking-wide transition-all shadow-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#290024]">
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
         x-transition:enter-start="opacity-0 translate-y-6 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-6 scale-95"
         class="fixed bottom-4 inset-x-4 max-w-md mx-auto sm:max-w-lg z-40 bg-[#290024] text-white rounded-2xl p-2.5 sm:p-3 border border-[#D4954D]/30 shadow-[0_12px_36px_rgba(41,0,36,0.35)] flex items-center justify-between gap-3">

        <!-- Info Jumlah Item & Estimasi Total -->
        <div class="flex items-center gap-3 pl-1.5">
            <div class="relative w-10 h-10 rounded-xl bg-[#D4954D] text-[#290024] flex items-center justify-center shrink-0 shadow-sm font-bold">
                <svg class="w-5 h-5 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <path d="M16 10a4 4 0 0 1-8 0"></path>
                </svg>
                <span class="absolute -top-1.5 -right-1.5 min-w-[20px] h-5 px-1 rounded-full bg-white text-[#290024] text-[10px] font-black flex items-center justify-center shadow-xs border border-[#290024]"
                      x-text="cartTotalItems"></span>
            </div>
            <div class="text-left">
                <div class="text-[11px] text-[#E3DEA4] font-medium leading-none mb-1">
                    <span x-text="cartTotalItems"></span> Menu Dipilih
                </div>
                <div class="text-sm sm:text-base font-serif font-bold text-white leading-none tracking-tight"
                     x-text="formattedCartTotal()"></div>
            </div>
        </div>

        <!-- Tombol Menuju Halaman Keranjang -->
        <a href="{{ route('pelanggan.keranjang', array_filter(['meja' => $tableNumber])) }}"
           class="inline-flex items-center justify-center gap-2 px-5 sm:px-6 py-2.5 rounded-full bg-[#D4954D] hover:bg-[#c4873f] text-[#290024] font-bold text-xs sm:text-sm tracking-wide transition-all shadow-sm active:scale-95 shrink-0">
            <span>Lihat Keranjang</span>
            <svg class="w-4 h-4 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
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

</div>
@endsection

@section('footer')
<footer class="bg-[#F2F5E2] border-t border-[#E3DEA4]/80 py-6 sm:py-8 text-center text-xs text-[#775533]" style="background-color: #F2F5E2 !important;">
    <div class="max-w-4xl mx-auto px-4 space-y-1.5">
        <p class="font-serif font-bold text-[#290024]">{{ $landing['cafe']['name'] ?? 'ABS Cafe' }}</p>
        <p class="text-[11px] text-[#775533]/80">{{ $landing['cafe']['tagline'] ?? 'Good Coffee, Better Moments.' }}</p>
        <p class="text-[10px] text-[#775533]/60">&copy; {{ date('Y') }} Hak cipta dilindungi.</p>
    </div>
</footer>
@endsection

@push('scripts')
<script>
function menuPage() {
    return {
        searchQuery: '',
        activeCategory: '{{ $selectedCategorySlug ?? "semua" }}',
        allMenus: @json($allMenusPayload ?? []),
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

        setCategory(slug) {
            this.activeCategory = slug;
            // Scroll pill yang aktif ke tengah viewport
            this.$nextTick(() => {
                const activePill = this.$refs.categoryScroll?.querySelector(
                    '[aria-selected="true"]'
                );
                if (activePill) {
                    activePill.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                }
            });
        },

        get activeCategoryName() {
            if (this.activeCategory === 'semua') return 'Semua';
            if (this.activeCategory === 'rekomendasi') return 'Rekomendasi';
            const found = this.allMenus.find(m => m.category_slug === this.activeCategory);
            return found ? found.category_name : this.activeCategory;
        },

        get isFiltering() {
            return this.searchQuery.trim().length > 0 || this.activeCategory !== 'semua';
        },

        get filteredMenus() {
            let results = this.allMenus;
            const query = this.searchQuery.trim().toLowerCase();

            // Filter berdasarkan kategori terlebih dahulu
            if (this.activeCategory !== 'semua') {
                if (this.activeCategory === 'rekomendasi') {
                    results = results.filter(m => m.is_best_seller);
                } else {
                    results = results.filter(m => m.category_slug === this.activeCategory);
                }
            }

            // Filter berdasarkan kata kunci pencarian (instan per huruf)
            if (query.length > 0) {
                results = results.filter(m =>
                    m.name.toLowerCase().includes(query) ||
                    m.description.toLowerCase().includes(query)
                );
            }

            return results;
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
