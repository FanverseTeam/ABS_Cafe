@extends('kasir.layouts.app')

@section('title', 'POS / Input Pesanan - Kasir')
@section('page_title', 'POS / Input Pesanan')

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

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 relative">
    <!-- Katalog Menu (Kiri di desktop, penuh di mobile) -->
    <div class="xl:col-span-2 space-y-4">
        <!-- Kategori Pill Tabs (Scroll Horizontal Khas Referensi) -->
        <div class="sticky top-16 sm:top-20 z-20 -mx-3.5 sm:mx-0 px-3.5 sm:px-0 py-2.5 bg-[#F2F5E2]/95 backdrop-blur-md border-b border-[#E3DEA4]/60 sm:border-0 flex items-center justify-between gap-3">
            <div class="flex gap-2 overflow-x-auto no-scrollbar py-0.5 scroll-smooth">
                <button type="button" data-cat="all" class="cat-tab shrink-0 px-4 py-2 rounded-full text-xs font-semibold bg-[#290024] text-[#F2F5E2] shadow-sm transition-all duration-200">
                    Semua
                </button>
                @foreach($categories as $category)
                    <button type="button" data-cat="{{ $category->id }}" class="cat-tab shrink-0 px-4 py-2 rounded-full text-xs font-semibold bg-[#FAFCF3] text-[#775533] border border-[#E3DEA4] hover:bg-[#E3DEA4]/60 transition-all duration-200">
                        {{ $category->name }}
                    </button>
                @endforeach
            </div>
            <span class="text-[11px] text-[#775533] shrink-0 hidden sm:inline">
                {{ $categories->sum(fn ($c) => $c->menus->count()) }} menu
            </span>
        </div>

        <!-- Daftar Menu per Kategori -->
        @foreach($categories as $category)
            <section data-cat-section="{{ $category->id }}" class="menu-cat space-y-3">
                <div class="flex items-center justify-between pt-1">
                    <h3 class="font-serif font-bold text-base text-[#290024] flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-[#D4954D]/20 text-[#D4954D] flex items-center justify-center">
                            <i data-lucide="{{ $category->icon ?: 'coffee' }}" class="w-4 h-4"></i>
                        </span>
                        {{ $category->name }}
                    </h3>
                    <span class="text-[11px] text-[#775533]">{{ $category->menus->count() }} item</span>
                </div>

                <!-- Grid Menu 2 Kolom Mobile (Sesuai Referensi Layar 2) -->
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                    @forelse($category->menus as $menu)
                        @php
                            $defaultPrice = $menu->variants->count() ? (float)$menu->variants->first()->price : (float)$menu->price;
                            $hasVariants = $menu->variants->count() > 1;
                        @endphp
                        <div class="pos-menu-card bg-[#FAFCF3] rounded-2xl border border-[#E3DEA4]/70 p-3 shadow-soft flex flex-col justify-between cursor-pointer group {{ ! $menu->is_available ? 'opacity-50 pointer-events-none' : '' }}"
                             onclick="handleMenuClick({{ $menu->id }}, '{{ addslashes($menu->name) }}', {{ $defaultPrice }}, {{ $menu->stock ?? 'null' }}, '{{ addslashes($menu->description ?? '') }}', '{{ $menu->image }}', {{ json_encode($menu->variants) }})">
                            <div>
                                <!-- Thumbnail Foto Membulat -->
                                <div class="relative w-full aspect-square rounded-xl bg-[#F2F5E2] overflow-hidden mb-2.5 flex items-center justify-center">
                                    @if($menu->image)
                                        <img src="{{ $menu->image }}" alt="{{ $menu->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-[#E3DEA4]/40 flex items-center justify-center text-[#D4954D]">
                                            <i data-lucide="coffee" class="w-5 h-5"></i>
                                        </div>
                                    @endif

                                    @if($menu->is_best_seller)
                                        <span class="absolute top-2 left-2 px-2 py-0.5 rounded-full bg-[#D4954D] text-[#290024] text-[9px] font-bold shadow-sm">
                                            Terlaris
                                        </span>
                                    @endif

                                    @if($menu->stock !== null && $menu->stock <= $menu->min_stock)
                                        <span class="absolute top-2 right-2 px-2 py-0.5 rounded-full bg-[#B03A2E] text-white text-[9px] font-bold shadow-sm">
                                            Sisa {{ $menu->stock }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Nama & Deskripsi -->
                                <h4 class="text-xs sm:text-sm font-bold text-[#290024] line-clamp-1 leading-snug group-hover:text-[#D4954D] transition-colors">
                                    {{ $menu->name }}
                                </h4>
                                <p class="text-[10px] text-[#775533] mt-0.5 line-clamp-1">
                                    {{ $menu->description ?: ($hasVariants ? $menu->variants->count() . ' varian pilihan' : 'Menu lezat ABS Cafe') }}
                                </p>
                            </div>

                            <!-- Harga & Tombol Tambah Bulat Gelap -->
                            <div class="mt-3 flex items-center justify-between gap-2 pt-2 border-t border-[#E3DEA4]/40">
                                <div>
                                    <span class="font-serif font-bold text-xs sm:text-sm text-[#290024] block">
                                        Rp {{ number_format($defaultPrice, 0, ',', '.') }}
                                    </span>
                                </div>

                                <button type="button" 
                                        onclick="event.stopPropagation(); handleMenuClick({{ $menu->id }}, '{{ addslashes($menu->name) }}', {{ $defaultPrice }}, {{ $menu->stock ?? 'null' }}, '{{ addslashes($menu->description ?? '') }}', '{{ $menu->image }}', {{ json_encode($menu->variants) }})"
                                        class="qty-btn w-8 h-8 rounded-full bg-[#290024] text-[#FAFCF3] hover:bg-[#D4954D] hover:text-[#290024] flex items-center justify-center transition-all shadow-sm"
                                        aria-label="Pilih {{ $menu->name }}">
                                    <i data-lucide="plus" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>
                    @empty
                        <p class="col-span-full text-xs text-[#775533] py-4 text-center bg-[#FAFCF3] rounded-2xl border border-[#E3DEA4]/50">
                            Tidak ada menu pada kategori ini.
                        </p>
                    @endforelse
                </div>
            </section>
        @endforeach
    </div>

    <!-- Panel Keranjang (Sidebar di Desktop, Bottom Sheet di Mobile) -->
    <div id="cart-panel-container" class="xl:sticky xl:top-24 h-fit hidden xl:block">
        <div class="bg-[#FAFCF3] rounded-3xl border border-[#E3DEA4]/80 shadow-soft-lg overflow-hidden">
            <!-- Header Keranjang -->
            <div class="px-5 py-4 border-b border-[#E3DEA4]/60 flex items-center justify-between bg-[#F2F5E2]/60">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-2xl bg-[#D4954D]/20 text-[#D4954D] flex items-center justify-center">
                        <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="font-serif font-bold text-sm text-[#290024]">Keranjang Pesanan</h3>
                        <p id="cart-count-label" class="text-[10px] text-[#775533]">0 item</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="clearCart()" class="text-[11px] text-[#775533] hover:text-[#B03A2E] font-medium flex items-center gap-1 transition-colors">
                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Kosongkan
                    </button>
                    <!-- Close button in mobile sheet only -->
                    <button type="button" onclick="closeMobileCart()" class="xl:hidden w-8 h-8 rounded-full bg-[#E3DEA4]/50 text-[#290024] flex items-center justify-center">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>

            <!-- Form POS -->
            <form id="pos-form" action="{{ route('kasir.pos.store') }}" method="POST" class="p-5 space-y-4">
                @csrf
                <input type="hidden" name="cart" id="cart-input" value="[]">
                <input type="hidden" name="voucher" id="voucher-input" value="">
                <input type="hidden" name="payment_method" id="payment-method-input" value="tunai">
                <input type="hidden" name="cash_received" id="cash-received-input" value="">
                <input type="hidden" name="reference" id="reference-input" value="">
                <input type="hidden" name="action" id="action-input" value="save">

                <!-- Daftar item keranjang (Layar 4 Referensi) -->
                <div id="cart-items" class="max-h-64 sm:max-h-72 overflow-y-auto pr-1 divide-y divide-[#E3DEA4]/40 space-y-1">
                    <div class="py-8 text-center" id="cart-empty">
                        <div class="w-12 h-12 mx-auto rounded-full bg-[#E3DEA4]/30 flex items-center justify-center text-[#775533]">
                            <i data-lucide="clipboard-list" class="w-5 h-5"></i>
                        </div>
                        <p class="text-xs text-[#775533] mt-2">Belum ada item di keranjang.</p>
                        <p class="text-[10px] text-[#775533]/70">Pilih menu dari katalog untuk menambahkan.</p>
                    </div>
                </div>

                <!-- Tipe Pesanan & Meja (Layar 5 & 6 Referensi: Dine In / Take Away) -->
                <div class="space-y-3 pt-2 border-t border-[#E3DEA4]/60">
                    <div>
                        <label class="block text-[10px] font-bold text-[#775533] uppercase tracking-wider mb-1.5">Tipe Pemesanan</label>
                        <div class="grid grid-cols-2 gap-2" id="order-type-selector">
                            <label class="flex items-center gap-2 p-2.5 rounded-2xl border border-[#E3DEA4] bg-[#FAFCF3] has-[:checked]:border-[#290024] has-[:checked]:bg-[#290024] has-[:checked]:text-[#FAFCF3] cursor-pointer transition-all select-none">
                                <input type="radio" name="order_type" value="dine_in" checked onchange="handleOrderTypeChange('dine_in')" class="sr-only">
                                <i data-lucide="utensils" class="w-4 h-4 text-[#D4954D]"></i>
                                <span class="text-xs font-semibold">Dine In</span>
                            </label>

                            <label class="flex items-center gap-2 p-2.5 rounded-2xl border border-[#E3DEA4] bg-[#FAFCF3] has-[:checked]:border-[#290024] has-[:checked]:bg-[#290024] has-[:checked]:text-[#FAFCF3] cursor-pointer transition-all select-none">
                                <input type="radio" name="order_type" value="take_away" onchange="handleOrderTypeChange('take_away')" class="sr-only">
                                <i data-lucide="shopping-bag" class="w-4 h-4 text-[#D4954D]"></i>
                                <span class="text-xs font-semibold">Take Away</span>
                            </label>
                        </div>
                        <!-- Hidden select to maintain existing scripts -->
                        <select id="order-type" class="hidden">
                            <option value="dine_in">Dine In</option>
                            <option value="take_away">Take Away</option>
                        </select>
                    </div>

                    <!-- Nomor Meja & Nama Pelanggan -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        <div id="table-number-wrapper">
                            <label class="block text-[10px] font-bold text-[#775533] uppercase tracking-wider mb-1">Nomor Meja</label>
                            <select id="table-number" name="table_number" class="w-full bg-[#E3DEA4]/40 text-xs text-[#290024] px-3 py-2.5 rounded-2xl border border-[#E3DEA4] focus:border-[#D4954D] focus:outline-none disabled:opacity-40 font-medium">
                                <option value="">— Pilih Meja —</option>
                                @foreach($tables as $table)
                                    <option value="{{ $table->number }}" {{ $table->status === 'nonaktif' ? 'disabled' : '' }}>
                                        Meja {{ $table->number }} ({{ $table->statusBadge()['label'] }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-[#775533] uppercase tracking-wider mb-1">Nama Pelanggan</label>
                            <input type="text" id="customer-name" name="customer_name" value="{{ $resume?->customer_name ?? '' }}" placeholder="Nama pelanggan (opsional)"
                                class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs text-[#290024] placeholder:text-[#775533]/50 px-3 py-2.5 rounded-2xl border border-[#E3DEA4] focus:border-[#D4954D] focus:outline-none">
                        </div>
                    </div>
                </div>

                <!-- Kode Voucher -->
                <div>
                    <label class="block text-[10px] font-bold text-[#775533] uppercase tracking-wider mb-1">Kode Voucher / Promo</label>
                    <div class="relative">
                        <input type="text" id="voucher-code" placeholder="Ketik kode (mis. RAMADHAN)" class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs text-[#290024] placeholder:text-[#775533]/50 px-3 py-2.5 rounded-2xl border border-[#E3DEA4] focus:border-[#D4954D] focus:outline-none">
                    </div>
                    @if($promos->isNotEmpty())
                        <div class="flex flex-wrap gap-1.5 mt-2">
                            @foreach($promos as $promo)
                                @if($promo->code)
                                    <button type="button" onclick="document.getElementById('voucher-code').value='{{ $promo->code }}'; refreshTotals();" class="px-2.5 py-1 rounded-full bg-[#D4954D]/15 text-[#290024] text-[10px] font-bold border border-[#D4954D]/30 hover:bg-[#D4954D]/30 transition-colors">
                                        {{ $promo->code }}
                                    </button>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Ringkasan Nota (Layar 4 Referensi) -->
                <div class="rounded-2xl bg-[#F2F5E2] border border-[#E3DEA4]/70 p-3.5 space-y-1.5">
                    <div class="flex justify-between text-xs text-[#775533]"><span>Subtotal</span><span id="sum-subtotal">Rp 0</span></div>
                    <div class="flex justify-between text-xs text-[#775533]"><span>Diskon</span><span id="sum-discount" class="text-[#B03A2E] font-medium">-Rp 0</span></div>
                    <div class="flex justify-between text-xs text-[#775533]"><span>Pajak (10%)</span><span id="sum-tax">Rp 0</span></div>
                    <div class="flex justify-between text-xs text-[#775533]"><span>Service Charge</span><span id="sum-service">Rp 0</span></div>
                    <div class="flex justify-between border-t border-[#E3DEA4] pt-2 mt-1">
                        <span class="text-xs font-bold text-[#290024]">Total Tagihan</span>
                        <span id="sum-total" class="font-serif font-bold text-[#290024] text-base sm:text-lg">Rp 0</span>
                    </div>
                </div>

                <!-- Tombol Aksi Keranjang -->
                <div class="grid grid-cols-2 gap-2.5 pt-1">
                    <button type="submit" onclick="setAction('save', event)" class="inline-flex items-center justify-center gap-1.5 px-4 py-3 rounded-full bg-[#FAFCF3] border border-[#775533]/40 text-[#775533] text-xs font-bold hover:bg-[#E3DEA4]/40 transition-colors active:scale-95">
                        <i data-lucide="bookmark" class="w-3.5 h-3.5"></i>
                        <span>Simpan</span>
                    </button>
                    <button type="submit" onclick="setAction('pay', event)" class="inline-flex items-center justify-center gap-1.5 px-4 py-3 rounded-full bg-[#290024] text-[#FAFCF3] text-xs font-bold shadow-md hover:bg-[#775533] transition-colors active:scale-95">
                        <i data-lucide="credit-card" class="w-3.5 h-3.5 text-[#D4954D]"></i>
                        <span>Bayar</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Pesanan Ditahan -->
        @if($heldOrders->isNotEmpty())
            <div class="mt-4 bg-[#FAFCF3] rounded-3xl border border-[#E3DEA4]/80 shadow-soft overflow-hidden">
                <div class="px-5 py-3 border-b border-[#E3DEA4]/50 flex items-center justify-between">
                    <h4 class="font-serif font-bold text-xs text-[#290024]">Pesanan Ditahan ({{ $heldOrders->count() }})</h4>
                </div>
                <div class="p-3 space-y-2">
                    @foreach($heldOrders as $held)
                        <div class="flex items-center justify-between gap-2 p-2.5 rounded-2xl bg-[#F2F5E2] border border-[#E3DEA4]/60">
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-[#290024]">{{ $held->order_code }}</p>
                                <p class="text-[10px] text-[#775533] truncate">{{ $held->customer_name ?: 'Pelanggan' }} &bull; {{ $held->table_number ? 'Meja '.$held->table_number : 'Take Away' }}</p>
                                <p class="text-[10px] text-[#290024] font-semibold mt-0.5">Rp {{ number_format($held->total, 0, ',', '.') }}</p>
                            </div>
                            <div class="flex gap-1.5 shrink-0">
                                <a href="{{ route('kasir.pos.index', ['resume' => $held->id]) }}" class="px-2.5 py-1 rounded-full bg-[#E3DEA4] text-[#290024] text-[10px] font-semibold hover:bg-[#D4954D] transition-colors">Buka</a>
                                <a href="{{ route('kasir.payments.checkout', $held) }}" class="px-2.5 py-1 rounded-full bg-[#290024] text-[#FAFCF3] text-[10px] font-semibold hover:bg-[#775533] transition-colors">Bayar</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

<!-- FLOATING CART BAR (Melayang di atas Bottom Nav pada Mobile) -->
<div id="mobile-floating-cart" class="xl:hidden fixed bottom-16 inset-x-3 z-30 hidden transition-all duration-300">
    <div class="bg-[#290024] text-[#FAFCF3] p-3 rounded-2xl shadow-xl border border-[#D4954D]/40 flex items-center justify-between gap-3 backdrop-blur-md">
        <div class="flex items-center gap-2.5 min-w-0">
            <div class="relative w-9 h-9 rounded-xl bg-[#D4954D] text-[#290024] flex items-center justify-center shrink-0 font-bold">
                <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                <span id="float-cart-count" class="absolute -top-1.5 -right-1.5 w-4 h-4 rounded-full bg-[#B03A2E] text-white text-[9px] flex items-center justify-center font-bold">0</span>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] text-[#E3DEA4] uppercase tracking-wider font-semibold">Total Pesanan</p>
                <p id="float-cart-total" class="font-serif font-bold text-sm sm:text-base text-[#FAFCF3] leading-none">Rp 0</p>
            </div>
        </div>

        <button type="button" onclick="openMobileCart()" class="shrink-0 inline-flex items-center gap-1.5 px-4 py-2.5 rounded-full bg-[#D4954D] text-[#290024] font-bold text-xs shadow-sm hover:brightness-105 active:scale-95 transition-all">
            <span>Lihat Keranjang</span>
            <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
        </button>
    </div>
</div>

<!-- BOTTOM SHEET: DETAIL MENU (Layar 3 Referensi: Foto besar, Varian Radio-Card, Stepper, Catatan) -->
<div id="menu-detail-sheet" class="fixed inset-0 z-50 hidden transition-all duration-300">
    <div class="fixed inset-0 bg-[#290024]/60 backdrop-blur-sm transition-opacity" onclick="closeMenuDetailSheet()"></div>
    <div class="fixed inset-x-0 bottom-0 max-h-[92vh] bg-[#FAFCF3] rounded-t-[2rem] border-t border-[#E3DEA4] shadow-2xl flex flex-col z-10 transform translate-y-full transition-transform duration-300 ease-out max-w-lg mx-auto">
        <!-- Handle -->
        <div class="w-full pt-3 pb-1 flex justify-center cursor-pointer" onclick="closeMenuDetailSheet()">
            <div class="w-12 h-1 bg-[#775533]/30 rounded-full"></div>
        </div>

        <div class="overflow-y-auto p-5 space-y-4 flex-1">
            <!-- Header Image & Info -->
            <div class="flex gap-4 items-start">
                <div id="sheet-menu-img-wrap" class="w-20 h-20 rounded-2xl bg-[#F2F5E2] overflow-hidden shrink-0 border border-[#E3DEA4] flex items-center justify-center">
                    <img id="sheet-menu-img" src="" alt="" class="w-full h-full object-cover">
                </div>
                <div class="min-w-0 flex-1">
                    <h3 id="sheet-menu-name" class="font-serif font-bold text-lg text-[#290024] leading-tight"></h3>
                    <p id="sheet-menu-desc" class="text-xs text-[#775533] mt-1 leading-snug"></p>
                    <p id="sheet-menu-price" class="font-serif font-bold text-base text-[#290024] mt-2"></p>
                </div>
            </div>

            <!-- Varian / Ukuran Pilihan (Radio-card bergaya referensi) -->
            <div id="sheet-variants-container" class="space-y-2 hidden">
                <label class="block text-[11px] font-bold text-[#775533] uppercase tracking-wider">Pilih Ukuran / Varian</label>
                <div id="sheet-variants-list" class="grid grid-cols-2 gap-2"></div>
            </div>

            <!-- Stepper Jumlah (- 1 +) -->
            <div class="pt-2 flex items-center justify-between bg-[#F2F5E2]/80 p-3.5 rounded-2xl border border-[#E3DEA4]/60">
                <span class="text-xs font-bold text-[#290024]">Jumlah Pesanan</span>
                <div class="inline-flex items-center gap-2 bg-[#FAFCF3] p-1 rounded-full border border-[#E3DEA4]">
                    <button type="button" onclick="adjustSheetQty(-1)" class="w-8 h-8 rounded-full bg-[#E3DEA4]/50 hover:bg-[#E3DEA4] text-[#290024] flex items-center justify-center font-bold text-sm">
                        <i data-lucide="minus" class="w-3.5 h-3.5"></i>
                    </button>
                    <span id="sheet-qty" class="w-8 text-center text-xs font-bold text-[#290024]">1</span>
                    <button type="button" onclick="adjustSheetQty(1)" class="w-8 h-8 rounded-full bg-[#290024] text-[#FAFCF3] flex items-center justify-center font-bold text-sm">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    </button>
                </div>
            </div>

            <!-- Catatan Tambahan -->
            <div>
                <label class="block text-[11px] font-bold text-[#775533] uppercase tracking-wider mb-1">Catatan Khusus (opsional)</label>
                <input type="text" id="sheet-notes" placeholder="Misal: Kurang manis, tanpa sedotan..." class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs text-[#290024] placeholder:text-[#775533]/50 px-3.5 py-2.5 rounded-2xl border border-[#E3DEA4] focus:border-[#D4954D] focus:outline-none">
            </div>
        </div>

        <!-- Sticky Submit Button di Bawah -->
        <div class="p-4 bg-[#F2F5E2] border-t border-[#E3DEA4]/60 pb-[max(1rem,env(safe-area-inset-bottom))]">
            <button type="button" id="sheet-add-btn" class="w-full py-3.5 px-4 rounded-full bg-[#290024] hover:bg-[#775533] text-[#FAFCF3] font-bold text-xs flex items-center justify-center gap-2 shadow-md transition-all active:scale-[0.98]">
                <i data-lucide="plus" class="w-4 h-4 text-[#D4954D]"></i>
                <span id="sheet-add-btn-text">Tambah ke Keranjang</span>
            </button>
        </div>
    </div>
</div>

<!-- BOTTOM SHEET: METODE PEMBAYARAN (Layar 7 Referensi) -->
<div id="payment-modal" class="fixed inset-0 z-50 hidden transition-all duration-300">
    <div class="fixed inset-0 bg-[#290024]/60 backdrop-blur-sm" onclick="closePaymentModal()"></div>
    <div class="fixed inset-x-0 bottom-0 max-h-[92vh] bg-[#FAFCF3] rounded-t-[2rem] border-t border-[#E3DEA4] shadow-2xl flex flex-col z-10 transform translate-y-full transition-transform duration-300 ease-out max-w-md mx-auto">
        <!-- Handle -->
        <div class="w-full pt-3 pb-1 flex justify-center cursor-pointer" onclick="closePaymentModal()">
            <div class="w-12 h-1 bg-[#775533]/30 rounded-full"></div>
        </div>

        <div class="px-5 py-3 border-b border-[#E3DEA4]/60 flex items-center justify-between">
            <div>
                <h3 class="font-serif font-bold text-base text-[#290024]">Pilih Metode Pembayaran</h3>
                <p class="text-[10px] text-[#775533]">Pilih salah satu metode pembayaran di bawah</p>
            </div>
            <button onclick="closePaymentModal()" class="w-8 h-8 rounded-full bg-[#E3DEA4]/50 text-[#290024] flex items-center justify-center"><i data-lucide="x" class="w-4 h-4"></i></button>
        </div>

        <div class="overflow-y-auto p-5 space-y-4 flex-1">
            <!-- Total Tagihan -->
            <div class="text-center p-3.5 rounded-2xl bg-[#F2F5E2] border border-[#E3DEA4]/60">
                <p class="text-[10px] font-semibold text-[#775533] uppercase tracking-wider">Total Tagihan</p>
                <p id="modal-total" class="font-serif text-2xl font-bold text-[#290024] mt-0.5">Rp 0</p>
            </div>

            <!-- Radio-Cards Metode Pembayaran (Sesuai Layar 7) -->
            <div class="space-y-2" id="method-buttons">
                @foreach($paymentMethods as $method)
                    <div data-method="{{ $method->code }}" onclick="selectMethod('{{ $method->code }}', this)"
                         class="method-btn p-3.5 rounded-2xl border cursor-pointer transition-all flex items-center justify-between gap-3 {{ $method->code === 'tunai' ? 'bg-[#FAFCF3] border-[#290024] shadow-sm' : 'bg-[#FAFCF3] border-[#E3DEA4] hover:border-[#D4954D]' }}">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-[#E3DEA4]/50 text-[#775533] flex items-center justify-center shrink-0">
                                <i data-lucide="{{ $method->icon ?: 'wallet' }}" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h5 class="text-xs font-bold text-[#290024]">{{ $method->name }}</h5>
                                <p class="text-[10px] text-[#775533]">{{ $method->code === 'tunai' ? 'Bayar langsung di kasir' : ($method->code === 'qris' ? 'Scan QRIS statis / dinamis' : 'Transfer akun resmi') }}</p>
                            </div>
                        </div>
                        <div class="method-radio w-5 h-5 rounded-full border-2 {{ $method->code === 'tunai' ? 'border-[#290024] bg-[#290024]' : 'border-[#775533]/40' }} flex items-center justify-center">
                            <span class="w-2 h-2 rounded-full bg-white {{ $method->code === 'tunai' ? '' : 'hidden' }}"></span>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Field Tunai & Kembalian -->
            <div id="cash-field" class="space-y-2 p-3.5 rounded-2xl bg-[#F2F5E2] border border-[#E3DEA4]/60">
                <label class="block text-[10px] font-bold text-[#775533] uppercase tracking-wider">Uang Diterima (Rp)</label>
                <input type="number" id="modal-cash" value="" step="1000" min="0" placeholder="0"
                    class="w-full bg-[#FAFCF3] focus:bg-white text-sm font-bold text-[#290024] placeholder:text-[#775533]/40 px-3.5 py-2.5 rounded-xl border border-[#E3DEA4] focus:border-[#D4954D] focus:outline-none">
                
                <div class="flex flex-wrap gap-1.5 pt-1">
                    <button type="button" data-cash="0" onclick="setCash(0)" class="px-3 py-1.5 rounded-full bg-[#FAFCF3] border border-[#E3DEA4] text-[#290024] text-[10px] font-bold hover:bg-[#E3DEA4]">Uang Pas</button>
                    <button type="button" data-cash="20000" onclick="setCash(20000)" class="px-3 py-1.5 rounded-full bg-[#FAFCF3] border border-[#E3DEA4] text-[#290024] text-[10px] font-bold hover:bg-[#E3DEA4]">20rb</button>
                    <button type="button" data-cash="50000" onclick="setCash(50000)" class="px-3 py-1.5 rounded-full bg-[#FAFCF3] border border-[#E3DEA4] text-[#290024] text-[10px] font-bold hover:bg-[#E3DEA4]">50rb</button>
                    <button type="button" data-cash="100000" onclick="setCash(100000)" class="px-3 py-1.5 rounded-full bg-[#FAFCF3] border border-[#E3DEA4] text-[#290024] text-[10px] font-bold hover:bg-[#E3DEA4]">100rb</button>
                    <button type="button" data-cash="150000" onclick="setCash(150000)" class="px-3 py-1.5 rounded-full bg-[#FAFCF3] border border-[#E3DEA4] text-[#290024] text-[10px] font-bold hover:bg-[#E3DEA4]">150rb</button>
                </div>

                <div class="flex justify-between items-center text-xs pt-2 border-t border-[#E3DEA4]">
                    <span class="font-medium text-[#775533]">Kembalian</span>
                    <span id="modal-change" class="font-serif font-bold text-sm text-[#4F7A4A]">Rp 0</span>
                </div>

                @if(in_array('transfer', $paymentMethods->pluck('code')->all()) || in_array('ewallet', $paymentMethods->pluck('code')->all()))
                    <div class="pt-2">
                        <label class="block text-[10px] font-semibold text-[#775533] uppercase tracking-wide mb-1">Referensi / No. Invoice (opsional)</label>
                        <input type="text" id="modal-reference" placeholder="Nomor referensi / bukti transfer"
                            class="w-full bg-[#FAFCF3] focus:bg-white text-xs text-[#290024] placeholder:text-[#775533]/50 px-3 py-2 rounded-xl border border-[#E3DEA4] focus:border-[#D4954D] focus:outline-none">
                    </div>
                @endif
            </div>
        </div>

        <div class="p-4 bg-[#F2F5E2] border-t border-[#E3DEA4]/60 pb-[max(1rem,env(safe-area-inset-bottom))]">
            <button type="button" onclick="confirmPayment()" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-full bg-[#290024] hover:bg-[#775533] text-[#FAFCF3] text-xs font-bold shadow-md transition-all active:scale-[0.98]">
                <i data-lucide="check-circle" class="w-4 h-4 text-[#D4954D]"></i>
                Konfirmasi & Bayar Sekarang
            </button>
        </div>
    </div>
</div>

<script>
// ---------- State ----------
let cart = [];
@if($resume)
    cart = {!! json_encode($resume->items->map(fn ($it) => [
        'menu_id' => $it->menu_id, 'name' => $it->name, 'price' => (float)$it->price, 'qty' => $it->quantity, 'notes' => $it->notes,
    ])->values()) !!};
@endif
let selectedMethod = 'tunai';

// State detail sheet
let currentMenuSheet = null;
let currentSelectedVariant = null;
let currentSheetQty = 1;

function formatRupiah(n) {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(n));
}

let totalsTimer = null;

function refreshTotals() {
    clearTimeout(totalsTimer);
    totalsTimer = setTimeout(() => {
        const body = new URLSearchParams({
            cart: JSON.stringify(cart),
            voucher: document.getElementById('voucher-code').value.trim(),
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
                    document.getElementById('sum-discount').textContent = '-Rp 0';
                    document.getElementById('sum-tax').textContent = 'Rp 0';
                    document.getElementById('sum-service').textContent = 'Rp 0';
                    document.getElementById('sum-total').textContent = 'Rp 0';
                    document.getElementById('float-cart-total').textContent = 'Rp 0';
                    return;
                }
                document.getElementById('sum-subtotal').textContent = formatRupiah(d.subtotal);
                document.getElementById('sum-discount').textContent = '-Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(d.discount));
                document.getElementById('sum-tax').textContent = formatRupiah(d.tax);
                document.getElementById('sum-service').textContent = formatRupiah(d.service);
                document.getElementById('sum-total').textContent = formatRupiah(d.total);
                document.getElementById('modal-total').textContent = formatRupiah(d.total);
                document.getElementById('float-cart-total').textContent = formatRupiah(d.total);
                document.getElementById('voucher-input').value = document.getElementById('voucher-code').value.trim();
                const total = d.total;
                const cash = parseFloat(document.getElementById('modal-cash').value) || 0;
                document.getElementById('modal-change').textContent = formatRupiah(Math.max(0, cash - total));
            })
            .catch(() => {});
    }, 300);
}

function renderCart() {
    const wrap = document.getElementById('cart-items');
    const empty = document.getElementById('cart-empty');
    const floatBar = document.getElementById('mobile-floating-cart');
    const floatCount = document.getElementById('float-cart-count');

    const totalItems = cart.reduce((s, i) => s + i.qty, 0);

    if (cart.length === 0) {
        wrap.innerHTML = '';
        wrap.appendChild(empty);
        empty.classList.remove('hidden');
        document.getElementById('cart-count-label').textContent = '0 item';
        floatBar.classList.add('hidden');
    } else {
        empty.classList.add('hidden');
        wrap.innerHTML = cart.map((item, idx) => `
            <div class="py-2.5 flex items-start gap-3 group">
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-[#290024] truncate">${item.name}</p>
                    <p class="text-[10px] text-[#775533]">Rp ${formatRupiah(item.price).replace('Rp ', '')} &times; ${item.qty}</p>
                    <button type="button" onclick="setNotes(${idx})" class="text-[9px] text-[#D4954D] hover:underline font-semibold block mt-0.5">${item.notes ? item.notes : '+ catatan'}</button>
                </div>
                <div class="flex items-center gap-1.5 shrink-0">
                    <button type="button" onclick="changeQty(${idx}, -1)" class="qty-btn w-6 h-6 rounded-full bg-[#E3DEA4]/60 text-[#775533] flex items-center justify-center"><i data-lucide="minus" class="w-3 h-3"></i></button>
                    <span class="w-6 text-center text-xs font-bold text-[#290024]">${item.qty}</span>
                    <button type="button" onclick="changeQty(${idx}, 1)" class="qty-btn w-6 h-6 rounded-full bg-[#290024] text-white flex items-center justify-center"><i data-lucide="plus" class="w-3 h-3"></i></button>
                    <button type="button" onclick="removeItem(${idx})" class="w-6 h-6 rounded-full text-[#775533] hover:text-[#B03A2E] flex items-center justify-center"><i data-lucide="trash-2" class="w-3 h-3"></i></button>
                </div>
            </div>
        `).join('');
        document.getElementById('cart-count-label').textContent = totalItems + ' item';
        floatCount.textContent = totalItems;
        floatBar.classList.remove('hidden');
        lucide.createIcons();
    }

    refreshTotals();
}

// ---------- Detail Menu Bottom Sheet Handler ----------
function handleMenuClick(id, name, defaultPrice, stock, desc, image, variants) {
    currentMenuSheet = { id, name, defaultPrice, stock, desc, image, variants };
    currentSheetQty = 1;
    document.getElementById('sheet-qty').textContent = '1';
    document.getElementById('sheet-notes').value = '';

    document.getElementById('sheet-menu-name').textContent = name;
    document.getElementById('sheet-menu-desc').textContent = desc || 'Menu spesial pilihan dari ABS Cafe.';
    
    const imgEl = document.getElementById('sheet-menu-img');
    const imgWrap = document.getElementById('sheet-menu-img-wrap');
    if (image) {
        imgEl.src = image;
        imgEl.classList.remove('hidden');
    } else {
        imgEl.classList.add('hidden');
    }

    const varContainer = document.getElementById('sheet-variants-container');
    const varList = document.getElementById('sheet-variants-list');
    varList.innerHTML = '';

    if (variants && variants.length > 1) {
        varContainer.classList.remove('hidden');
        currentSelectedVariant = variants[0];
        document.getElementById('sheet-menu-price').textContent = formatRupiah(currentSelectedVariant.price);

        variants.forEach((v, vIdx) => {
            const isSelected = vIdx === 0;
            const card = document.createElement('div');
            card.className = `p-2.5 rounded-2xl border cursor-pointer transition-all text-left flex items-center justify-between ${isSelected ? 'border-[#290024] bg-[#290024] text-[#FAFCF3]' : 'border-[#E3DEA4] bg-[#FAFCF3] text-[#290024]'}`;
            card.innerHTML = `
                <div>
                    <p class="text-xs font-bold leading-tight">${v.name}</p>
                    <p class="text-[10px] ${isSelected ? 'text-[#D4954D]' : 'text-[#775533]'}">${formatRupiah(v.price)}</p>
                </div>
                <div class="w-4 h-4 rounded-full border ${isSelected ? 'border-[#D4954D] bg-[#D4954D]' : 'border-[#775533]/40'} flex items-center justify-center">
                    <span class="w-1.5 h-1.5 rounded-full ${isSelected ? 'bg-[#290024]' : 'opacity-0'}"></span>
                </div>
            `;
            card.onclick = () => {
                currentSelectedVariant = v;
                document.getElementById('sheet-menu-price').textContent = formatRupiah(v.price);
                Array.from(varList.children).forEach(c => {
                    c.className = 'p-2.5 rounded-2xl border cursor-pointer transition-all text-left flex items-center justify-between border-[#E3DEA4] bg-[#FAFCF3] text-[#290024]';
                    c.querySelector('div:last-child').className = 'w-4 h-4 rounded-full border border-[#775533]/40 flex items-center justify-center';
                    c.querySelector('div:last-child span').className = 'w-1.5 h-1.5 rounded-full opacity-0';
                });
                card.className = 'p-2.5 rounded-2xl border cursor-pointer transition-all text-left flex items-center justify-between border-[#290024] bg-[#290024] text-[#FAFCF3]';
                card.querySelector('div:last-child').className = 'w-4 h-4 rounded-full border border-[#D4954D] bg-[#D4954D] flex items-center justify-center';
                card.querySelector('div:last-child span').className = 'w-1.5 h-1.5 rounded-full bg-[#290024]';
            };
            varList.appendChild(card);
        });
    } else {
        varContainer.classList.add('hidden');
        currentSelectedVariant = (variants && variants.length === 1) ? variants[0] : null;
        document.getElementById('sheet-menu-price').textContent = formatRupiah(defaultPrice);
    }

    // Set submit button action
    document.getElementById('sheet-add-btn').onclick = function() {
        const finalName = currentSelectedVariant ? `${name} (${currentSelectedVariant.name})` : name;
        const finalPrice = currentSelectedVariant ? (float)currentSelectedVariant.price : defaultPrice;
        const notes = document.getElementById('sheet-notes').value.trim();

        addToCartWithOptions(id, finalName, finalPrice, stock, currentSheetQty, notes);
        closeMenuDetailSheet();
        showToast('Ditambahkan', `${currentSheetQty}x ${finalName} masuk ke keranjang`);
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
    setTimeout(() => { sheet.classList.add('hidden'); }, 300);
}

// ---------- Mobile Cart Bottom Sheet ----------
function openMobileCart() {
    const container = document.getElementById('cart-panel-container');
    container.classList.remove('hidden');
    container.classList.add('fixed', 'inset-0', 'z-50', 'flex', 'items-end', 'bg-[#290024]/60', 'backdrop-blur-sm', 'p-0', 'sm:p-4');
    container.firstElementChild.classList.add('max-h-[92vh]', 'w-full', 'max-w-lg', 'mx-auto', 'overflow-y-auto', 'rounded-t-[2rem]', 'sm:rounded-3xl');
    document.body.classList.add('overflow-hidden');
    lucide.createIcons();
}

function closeMobileCart() {
    const container = document.getElementById('cart-panel-container');
    container.classList.remove('fixed', 'inset-0', 'z-50', 'flex', 'items-end', 'bg-[#290024]/60', 'backdrop-blur-sm', 'p-0', 'sm:p-4');
    container.classList.add('hidden', 'xl:block');
    container.firstElementChild.classList.remove('max-h-[92vh]', 'w-full', 'max-w-lg', 'mx-auto', 'overflow-y-auto', 'rounded-t-[2rem]', 'sm:rounded-3xl');
    document.body.classList.remove('overflow-hidden');
}

// ---------- Cart Operations ----------
function addToCart(menuId, name, price, stock) {
    addToCartWithOptions(menuId, name, price, stock, 1, '');
}

function addToCartWithOptions(menuId, name, price, stock, qty = 1, notes = '') {
    if (stock !== null && cart.filter(i => i.menu_id === menuId).reduce((s, i) => s + i.qty, 0) + qty > stock) {
        showToast('Stok Terbatas', `Stok "${name}" hanya tersisa ${stock}`, 'error');
        return;
    }
    const existing = cart.find(i => i.menu_id === menuId && i.name === name);
    if (existing) {
        existing.qty += qty;
        if (notes) existing.notes = notes;
    } else {
        cart.push({ menu_id: menuId, name, price, qty, notes });
    }
    renderCart();
}

function changeQty(idx, delta) {
    const item = cart[idx];
    item.qty = Math.max(1, item.qty + delta);
    renderCart();
}

function removeItem(idx) {
    cart.splice(idx, 1);
    renderCart();
}

function setNotes(idx) {
    const current = cart[idx].notes;
    const note = prompt('Catatan untuk item ini:', current || '');
    if (note !== null) { cart[idx].notes = note.trim(); renderCart(); }
}

function clearCart() {
    if (cart.length === 0) return;
    if (confirm('Kosongkan keranjang belanja?')) { cart = []; renderCart(); }
}

// ---------- Order Type Change ----------
function handleOrderTypeChange(type) {
    document.getElementById('order-type').value = type;
    const tableSelect = document.getElementById('table-number');
    const tableWrap = document.getElementById('table-number-wrapper');
    if (type === 'take_away') {
        tableSelect.disabled = true;
        tableSelect.value = '';
        tableWrap.classList.add('opacity-40');
    } else {
        tableSelect.disabled = false;
        tableWrap.classList.remove('opacity-40');
    }
}

// ---------- Voucher ----------
document.getElementById('voucher-code').addEventListener('input', refreshTotals);

function setAction(act, event) {
    if (cart.length === 0) {
        event.preventDefault();
        showToast('Keranjang Kosong', 'Tambahkan minimal 1 item terlebih dahulu.', 'error');
        return;
    }
    if (act === 'pay') {
        event.preventDefault();
        document.getElementById('action-input').value = 'pay';
        openPaymentModal();
    } else {
        document.getElementById('action-input').value = 'save';
        document.getElementById('cart-input').value = JSON.stringify(cart);
    }
}

// ---------- Kategori tab filter ----------
document.querySelectorAll('.cat-tab').forEach(btn => {
    btn.addEventListener('click', function () {
        const cat = this.dataset.cat;
        document.querySelectorAll('.cat-tab').forEach(b => {
            b.className = 'cat-tab shrink-0 px-4 py-2 rounded-full text-xs font-semibold bg-[#FAFCF3] text-[#775533] border border-[#E3DEA4] hover:bg-[#E3DEA4]/60 transition-all duration-200';
        });
        this.className = 'cat-tab shrink-0 px-4 py-2 rounded-full text-xs font-semibold bg-[#290024] text-[#F2F5E2] shadow-sm transition-all duration-200';
        document.querySelectorAll('.menu-cat').forEach(sec => {
            sec.classList.toggle('hidden', cat !== 'all' && sec.dataset.catSection !== cat);
        });
    });
});

// ---------- Modal Pembayaran Bottom Sheet ----------
function openPaymentModal() {
    document.getElementById('payment-method-input').value = selectedMethod;
    document.getElementById('cash-received-input').value = '';
    const refEl = document.getElementById('modal-reference');
    if (refEl) document.getElementById('reference-input').value = refEl.value || '';
    
    const total = parseFloat(document.getElementById('modal-total').textContent.replace(/[^\d-]/g, '')) || 0;
    document.getElementById('modal-cash').value = Math.ceil(total / 1000) * 1000;
    
    const modal = document.getElementById('payment-modal');
    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.querySelector('.transform').classList.remove('translate-y-full');
        modal.querySelector('.transform').classList.add('translate-y-0');
    }, 10);
    
    updateCashFields(total);
    lucide.createIcons();
}

function closePaymentModal() {
    const modal = document.getElementById('payment-modal');
    const panel = modal.querySelector('.transform');
    if (panel) {
        panel.classList.remove('translate-y-0');
        panel.classList.add('translate-y-full');
    }
    setTimeout(() => { modal.classList.add('hidden'); }, 300);
}

function selectMethod(code, btn) {
    selectedMethod = code;
    document.getElementById('payment-method-input').value = code;

    document.querySelectorAll('.method-btn').forEach(b => {
        b.className = 'method-btn p-3.5 rounded-2xl border cursor-pointer transition-all flex items-center justify-between gap-3 bg-[#FAFCF3] border-[#E3DEA4] hover:border-[#D4954D]';
        const rad = b.querySelector('.method-radio');
        rad.className = 'method-radio w-5 h-5 rounded-full border-2 border-[#775533]/40 flex items-center justify-center';
        rad.querySelector('span').classList.add('hidden');
    });

    btn.className = 'method-btn p-3.5 rounded-2xl border cursor-pointer transition-all flex items-center justify-between gap-3 bg-[#FAFCF3] border-[#290024] shadow-sm';
    const activeRad = btn.querySelector('.method-radio');
    activeRad.className = 'method-radio w-5 h-5 rounded-full border-2 border-[#290024] bg-[#290024] flex items-center justify-center';
    activeRad.querySelector('span').classList.remove('hidden');

    const cashField = document.getElementById('cash-field');
    if (code !== 'tunai') {
        cashField.classList.add('opacity-40', 'pointer-events-none');
        document.getElementById('modal-cash').value = '';
        document.getElementById('cash-received-input').value = '';
        document.getElementById('modal-change').textContent = 'Rp 0';
    } else {
        cashField.classList.remove('opacity-40', 'pointer-events-none');
        const total = parseFloat(document.getElementById('modal-total').textContent.replace(/[^\d-]/g, '')) || 0;
        document.getElementById('modal-cash').value = Math.ceil(total / 1000) * 1000;
        updateCashFields(total);
    }
    lucide.createIcons();
}

function setCash(val) {
    const total = parseFloat(document.getElementById('modal-total').textContent.replace(/[^\d-]/g, '')) || 0;
    document.getElementById('modal-cash').value = val === 0 ? total : val;
    updateCashFields(total);
}

function updateCashFields(total) {
    const cashInput = document.getElementById('modal-cash');
    if (selectedMethod === 'tunai' && cashInput.value) {
        const cash = parseFloat(cashInput.value) || 0;
        const chg = Math.max(0, cash - total);
        document.getElementById('modal-change').textContent = formatRupiah(chg);
        document.getElementById('cash-received-input').value = cash;
    }
}

document.getElementById('modal-cash').addEventListener('input', function () {
    const total = parseFloat(document.getElementById('modal-total').textContent.replace(/[^\d-]/g, '')) || 0;
    updateCashFields(total);
});

function confirmPayment() {
    document.getElementById('action-input').value = 'pay';
    document.getElementById('cart-input').value = JSON.stringify(cart);
    const ref = document.getElementById('modal-reference');
    if (ref) document.getElementById('reference-input').value = ref.value || '';
    document.getElementById('pos-form').submit();
}

// Initial state setup
@if($resume?->order_type === 'take_away')
    handleOrderTypeChange('take_away');
    document.querySelector('input[name="order_type"][value="take_away"]').checked = true;
@elseif($resume?->table_number)
    document.getElementById('table-number').value = '{{ $resume->table_number }}';
@endif
renderCart();
</script>
@endsection