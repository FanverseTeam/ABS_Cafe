@extends('pelanggan.layouts.app')

@section('title', 'Keranjang Belanja - ' . ($landing['cafe']['name'] ?? 'ABS Cafe'))
@section('meta_description', 'Periksa pesanan makanan dan minuman pilihan Anda di ' . ($landing['cafe']['name'] ?? 'ABS Cafe') . '.')

@section('body_class', 'bg-white text-[#290024]')
@section('body_style', 'background-color: #ffffff !important; color: #290024 !important;')
@section('main_class', 'bg-white')

@section('header')
<header class="sticky top-0 inset-x-0 z-30 bg-white border-b border-[#F0ECE4]" style="background-color: #ffffff !important;">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 h-16 flex items-center justify-between gap-4">
        <!-- Tombol Kembali ke Menu -->
        <a href="{{ route('pelanggan.menu', array_filter(['meja' => $tableNumber])) }}"
           class="inline-flex items-center gap-2 text-xs sm:text-sm font-semibold text-[#290024] hover:text-[#775533] p-2 -ml-2 rounded-xl hover:bg-[#FAF7F2] transition-colors"
           aria-label="Kembali ke Menu">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            <span>Tambah Menu</span>
        </a>

        <!-- Judul Header -->
        <h1 class="font-serif text-base sm:text-lg font-bold text-[#290024] tracking-tight">
            Keranjang Belanja
        </h1>

        <!-- Logo Cafe Ringkas -->
        <a href="{{ route('pelanggan.beranda') }}" class="shrink-0" aria-label="Beranda">
            <x-logo :show-text="false" size="sm" />
        </a>
    </div>
</header>
@endsection

@section('content')
<div x-data="cartPage()"
     x-init="initCart()"
     style="background-color: #ffffff !important;"
     class="min-h-screen bg-white pb-32 pt-4 sm:pt-6">

    <div class="mx-auto max-w-3xl px-4 sm:px-6 space-y-6">

        <!-- Banner Indikator Meja / Tipe Pesanan -->
        <div class="p-4 rounded-2xl border transition-all"
             :class="orderType === 'dine_in' ? 'bg-[#290024] text-[#E3DEA4] border-[#D4954D]/30' : 'bg-[#FAF7F2] text-[#290024] border-[#EDE8E0]'">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-base shadow-inner shrink-0"
                         :class="orderType === 'dine_in' ? 'bg-[#D4954D] text-[#290024]' : 'bg-[#290024] text-[#E3DEA4]'">
                        <template x-if="orderType === 'dine_in'">
                            <span x-text="tableNumber ? tableNumber : '?'"></span>
                        </template>
                        <template x-if="orderType === 'take_away'">
                            <svg class="w-5 h-5 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </template>
                    </div>

                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wider"
                             :class="orderType === 'dine_in' ? 'text-[#E3DEA4]/80' : 'text-[#775533]'">
                            Metode Pemesanan
                        </div>
                        <div class="text-xs sm:text-sm font-bold"
                             :class="orderType === 'dine_in' ? 'text-white' : 'text-[#290024]'">
                            <span x-show="orderType === 'dine_in'">
                                Santap di Tempat: Meja #<span x-text="tableNumber ? tableNumber : 'Belum Dipilih'"></span>
                            </span>
                            <span x-show="orderType === 'take_away'">
                                Pesan Bawa Pulang (Takeaway)
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Toggle Tipe Pesanan -->
                <div class="flex items-center gap-1 p-1 rounded-xl"
                     :class="orderType === 'dine_in' ? 'bg-white/10' : 'bg-white border border-[#EDE8E0]'">
                    <button type="button"
                            @click="setOrderType('dine_in')"
                            :class="orderType === 'dine_in' ? 'bg-[#D4954D] text-[#290024] font-bold' : 'text-[#775533] hover:text-[#290024]'"
                            class="px-2.5 py-1.5 rounded-lg text-[11px] transition-all">
                        Dine-in
                    </button>
                    <button type="button"
                            @click="setOrderType('take_away')"
                            :class="orderType === 'take_away' ? 'bg-[#290024] text-white font-bold' : 'text-[#E3DEA4]/70 hover:text-white'"
                            class="px-2.5 py-1.5 rounded-lg text-[11px] transition-all">
                        Takeaway
                    </button>
                </div>
            </div>

            <!-- Pilihan Nomor Meja jika Dine In -->
            <div x-show="orderType === 'dine_in'" class="mt-3 pt-3 border-t border-white/10 flex items-center justify-between gap-2">
                <span class="text-[11px] text-[#E3DEA4]/80">Pilih nomor meja Anda:</span>
                <select x-model="tableNumber"
                        class="bg-white/10 text-white border border-white/20 text-xs rounded-lg px-2.5 py-1 focus:outline-none focus:ring-1 focus:ring-[#D4954D]">
                    <option value="" class="text-stone-800">-- Pilih Nomor Meja --</option>
                    @foreach($allTables as $t)
                        <option value="{{ $t->number }}" class="text-stone-800" {{ $tableNumber == $t->number ? 'selected' : '' }}>
                            Meja #{{ $t->number }} ({{ $t->location_notes ?? 'Area Utama' }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- STATE JIKA KERANJANG KOSONG -->
        <div x-show="cart.length === 0" x-cloak class="bg-white border border-[#EDE8E0] rounded-3xl p-8 sm:p-12 text-center space-y-4 my-8">
            <div class="w-16 h-16 rounded-full bg-[#FAF7F2] text-[#775533] flex items-center justify-center mx-auto">
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
                   class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-[#290024] hover:bg-[#D4954D] hover:text-[#290024] text-white text-xs sm:text-sm font-semibold transition-all shadow-md">
                    <span>Lihat Daftar Menu</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </a>
            </div>
        </div>

        <!-- DAFTAR ITEM KERANJANG -->
        <div x-show="cart.length > 0" class="space-y-4">
            <div class="flex items-center justify-between pb-2 border-b border-[#EDE8E0]">
                <h2 class="font-serif text-base sm:text-lg font-bold text-[#290024]">
                    Menu yang Dipesan (<span x-text="cartTotalItems"></span>)
                </h2>
                <button type="button" @click="clearCart()" class="text-xs text-[#775533] hover:text-rose-600 transition-colors">
                    Hapus Semua
                </button>
            </div>

            <div class="space-y-3">
                <template x-for="(item, index) in cart" :key="index">
                    <div class="p-3.5 sm:p-4 rounded-2xl bg-white border border-[#EDE8E0] shadow-2xs flex items-start gap-3 sm:gap-4 transition-all hover:border-[#D4954D]/40">
                        <!-- Foto Menu -->
                        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-xl bg-stone-100 overflow-hidden shrink-0 border border-stone-200/60">
                            <template x-if="item.foto">
                                <img :src="item.foto" :alt="item.nama" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!item.foto">
                                <div class="w-full h-full flex items-center justify-center text-stone-400">
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
                                    <button type="button" @click="removeItem(index)" class="text-stone-400 hover:text-rose-500 p-1 -mr-1 transition-colors" title="Hapus menu ini">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <line x1="18" y1="6" x2="6" y2="18"></line>
                                            <line x1="6" y1="6" x2="18" y2="18"></line>
                                        </svg>
                                    </button>
                                </div>

                                <div x-show="item.varian_nama" class="text-[11px] font-medium text-[#775533]" x-text="'Varian: ' + item.varian_nama"></div>
                                <div x-show="item.catatan" class="text-[11px] text-stone-500 italic" x-text="'Catatan: ' + item.catatan"></div>
                            </div>

                            <div class="flex items-center justify-between pt-1">
                                <span class="font-serif font-bold text-xs sm:text-sm text-[#290024]" x-text="formatRupiah(item.harga * item.jumlah)"></span>

                                <!-- Kontrol Qty (+ / -) -->
                                <div class="flex items-center gap-2 bg-[#FAF7F2] p-1 rounded-full border border-[#EDE8E0]">
                                    <button type="button" @click="updateQty(index, -1)" class="w-6 h-6 rounded-full bg-white text-[#290024] hover:bg-stone-100 flex items-center justify-center font-bold text-xs shadow-2xs">
                                        -
                                    </button>
                                    <span class="text-xs font-bold text-[#290024] w-5 text-center" x-text="item.jumlah"></span>
                                    <button type="button" @click="updateQty(index, 1)" class="w-6 h-6 rounded-full bg-white text-[#290024] hover:bg-stone-100 flex items-center justify-center font-bold text-xs shadow-2xs">
                                        +
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- FORM INFORMASI PELANGGAN -->
            <div class="bg-[#FAF7F2] p-4 sm:p-5 rounded-2xl border border-[#EDE8E0] space-y-3 pt-4">
                <h3 class="font-serif text-sm font-bold text-[#290024] flex items-center gap-2">
                    <svg class="w-4 h-4 stroke-[#775533]" fill="none" viewBox="0 0 24 24" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    <span>Informasi Pemesan</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-[#290024] mb-1">Nama Lengkap *</label>
                        <input type="text"
                               x-model="customerName"
                               placeholder="Nama Anda..."
                               required
                               class="w-full bg-white text-xs p-2.5 rounded-xl border border-[#EDE8E0] focus:border-[#290024] focus:outline-none text-[#290024]">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#290024] mb-1">Nomor WhatsApp / HP (Opsional)</label>
                        <input type="tel"
                               x-model="customerPhone"
                               placeholder="0812xxxxxx"
                               class="w-full bg-white text-xs p-2.5 rounded-xl border border-[#EDE8E0] focus:border-[#290024] focus:outline-none text-[#290024]">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#290024] mb-1">Catatan Tambahan untuk Kasir / Barista</label>
                    <textarea x-model="orderNotes"
                              rows="2"
                              placeholder="Misal: Mohon disajikan bersamaan, es dipisah..."
                              class="w-full bg-white text-xs p-2.5 rounded-xl border border-[#EDE8E0] focus:border-[#290024] focus:outline-none text-[#290024]"></textarea>
                </div>
            </div>

            <!-- RINGKASAN PEMBAYARAN -->
            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-[#EDE8E0] shadow-xs space-y-2.5 text-xs text-[#775533]">
                <h3 class="font-serif text-sm font-bold text-[#290024] mb-2">Ringkasan Pembayaran</h3>

                <div class="flex items-center justify-between">
                    <span>Subtotal Pesanan</span>
                    <span class="font-semibold text-[#290024]" x-text="formatRupiah(subtotal)"></span>
                </div>

                <div class="flex items-center justify-between">
                    <span>Pajak (PB1 10%)</span>
                    <span class="font-semibold text-[#290024]" x-text="formatRupiah(tax)"></span>
                </div>

                <div class="flex items-center justify-between pt-2 border-t border-[#EDE8E0] text-sm">
                    <span class="font-bold text-[#290024]">Total Pembayaran</span>
                    <span class="font-serif font-black text-base text-[#290024]" x-text="formatRupiah(total)"></span>
                </div>

                <p class="text-[11px] text-stone-400 pt-1">
                    * Pembayaran dilakukan di kasir saat mengambil atau setelah santap selesai (Tunai, QRIS, atau Kartu Debit).
                </p>
            </div>
        </div>

    </div>

    <!-- FLOATING CHECKOUT BAR (Mobile First) -->
    <div x-show="cart.length > 0"
         x-cloak
         class="fixed bottom-0 inset-x-0 z-40 bg-white/95 backdrop-blur-md border-t border-[#EDE8E0] p-4 shadow-[0_-8px_30px_rgba(0,0,0,0.06)]">
        <div class="max-w-3xl mx-auto flex items-center justify-between gap-4">
            <div>
                <span class="block text-[11px] text-[#775533]">Total Pesanan:</span>
                <span class="font-serif font-bold text-base sm:text-lg text-[#290024]" x-text="formatRupiah(total)"></span>
            </div>

            <button type="button"
                    @click="submitOrder()"
                    :disabled="isSubmitting || !customerName.trim() || (orderType === 'dine_in' && !tableNumber)"
                    :class="(!customerName.trim() || (orderType === 'dine_in' && !tableNumber)) ? 'opacity-50 cursor-not-allowed bg-stone-300 text-stone-600' : 'bg-[#290024] hover:bg-[#D4954D] hover:text-[#290024] text-white shadow-md'"
                    class="px-6 sm:px-8 py-3 rounded-full text-xs sm:text-sm font-bold tracking-wide transition-all flex items-center justify-center gap-2">
                <template x-if="isSubmitting">
                    <span class="flex items-center gap-2">
                        <svg class="animate-spin w-4 h-4" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span>Memproses...</span>
                    </span>
                </template>
                <template x-if="!isSubmitting">
                    <span class="flex items-center gap-2">
                        <span>Pesan Sekarang</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </span>
                </template>
            </button>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function cartPage() {
    return {
        cart: [],
        tableNumber: '{{ $tableNumber ? $tableNumber : '' }}',
        orderType: '{{ $tableNumber ? 'dine_in' : 'take_away' }}',
        customerName: '',
        customerPhone: '',
        orderNotes: '',
        isSubmitting: false,

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

        setOrderType(type) {
            this.orderType = type;
            if (type === 'take_away') {
                // Jangan reset tableNumber agar bisa switch kembali
            }
        },

        updateQty(index, change) {
            if (this.cart[index]) {
                this.cart[index].jumlah += change;
                if (this.cart[index].jumlah <= 0) {
                    this.removeItem(index);
                    return;
                }
                this.saveCart();
            }
        },

        removeItem(index) {
            if (confirm('Hapus menu ini dari keranjang?')) {
                this.cart.splice(index, 1);
                this.saveCart();
            }
        },

        clearCart() {
            if (confirm('Hapus semua isi keranjang?')) {
                this.cart = [];
                this.saveCart();
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

            if (this.orderType === 'dine_in' && !this.tableNumber) {
                alert('Silakan pilih nomor meja Anda untuk santap di tempat.');
                return;
            }

            if (this.cart.length === 0) {
                alert('Keranjang belanja Anda masih kosong.');
                return;
            }

            this.isSubmitting = true;

            try {
                const payload = {
                    customer_name: this.customerName.trim(),
                    customer_phone: this.customerPhone ? this.customerPhone.trim() : null,
                    order_type: this.orderType,
                    table_number: this.orderType === 'dine_in' ? this.tableNumber : null,
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
                    // Kosongkan keranjang di localStorage
                    localStorage.removeItem('abscafe_cart');
                    window.location.href = result.redirect_url;
                } else {
                    alert(result.message || 'Terjadi kesalahan saat membuat pesanan.');
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
