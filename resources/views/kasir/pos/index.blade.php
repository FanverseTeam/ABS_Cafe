@extends('kasir.layouts.app')

@section('title', 'Pesan Manual (POS) - Kasir')
@section('page_title', 'Pesan Manual / POS')

@section('content')
<style>
    .pos-menu-card { transition: transform .15s, box-shadow .15s; }
    .pos-menu-card:hover { transform: translateY(-2px); box-shadow: 0 10px 25px -3px rgba(41,0,36,.1); }
    .pos-menu-card:active { transform: scale(.98); }
    .qty-btn { transition: background .15s, transform .1s; }
    .qty-btn:active { transform: scale(0.92); }
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>

@php
    $posMenuData = $categories->flatMap->menus->mapWithKeys(fn ($menu) => [
        $menu->id => [
            'id' => $menu->id,
            'name' => $menu->name,
            'base_name' => $menu->variantBaseName(),
            'price' => (float) $menu->price,
            'stock' => $menu->stock,
            'description' => $menu->description,
            'image' => $menu->image,
            'is_available' => (bool) $menu->is_available,
            'variants' => $menu->variants->map(fn ($variant) => [
                'id' => $variant->id,
                'name' => $variant->name,
                'size' => $variant->size,
                'display_name' => $variant->displayName(),
                'price' => (float) $variant->price,
                'is_available' => (bool) $variant->is_available,
            ])->values(),
        ],
    ]);
@endphp

<!-- ==================== VIEW 1: KATALOG MENU & KERANJANG CEPAT ==================== -->
<div id="pos-view-catalog" class="space-y-4">
    <div class="grid grid-cols-1 md:landscape:grid-cols-12 lg:grid-cols-12 xl:grid-cols-12 gap-4 lg:gap-6 relative">
        <!-- Katalog Menu (Kiri di desktop, tablet, dan hp landscape) -->
        <div class="md:landscape:col-span-7 lg:col-span-7 xl:col-span-8 space-y-3 sm:space-y-4">
            <!-- Search Bar & Kategori Pill Tabs -->
            <div class="sticky top-14 sm:top-16 landscape:top-12 z-20 -mx-3 sm:mx-0 px-3 sm:px-0 py-2 sm:py-2.5 bg-[#F2F5E2]/95 backdrop-blur-md border-b border-[#E3DEA4]/60 sm:border-0 space-y-2">
                <!-- Search Menu Input -->
                <div class="relative w-full">
                    <input type="text"
                           id="pos-menu-search"
                           placeholder="Cari menu untuk customer (misal: Latte, Toast, Croissant)..."
                           oninput="handleMenuSearch(this.value)"
                           autocomplete="off"
                           class="w-full bg-[#F2F5E2] text-xs text-[#290024] placeholder:text-[#775533]/60 pl-9 pr-8 py-2 sm:py-2.5 rounded-full border border-[#E3DEA4] focus:border-[#D4954D] focus:outline-none focus:ring-1 focus:ring-[#D4954D]/50 shadow-sm transition-all">
                    <i data-lucide="search" class="w-4 h-4 text-[#775533] absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                    <button type="button"
                            id="clear-search-btn"
                            onclick="clearMenuSearch()"
                            class="hidden absolute right-2.5 top-1/2 -translate-y-1/2 w-5 h-5 rounded-full bg-[#E3DEA4]/60 text-[#775533] hover:text-[#290024] flex items-center justify-center text-xs">
                        <i data-lucide="x" class="w-3.5 h-3.5"></i>
                    </button>
                </div>

                <!-- Categories Tabs -->
                <div class="flex items-center justify-between gap-2">
                    <div class="flex gap-1.5 sm:gap-2 overflow-x-auto no-scrollbar py-0.5 scroll-smooth flex-1">
                        <button type="button" data-cat="all" class="cat-tab shrink-0 px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-xs font-semibold bg-[#290024] text-[#F2F5E2] shadow-sm transition-all duration-200">
                            Semua
                        </button>
                        @foreach($categories as $category)
                            <button type="button" data-cat="{{ $category->id }}" class="cat-tab shrink-0 px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-xs font-semibold bg-[#F2F5E2] text-[#775533] border border-[#E3DEA4] hover:bg-[#E3DEA4]/60 transition-all duration-200">
                                {{ $category->name }}
                            </button>
                        @endforeach
                    </div>
                    <span id="pos-menu-counter" class="text-[10px] sm:text-[11px] text-[#775533] shrink-0 font-medium hidden sm:inline">
                        {{ $categories->sum(fn ($c) => $c->menus->count()) }} menu
                    </span>
                </div>
            </div>

            <!-- Daftar Menu per Kategori -->
            @foreach($categories as $category)
                <section data-cat-section="{{ $category->id }}" class="menu-cat space-y-2.5 sm:space-y-3">
                    <div class="flex items-center justify-between pt-1">
                        <h3 class="font-serif font-bold text-sm sm:text-base text-[#290024] flex items-center gap-2">
                            <span class="w-6 h-6 sm:w-7 sm:h-7 rounded-lg bg-[#D4954D]/20 text-[#D4954D] flex items-center justify-center">
                                <i data-lucide="{{ $category->icon ?: 'coffee' }}" class="w-3.5 h-3.5 sm:w-4 sm:h-4"></i>
                            </span>
                            {{ $category->name }}
                        </h3>
                        <span class="text-[10px] sm:text-[11px] text-[#775533]">{{ $category->menus->count() }} item</span>
                    </div>

                    <!-- Grid Menu (2-kolom mobile portrait, 2-kolom hp landscape, 3-kolom tablet/desktop) -->
                    <div class="grid grid-cols-2 md:landscape:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-2.5 sm:gap-3">
                        @forelse($category->menus as $menu)
                            @php
                                $availableVariants = $menu->variants->where('is_available', true);
                                $defaultPrice = $availableVariants->count() ? (float) $availableVariants->min('price') : (float) $menu->price;
                                $hasVariants = $menu->variants->count() > 1;
                                $priceLabel = $hasVariants ? $menu->priceRangeLabel() : 'Rp '.number_format($defaultPrice, 0, ',', '.');
                                $isSoldOut = ! $menu->is_available || ($menu->variants->isNotEmpty() && $availableVariants->isEmpty());
                            @endphp
                            <div class="menu-item-wrapper pos-menu-card bg-[#F2F5E2] rounded-2xl border border-[#E3DEA4]/70 p-2.5 sm:p-3 landscape:p-2.5 shadow-soft flex flex-col justify-between cursor-pointer group {{ $isSoldOut ? 'opacity-50 pointer-events-none' : '' }}"
                                 data-menu-name="{{ strtolower($menu->name) }}"
                                 data-cat-id="{{ $category->id }}"
                                 onclick="handleMenuClick({{ $menu->id }})">
                                <div>
                                    <!-- Thumbnail Foto Membulat -->
                                    <div class="relative w-full aspect-square landscape:aspect-[16/10] sm:landscape:aspect-square rounded-xl bg-[#F2F5E2] overflow-hidden mb-2 sm:mb-2.5 flex items-center justify-center">
                                        @if($menu->image)
                                            <img src="{{ $menu->image }}" alt="{{ $menu->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        @else
                                            <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-[#E3DEA4]/40 flex items-center justify-center text-[#D4954D]">
                                                <i data-lucide="coffee" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                                            </div>
                                        @endif

                                        @if($menu->is_best_seller)
                                            <span class="absolute top-1.5 left-1.5 px-2 py-0.5 rounded-full bg-[#D4954D] text-[#290024] text-[9px] font-bold shadow-sm">
                                                Terlaris
                                            </span>
                                        @endif
                                        @if($isSoldOut)
                                            <span class="absolute inset-0 bg-[#290024]/75 text-beige text-[10px] sm:text-xs font-bold flex items-center justify-center uppercase tracking-wide">
                                                Habis
                                            </span>
                                        @endif
                                    </div>
                                    <h4 class="font-serif font-bold text-xs sm:text-sm text-[#290024] leading-tight line-clamp-1 group-hover:text-[#775533] transition-colors">
                                        {{ $menu->name }}
                                    </h4>
                                    <p class="text-[10px] sm:text-[11px] text-[#775533] line-clamp-2 mt-0.5 leading-snug">
                                        {{ $menu->description ?: 'Menu nikmat dari ABS Cafe.' }}
                                    </p>
                                </div>
                                <div class="mt-2.5 pt-2 border-t border-[#E3DEA4]/50 flex items-center justify-between">
                                    <span class="font-serif font-bold text-xs sm:text-sm text-[#290024]">
                                        {{ $priceLabel }}
                                    </span>
                                    <span class="w-6 h-6 sm:w-7 sm:h-7 rounded-full bg-[#290024] text-beige flex items-center justify-center group-hover:bg-[#D4954D] group-hover:text-[#290024] transition-colors shadow-sm">
                                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="col-span-full text-xs text-[#775533] py-4 text-center bg-[#F2F5E2] rounded-2xl border border-[#E3DEA4]/50">
                                Tidak ada menu pada kategori ini.
                            </p>
                        @endforelse
                    </div>
                </section>
            @endforeach
        </div>

        <!-- Sidebar Keranjang di Desktop & Landscape Tablet -->
        <div class="hidden md:landscape:block lg:block xl:block md:landscape:col-span-5 lg:col-span-5 xl:col-span-4 md:landscape:sticky md:landscape:top-14 lg:sticky lg:top-20 xl:top-24 h-fit">
            <div class="bg-[#F2F5E2] rounded-3xl border border-[#E3DEA4]/80 shadow-soft-lg overflow-hidden">
                <div class="px-4 py-2.5 bg-gradient-to-r from-[#290024] to-[#290024] text-[#F2F5E2] flex items-center justify-between gap-2 border-b border-[#D4954D]/30">
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="w-2 h-2 rounded-full bg-[#D4954D] animate-pulse shrink-0"></span>
                        <span class="text-xs font-bold tracking-wide truncate">Pesan Manual Customer</span>
                    </div>
                    <span class="text-[9px] bg-[#D4954D] text-[#290024] px-2 py-0.5 rounded-full font-bold uppercase shrink-0">Kasir POS</span>
                </div>

                <div class="px-4 sm:px-5 py-3 border-b border-[#E3DEA4]/60 flex items-center justify-between bg-[#F2F5E2]/60">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-2xl bg-[#D4954D]/20 text-[#D4954D] flex items-center justify-center">
                            <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h3 class="font-serif font-bold text-xs sm:text-sm text-[#290024]">Keranjang Pesanan</h3>
                            <p id="catalog-cart-count" class="text-[10px] text-[#775533]">0 item</p>
                        </div>
                    </div>
                    <button type="button" onclick="clearCart()" class="text-[10px] text-[#775533] hover:text-[#775533] font-medium flex items-center gap-1 transition-colors">
                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Kosongkan
                    </button>
                </div>

                <div class="p-4 space-y-3">
                    <div id="catalog-cart-items" class="max-h-60 overflow-y-auto divide-y divide-[#E3DEA4]/40 space-y-1">
                        <div class="py-6 text-center" id="catalog-cart-empty">
                            <i data-lucide="clipboard-list" class="w-8 h-8 mx-auto text-[#775533]/40"></i>
                            <p class="text-xs text-[#775533] mt-2">Keranjang masih kosong</p>
                            <p class="text-[10px] text-[#775533]/70">Pilih menu dari katalog di samping.</p>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-[#F2F5E2] border border-[#E3DEA4]/70 p-3 flex justify-between items-center text-xs">
                        <span class="font-bold text-[#290024]">Total Tagihan</span>
                        <span id="catalog-cart-total" class="font-serif font-bold text-[#290024] text-base">Rp 0</span>
                    </div>

                    <button type="button" onclick="goToCheckout()" class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-full bg-[#290024] hover:bg-[#775533] text-[#F2F5E2] font-bold text-xs shadow-md transition-all active:scale-95">
                        <i data-lucide="credit-card" class="w-4 h-4 text-[#D4954D]"></i>
                        <span>Lanjut ke Menu Bayar</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                    </button>
                </div>
            </div>

            <!-- Pesanan Ditahan -->
            @if($heldOrders->isNotEmpty())
                <div class="mt-3 bg-[#F2F5E2] rounded-3xl border border-[#E3DEA4]/80 shadow-soft overflow-hidden">
                    <div class="px-4 py-2.5 border-b border-[#E3DEA4]/50 flex items-center justify-between">
                        <h4 class="font-serif font-bold text-xs text-[#290024]">Pesanan Ditahan ({{ $heldOrders->count() }})</h4>
                    </div>
                    <div class="p-2.5 space-y-2">
                        @foreach($heldOrders as $held)
                            <div class="flex items-center justify-between gap-2 p-2 rounded-2xl bg-[#F2F5E2] border border-[#E3DEA4]/60">
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-[#290024]">{{ $held->order_code }}</p>
                                    <p class="text-[10px] text-[#775533] truncate">{{ $held->customer_name ?: 'Pelanggan' }} &bull; {{ $held->table_number ? 'Meja '.$held->table_number : 'Take Away' }}</p>
                                    <p class="text-[10px] text-[#290024] font-semibold mt-0.5">Rp {{ number_format($held->total, 0, ',', '.') }}</p>
                                </div>
                                <div class="flex gap-1.5 shrink-0">
                                    <a href="{{ route('kasir.pos.index', ['resume' => $held->id]) }}" class="px-2.5 py-1 rounded-full bg-[#E3DEA4] text-[#290024] text-[10px] font-semibold hover:bg-[#D4954D] transition-colors">Buka</a>
                                    <a href="{{ route('kasir.payments.checkout', $held) }}" class="px-2.5 py-1 rounded-full bg-[#290024] text-[#F2F5E2] text-[10px] font-semibold hover:bg-[#775533] transition-colors">Bayar</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- ==================== VIEW 2: MENU BAYAR (FULL SCREEN / PAGE, BUKAN POP-UP) ==================== -->
<div id="pos-view-checkout" class="hidden space-y-4 sm:space-y-6">
    <!-- Header Menu Bayar: Kembali ke Katalog & Judul -->
    <div class="flex flex-wrap items-center justify-between gap-3 bg-[#F2F5E2] p-4 sm:p-5 rounded-3xl border border-[#E3DEA4] shadow-soft">
        <button type="button" onclick="goToCatalog()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-[#E3DEA4]/60 hover:bg-[#E3DEA4] text-[#290024] text-xs font-bold transition-all shadow-sm active:scale-95">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Tambah Menu Lain / Kembali ke Katalog</span>
        </button>
        <div class="text-right">
            <h2 class="font-serif font-bold text-sm sm:text-base text-[#290024] flex items-center justify-end gap-1.5">
                <i data-lucide="credit-card" class="w-4 h-4 text-[#D4954D]"></i>
                Menu Pembayaran Pesanan
            </h2>
            <p class="text-[10px] text-[#775533]">Lengkapi data pelanggan dan proses pembayaran langsung</p>
        </div>
    </div>

    <!-- Form Pembayaran POS Lengkap -->
    <form id="pos-form" action="{{ route('kasir.pos.store') }}" method="POST">
        @csrf
        <input type="hidden" name="cart" id="cart-input" value="[]">
        <input type="hidden" name="voucher" id="voucher-input" value="">
        <input type="hidden" name="payment_method" id="payment-method-input" value="tunai">
        <input type="hidden" name="cash_received" id="cash-received-input" value="">
        <input type="hidden" name="reference" id="reference-input" value="">
        <input type="hidden" name="action" id="action-input" value="pay">

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 lg:gap-6">
            <!-- Kolom Kiri: Data Pelanggan & Metode Pembayaran -->
            <div class="lg:col-span-7 space-y-4">
                <!-- KARTU DATA PELANGGAN -->
                <div class="bg-[#F2F5E2] rounded-3xl border border-[#E3DEA4]/80 shadow-soft p-5 sm:p-6 space-y-4">
                    <div class="flex items-center gap-2.5 border-b border-[#E3DEA4]/50 pb-3">
                        <div class="w-8 h-8 rounded-2xl bg-[#D4954D]/20 text-[#D4954D] flex items-center justify-center">
                            <i data-lucide="user" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h3 class="font-serif font-bold text-sm text-[#290024]">Data Pelanggan &amp; Meja</h3>
                            <p class="text-[10px] text-[#775533]">Informasi pemesan untuk transaksi ini</p>
                        </div>
                    </div>

                    <!-- Tipe Pemesanan -->
                    <div>
                        <label class="block text-xs font-bold text-[#775533] uppercase tracking-wider mb-1.5">Tipe Pemesanan <span class="text-[#775533]">*</span></label>
                        <div class="grid grid-cols-2 gap-2.5">
                            <label class="flex items-center justify-center gap-2 p-3 rounded-2xl border border-[#E3DEA4] bg-[#F2F5E2] has-[:checked]:border-[#290024] has-[:checked]:bg-[#290024] has-[:checked]:text-[#F2F5E2] cursor-pointer transition-all select-none">
                                <input type="radio" name="order_type" value="dine_in" checked onchange="handleOrderTypeChange('dine_in')" class="sr-only">
                                <i data-lucide="utensils" class="w-4 h-4 text-[#D4954D]"></i>
                                <span class="text-xs font-semibold">Dine In (Makan di Tempat)</span>
                            </label>
                            <label class="flex items-center justify-center gap-2 p-3 rounded-2xl border border-[#E3DEA4] bg-[#F2F5E2] has-[:checked]:border-[#290024] has-[:checked]:bg-[#290024] has-[:checked]:text-[#F2F5E2] cursor-pointer transition-all select-none">
                                <input type="radio" name="order_type" value="take_away" onchange="handleOrderTypeChange('take_away')" class="sr-only">
                                <i data-lucide="shopping-bag" class="w-4 h-4 text-[#D4954D]"></i>
                                <span class="text-xs font-semibold">Take Away (Bawa Pulang)</span>
                            </label>
                        </div>
                    </div>

                    <!-- Nomor Meja (Wajib jika Dine In) -->
                    <div id="checkout-table-wrapper" class="transition-all">
                        <label class="block text-xs font-bold text-[#290024] mb-1.5 flex items-center justify-between">
                            <span>Nomor Meja <span class="text-[#775533]" id="table-star">*</span></span>
                            <span class="text-[10px] font-semibold text-[#775533]" id="table-badge-req">Wajib untuk Dine In</span>
                        </label>
                        <select id="table-number" name="table_number" class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs font-medium text-[#290024] px-3.5 py-2.5 sm:py-3 rounded-2xl border border-[#E3DEA4] focus:border-[#D4954D] focus:outline-none transition-all">
                            <option value="">— Pilih Nomor Meja —</option>
                            @foreach($tables as $table)
                                <option value="{{ $table->number }}" {{ $table->status === 'nonaktif' ? 'disabled' : '' }}>
                                    Meja {{ $table->number }} ({{ $table->statusBadge()['label'] }})
                                </option>
                            @endforeach
                        </select>
                        <p id="table-hint" class="text-[10px] text-[#775533] mt-1">* Wajib dipilih untuk pesanan Dine In agar meja tercatat terpakai.</p>
                    </div>

                    <!-- Nama Pelanggan (Wajib) -->
                    <div>
                        <label class="block text-xs font-bold text-[#290024] mb-1.5 flex items-center justify-between">
                            <span>Nama Pelanggan <span class="text-[#775533]">*</span></span>
                            <span class="text-[10px] font-semibold text-[#775533]">Wajib Diisi</span>
                        </label>
                        <input type="text" id="customer-name" name="customer_name" required value="{{ $resume?->customer_name ?? '' }}" placeholder="Masukkan nama pelanggan..."
                               class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs font-medium text-[#290024] placeholder:text-[#775533]/50 px-3.5 py-2.5 sm:py-3 rounded-2xl border border-[#E3DEA4] focus:border-[#D4954D] focus:outline-none transition-all">
                    </div>

                    <!-- No. Telepon & Email (Opsional) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-[#775533] mb-1.5">No. Telepon / WA <span class="text-[10px] font-normal text-[#775533]/70">(Opsional)</span></label>
                            <input type="tel" id="customer-phone" name="customer_phone" value="{{ $resume?->customer_phone ?? '' }}" placeholder="08xxxxxxxxxx"
                                   class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs text-[#290024] placeholder:text-[#775533]/50 px-3.5 py-2.5 sm:py-3 rounded-2xl border border-[#E3DEA4] focus:border-[#D4954D] focus:outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[#775533] mb-1.5">Email Pelanggan <span class="text-[10px] font-normal text-[#775533]/70">(Opsional)</span></label>
                            <input type="email" id="customer-email" name="customer_email" value="{{ $resume?->customer_email ?? '' }}" placeholder="pelanggan@email.com"
                                   class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs text-[#290024] placeholder:text-[#775533]/50 px-3.5 py-2.5 sm:py-3 rounded-2xl border border-[#E3DEA4] focus:border-[#D4954D] focus:outline-none transition-all">
                        </div>
                    </div>
                </div>

                <!-- KARTU METODE PEMBAYARAN -->
                <div class="bg-[#F2F5E2] rounded-3xl border border-[#E3DEA4]/80 shadow-soft p-5 sm:p-6 space-y-4">
                    <div class="flex items-center gap-2.5 border-b border-[#E3DEA4]/50 pb-3">
                        <div class="w-8 h-8 rounded-2xl bg-[#D4954D]/20 text-[#D4954D] flex items-center justify-center">
                            <i data-lucide="wallet" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h3 class="font-serif font-bold text-sm text-[#290024]">Metode Pembayaran</h3>
                            <p class="text-[10px] text-[#775533]">Pilih metode bayar yang digunakan pelanggan</p>
                        </div>
                    </div>

                    <!-- Tombol Pilihan Metode -->
                    <div class="grid grid-cols-2 gap-2 sm:gap-2.5" id="checkout-method-buttons">
                        @foreach($paymentMethods as $method)
                            <div data-method="{{ $method->code }}" onclick="selectMethod('{{ $method->code }}', this)"
                                 class="method-btn p-3 rounded-2xl border cursor-pointer transition-all flex items-center justify-between gap-2.5 {{ $method->code === 'tunai' ? 'bg-[#F2F5E2] border-[#290024] ring-2 ring-[#290024]/10 shadow-sm' : 'bg-[#F2F5E2] border-[#E3DEA4] hover:border-[#D4954D]' }}">
                                <div class="flex items-center gap-2 min-w-0">
                                    <div class="w-8 h-8 rounded-xl bg-[#E3DEA4]/50 text-[#775533] flex items-center justify-center shrink-0">
                                        <i data-lucide="{{ $method->icon() }}" class="w-4 h-4"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <h5 class="text-xs font-bold text-[#290024] truncate">{{ $method->name }}</h5>
                                        <p class="text-[9px] text-[#775533] truncate">{{ $method->code === 'tunai' ? 'Kasir' : ($method->code === 'qris' ? 'Scan' : 'Transfer') }}</p>
                                    </div>
                                </div>
                                <div class="method-radio w-4 h-4 rounded-full border-2 {{ $method->code === 'tunai' ? 'border-[#290024] bg-[#290024]' : 'border-[#775533]/40' }} flex items-center justify-center shrink-0">
                                    <span class="w-1.5 h-1.5 rounded-full bg-beige {{ $method->code === 'tunai' ? '' : 'hidden' }}"></span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Input Tunai & Hitung Kembalian -->
                    <div id="checkout-cash-field" class="p-4 rounded-2xl bg-[#F2F5E2] border border-[#E3DEA4]/80 space-y-3">
                        <label class="block text-xs font-bold text-[#290024]">Uang Tunai Diterima (Rp)</label>
                        <input type="number" id="checkout-cash" value="" step="1000" min="0" placeholder="0"
                               class="w-full bg-[#F2F5E2] focus:bg-beige text-base font-bold text-[#290024] px-4 py-2.5 rounded-xl border border-[#E3DEA4] focus:border-[#D4954D] focus:outline-none">

                        <div class="flex flex-wrap gap-1.5 pt-0.5">
                            <button type="button" onclick="setCash(0)" class="px-3 py-1 rounded-full bg-[#F2F5E2] border border-[#E3DEA4] text-[#290024] text-[10px] font-bold hover:bg-[#E3DEA4] transition-colors">Uang Pas</button>
                            <button type="button" onclick="setCash(20000)" class="px-3 py-1 rounded-full bg-[#F2F5E2] border border-[#E3DEA4] text-[#290024] text-[10px] font-bold hover:bg-[#E3DEA4] transition-colors">20rb</button>
                            <button type="button" onclick="setCash(50000)" class="px-3 py-1 rounded-full bg-[#F2F5E2] border border-[#E3DEA4] text-[#290024] text-[10px] font-bold hover:bg-[#E3DEA4] transition-colors">50rb</button>
                            <button type="button" onclick="setCash(100000)" class="px-3 py-1 rounded-full bg-[#F2F5E2] border border-[#E3DEA4] text-[#290024] text-[10px] font-bold hover:bg-[#E3DEA4] transition-colors">100rb</button>
                            <button type="button" onclick="setCash(150000)" class="px-3 py-1 rounded-full bg-[#F2F5E2] border border-[#E3DEA4] text-[#290024] text-[10px] font-bold hover:bg-[#E3DEA4] transition-colors">150rb</button>
                            <button type="button" onclick="setCash(200000)" class="px-3 py-1 rounded-full bg-[#F2F5E2] border border-[#E3DEA4] text-[#290024] text-[10px] font-bold hover:bg-[#E3DEA4] transition-colors">200rb</button>
                        </div>

                        <div class="flex justify-between items-center pt-2 border-t border-[#E3DEA4]/70">
                            <span class="text-xs font-semibold text-[#775533]">Kembalian Pelanggan:</span>
                            <span id="checkout-change" class="font-serif font-bold text-base sm:text-lg text-[#775533]">Rp 0</span>
                        </div>
                    </div>

                    <!-- Referensi (Opsional) -->
                    <div>
                        <label class="block text-[11px] font-semibold text-[#775533] uppercase tracking-wide mb-1">Nomor Referensi / No. Bukti Transfer (Opsional)</label>
                        <input type="text" id="checkout-reference" placeholder="Misal: No. invoice EDC, kode transaksi QRIS..."
                               class="w-full bg-[#E3DEA4]/30 focus:bg-beige text-xs text-[#290024] placeholder:text-[#775533]/50 px-3.5 py-2.5 rounded-xl border border-[#E3DEA4] focus:border-[#D4954D] focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Rincian Pesanan & Ringkasan Tagihan -->
            <div class="lg:col-span-5 space-y-4">
                <div class="bg-[#F2F5E2] rounded-3xl border border-[#E3DEA4]/80 shadow-soft-lg p-5 space-y-4 lg:sticky lg:top-20">
                    <div class="flex items-center justify-between border-b border-[#E3DEA4]/50 pb-3">
                        <div class="flex items-center gap-2">
                            <i data-lucide="shopping-bag" class="w-4 h-4 text-[#D4954D]"></i>
                            <h3 class="font-serif font-bold text-sm text-[#290024]">Rincian Item Pesanan</h3>
                        </div>
                        <span id="checkout-cart-count" class="text-xs font-semibold text-[#775533]">0 item</span>
                    </div>

                    <!-- List Item di Keranjang -->
                    <div id="checkout-cart-items" class="max-h-72 overflow-y-auto divide-y divide-[#E3DEA4]/40 pr-1 space-y-1">
                        <!-- Disuntikkan via JS renderCart() -->
                    </div>

                    <!-- Tombol Tambah Menu Lain -->
                    <button type="button" onclick="goToCatalog()" class="w-full py-2.5 px-3 rounded-2xl bg-[#E3DEA4]/40 hover:bg-[#E3DEA4]/70 text-[#290024] text-xs font-bold flex items-center justify-center gap-1.5 transition-all">
                        <i data-lucide="plus-circle" class="w-4 h-4 text-[#D4954D]"></i>
                        <span>Tambah Menu Lain ke Keranjang</span>
                    </button>

                    <!-- Kode Voucher Promo -->
                    <div class="pt-2 border-t border-[#E3DEA4]/60 space-y-1.5">
                        <label class="block text-[10px] font-bold text-[#775533] uppercase tracking-wider">Kode Voucher / Promo</label>
                        <div class="relative">
                            <input type="text" id="voucher-code" placeholder="Ketik kode promo..." class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs text-[#290024] placeholder:text-[#775533]/50 px-3 py-2 rounded-xl border border-[#E3DEA4] focus:border-[#D4954D] focus:outline-none">
                        </div>
                        @if($promos->isNotEmpty())
                            <div class="flex flex-wrap gap-1 pt-1">
                                @foreach($promos as $promo)
                                    @if($promo->code)
                                        <button type="button" onclick="document.getElementById('voucher-code').value='{{ $promo->code }}'; refreshTotals();" class="px-2 py-0.5 rounded-full bg-[#D4954D]/15 text-[#290024] text-[10px] font-bold border border-[#D4954D]/30 hover:bg-[#D4954D]/30 transition-colors">
                                            {{ $promo->code }}
                                        </button>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Ringkasan Tagihan -->
                    <div class="rounded-2xl bg-[#F2F5E2] border border-[#E3DEA4]/80 p-3.5 space-y-1.5 text-xs">
                        <div class="flex justify-between text-[#775533]"><span>Subtotal</span><span id="sum-subtotal">Rp 0</span></div>
                        <div class="flex justify-between text-[#775533]"><span>Diskon</span><span id="sum-discount" class="text-[#775533] font-medium">-Rp 0</span></div>
                        <div class="flex justify-between text-[#775533]"><span>Pajak (10%)</span><span id="sum-tax">Rp 0</span></div>
                        <div class="flex justify-between text-[#775533]"><span>Service Charge</span><span id="sum-service">Rp 0</span></div>
                        <div class="flex justify-between border-t border-[#E3DEA4] pt-2 mt-1">
                            <span class="font-bold text-[#290024] text-sm">Total Tagihan</span>
                            <span id="sum-total" class="font-serif font-bold text-[#290024] text-lg sm:text-xl">Rp 0</span>
                        </div>
                    </div>

                    <!-- Tombol Aksi Utama -->
                    <div class="space-y-2 pt-1">
                        <button type="button" onclick="submitPosPayment('pay', event)" class="w-full inline-flex items-center justify-center gap-2 py-3.5 sm:py-4 px-4 rounded-full bg-[#290024] hover:bg-[#775533] text-[#F2F5E2] font-bold text-xs sm:text-sm shadow-md hover:shadow-lg transition-all active:scale-[0.98]">
                            <i data-lucide="check-circle" class="w-4 h-4 text-[#D4954D]"></i>
                            <span>Bayar Sekarang &amp; Cetak Struk</span>
                        </button>
                        <button type="button" onclick="submitPosPayment('save', event)" class="w-full inline-flex items-center justify-center gap-1.5 py-2.5 px-4 rounded-full bg-[#F2F5E2] border border-[#775533]/40 text-[#775533] hover:bg-[#E3DEA4]/40 text-xs font-bold transition-all active:scale-[0.98]">
                            <i data-lucide="bookmark" class="w-3.5 h-3.5"></i>
                            <span>Simpan Pesanan (Bayar Nanti)</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- FLOATING CART BAR (Melayang di atas Bottom Nav pada Mobile Portrait Katalog) -->
<div id="mobile-floating-cart" class="md:landscape:hidden lg:hidden fixed bottom-[calc(4.5rem+env(safe-area-inset-bottom))] landscape:bottom-3 inset-x-3 z-30 hidden transition-all duration-300">
    <div class="bg-[#290024] text-[#F2F5E2] p-3 rounded-2xl shadow-xl border border-[#D4954D]/40 flex items-center justify-between gap-3 backdrop-blur-md">
        <div class="flex items-center gap-2.5 min-w-0">
            <div class="relative w-9 h-9 rounded-xl bg-[#D4954D] text-[#290024] flex items-center justify-center shrink-0 font-bold">
                <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                <span id="float-cart-count" class="absolute -top-1.5 -right-1.5 w-4 h-4 rounded-full bg-[#775533] text-beige text-[9px] flex items-center justify-center font-bold">0</span>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] text-[#E3DEA4] uppercase tracking-wider font-semibold">Total Pesanan</p>
                <p id="float-cart-total" class="font-serif font-bold text-sm sm:text-base text-[#F2F5E2] leading-none">Rp 0</p>
            </div>
        </div>

        <button type="button" onclick="goToCheckout()" class="shrink-0 inline-flex items-center gap-1.5 px-4 py-2.5 rounded-full bg-[#D4954D] text-[#290024] font-bold text-xs shadow-sm hover:brightness-105 active:scale-95 transition-all">
            <span>Bayar Sekarang</span>
            <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
        </button>
    </div>
</div>

<!-- BOTTOM SHEET: DETAIL MENU (Pilih Varian, Ukuran, Stepper, Catatan) -->
<div id="menu-detail-sheet" class="fixed inset-0 z-[70] hidden transition-all duration-300">
    <div class="fixed inset-0 bg-[#290024]/60 backdrop-blur-sm transition-opacity" onclick="closeMenuDetailSheet()"></div>
    <div class="fixed inset-x-0 bottom-0 sm:inset-x-auto sm:left-1/2 sm:-translate-x-1/2 sm:w-full sm:max-w-lg landscape:max-w-2xl max-h-[92vh] landscape:max-h-[96vh] bg-[#F2F5E2] rounded-t-[2rem] sm:rounded-3xl border-t sm:border border-[#E3DEA4] shadow-2xl flex flex-col z-10 transform translate-y-full transition-transform duration-300 ease-out mx-auto">
        <!-- Handle -->
        <div class="w-full pt-3 pb-1 flex justify-center cursor-pointer" onclick="closeMenuDetailSheet()">
            <div class="w-12 h-1 bg-[#775533]/30 rounded-full"></div>
        </div>

        <div class="overflow-y-auto p-4 sm:p-5 landscape:p-4 space-y-3 sm:space-y-4 flex-1">
            <!-- Grid 2-kolom di landscape -->
            <div class="grid grid-cols-1 landscape:grid-cols-2 gap-3 sm:gap-4">
                <!-- Kiri: Foto, Nama, Deskripsi, Harga, Stepper -->
                <div class="space-y-3">
                    <div class="flex gap-3 sm:gap-4 items-start">
                        <div id="sheet-menu-img-wrap" class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-[#F2F5E2] overflow-hidden shrink-0 border border-[#E3DEA4] flex items-center justify-center">
                            <img id="sheet-menu-img" src="" alt="" class="w-full h-full object-cover">
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 id="sheet-menu-name" class="font-serif font-bold text-base sm:text-lg text-[#290024] leading-tight"></h3>
                            <p id="sheet-menu-desc" class="text-xs text-[#775533] mt-1 leading-snug line-clamp-2"></p>
                            <p id="sheet-menu-price" class="font-serif font-bold text-sm sm:text-base text-[#290024] mt-1.5"></p>
                        </div>
                    </div>

                    <!-- Stepper Jumlah (- 1 +) -->
                    <div class="flex items-center justify-between bg-[#F2F5E2]/80 p-2.5 sm:p-3 rounded-2xl border border-[#E3DEA4]/60">
                        <span class="text-xs font-bold text-[#290024]">Jumlah Pesanan</span>
                        <div class="inline-flex items-center gap-2 bg-[#F2F5E2] p-1 rounded-full border border-[#E3DEA4]">
                            <button type="button" onclick="adjustSheetQty(-1)" class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-[#E3DEA4]/50 hover:bg-[#E3DEA4] text-[#290024] flex items-center justify-center font-bold text-sm">
                                <i data-lucide="minus" class="w-3.5 h-3.5"></i>
                            </button>
                            <span id="sheet-qty" class="w-7 sm:w-8 text-center text-xs font-bold text-[#290024]">1</span>
                            <button type="button" onclick="adjustSheetQty(1)" class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-[#290024] text-[#F2F5E2] flex items-center justify-center font-bold text-sm">
                                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Kanan: Varian, Ukuran, Catatan -->
                <div class="space-y-3">
                    <!-- Varian -->
                    <div id="sheet-variants-container" class="space-y-1.5 hidden">
                        <label class="block text-[10px] sm:text-[11px] font-bold text-[#775533] uppercase tracking-wider">Pilih Varian</label>
                        <div id="sheet-variants-list" class="grid grid-cols-2 gap-2"></div>
                    </div>

                    <!-- Ukuran -->
                    <div id="sheet-sizes-container" class="space-y-1.5 hidden">
                        <label class="block text-[10px] sm:text-[11px] font-bold text-[#775533] uppercase tracking-wider">Pilih Ukuran</label>
                        <div id="sheet-sizes-list" class="grid grid-cols-2 gap-2"></div>
                    </div>

                    <!-- Catatan Tambahan -->
                    <div>
                        <label class="block text-[10px] sm:text-[11px] font-bold text-[#775533] uppercase tracking-wider mb-1">Catatan Khusus (opsional)</label>
                        <input type="text" id="sheet-notes" placeholder="Misal: Kurang manis, tanpa sedotan..." class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs text-[#290024] placeholder:text-[#775533]/50 px-3 py-2 sm:px-3.5 sm:py-2.5 rounded-2xl border border-[#E3DEA4] focus:border-[#D4954D] focus:outline-none">
                    </div>
                </div>
            </div>
        </div>

        <!-- Sticky Submit Button di Bawah -->
        <div class="shrink-0 p-3 sm:p-4 bg-[#F2F5E2] border-t border-[#E3DEA4]/60 pb-[max(0.85rem,env(safe-area-inset-bottom))]">
            <button type="button" id="sheet-add-btn" class="w-full py-3.5 px-4 rounded-full bg-[#290024] hover:bg-[#775533] text-[#F2F5E2] font-bold text-xs sm:text-sm flex items-center justify-center gap-2 shadow-md transition-all active:scale-[0.98]">
                <i data-lucide="plus" class="w-4 h-4 text-[#D4954D]"></i>
                <span id="sheet-add-btn-text">Tambah ke Keranjang &amp; Bayar</span>
            </button>
        </div>
    </div>
</div>

<script>
// ---------- State ----------
const posMenus = @json($posMenuData);
let cart = [];
@if($resume)
    cart = {!! json_encode($resume->items->map(fn ($it) => [
        'menu_id' => $it->menu_id,
        'variant_id' => $it->variant_id,
        'name' => $it->name,
        'price' => (float) $it->price,
        'qty' => $it->quantity,
        'notes' => $it->notes,
    ])->values()) !!};
@endif
let selectedMethod = 'tunai';
let currentTotals = { subtotal: 0, discount: 0, tax: 0, service: 0, total: 0 };

// State detail sheet
let currentMenuSheet = null;
let currentSelectedVariant = null;
let currentSelectedVariantName = null;
let currentSheetQty = 1;

function formatRupiah(n) {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(n || 0));
}

function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, (char) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;',
    }[char]));
}

// ---------- View Switcher (Katalog <-> Menu Bayar Full) ----------
function goToCheckout() {
    if (cart.length === 0) {
        showToast('Keranjang Kosong', 'Pilih minimal 1 menu untuk dipesan.', 'error');
        return;
    }
    document.getElementById('pos-view-catalog').classList.add('hidden');
    document.getElementById('pos-view-checkout').classList.remove('hidden');
    window.scrollTo({ top: 0, behavior: 'smooth' });
    renderCart();
    refreshTotals();
    lucide.createIcons();
}

function goToCatalog() {
    document.getElementById('pos-view-checkout').classList.add('hidden');
    document.getElementById('pos-view-catalog').classList.remove('hidden');
    window.scrollTo({ top: 0, behavior: 'smooth' });
    renderCart();
    lucide.createIcons();
}

let totalsTimer = null;

function refreshTotals() {
    clearTimeout(totalsTimer);
    totalsTimer = setTimeout(() => {
        const voucherVal = document.getElementById('voucher-code')?.value.trim() || '';
        const body = new URLSearchParams({
            cart: JSON.stringify(cart),
            voucher: voucherVal,
        });

        fetch('{{ route('kasir.pos.totals') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: body.toString(),
        })
            .then(r => r.json().then(d => ({ ok: r.ok, d })))
            .then(({ ok, d }) => {
                if (! ok || ! d.ok) {
                    currentTotals = { subtotal: 0, discount: 0, tax: 0, service: 0, total: 0 };
                    updateTotalElements(currentTotals);
                    return;
                }
                currentTotals = {
                    subtotal: d.subtotal,
                    discount: d.discount,
                    tax: d.tax,
                    service: d.service,
                    total: d.total,
                };
                updateTotalElements(currentTotals);
                calcChange();
            })
            .catch(() => {});
    }, 250);
}

function updateTotalElements(t) {
    const subtotalStr = formatRupiah(t.subtotal);
    const discountStr = '-Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(t.discount));
    const taxStr = formatRupiah(t.tax);
    const serviceStr = formatRupiah(t.service);
    const totalStr = formatRupiah(t.total);

    document.querySelectorAll('#sum-subtotal').forEach(el => el.textContent = subtotalStr);
    document.querySelectorAll('#sum-discount').forEach(el => el.textContent = discountStr);
    document.querySelectorAll('#sum-tax').forEach(el => el.textContent = taxStr);
    document.querySelectorAll('#sum-service').forEach(el => el.textContent = serviceStr);
    document.querySelectorAll('#sum-total').forEach(el => el.textContent = totalStr);
    
    const catTotal = document.getElementById('catalog-cart-total');
    if (catTotal) catTotal.textContent = totalStr;

    const floatTotal = document.getElementById('float-cart-total');
    if (floatTotal) floatTotal.textContent = totalStr;
}

function renderCart() {
    const totalItems = cart.reduce((s, i) => s + i.qty, 0);

    // 1. Render Catalog Sidebar Cart
    const catWrap = document.getElementById('catalog-cart-items');
    const catEmpty = document.getElementById('catalog-cart-empty');
    const catCount = document.getElementById('catalog-cart-count');
    if (catCount) catCount.textContent = totalItems + ' item';

    if (catWrap) {
        if (cart.length === 0) {
            catWrap.innerHTML = '';
            if (catEmpty) {
                catWrap.appendChild(catEmpty);
                catEmpty.classList.remove('hidden');
            }
        } else {
            catWrap.innerHTML = cart.map((item, idx) => `
                <div class="py-2 flex items-start justify-between gap-2">
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-bold text-[#290024] truncate">${escapeHtml(item.name)}</p>
                        <p class="text-[10px] text-[#775533]">${formatRupiah(item.price)} &times; ${item.qty}</p>
                    </div>
                    <div class="flex items-center gap-1 shrink-0">
                        <button type="button" onclick="changeQty(${idx}, -1)" class="qty-btn w-5 h-5 rounded-full bg-[#E3DEA4]/60 text-[#775533] flex items-center justify-center text-xs font-bold">-</button>
                        <span class="w-5 text-center text-xs font-bold text-[#290024]">${item.qty}</span>
                        <button type="button" onclick="changeQty(${idx}, 1)" class="qty-btn w-5 h-5 rounded-full bg-[#290024] text-beige flex items-center justify-center text-xs font-bold">+</button>
                    </div>
                </div>
            `).join('');
        }
    }

    // 2. Render Checkout Full View Cart
    const chkWrap = document.getElementById('checkout-cart-items');
    const chkCount = document.getElementById('checkout-cart-count');
    if (chkCount) chkCount.textContent = totalItems + ' item';

    if (chkWrap) {
        if (cart.length === 0) {
            chkWrap.innerHTML = `
                <div class="py-8 text-center text-[#775533]">
                    <i data-lucide="shopping-bag" class="w-8 h-8 mx-auto text-[#775533]/40"></i>
                    <p class="text-xs mt-2 font-medium">Keranjang masih kosong.</p>
                    <button type="button" onclick="goToCatalog()" class="mt-3 px-4 py-2 rounded-full bg-[#290024] text-beige text-xs font-bold">
                        Pilih Menu Sekarang
                    </button>
                </div>
            `;
        } else {
            chkWrap.innerHTML = cart.map((item, idx) => `
                <div class="py-2.5 flex items-start gap-3">
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-[#290024] leading-snug">${escapeHtml(item.name)}</p>
                        <p class="text-[10px] text-[#775533] mt-0.5">${formatRupiah(item.price)} &times; ${item.qty} = <span class="font-bold text-[#290024]">${formatRupiah(item.price * item.qty)}</span></p>
                        <button type="button" onclick="setNotes(${idx})" class="text-[10px] text-[#D4954D] hover:underline font-semibold block mt-1">
                            ${item.notes ? 'Catatan: ' + escapeHtml(item.notes) : '+ tambah catatan'}
                        </button>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0 pt-0.5">
                        <button type="button" onclick="changeQty(${idx}, -1)" class="qty-btn w-6 h-6 rounded-full bg-[#E3DEA4]/60 text-[#775533] flex items-center justify-center"><i data-lucide="minus" class="w-3 h-3"></i></button>
                        <span class="w-6 text-center text-xs font-bold text-[#290024]">${item.qty}</span>
                        <button type="button" onclick="changeQty(${idx}, 1)" class="qty-btn w-6 h-6 rounded-full bg-[#290024] text-beige flex items-center justify-center"><i data-lucide="plus" class="w-3 h-3"></i></button>
                        <button type="button" onclick="removeItem(${idx})" class="w-6 h-6 rounded-full text-[#775533] hover:text-[#775533] flex items-center justify-center transition-colors"><i data-lucide="trash-2" class="w-3.5 h-3.5"></i></button>
                    </div>
                </div>
            `).join('');
        }
    }

    // 3. Floating Mobile Cart Bar
    const floatBar = document.getElementById('mobile-floating-cart');
    const floatCount = document.getElementById('float-cart-count');
    if (floatBar) {
        if (cart.length > 0 && document.getElementById('pos-view-catalog').offsetParent !== null) {
            floatBar.classList.remove('hidden');
            if (floatCount) floatCount.textContent = totalItems;
        } else {
            floatBar.classList.add('hidden');
        }
    }

    refreshTotals();
    lucide.createIcons();
}

// ---------- Detail Menu Bottom Sheet Handler ----------
function optionCard(label, sublabel, selected, disabled, onClick) {
    const card = document.createElement('button');
    card.type = 'button';
    card.disabled = disabled;
    card.className = `p-2.5 rounded-2xl border transition-all text-left flex items-center justify-between gap-2 ${
        selected
            ? 'border-[#290024] bg-[#290024] text-[#F2F5E2]'
            : 'border-[#E3DEA4] bg-[#F2F5E2] text-[#290024]'
    } ${disabled ? 'opacity-40 cursor-not-allowed' : 'cursor-pointer hover:border-[#D4954D]'}`;
    card.innerHTML = `
        <div class="min-w-0">
            <p class="text-xs font-bold leading-tight truncate">${escapeHtml(label)}</p>
            <p class="text-[10px] ${selected ? 'text-[#D4954D]' : 'text-[#775533]'}">${escapeHtml(sublabel)}</p>
        </div>
        <div class="w-4 h-4 rounded-full border ${selected ? 'border-[#D4954D] bg-[#D4954D]' : 'border-[#775533]/40'} flex items-center justify-center shrink-0">
            <span class="w-1.5 h-1.5 rounded-full ${selected ? 'bg-[#290024]' : 'opacity-0'}"></span>
        </div>
    `;
    if (! disabled && onClick) {
        card.onclick = onClick;
    }

    return card;
}

function variantGroups(menu) {
    const groups = new Map();

    (menu.variants || []).forEach((variant) => {
        if (! groups.has(variant.name)) {
            groups.set(variant.name, []);
        }
        groups.get(variant.name).push(variant);
    });

    return Array.from(groups, ([name, rows]) => ({
        name,
        rows,
        available: rows.some((row) => row.is_available),
        minPrice: Math.min(...rows.map((row) => Number(row.price))),
        maxPrice: Math.max(...rows.map((row) => Number(row.price))),
    }));
}

function selectVariantGroup(group) {
    if (! currentMenuSheet) return;

    currentSelectedVariantName = group.name;
    const sizeContainer = document.getElementById('sheet-sizes-container');
    const sizeList = document.getElementById('sheet-sizes-list');
    sizeList.innerHTML = '';

    const renderGroupSelection = () => {
        const list = document.getElementById('sheet-variants-list');
        Array.from(list.children).forEach((card, index) => {
            const item = currentMenuSheet.groups[index];
            if (! item) return;
            card.className = `p-2.5 rounded-2xl border transition-all text-left flex items-center justify-between gap-2 ${
                item.name === currentSelectedVariantName
                    ? 'border-[#290024] bg-[#290024] text-[#F2F5E2]'
                    : 'border-[#E3DEA4] bg-[#F2F5E2] text-[#290024]'
            } ${item.available ? 'cursor-pointer hover:border-[#D4954D]' : 'opacity-40 cursor-not-allowed'}`;
        });
    };

    const rows = group.rows;
    const availableRows = rows.filter((row) => row.is_available);
    const hasSizes = rows.some((row) => row.size) || rows.length > 1;
    const retainedSelection = currentSelectedVariant
        ? availableRows.find((row) => row.id === currentSelectedVariant.id)
        : null;

    if (! hasSizes) {
        currentSelectedVariant = retainedSelection || availableRows[0] || null;
        currentSelectedVariantName = group.name;
        sizeContainer.classList.add('hidden');
        document.getElementById('sheet-menu-price').textContent = formatRupiah(
            currentSelectedVariant ? currentSelectedVariant.price : group.minPrice
        );
        renderGroupSelection();
        return;
    }

    sizeContainer.classList.remove('hidden');
    currentSelectedVariant = retainedSelection || availableRows[0] || null;

    rows.forEach((row) => {
        const label = row.size || 'Reguler';
        const selected = currentSelectedVariant?.id === row.id;
        sizeList.appendChild(optionCard(
            label,
            formatRupiah(row.price),
            selected,
            ! row.is_available,
            () => {
                currentSelectedVariant = row;
                document.getElementById('sheet-menu-price').textContent = formatRupiah(row.price);
                selectVariantGroup(group);
            }
        ));
    });

    document.getElementById('sheet-menu-price').textContent = formatRupiah(
        currentSelectedVariant ? currentSelectedVariant.price : group.minPrice
    );
    renderGroupSelection();
}

function handleMenuClick(id) {
    const menu = posMenus[id];
    if (! menu) return;

    currentMenuSheet = { ...menu, groups: variantGroups(menu) };
    currentSheetQty = 1;
    currentSelectedVariant = null;
    currentSelectedVariantName = null;
    document.getElementById('sheet-qty').textContent = '1';
    document.getElementById('sheet-notes').value = '';

    document.getElementById('sheet-menu-name').textContent = menu.name;
    document.getElementById('sheet-menu-desc').textContent = menu.description || 'Menu spesial pilihan dari ABS Cafe.';

    const imgEl = document.getElementById('sheet-menu-img');
    if (menu.image) {
        imgEl.src = menu.image;
        imgEl.classList.remove('hidden');
    } else {
        imgEl.classList.add('hidden');
    }

    const varContainer = document.getElementById('sheet-variants-container');
    const varList = document.getElementById('sheet-variants-list');
    const sizeContainer = document.getElementById('sheet-sizes-container');
    varList.innerHTML = '';
    sizeContainer.classList.add('hidden');
    document.getElementById('sheet-sizes-list').innerHTML = '';

    const groups = currentMenuSheet.groups;

    if (groups.length > 1) {
        varContainer.classList.remove('hidden');
        groups.forEach((group) => {
            const price = group.minPrice === group.maxPrice
                ? formatRupiah(group.minPrice)
                : `${formatRupiah(group.minPrice)} - ${formatRupiah(group.maxPrice)}`;
            varList.appendChild(optionCard(
                group.name,
                price,
                false,
                ! group.available,
                () => selectVariantGroup(group)
            ));
        });

        const firstAvailable = groups.find((group) => group.available) || groups[0];
        selectVariantGroup(firstAvailable);
    } else if (groups.length === 1) {
        varContainer.classList.add('hidden');
        selectVariantGroup(groups[0]);
    } else {
        varContainer.classList.add('hidden');
        sizeContainer.classList.add('hidden');
        currentSelectedVariant = null;
        document.getElementById('sheet-menu-price').textContent = formatRupiah(menu.price);
    }

    // Set submit button action: setelah masuk keranjang -> langsung masuk menu bayar (full)!
    document.getElementById('sheet-add-btn').onclick = function() {
        if (currentMenuSheet.groups.length > 0 && ! currentSelectedVariant) {
            showToast('Varian Habis', 'Varian untuk menu ini sedang tidak tersedia.', 'error');
            return;
        }

        const variant = currentSelectedVariant;
        const isMergedName = /\s*\([^)]*\/[^)]*\)\s*$/.test(menu.name);
        let finalName = menu.name;

        if (variant && isMergedName) {
            finalName = `${menu.base_name} - ${variant.name}${variant.size ? ` - ${variant.size}` : ''}`;
        } else if (variant && variant.name !== 'Porsi' && menu.name.includes(variant.name)) {
            finalName = variant.size ? `${menu.name} - ${variant.size}` : menu.name;
        } else if (variant && variant.name !== 'Porsi') {
            finalName = `${menu.name} (${variant.display_name || variant.name})`;
        }

        const finalPrice = variant ? Number(variant.price) : Number(menu.price);
        const notes = document.getElementById('sheet-notes').value.trim();

        addToCartWithOptions(menu.id, finalName, finalPrice, menu.stock, currentSheetQty, notes, variant?.id ?? null);
        closeMenuDetailSheet();
        showToast('Ditambahkan', `${currentSheetQty}x ${finalName} masuk ke keranjang`);

        // Langsung masuk menu bayar secara full (bukan popup)!
        goToCheckout();
    };

    openMenuDetailSheet();
}

function adjustSheetQty(delta) {
    currentSheetQty = Math.max(1, currentSheetQty + delta);
    document.getElementById('sheet-qty').textContent = currentSheetQty;
}

function openMenuDetailSheet() {
    const sheet = document.getElementById('menu-detail-sheet');
    sheet.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
    const nav = document.getElementById('mobile-bottom-nav');
    if (nav) nav.classList.add('hidden');
    setTimeout(() => {
        sheet.querySelector('.transform').classList.remove('translate-y-full');
        sheet.querySelector('.transform').classList.add('translate-y-0');
    }, 10);
    lucide.createIcons();
}

function closeMenuDetailSheet() {
    const sheet = document.getElementById('menu-detail-sheet');
    const panel = sheet.querySelector('.transform');
    if (panel) {
        panel.classList.remove('translate-y-0');
        panel.classList.add('translate-y-full');
    }
    setTimeout(() => {
        sheet.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        const nav = document.getElementById('mobile-bottom-nav');
        if (nav) nav.classList.remove('hidden');
    }, 300);
}

// ---------- Cart Operations ----------
function addToCartWithOptions(menuId, name, price, stock, qty = 1, notes = '', variantId = null) {
    if (stock !== null && cart.filter(i => i.menu_id === menuId).reduce((s, i) => s + i.qty, 0) + qty > stock) {
        showToast('Stok Terbatas', `Stok "${name}" hanya tersisa ${stock}`, 'error');
        return;
    }
    const existing = cart.find(i => i.menu_id === menuId && i.variant_id === variantId);
    if (existing) {
        existing.qty += qty;
        if (notes) existing.notes = notes;
    } else {
        cart.push({ menu_id: menuId, variant_id: variantId, name, price, qty, notes });
    }
    renderCart();
}

function changeQty(idx, delta) {
    const item = cart[idx];
    if (! item) return;
    item.qty = Math.max(1, item.qty + delta);
    renderCart();
}

function removeItem(idx) {
    cart.splice(idx, 1);
    renderCart();
}

function setNotes(idx) {
    const current = cart[idx]?.notes;
    const note = prompt('Catatan untuk item ini:', current || '');
    if (note !== null && cart[idx]) {
        cart[idx].notes = note.trim();
        renderCart();
    }
}

function clearCart() {
    if (cart.length === 0) return;
    if (confirm('Kosongkan keranjang belanja?')) {
        cart = [];
        renderCart();
    }
}

// ---------- Tipe Pemesanan & Nomor Meja Handler ----------
function handleOrderTypeChange(type) {
    const tableSelect = document.getElementById('table-number');
    const tableWrap = document.getElementById('checkout-table-wrapper');
    const tableStar = document.getElementById('table-star');
    const tableReq = document.getElementById('table-badge-req');
    const tableHint = document.getElementById('table-hint');

    if (type === 'take_away') {
        tableSelect.disabled = true;
        tableSelect.value = '';
        tableWrap.classList.add('opacity-40');
        if (tableStar) tableStar.classList.add('hidden');
        if (tableReq) tableReq.textContent = 'Tidak Perlu Meja';
        if (tableHint) tableHint.textContent = 'Pesanan Take Away (bawa pulang) tidak memerlukan nomor meja.';
    } else {
        tableSelect.disabled = false;
        tableWrap.classList.remove('opacity-40');
        if (tableStar) tableStar.classList.remove('hidden');
        if (tableReq) tableReq.textContent = 'Wajib untuk Dine In';
        if (tableHint) tableHint.textContent = '* Wajib dipilih untuk pesanan Dine In agar meja tercatat terpakai.';
    }
}

// ---------- Metode Pembayaran & Uang Tunai ----------
function selectMethod(code, btn) {
    selectedMethod = code;
    document.getElementById('payment-method-input').value = code;

    document.querySelectorAll('.method-btn').forEach(b => {
        b.className = 'method-btn p-3 rounded-2xl border cursor-pointer transition-all flex items-center justify-between gap-2.5 bg-[#F2F5E2] border-[#E3DEA4] hover:border-[#D4954D]';
        const rad = b.querySelector('.method-radio');
        if (rad) {
            rad.className = 'method-radio w-4 h-4 rounded-full border-2 border-[#775533]/40 flex items-center justify-center';
            rad.querySelector('span')?.classList.add('hidden');
        }
    });

    btn.className = 'method-btn p-3 rounded-2xl border cursor-pointer transition-all flex items-center justify-between gap-2.5 bg-[#F2F5E2] border-[#290024] ring-2 ring-[#290024]/10 shadow-sm';
    const activeRad = btn.querySelector('.method-radio');
    if (activeRad) {
        activeRad.className = 'method-radio w-4 h-4 rounded-full border-2 border-[#290024] bg-[#290024] flex items-center justify-center';
        activeRad.querySelector('span')?.classList.remove('hidden');
    }

    const cashField = document.getElementById('checkout-cash-field');
    const cashInput = document.getElementById('checkout-cash');
    if (code !== 'tunai') {
        cashField.classList.add('opacity-40', 'pointer-events-none');
        cashInput.value = '';
        document.getElementById('cash-received-input').value = '';
        document.getElementById('checkout-change').textContent = 'Rp 0';
    } else {
        cashField.classList.remove('opacity-40', 'pointer-events-none');
        const total = currentTotals.total;
        cashInput.value = Math.ceil(total / 1000) * 1000;
        calcChange();
    }
    lucide.createIcons();
}

function setCash(val) {
    const total = currentTotals.total;
    const cashInput = document.getElementById('checkout-cash');
    cashInput.value = val === 0 ? total : val;
    calcChange();
}

function calcChange() {
    const cashInput = document.getElementById('checkout-cash');
    const cash = parseFloat(cashInput?.value) || 0;
    const total = currentTotals.total;
    const chg = Math.max(0, cash - total);
    
    const changeEl = document.getElementById('checkout-change');
    if (changeEl) changeEl.textContent = formatRupiah(chg);

    document.getElementById('cash-received-input').value = cash;
}

document.getElementById('checkout-cash')?.addEventListener('input', calcChange);
document.getElementById('voucher-code')?.addEventListener('input', refreshTotals);

// ---------- Submit Pembayaran / Simpan Pesanan ----------
function submitPosPayment(action, event) {
    if (event) event.preventDefault();

    if (cart.length === 0) {
        showToast('Keranjang Kosong', 'Tambahkan minimal 1 item pesanan terlebih dahulu.', 'error');
        goToCatalog();
        return;
    }

    // Validasi Nama Pelanggan (Wajib)
    const custName = document.getElementById('customer-name')?.value.trim();
    if (! custName) {
        showToast('Nama Wajib Diisi', 'Silakan masukkan nama pelanggan.', 'error');
        document.getElementById('customer-name')?.focus();
        return;
    }

    // Validasi Nomor Meja jika Dine In (Wajib)
    const orderType = document.querySelector('input[name="order_type"]:checked')?.value || 'dine_in';
    const tableNum = document.getElementById('table-number')?.value;
    if (orderType === 'dine_in' && ! tableNum) {
        showToast('Meja Wajib Dipilih', 'Pilih nomor meja untuk pesanan Dine In.', 'error');
        document.getElementById('table-number')?.focus();
        return;
    }

    // Validasi Uang Tunai jika Bayar Tunai
    if (action === 'pay' && selectedMethod === 'tunai') {
        const total = currentTotals.total;
        const cashVal = parseFloat(document.getElementById('checkout-cash')?.value) || 0;
        if (cashVal < total) {
            showToast('Uang Tunai Kurang', `Uang diterima (${formatRupiah(cashVal)}) kurang dari total tagihan (${formatRupiah(total)})`, 'error');
            document.getElementById('checkout-cash')?.focus();
            return;
        }
        document.getElementById('cash-received-input').value = cashVal;
    }

    document.getElementById('action-input').value = action;
    document.getElementById('cart-input').value = JSON.stringify(cart);
    document.getElementById('payment-method-input').value = selectedMethod;
    document.getElementById('voucher-input').value = document.getElementById('voucher-code')?.value.trim() || '';
    
    const ref = document.getElementById('checkout-reference')?.value.trim() || '';
    document.getElementById('reference-input').value = ref;

    document.getElementById('pos-form').submit();
}

// ---------- Kategori & Search Filter ----------
let currentActiveCat = 'all';

function filterMenus() {
    const q = (document.getElementById('pos-menu-search')?.value || '').trim().toLowerCase();
    const clearBtn = document.getElementById('clear-search-btn');
    if (clearBtn) clearBtn.classList.toggle('hidden', q.length === 0);

    let visibleTotal = 0;
    document.querySelectorAll('.menu-cat').forEach(sec => {
        const catId = sec.dataset.catSection;
        const catMatches = currentActiveCat === 'all' || currentActiveCat === catId;
        let visibleInCategory = 0;

        sec.querySelectorAll('.menu-item-wrapper').forEach(card => {
            const name = (card.dataset.menuName || '').toLowerCase();
            const searchMatches = !q || name.includes(q);
            const shouldShow = catMatches && searchMatches;

            card.classList.toggle('hidden', !shouldShow);
            if (shouldShow) {
                visibleInCategory++;
                visibleTotal++;
            }
        });

        sec.classList.toggle('hidden', visibleInCategory === 0);
    });

    const counter = document.getElementById('pos-menu-counter');
    if (counter) counter.textContent = visibleTotal + ' menu';
}

function handleMenuSearch(val) {
    filterMenus();
}

function clearMenuSearch() {
    const input = document.getElementById('pos-menu-search');
    if (input) {
        input.value = '';
        filterMenus();
        input.focus();
    }
}

document.querySelectorAll('.cat-tab').forEach(btn => {
    btn.addEventListener('click', function () {
        currentActiveCat = this.dataset.cat;
        document.querySelectorAll('.cat-tab').forEach(b => {
            b.className = 'cat-tab shrink-0 px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-xs font-semibold bg-[#F2F5E2] text-[#775533] border border-[#E3DEA4] hover:bg-[#E3DEA4]/60 transition-all duration-200';
        });
        this.className = 'cat-tab shrink-0 px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-xs font-semibold bg-[#290024] text-[#F2F5E2] shadow-sm transition-all duration-200';
        filterMenus();
    });
});

// ---------- Inisialisasi ----------
@if($resume?->order_type === 'take_away')
    handleOrderTypeChange('take_away');
    document.querySelector('input[name="order_type"][value="take_away"]').checked = true;
@elseif($resume?->table_number)
    document.getElementById('table-number').value = '{{ $resume->table_number }}';
@endif

renderCart();

@if($resume && $resume->items->isNotEmpty())
    goToCheckout();
@endif
</script>
@endsection
