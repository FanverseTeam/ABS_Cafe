@extends('pelanggan.layouts.app')

@section('title', 'Detail Pesanan - ' . ($landing['cafe']['name'] ?? 'ABS Cafe'))
@section('meta_description', 'Periksa pesanan makanan dan minuman pilihan Anda di ' . ($landing['cafe']['name'] ?? 'ABS Cafe') . '.')

@section('body_class', 'bg-[#F2F5E2] text-[#290024]')
@section('body_style', 'background-color: #F2F5E2 !important; color: #290024 !important;')
@section('main_class', 'bg-[#F2F5E2]')

@section('header')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('checkout', {
            step: 1
        });
    });
    if (window.Alpine && !window.Alpine.store('checkout')) {
        window.Alpine.store('checkout', {
            step: 1
        });
    }
</script>

<!-- TOPBAR HEADER (RAPI, BERSIH, & DINAMIS SESUAI TAHAP) -->
<header x-data
        class="sticky top-0 inset-x-0 z-30 bg-[#F2F5E2]/95 backdrop-blur-md border-b border-[#E3DEA4]/80 shadow-xs"
        style="background-color: #F2F5E2 !important;">
    <div class="mx-auto max-w-3xl px-4 sm:px-6 h-14 flex items-center justify-between gap-2">
        <!-- Sisi Kiri: Tombol Kembali Sesuai Step -->
        <div class="w-24 flex items-center justify-start">
            <!-- Saat di Step 1: Kembali ke Menu -->
            <a x-show="!$store.checkout || $store.checkout.step === 1"
               href="{{ route('pelanggan.menu', array_filter(['meja' => $tableNumber])) }}"
               class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#290024] hover:text-[#775533] py-1.5 px-2 -ml-2 rounded-xl hover:bg-[#E3DEA4]/40 transition-colors"
               aria-label="Kembali ke Menu">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                <span>Menu</span>
            </a>

            <!-- Saat di Step 2: Kembali ke Detail Pesanan (Step 1) -->
            <button x-show="$store.checkout && $store.checkout.step === 2"
                    x-cloak
                    type="button"
                    @click="$store.checkout.step = 1; window.scrollTo({ top: 0, behavior: 'smooth' })"
                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#290024] hover:text-[#775533] py-1.5 px-2 -ml-2 rounded-xl hover:bg-[#E3DEA4]/40 transition-colors"
                    aria-label="Kembali ke Detail Pesanan">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                <span>Kembali</span>
            </button>
        </div>

        <!-- Judul Header di Tengah (Tepat di Tengah & Tidak Berantakan) -->
        <h1 class="flex-1 text-center font-serif text-sm sm:text-base font-bold text-[#290024] tracking-tight">
            <span x-show="!$store.checkout || $store.checkout.step === 1">Detail Pesanan</span>
            <span x-show="$store.checkout && $store.checkout.step === 2" x-cloak>Isi Data Pelanggan</span>
        </h1>

        <!-- Sisi Kanan: Logo Cafe -->
        <div class="w-24 flex items-center justify-end">
            <a href="{{ route('pelanggan.beranda') }}" class="shrink-0" aria-label="Beranda">
                <x-logo :show-text="false" size="sm" />
            </a>
        </div>
    </div>
</header>
@endsection

@section('content')
<div x-data="cartPage()"
     x-init="initCart()"
     style="background-color: #F2F5E2 !important;"
     class="min-h-screen bg-[#F2F5E2] pb-32">

    <div class="mx-auto max-w-3xl px-4 sm:px-6 space-y-6 pt-4 sm:pt-6">

        {{-- ============================================================= --}}
        {{-- STATE KETIKA KERANJANG KOSONG                                 --}}
        {{-- ============================================================= --}}
        <div x-show="cart.length === 0" x-cloak class="bg-[#F2F5E2] border border-[#E3DEA4] rounded-3xl p-8 sm:p-12 text-center space-y-4 my-8 shadow-soft">
            <div class="w-16 h-16 rounded-full bg-[#E3DEA4]/40 text-[#775533] flex items-center justify-center mx-auto">
                <svg class="w-8 h-8 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="1.8">
                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <path d="M16 10a4 4 0 0 1-8 0"></path>
                </svg>
            </div>
            <div class="space-y-1">
                <h2 class="font-serif text-lg sm:text-xl font-bold text-[#290024]">Keranjang Anda Masih Kosong</h2>
                <p class="text-xs sm:text-sm text-[#775533]">Jelajahi menu kopi dan hidangan spesial kami sekarang.</p>
            </div>
            <div>
                <a href="{{ route('pelanggan.menu', array_filter(['meja' => $tableNumber])) }}"
                   class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-[#290024] hover:bg-[#D4954D] hover:text-[#290024] text-[#F2F5E2] text-xs sm:text-sm font-semibold transition-all shadow-md">
                    <span>Lihat Daftar Menu</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </a>
            </div>
        </div>

        {{-- ============================================================= --}}
        {{-- STEP 1: TINJAU & EDIT ISI KERANJANG                           --}}
        {{-- ============================================================= --}}
        <div x-show="cart.length > 0 && step === 1" class="space-y-4">
            <div class="flex items-center justify-between pb-2 border-b border-[#E3DEA4]">
                <div>
                    <h2 class="font-serif text-base sm:text-lg font-bold text-[#290024]">
                        Daftar Pesanan Anda (<span x-text="cartTotalItems"></span>)
                    </h2>
                    <p class="text-xs text-[#775533] mt-0.5">
                        Anda dapat menambah, mengurangi jumlah, atau menghapus menu sebelum lanjut memesan.
                    </p>
                </div>

                <button type="button"
                        @click="clearCart()"
                        class="text-xs text-[#775533] hover:text-[#D4954D] transition-colors shrink-0">
                    Kosongkan
                </button>
            </div>

            <!-- List Item Keranjang -->
            <div class="space-y-3">
                <template x-for="(item, index) in cart" :key="index">
                    <div class="p-3.5 sm:p-4 rounded-2xl bg-[#F2F5E2] border border-[#E3DEA4]/80 shadow-soft flex items-start gap-3 sm:gap-4 transition-all hover:border-[#D4954D]/50">
                        <!-- Foto Menu -->
                        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-xl bg-[#E3DEA4]/25 overflow-hidden shrink-0 border border-[#E3DEA4]/60">
                            <template x-if="item.foto">
                                <img :src="item.foto" :alt="item.nama" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!item.foto">
                                <div class="w-full h-full flex items-center justify-center text-[#775533]/60">
                                    <svg class="w-6 h-6 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="1.5">
                                        <path d="M18 8h1a4 4 0 0 1 0 8h-1"></path>
                                        <path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"></path>
                                        <line x1="6" y1="1" x2="6" y2="4"></line>
                                        <line x1="10" y1="1" x2="10" y2="4"></line>
                                        <line x1="14" y1="1" x2="14" y2="4"></line>
                                    </svg>
                                </div>
                            </template>
                        </div>

                        <!-- Info & Kontrol Jumlah -->
                        <div class="flex-1 min-w-0 flex flex-col justify-between h-full space-y-2">
                            <div>
                                <div class="flex items-start justify-between gap-2">
                                    <h3 class="font-bold text-xs sm:text-sm text-[#290024] leading-snug truncate" x-text="item.nama"></h3>
                                    <!-- Tombol Hapus -->
                                    <button type="button"
                                            @click="removeItem(index)"
                                            class="text-[#775533]/60 hover:text-[#D4954D] p-1 -mr-1 transition-colors"
                                            title="Hapus menu ini">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <line x1="18" y1="6" x2="6" y2="18"></line>
                                            <line x1="6" y1="6" x2="18" y2="18"></line>
                                        </svg>
                                    </button>
                                </div>

                                <div x-show="item.varian_nama" class="text-[11px] font-medium text-[#775533]" x-text="'Varian: ' + item.varian_nama"></div>
                                <div x-show="item.catatan" class="text-[11px] text-[#775533] italic" x-text="'Catatan: ' + item.catatan"></div>
                            </div>

                            <div class="flex items-center justify-between pt-1">
                                <div>
                                    <span class="font-serif font-bold text-xs sm:text-sm text-[#290024]" x-text="formatRupiah(item.harga * item.jumlah)"></span>
                                    <span class="text-[10px] text-[#775533]/60 block" x-show="item.jumlah > 1" x-text="'(' + formatRupiah(item.harga) + ' / porsi)'"></span>
                                </div>

                                <!-- Kontrol Edit Qty (+ / -) yang Jelas -->
                                <div class="flex items-center gap-2 bg-[#E3DEA4]/30 p-1 rounded-full border border-[#E3DEA4]">
                                    <button type="button"
                                            @click="updateQty(index, -1)"
                                            class="w-7 h-7 rounded-full bg-[#F2F5E2] text-[#290024] hover:bg-[#E3DEA4]/50 active:scale-95 flex items-center justify-center font-bold text-sm shadow-2xs transition-all"
                                            aria-label="Kurangi 1">
                                        -
                                    </button>
                                    <span class="text-xs font-bold text-[#290024] w-6 text-center" x-text="item.jumlah"></span>
                                    <button type="button"
                                            @click="updateQty(index, 1)"
                                            class="w-7 h-7 rounded-full bg-[#F2F5E2] text-[#290024] hover:bg-[#E3DEA4]/50 active:scale-95 flex items-center justify-center font-bold text-sm shadow-2xs transition-all"
                                            aria-label="Tambah 1">
                                        +
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Tombol Tambah Menu Lainnya -->
            <div class="pt-2">
                <a href="{{ route('pelanggan.menu', array_filter(['meja' => $tableNumber])) }}"
                   class="w-full py-3 px-4 rounded-2xl border border-dashed border-[#775533]/40 hover:border-[#290024] text-[#775533] hover:text-[#290024] text-xs font-semibold flex items-center justify-center gap-2 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    <span>Tambah Menu Minuman / Makanan Lainnya</span>
                </a>
            </div>

            <!-- Ringkasan Subtotal Tahap 1 -->
            <div class="bg-[#F2F5E2] p-4 sm:p-5 rounded-2xl border border-[#E3DEA4]/80 shadow-soft space-y-2 text-xs">
                <div class="flex items-center justify-between text-[#775533]">
                    <span>Total Item</span>
                    <span class="font-bold text-[#290024]"><span x-text="cartTotalItems"></span> porsi</span>
                </div>
                <div class="flex items-center justify-between text-[#775533]">
                    <span>Subtotal Menu</span>
                    <span class="font-bold text-[#290024]" x-text="formatRupiah(subtotal)"></span>
                </div>
                <div class="pt-2 border-t border-[#E3DEA4]/60 text-[11px] text-[#775533]/70">
                    * Pengisian data pemesan dan pilihan metode pesanan akan diatur pada halaman selanjutnya.
                </div>
            </div>
        </div>

        {{-- ============================================================= --}}
        {{-- STEP 2: DATA PELANGGAN & METODE PESANAN                       --}}
        {{-- ============================================================= --}}
        <div x-show="cart.length > 0 && step === 2" x-cloak class="space-y-5">

            <!-- 1. DATA PELANGGAN (PALING ATAS) -->
            <div class="bg-[#F2F5E2] p-4 sm:p-5 rounded-2xl border border-[#E3DEA4]/80 shadow-soft space-y-3.5">
                <div class="pb-2 border-b border-[#E3DEA4]/60 flex items-center justify-between">
                    <h2 class="font-serif text-sm sm:text-base font-bold text-[#290024] flex items-center gap-2">
                        <svg class="w-4 h-4 stroke-[#775533]" fill="none" viewBox="0 0 24 24" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <span>Data Pelanggan</span>
                    </h2>
                    <span class="text-[10px] text-[#775533] font-medium">* Wajib diisi</span>
                </div>

                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-[#290024] mb-1">
                            Nama Pemesan <span class="text-[#D4954D]">*</span>
                        </label>
                        <input type="text"
                               x-model="customerName"
                               placeholder="Masukkan nama lengkap atau panggilan..."
                               required
                               class="w-full bg-[#F2F5E2]/80 text-xs p-3 rounded-xl border border-[#E3DEA4] focus:bg-[#F2F5E2] focus:border-[#290024] focus:outline-none text-[#290024]">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#290024] mb-1">
                            Nomor WhatsApp / HP <span class="text-[#D4954D]">*</span>
                        </label>
                        <input type="tel"
                               x-model="customerPhone"
                               placeholder="Contoh: 081234567890"
                               required
                               class="w-full bg-[#F2F5E2]/80 text-xs p-3 rounded-xl border border-[#E3DEA4] focus:bg-[#F2F5E2] focus:border-[#290024] focus:outline-none text-[#290024]">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#290024] mb-1">
                            Catatan Pesanan <span class="text-[#775533]/70 font-normal">(Opsional)</span>
                        </label>
                        <textarea x-model="orderNotes"
                                  rows="2"
                                  placeholder="Contoh: Tolong sendok garpu disiapkan, disajikan bersamaan..."
                                  class="w-full bg-[#F2F5E2]/80 text-xs p-3 rounded-xl border border-[#E3DEA4] focus:bg-[#F2F5E2] focus:border-[#290024] focus:outline-none text-[#290024]"></textarea>
                    </div>
                </div>
            </div>

            <!-- 2. PILIHAN DINE IN / TAKE AWAY (KOTAK PERSEGI JEJER 2 HORIZONTAL) -->
            <div class="space-y-2">
                <label class="block text-xs font-bold uppercase tracking-wider text-[#775533]">
                    Pilihan Pesanan <span class="text-[#D4954D]">*</span>
                </label>

                <div class="grid grid-cols-2 gap-2.5 sm:gap-3">
                    <!-- Kotak 1: Makan di Tempat (Dine In) -->
                    <button type="button"
                            @click="setOrderType('dine_in')"
                            class="p-3 sm:p-4 rounded-2xl border-2 transition-all flex flex-col items-center justify-center text-center cursor-pointer min-h-[96px] focus:outline-none"
                            :class="orderType === 'dine_in'
                                ? 'border-[#290024] bg-[#290024] text-[#F2F5E2] shadow-sm ring-1 ring-[#D4954D]/50'
                                : 'border-[#E3DEA4] bg-[#F2F5E2] text-[#290024] hover:bg-[#E3DEA4]/30'">

                        <div class="w-9 h-9 rounded-xl flex items-center justify-center mb-1.5 transition-colors shrink-0"
                             :class="orderType === 'dine_in' ? 'bg-[#D4954D] text-[#290024]' : 'bg-[#E3DEA4]/40 text-[#290024] border border-[#E3DEA4]'">
                            <svg class="w-5 h-5 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>

                        <span class="font-bold text-xs sm:text-sm leading-tight"
                              :class="orderType === 'dine_in' ? 'text-[#F2F5E2]' : 'text-[#290024]'">
                            Dine In
                        </span>
                        <span class="text-[10px] mt-0.5 leading-tight"
                              :class="orderType === 'dine_in' ? 'text-[#E3DEA4]' : 'text-[#775533]'">
                            Santap di Tempat
                        </span>
                    </button>

                    <!-- Kotak 2: Bawa Pulang (Take Away) -->
                    <button type="button"
                            @click="setOrderType('take_away')"
                            class="p-3 sm:p-4 rounded-2xl border-2 transition-all flex flex-col items-center justify-center text-center cursor-pointer min-h-[96px] focus:outline-none"
                            :class="orderType === 'take_away'
                                ? 'border-[#290024] bg-[#290024] text-[#F2F5E2] shadow-sm ring-1 ring-[#D4954D]/50'
                                : 'border-[#E3DEA4] bg-[#F2F5E2] text-[#290024] hover:bg-[#E3DEA4]/30'">

                        <div class="w-9 h-9 rounded-xl flex items-center justify-center mb-1.5 transition-colors shrink-0"
                             :class="orderType === 'take_away' ? 'bg-[#D4954D] text-[#290024]' : 'bg-[#E3DEA4]/40 text-[#290024] border border-[#E3DEA4]'">
                            <svg class="w-5 h-5 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>

                        <span class="font-bold text-xs sm:text-sm leading-tight"
                              :class="orderType === 'take_away' ? 'text-[#F2F5E2]' : 'text-[#290024]'">
                            Take Away
                        </span>
                        <span class="text-[10px] mt-0.5 leading-tight"
                              :class="orderType === 'take_away' ? 'text-[#E3DEA4]' : 'text-[#775533]'">
                            Bawa Pulang
                        </span>
                    </button>
                </div>
            </div>

            <!-- Catatan Informasi Pembayaran Ringkas -->
            <div class="px-3 py-2 rounded-xl bg-[#E3DEA4]/30 border border-[#E3DEA4] text-[11px] text-[#775533] text-center">
                * Pembayaran dilakukan di kasir saat mengambil atau setelah selesai santap (Tunai, QRIS, Kartu Debit).
            </div>
        </div>

    </div>

    {{-- ============================================================= --}}
    {{-- FLOATING BOTTOM BAR (DENGAN TOMBOL PANAH KECIL UNTUK RINCIAN)  --}}
    {{-- ============================================================= --}}
    <div x-show="cart.length > 0"
         x-cloak
         class="fixed bottom-0 inset-x-0 z-40 bg-[#F2F5E2]/95 backdrop-blur-md border-t border-[#E3DEA4] px-4 py-3 sm:py-3.5 shadow-[0_-8px_30px_rgba(41,0,36,0.08)]">
        <div class="max-w-3xl mx-auto flex items-center justify-between gap-3">

            <!-- Kiri: Total Pesanan + Tombol Panah Kecil untuk Perlebar Halaman / Rincian -->
            <div class="flex items-center gap-1.5 min-w-0">
                <div>
                    <div class="flex items-center gap-1">
                        <span class="text-[11px] text-[#775533] leading-none"
                              x-text="step === 1 ? 'Estimasi Total' : 'Total Pesanan'"></span>
                        <!-- Tombol panah kecil untuk perlebar / lihat rincian pesanan -->
                        <button type="button"
                                @click="showDetails = !showDetails"
                                class="w-4 h-4 rounded-full bg-[#E3DEA4]/40 hover:bg-[#E3DEA4] text-[#290024] inline-flex items-center justify-center transition-transform duration-200"
                                :class="showDetails ? 'rotate-180' : ''"
                                aria-label="Lihat rincian pesanan">
                            <svg class="w-2.5 h-2.5 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="3">
                                <polyline points="18 15 12 9 6 15"></polyline>
                            </svg>
                        </button>
                    </div>
                    <div class="font-serif font-bold text-base sm:text-lg text-[#290024] leading-tight cursor-pointer"
                         @click="showDetails = !showDetails"
                         x-text="formatRupiah(total)"></div>
                </div>
            </div>

            <!-- Kanan: Tombol Aksi Berdasarkan Step -->
            <div class="shrink-0">
                <!-- Step 1: Lanjut -->
                <template x-if="step === 1">
                    <button type="button"
                            @click="goToStep(2)"
                            class="px-6 sm:px-7 py-2.5 rounded-full bg-[#290024] hover:bg-[#D4954D] hover:text-[#290024] text-[#F2F5E2] text-xs sm:text-sm font-bold tracking-wide transition-all shadow-md flex items-center gap-1.5">
                        <span>Lanjut</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </button>
                </template>

                <!-- Step 2: Pesan Sekarang -->
                <template x-if="step === 2">
                    <button type="button"
                            @click="submitOrder()"
                            :disabled="isSubmitting || !customerName.trim()"
                            :class="(!customerName.trim())
                                ? 'opacity-50 cursor-not-allowed bg-[#E3DEA4] text-[#775533]'
                                : 'bg-[#290024] hover:bg-[#D4954D] hover:text-[#290024] text-[#F2F5E2] shadow-md'"
                            class="px-5 sm:px-7 py-2.5 rounded-full text-xs sm:text-sm font-bold tracking-wide transition-all flex items-center justify-center gap-1.5">
                        <template x-if="isSubmitting">
                            <span class="flex items-center gap-1.5">
                                <svg class="animate-spin w-4 h-4" viewBox="0 0 24 24" fill="none">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                                <span>Memproses...</span>
                            </span>
                        </template>
                        <template x-if="!isSubmitting">
                            <span class="flex items-center gap-1.5">
                                <span>Pesan Sekarang</span>
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                    <polyline points="12 5 19 12 12 19"></polyline>
                                </svg>
                            </span>
                        </template>
                    </button>
                </template>
            </div>
        </div>
    </div>

    {{-- ============================================================= --}}
    {{-- MODAL / DRAWER RINCIAN PESANAN (MUNCUL KETIKA PANAH DITEKAN) --}}
    {{-- ============================================================= --}}
    <!-- Backdrop Gelap -->
    <div x-show="showDetails"
         x-cloak
         x-transition:enter="transition-opacity ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="showDetails = false"
         class="fixed inset-0 bg-[#290024]/60 z-40 backdrop-blur-2xs"></div>

    <!-- Panel Drawer Slide Up -->
    <div x-show="showDetails"
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-full"
         x-transition:enter-end="translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0"
         x-transition:leave-end="translate-y-full"
         class="fixed inset-x-0 bottom-0 z-50 bg-[#F2F5E2] rounded-t-3xl border-t border-[#E3DEA4] shadow-2xl max-h-[82vh] flex flex-col">

        <!-- Header Drawer dengan Tombol Panah Kecil untuk Tutup -->
        <div class="px-5 py-3.5 border-b border-[#E3DEA4] flex items-center justify-between bg-[#F2F5E2] rounded-t-3xl sticky top-0 z-10">
            <div>
                <h3 class="font-serif text-sm sm:text-base font-bold text-[#290024]">Rincian Pesanan</h3>
                <p class="text-[11px] text-[#775533]">
                    Total <span x-text="cartTotalItems"></span> menu dipesan
                </p>
            </div>

            <!-- Tombol panah kecil untuk tutup rincian (kecil aa) -->
            <button type="button"
                    @click="showDetails = false"
                    class="w-6 h-6 rounded-full bg-[#E3DEA4]/40 hover:bg-[#E3DEA4] text-[#290024] flex items-center justify-center transition-colors shadow-2xs"
                    aria-label="Tutup rincian">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </button>
        </div>

        <!-- Daftar Menu & Breakdown Harga -->
        <div class="px-5 py-3.5 overflow-y-auto space-y-3.5 flex-1 text-xs">
            <!-- List Items -->
            <div class="space-y-2">
                <template x-for="(item, idx) in cart" :key="idx">
                    <div class="flex items-start justify-between py-1.5 border-b border-[#E3DEA4]/60 last:border-0">
                        <div class="min-w-0 pr-3">
                            <div class="font-bold text-[#290024] text-xs truncate" x-text="item.nama"></div>
                            <div x-show="item.varian_nama" class="text-[10px] text-[#775533]" x-text="'Varian: ' + item.varian_nama"></div>
                            <div class="text-[11px] text-[#775533]/70" x-text="item.jumlah + 'x @ ' + formatRupiah(item.harga)"></div>
                        </div>
                        <div class="font-serif font-bold text-[#290024] shrink-0 text-xs" x-text="formatRupiah(item.harga * item.jumlah)"></div>
                    </div>
                </template>
            </div>

            <!-- Rincian Perhitungan Subtotal, Pajak, Total -->
            <div class="bg-[#E3DEA4]/30 p-3 rounded-xl border border-[#E3DEA4] space-y-1.5">
                <div class="flex items-center justify-between text-[#775533]">
                    <span>Subtotal Menu</span>
                    <span class="font-bold text-[#290024]" x-text="formatRupiah(subtotal)"></span>
                </div>
                <div class="flex items-center justify-between text-[#775533]">
                    <span>Pajak (PB1 10%)</span>
                    <span class="font-bold text-[#290024]" x-text="formatRupiah(tax)"></span>
                </div>
                <div class="pt-2 border-t border-[#E3DEA4] flex items-center justify-between font-bold text-sm text-[#290024]">
                    <span>Total Pesanan</span>
                    <span class="font-serif font-black text-base text-[#290024]" x-text="formatRupiah(total)"></span>
                </div>
            </div>
        </div>

        <!-- Bagian Bawah Drawer: Total & Tombol Lanjut / Pesan Sekarang -->
        <div class="p-4 border-t border-[#E3DEA4] bg-[#F2F5E2] flex items-center justify-between gap-3">
            <div>
                <span class="text-[10px] text-[#775533] block leading-none">Total:</span>
                <span class="font-serif font-bold text-base text-[#290024]" x-text="formatRupiah(total)"></span>
            </div>

            <div>
                <template x-if="step === 1">
                    <button type="button"
                            @click="showDetails = false; goToStep(2)"
                            class="px-6 py-2 rounded-full bg-[#290024] hover:bg-[#D4954D] hover:text-[#290024] text-[#F2F5E2] text-xs font-bold tracking-wide transition-all shadow-md flex items-center gap-1.5">
                        <span>Lanjut</span>
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </button>
                </template>

                <template x-if="step === 2">
                    <button type="button"
                            @click="showDetails = false; submitOrder()"
                            :disabled="isSubmitting || !customerName.trim()"
                            :class="(!customerName.trim())
                                ? 'opacity-50 cursor-not-allowed bg-[#E3DEA4] text-[#775533]'
                                : 'bg-[#290024] hover:bg-[#D4954D] hover:text-[#290024] text-[#F2F5E2] shadow-md'"
                            class="px-5 py-2 rounded-full text-xs font-bold tracking-wide transition-all flex items-center justify-center gap-1.5">
                        <span>Pesan Sekarang</span>
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </button>
                </template>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function cartPage() {
    return {
        get step() {
            return (window.Alpine && Alpine.store('checkout')) ? Alpine.store('checkout').step : 1;
        },
        set step(val) {
            if (window.Alpine && Alpine.store('checkout')) {
                Alpine.store('checkout').step = val;
            }
        },
        cart: [],
        tableNumber: '{{ $tableNumber ? $tableNumber : '' }}',
        orderType: '{{ $tableNumber ? 'dine_in' : 'take_away' }}',
        customerName: '',
        customerPhone: '',
        orderNotes: '',
        isSubmitting: false,
        showDetails: false, // State buka/tutup rincian harga

        initCart() {
            this.loadCart();

            if (!this.tableNumber) {
                const storedTable = localStorage.getItem('abscafe_table_number');
                if (storedTable) {
                    this.tableNumber = storedTable;
                    this.orderType = 'dine_in';
                }
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
            } catch (e) {}
        },

        goToStep(targetStep) {
            if (targetStep === 2) {
                if (this.cart.length === 0) {
                    alert('Keranjang belanja Anda masih kosong.');
                    return;
                }
            }
            this.step = targetStep;
            this.showDetails = false;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        setOrderType(type) {
            this.orderType = type;
        },

        updateQty(index, change) {
            if (this.cart[index]) {
                const newQty = this.cart[index].jumlah + change;
                if (newQty <= 0) {
                    this.removeItem(index);
                    return;
                }
                this.cart[index].jumlah = newQty;
                this.saveCart();
            }
        },

        removeItem(index) {
            if (confirm('Hapus menu ini dari pesanan?')) {
                this.cart.splice(index, 1);
                this.saveCart();
                if (this.cart.length === 0) {
                    this.step = 1;
                    this.showDetails = false;
                }
            }
        },

        clearCart() {
            if (confirm('Kosongkan semua menu pesanan?')) {
                this.cart = [];
                this.saveCart();
                this.step = 1;
                this.showDetails = false;
            }
        },

        get cartTotalItems() {
            return this.cart.reduce((sum, item) => sum + (parseInt(item.jumlah) || 0), 0);
        },

        get subtotal() {
            return this.cart.reduce((sum, item) => {
                const price = parseFloat(item.harga) || 0;
                const qty = parseInt(item.jumlah) || 0;
                return sum + (price * qty);
            }, 0);
        },

        get tax() {
            return Math.round(this.subtotal * 0.1);
        },

        get total() {
            return this.subtotal + this.tax;
        },

        formatRupiah(num) {
            return 'Rp ' + (num || 0).toLocaleString('id-ID');
        },

        async submitOrder() {
            if (!this.customerName.trim()) {
                alert('Silakan masukkan nama Anda.');
                return;
            }

            if (!this.customerPhone || !this.customerPhone.trim()) {
                alert('Silakan masukkan nomor WhatsApp / HP Anda.');
                return;
            }

            if (this.cart.length === 0) {
                alert('Keranjang belanja Anda masih kosong.');
                this.step = 1;
                return;
            }

            this.isSubmitting = true;

            try {
                const payload = {
                    customer_name: this.customerName.trim(),
                    customer_phone: this.customerPhone.trim(),
                    order_type: this.orderType,
                    table_number: null,
                    notes: this.orderNotes ? this.orderNotes.trim() : null,
                    items: this.cart
                };

                const response = await fetch("{{ route('pelanggan.pesanan.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(payload)
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    localStorage.removeItem('abscafe_cart');
                    window.location.href = result.redirect_url;
                } else {
                    alert(result.message || 'Terjadi kendala saat memproses pesanan.');
                }
            } catch (error) {
                alert('Koneksi terputus. Silakan coba kembali.');
            } finally {
                this.isSubmitting = false;
            }
        }
    };
}
</script>
@endpush
