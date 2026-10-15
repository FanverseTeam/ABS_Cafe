@extends('kasir.layouts.app')

@section('title', 'POS / Input Pesanan - Kasir')
@section('page_title', 'POS / Input Pesanan')

@section('content')
<style>
    .pos-menu-card { transition: transform .15s, box-shadow .15s; }
    .pos-menu-card:hover { transform: translateY(-2px); box-shadow: 0 10px 25px -3px rgba(41,0,36,.1); }
    .pos-menu-card:active { transform: scale(.98); }
    .qty-btn { transition: background .15s; }
</style>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    <!-- Katalog Menu -->
    <div class="xl:col-span-2 space-y-4">
        <div class="flex items-center justify-between gap-3 flex-wrap">
            <div class="flex gap-2 overflow-x-auto pb-1">
                <button type="button" data-cat="all" class="cat-tab px-4 py-2 rounded-full text-[11px] font-semibold bg-cafe-dark text-white transition-colors">Semua</button>
                @foreach($categories as $category)
                    <button type="button" data-cat="{{ $category->id }}" class="cat-tab px-4 py-2 rounded-full text-[11px] font-semibold bg-cafe-beige/50 text-cafe-brown hover:bg-cafe-dark hover:text-white transition-colors">{{ $category->name }}</button>
                @endforeach
            </div>
            <p class="text-[11px] text-cafe-muted shrink-0">{{ $categories->sum(fn ($c) => $c->menus->count()) }} menu tersedia</p>
        </div>

        @foreach($categories as $category)
            <section data-cat-section="{{ $category->id }}" class="menu-cat">
                <h3 class="font-serif font-bold text-sm text-cafe-dark mb-3 flex items-center gap-2">
                    <i data-lucide="{{ $category->icon ?: 'coffee' }}" class="w-4 h-4 text-cafe-gold"></i>
                    {{ $category->name }}
                </h3>

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                    @forelse($category->menus as $menu)
                        <div class="pos-menu-card bg-cafe-card rounded-2xl border border-cafe-beige/60 shadow-soft overflow-hidden {{ ! $menu->is_available ? 'opacity-60' : '' }}">
                            <div class="h-24 flex items-center justify-center bg-cafe-cream relative overflow-hidden">
                                @if($menu->image)
                                    <img src="{{ $menu->image }}" alt="{{ $menu->name }}" class="w-full h-full object-cover">
                                @else
                                    <i data-lucide="coffee" class="w-8 h-8 text-cafe-gold"></i>
                                @endif
                                @if($menu->is_best_seller)
                                    <span class="absolute top-2 left-2 px-2 py-0.5 rounded-full bg-cafe-gold text-cafe-dark text-[9px] font-bold">Terlaris</span>
                                @endif
                                @if($menu->stock !== null && $menu->stock <= $menu->min_stock)
                                    <span class="absolute top-2 right-2 px-2 py-0.5 rounded-full bg-cafe-danger text-white text-[9px] font-bold">Stok {{ $menu->stock }}</span>
                                @endif
                            </div>

                            <div class="p-3">
                                <p class="text-xs font-bold text-cafe-dark leading-snug line-clamp-1">{{ $menu->name }}</p>
                                <p class="text-[10px] text-cafe-muted mt-0.5 line-clamp-1">{{ $menu->description }}</p>

                                @if($menu->variants->count() > 1)
                                    <select data-variant-for="{{ $menu->id }}" class="mt-2 w-full text-[10px] bg-[#E3DEA4]/50 text-cafe-text px-2 py-1.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                                        @foreach($menu->variants as $variant)
                                            <option value="{{ $variant->id }}" data-variant-price="{{ (float)$variant->price }}">{{ $variant->name }} — {{ number_format($variant->price, 0, ',', '.') }}</option>
                                        @endforeach
                                    </select>
                                @endif

                                <div class="mt-2 flex items-center justify-between gap-2">
                                    <p class="font-serif font-bold text-cafe-dark text-sm">
                                        Rp {{ number_format($menu->variants->count() ? (float)$menu->variants->first()->price : (float)$menu->price, 0, ',', '.') }}
                                    </p>
                                    <button type="button" onclick="addToCart({{ $menu->id }}, '{{ addslashes($menu->name) }}', {{ $menu->variants->count() ? (float)$menu->variants->first()->price : (float)$menu->price }}, {{ $menu->stock ?? 'null' }})"
                                        class="qty-btn w-8 h-8 rounded-full bg-cafe-dark text-white flex items-center justify-center hover:bg-cafe-gold hover:text-cafe-dark transition-colors {{ ! $menu->is_available ? 'pointer-events-none opacity-40' : '' }}">
                                        <i data-lucide="plus" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="col-span-full text-xs text-cafe-muted py-4">Tidak ada menu pada kategori ini.</p>
                    @endforelse
                </div>
            </section>
        @endforeach
    </div>

    <!-- Panel Keranjang -->
    <div class="xl:sticky xl:top-24 h-fit">
        <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft-lg overflow-hidden">
            <div class="px-5 py-4 border-b border-cafe-beige/50 flex items-center justify-between bg-cafe-cream/50">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-2xl bg-cafe-gold/15 text-cafe-gold flex items-center justify-center"><i data-lucide="shopping-bag" class="w-4 h-4"></i></div>
                    <div>
                        <h3 class="font-serif font-bold text-sm text-cafe-dark">Pesanan</h3>
                        <p id="cart-count-label" class="text-[10px] text-cafe-muted">0 item</p>
                    </div>
                </div>
                <button type="button" onclick="clearCart()" class="text-[11px] text-cafe-muted hover:text-cafe-danger font-semibold flex items-center gap-1">
                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Kosongkan
                </button>
            </div>

            <form id="pos-form" action="{{ route('kasir.pos.store') }}" method="POST" class="p-5 space-y-5">
                @csrf
                <input type="hidden" name="cart" id="cart-input" value="[]">
                <input type="hidden" name="voucher" id="voucher-input" value="">
                <input type="hidden" name="payment_method" id="payment-method-input" value="tunai">
                <input type="hidden" name="cash_received" id="cash-received-input" value="">
                <input type="hidden" name="reference" id="reference-input" value="">
                <input type="hidden" name="action" id="action-input" value="save">

                <!-- Daftar item keranjang -->
                <div id="cart-items" class="max-h-72 overflow-y-auto pr-1 divide-y divide-cafe-beige/30 space-y-0">
                    <div class="py-8 text-center" id="cart-empty">
                        <div class="w-12 h-12 mx-auto rounded-full bg-cafe-cream flex items-center justify-center text-cafe-muted">
                            <i data-lucide="clipboard-list" class="w-5 h-5"></i>
                        </div>
                        <p class="text-[11px] text-cafe-muted mt-2">Klik menu di katalog untuk menambahkan item.</p>
                    </div>
                </div>

                <!-- Tipe pesanan & meja -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-semibold text-cafe-muted uppercase tracking-wide mb-1.5">Tipe Pesanan</label>
                        <select id="order-type" name="order_type" class="w-full bg-[#E3DEA4]/50 text-xs text-cafe-text px-3 py-2.5 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                            <option value="dine_in">Dine In</option>
                            <option value="take_away">Take Away</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-cafe-muted uppercase tracking-wide mb-1.5">Nomor Meja</label>
                        <select id="table-number" name="table_number" class="w-full bg-[#E3DEA4]/50 text-xs text-cafe-text px-3 py-2.5 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none disabled:opacity-40">
                            <option value="">— Pilih Meja —</option>
                            @foreach($tables as $table)
                                <option value="{{ $table->number }}" {{ $table->status === 'nonaktif' ? 'disabled' : '' }}>
                                    Meja {{ $table->number }} ({{ $table->statusBadge()['label'] }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-semibold text-cafe-muted uppercase tracking-wide mb-1.5">Nama Pelanggan (opsional)</label>
                    <input type="text" id="customer-name" name="customer_name" value="{{ $resume?->customer_name ?? '' }}" placeholder="Nama pelanggan"
                        class="w-full bg-[#E3DEA4]/50 focus:bg-white text-xs text-cafe-text placeholder:text-cafe-muted/60 px-3 py-2.5 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none focus:ring-2 focus:ring-cafe-gold/20">
                </div>

                <!-- Voucher -->
                <div>
                    <label class="block text-[10px] font-semibold text-cafe-muted uppercase tracking-wide mb-1.5">Kode Voucher</label>
                    <input type="text" id="voucher-code" placeholder="Masukkan kode (mis. RAMADHAN)" class="w-full bg-[#E3DEA4]/50 focus:bg-white text-xs text-cafe-text placeholder:text-cafe-muted/60 px-3 py-2.5 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none focus:ring-2 focus:ring-cafe-gold/20">
                    @if($promos->isNotEmpty())
                        <div class="flex flex-wrap gap-1.5 mt-2">
                            @foreach($promos as $promo)
                                @if($promo->code)
                                    <button type="button" onclick="document.getElementById('voucher-code').value='{{ $promo->code }}'" class="px-2.5 py-1 rounded-full bg-cafe-gold/15 text-cafe-gold text-[10px] font-bold border border-cafe-gold/30 hover:bg-cafe-gold/30 transition-colors">{{ $promo->code }}</button>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Ringkasan -->
                <div class="rounded-2xl bg-cafe-cream border border-cafe-beige/60 p-4 space-y-2">
                    <div class="flex justify-between text-[11px] text-cafe-muted"><span>Subtotal</span><span id="sum-subtotal">Rp 0</span></div>
                    <div class="flex justify-between text-[11px] text-cafe-muted"><span>Diskon</span><span id="sum-discount" class="text-cafe-danger">-Rp 0</span></div>
                    <div class="flex justify-between text-[11px] text-cafe-muted"><span>Pajak</span><span id="sum-tax">Rp 0</span></div>
                    <div class="flex justify-between text-[11px] text-cafe-muted"><span>Service Charge</span><span id="sum-service">Rp 0</span></div>
                    <div class="flex justify-between border-t border-cafe-beige/60 pt-2">
                        <span class="text-xs font-bold text-cafe-dark">Total</span>
                        <span id="sum-total" class="font-serif font-bold text-cafe-dark text-lg">Rp 0</span>
                    </div>
                </div>

                <!-- Tombol aksi -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <button type="submit" onclick="setAction('save', event)" class="inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-full bg-cafe-beige text-cafe-brown text-xs font-bold hover:bg-cafe-gold/40 transition-colors">
                        <i data-lucide="bookmark" class="w-4 h-4"></i>
                        Simpan Pesanan
                    </button>
                    <button type="submit" onclick="setAction('pay', event)" class="inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-full bg-cafe-dark text-white text-xs font-bold shadow-md hover:bg-cafe-brown transition-colors">
                        <i data-lucide="credit-card" class="w-4 h-4"></i>
                        Bayar Sekarang
                    </button>
                </div>
            </form>
        </div>

        <!-- Pesanan Ditahan -->
        @if($heldOrders->isNotEmpty())
            <div class="mt-4 bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft overflow-hidden">
                <div class="px-5 py-3.5 border-b border-cafe-beige/50">
                    <h4 class="font-serif font-bold text-xs text-cafe-dark">Pesanan Ditahan ({{ $heldOrders->count() }})</h4>
                </div>
                <div class="p-4 space-y-2">
                    @foreach($heldOrders as $held)
                        <div class="flex items-center justify-between gap-2 p-3 rounded-2xl bg-cafe-cream border border-cafe-beige/50">
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-cafe-dark">{{ $held->order_code }}</p>
                                <p class="text-[10px] text-cafe-muted truncate">{{ $held->customer_name }} &bull; {{ $held->orderTypeLabel() }} {{ $held->table_number ? 'Meja '.$held->table_number : '' }}</p>
                                <p class="text-[10px] text-cafe-gold font-semibold mt-0.5">Rp {{ number_format($held->total, 0, ',', '.') }}</p>
                            </div>
                            <div class="flex gap-1.5 shrink-0">
                                <a href="{{ route('kasir.pos.index', ['resume' => $held->id]) }}" class="px-3 py-1.5 rounded-full bg-cafe-info text-white text-[10px] font-semibold hover:bg-cafe-info/90 transition-colors">Lanjutkan</a>
                                <a href="{{ route('kasir.payments.checkout', $held) }}" class="px-3 py-1.5 rounded-full bg-cafe-gold text-cafe-dark text-[10px] font-semibold hover:bg-[#B87C3C] transition-colors">Bayar</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modal Pembayaran -->
<div id="payment-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-[#290024]/60 backdrop-blur-sm" onclick="closePaymentModal()"></div>
    <div class="relative bg-cafe-card rounded-3xl shadow-soft-lg w-full max-w-md p-6 border border-cafe-beige/60 animate-in fade-in">
        <div class="flex items-center justify-between pb-3 border-b border-cafe-beige/50">
            <h3 class="font-serif font-bold text-sm text-cafe-dark">Pembayaran</h3>
            <button onclick="closePaymentModal()" class="p-1.5 rounded-full bg-cafe-cream text-cafe-muted hover:text-cafe-dark"><i data-lucide="x" class="w-4 h-4"></i></button>
        </div>

        <div class="py-4 space-y-4">
            <div class="text-center">
                <p class="text-[11px] text-cafe-muted">Total Tagihan</p>
                <p id="modal-total" class="font-serif text-2xl font-bold text-cafe-dark mt-1">Rp 0</p>
            </div>

            <div>
                <label class="block text-[10px] font-semibold text-cafe-muted uppercase tracking-wide mb-1.5">Metode Pembayaran</label>
                <div class="grid grid-cols-2 gap-2" id="method-buttons">
                    @foreach($paymentMethods as $method)
                        <button type="button" data-method="{{ $method->code }}" onclick="selectMethod('{{ $method->code }}', this)"
                            class="method-btn px-3 py-3 rounded-2xl border text-[11px] font-semibold transition-all flex flex-col items-center gap-1.5 {{ $method->code === 'tunai' ? 'bg-cafe-dark text-white border-cafe-dark' : 'bg-cafe-beige/30 text-cafe-brown border-cafe-beige hover:bg-cafe-beige' }}">
                            <i data-lucide="{{ $method->icon ?: 'circle' }}" class="w-4 h-4"></i>
                            {{ $method->name }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div id="cash-field" class="space-y-2">
                <label class="block text-[10px] font-semibold text-cafe-muted uppercase tracking-wide">Uang Diterima (Rp)</label>
                <div class="flex flex-wrap gap-1.5">
                    <input type="number" id="modal-cash" value="" step="1000" min="0" placeholder="0"
                        class="w-full bg-[#E3DEA4]/50 focus:bg-white text-xs text-cafe-text placeholder:text-cafe-muted/60 px-3 py-3 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none focus:ring-2 focus:ring-cafe-gold/20">
                    <button type="button" data-cash="0" onclick="setCash(0)" class="px-3 py-2 rounded-full bg-cafe-beige/60 text-cafe-brown text-[10px] font-bold">Exact</button>
                    <button type="button" data-cash="20000" onclick="setCash(20000)" class="px-3 py-2 rounded-full bg-cafe-beige/60 text-cafe-brown text-[10px] font-bold">20rb</button>
                    <button type="button" data-cash="50000" onclick="setCash(50000)" class="px-3 py-2 rounded-full bg-cafe-beige/60 text-cafe-brown text-[10px] font-bold">50rb</button>
                    <button type="button" data-cash="100000" onclick="setCash(100000)" class="px-3 py-2 rounded-full bg-cafe-beige/60 text-cafe-brown text-[10px] font-bold">100rb</button>
                    <button type="button" data-cash="150000" onclick="setCash(150000)" class="px-3 py-2 rounded-full bg-cafe-beige/60 text-cafe-brown text-[10px] font-bold">150rb</button>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-cafe-muted">Kembalian</span>
                    <span id="modal-change" class="font-bold text-cafe-success">Rp 0</span>
                </div>
                @if(in_array('transfer', $paymentMethods->pluck('code')->all()) || in_array('ewallet', $paymentMethods->pluck('code')->all()))
                    <div>
                        <label class="block text-[10px] font-semibold text-cafe-muted uppercase tracking-wide mb-1">Referensi / No. Invoice (opsional)</label>
                        <input type="text" id="modal-reference" placeholder="Nomor referensi / invoice"
                            class="w-full bg-[#E3DEA4]/50 focus:bg-white text-xs text-cafe-text placeholder:text-cafe-muted/60 px-3 py-2.5 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                    </div>
                @endif
            </div>
        </div>

        <button type="button" onclick="confirmPayment()" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-full bg-cafe-success text-white text-xs font-bold shadow-md hover:bg-cafe-success/90 transition-colors">
            <i data-lucide="check-circle" class="w-4 h-4"></i>
            Konfirmasi Pembayaran
        </button>
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

const TAX_PERCENT = parseFloat('{{ \App\Models\CafeSetting::get('tax_percent', 10) }}') || 0;
const SERVICE_ACTIVE = {{ \App\Models\CafeSetting::get('service_charge_active', 0) ? 'true' : 'false' }};
const SERVICE_PERCENT = SERVICE_ACTIVE ? (parseFloat('{{ \App\Models\CafeSetting::get('service_charge_percent', 5) }}') || 0) : 0;

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
                    return;
                }
                document.getElementById('sum-subtotal').textContent = formatRupiah(d.subtotal);
                document.getElementById('sum-discount').textContent = '-Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(d.discount));
                document.getElementById('sum-tax').textContent = formatRupiah(d.tax);
                document.getElementById('sum-service').textContent = formatRupiah(d.service);
                document.getElementById('sum-total').textContent = formatRupiah(d.total);
                document.getElementById('modal-total').textContent = formatRupiah(d.total);
                document.getElementById('voucher-input').value = document.getElementById('voucher-code').value.trim();
                const total = d.total;
                const cash = parseFloat(document.getElementById('modal-cash').value) || 0;
                document.getElementById('modal-change').textContent = formatRupiah(Math.max(0, cash - total));
            })
            .catch(() => {});
    }, 350);
}

function renderCart() {
    const wrap = document.getElementById('cart-items');
    const empty = document.getElementById('cart-empty');

    if (cart.length === 0) {
        wrap.innerHTML = '';
        wrap.appendChild(empty);
        empty.classList.remove('hidden');
        document.getElementById('cart-count-label').textContent = '0 item';
    } else {
        empty.classList.add('hidden');
        wrap.innerHTML = cart.map((item, idx) => `
            <div class="py-2.5 flex items-start gap-3 group">
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-cafe-dark truncate">${item.name}</p>
                    <p class="text-[10px] text-cafe-muted">Rp ${formatRupiah(item.price).replace('Rp ', '')} &times; ${item.qty}</p>
                    <button type="button" onclick="setNotes(${idx})" class="text-[9px] text-cafe-info hover:underline font-semibold">${item.notes ? '✏️ '+item.notes : '+ catatan'}</button>
                </div>
                <div class="flex items-center gap-1.5 shrink-0">
                    <button type="button" onclick="changeQty(${idx}, -1)" class="qty-btn w-6 h-6 rounded-full bg-cafe-beige/60 text-cafe-brown flex items-center justify-center"><i data-lucide="minus" class="w-3 h-3"></i></button>
                    <span class="w-6 text-center text-xs font-bold text-cafe-dark">${item.qty}</span>
                    <button type="button" onclick="changeQty(${idx}, 1)" class="qty-btn w-6 h-6 rounded-full bg-cafe-dark text-white flex items-center justify-center"><i data-lucide="plus" class="w-3 h-3"></i></button>
                    <button type="button" onclick="removeItem(${idx})" class="w-6 h-6 rounded-full text-cafe-muted hover:text-cafe-danger flex items-center justify-center"><i data-lucide="x" class="w-3 h-3"></i></button>
                </div>
            </div>
        `).join('');
        document.getElementById('cart-count-label').textContent = cart.length + ' item';
        lucide.createIcons();
    }

    refreshTotals();
}

// ---------- Cart ops ----------
function addToCart(menuId, name, price, stock) {
    if (stock !== null && cart.filter(i => i.menu_id === menuId).reduce((s, i) => s + i.qty, 0) >= stock) {
        showToast('Stok Terbatas', 'Stok "📦 '+name+'" hanya tersisa '+stock, 'error');
        return;
    }
    const existing = cart.find(i => i.menu_id === menuId);
    if (existing) { existing.qty += 1; } else { cart.push({ menu_id: menuId, name, price, qty: 1, notes: '' }); }
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
    if (confirm('Kosongkan keranjang?')) { cart = []; renderCart(); }
}

// ---------- Voucher ----------
document.getElementById('voucher-code').addEventListener('input', refreshTotals);

function setAction(act, event) {
    if (cart.length === 0) { event.preventDefault(); showToast('Keranjang Kosong', 'Tambahkan minimal 1 item terlebih dahulu.', 'error'); return; }
    if (act === 'pay') {
        event.preventDefault();
        document.getElementById('action-input').value = 'pay';
        openPaymentModal();
    } else {
        document.getElementById('action-input').value = 'save';
        document.getElementById('cart-input').value = JSON.stringify(cart);
    }
}

// ---------- Kategori tab ----------
document.querySelectorAll('.cat-tab').forEach(btn => {
    btn.addEventListener('click', function () {
        const cat = this.dataset.cat;
        document.querySelectorAll('.cat-tab').forEach(b => b.className = 'cat-tab px-4 py-2 rounded-full text-[11px] font-semibold bg-cafe-beige/50 text-cafe-brown hover:bg-cafe-dark hover:text-white transition-colors');
        if (cat === 'all') { this.className = 'cat-tab px-4 py-2 rounded-full text-[11px] font-semibold bg-cafe-dark text-white transition-colors'; }
        else { this.className = 'cat-tab px-4 py-2 rounded-full text-[11px] font-semibold bg-cafe-dark text-white transition-colors'; }
        document.querySelectorAll('.menu-cat').forEach(sec => sec.classList.toggle('hidden', cat !== 'all' && sec.dataset.catSection !== cat));
    });
});

document.getElementById('order-type').addEventListener('change', function () {
    document.getElementById('table-number').disabled = this.value !== 'dine_in';
    if (this.value !== 'dine_in') document.getElementById('table-number').value = '';
});

// ---------- Modal Pembayaran ----------
function openPaymentModal() {
    document.getElementById('payment-method-input').value = selectedMethod;
    document.getElementById('cash-received-input').value = '';
    document.getElementById('reference-input').value = document.getElementById('modal-reference').value || '';
    const total = parseFloat(document.getElementById('modal-total').textContent.replace(/[^\d-]/g, ''));
    document.getElementById('modal-cash').value = Math.ceil(total / 1000) * 1000;
    document.getElementById('payment-modal').classList.remove('hidden');
    updateCashFields(total);
}

function closePaymentModal() {
    document.getElementById('payment-modal').classList.add('hidden');
}

function selectMethod(code, btn) {
    selectedMethod = code;
    document.getElementById('payment-method-input').value = code;
    document.querySelectorAll('.method-btn').forEach(b => b.className = 'method-btn px-3 py-3 rounded-2xl border text-[11px] font-semibold transition-all flex flex-col items-center gap-1.5 bg-cafe-beige/30 text-cafe-brown border-cafe-beige hover:bg-cafe-beige');
    btn.className = 'method-btn px-3 py-3 rounded-2xl border text-[11px] font-semibold transition-all flex flex-col items-center gap-1.5 bg-cafe-dark text-white border-cafe-dark';
    if (code !== 'tunai') {
        document.getElementById('modal-cash').value = '';
        document.getElementById('cash-received-input').value = '';
        document.getElementById('modal-change').textContent = 'Rp 0';
    } else {
        openPaymentModal();
    }
    lucide.createIcons();
}

function setCash(val) {
    document.getElementById('modal-cash').value = val;
    const total = parseFloat(document.getElementById('modal-total').textContent.replace(/[^\d-]/g, ''));
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
    const total = parseFloat(document.getElementById('modal-total').textContent.replace(/[^\d-]/g, ''));
    updateCashFields(total);
});

function confirmPayment() {
    document.getElementById('action-input').value = 'pay';
    document.getElementById('cart-input').value = JSON.stringify(cart);
    document.getElementById('reference-input').value = document.getElementById('modal-reference').value || '';
    document.getElementById('pos-form').submit();
}

// Init
if (document.getElementById('order-type').value !== 'dine_in') {
    document.getElementById('table-number').disabled = true;
}
@if($resume?->order_type === 'take_away')
    document.getElementById('order-type').value = 'take_away';
    document.getElementById('table-number').disabled = true;
@elseif($resume?->table_number)
    document.getElementById('table-number').value = '{{ $resume->table_number }}';
@endif
renderCart();
</script>
@endsection