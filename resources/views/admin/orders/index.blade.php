@extends('admin.layouts.app')

@section('title', 'Manajemen Pesanan')
@section('page_title', 'Pesanan')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <x-breadcrumb
        :items="[
            ['label' => 'Admin', 'url' => route('admin.dashboard'), 'icon' => 'home'],
            ['label' => 'Pesanan'],
        ]"
        class="mb-3"
    />

    <!-- Page Header & Export -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-serif text-2xl font-bold text-cafe-dark">Daftar Pesanan</h2>
            <p class="text-xs text-cafe-muted mt-0.5">Kelola pesanan kafe, verifikasi kasir, dan pantau status hidangan</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.orders.export') }}" class="px-4 py-2.5 rounded-full bg-cafe-card hover:bg-cafe-beige text-cafe-dark font-semibold text-xs border border-cafe-beige/80 transition-all flex items-center gap-2 shadow-xs">
                <i data-lucide="download" class="w-4 h-4 text-cafe-muted"></i>
                <span>Ekspor CSV</span>
            </a>
        </div>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="bg-cafe-card p-4 sm:p-5 rounded-2xl shadow-soft border border-cafe-beige/40">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <!-- Search Input -->
            <div class="relative">
                <i data-lucide="search" class="w-4 h-4 text-cafe-muted absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode (#AB...) atau nama..." class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs text-cafe-text placeholder:text-cafe-muted/70 pl-9 pr-3.5 py-2.5 rounded-full border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all">
            </div>

            <!-- Filter Status Bayar -->
            <div>
                <select name="payment_status" onchange="this.form.submit()" class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs text-cafe-text py-2.5 px-3.5 rounded-full border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all cursor-pointer">
                    <option value="all">Semua Status Bayar</option>
                    <option value="menunggu_pembayaran" {{ request('payment_status') == 'menunggu_pembayaran' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                    <option value="lunas" {{ request('payment_status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                    <option value="dibatalkan" {{ request('payment_status') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>

            <!-- Filter Status Pesanan -->
            <div>
                <select name="order_status" onchange="this.form.submit()" class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs text-cafe-text py-2.5 px-3.5 rounded-full border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all cursor-pointer">
                    <option value="all">Semua Status Pesanan</option>
                    <option value="diproses" {{ request('order_status') == 'diproses' ? 'selected' : '' }}>Diproses</option>
                    <option value="siap" {{ request('order_status') == 'siap' ? 'selected' : '' }}>Siap</option>
                    <option value="selesai" {{ request('order_status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>

            <!-- Filter Tipe Pesanan -->
            <div>
                <select name="order_type" onchange="this.form.submit()" class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs text-cafe-text py-2.5 px-3.5 rounded-full border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all cursor-pointer">
                    <option value="all">Semua Tipe (Dine-in / Take-away)</option>
                    <option value="dine_in" {{ request('order_type') == 'dine_in' ? 'selected' : '' }}>Dine-in (Makan di Tempat)</option>
                    <option value="take_away" {{ request('order_type') == 'take_away' ? 'selected' : '' }}>Take-away (Bawa Pulang)</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Orders Table Container -->
    <div class="bg-cafe-card rounded-2xl shadow-soft border border-cafe-beige/40 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#F2F5E2]/70 border-b border-cafe-beige text-cafe-muted uppercase font-semibold text-[11px] tracking-wider">
                    <tr>
                        <th class="py-3.5 pl-6">Kode Order</th>
                        <th class="py-3.5 px-3">Tanggal &amp; Jam</th>
                        <th class="py-3.5 px-3">Nama Customer</th>
                        <th class="py-3.5 px-3">Meja / Tipe</th>
                        <th class="py-3.5 px-3">Total</th>
                        <th class="py-3.5 px-3">Metode Bayar</th>
                        <th class="py-3.5 px-3">Pembayaran</th>
                        <th class="py-3.5 px-3">Status Pesanan</th>
                        <th class="py-3.5 pr-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-cafe-beige/30">
                    @forelse($orders as $idx => $order)
                        <tr class="hover:bg-cafe-cream/80 transition-colors {{ $idx % 2 === 1 ? 'bg-[#F2F5E2]/40' : '' }}">
                            <td class="py-4 pl-6 font-mono font-bold text-cafe-dark">{{ $order->order_code }}</td>
                            <td class="py-4 px-3 text-cafe-muted">{{ $order->created_at->format('d M Y, H:i') }} WIB</td>
                            <td class="py-4 px-3 font-semibold text-cafe-text">{{ $order->customer_name }}</td>
                            <td class="py-4 px-3">
                                <span class="font-medium text-cafe-dark">
                                    {{ $order->order_type === 'dine_in' ? 'Meja ' . $order->table_number : 'Take-away' }}
                                </span>
                            </td>
                            <td class="py-4 px-3 font-bold text-cafe-dark">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                            <td class="py-4 px-3 uppercase font-medium text-cafe-muted">{{ $order->payment_method }}</td>
                            <td class="py-4 px-3">
                                @if($order->payment_status === 'lunas')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-olive/90 text-beige border border-olive">
                                        <i data-lucide="check" class="w-3.5 h-3.5"></i> Lunas
                                    </span>
                                @elseif($order->payment_status === 'menunggu_pembayaran')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-custard text-olive border border-olive/40">
                                        <i data-lucide="clock" class="w-3.5 h-3.5"></i> Menunggu
                                    </span>
                                @elseif($order->payment_status === 'dibatalkan')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-custard/60 text-olive border border-olive/40 line-through">
                                        <i data-lucide="x" class="w-3.5 h-3.5"></i> Dibatalkan
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-custard text-olive border border-olive/40">
                                        <span class="w-1.5 h-1.5 rounded-full bg-olive"></span> {{ str_replace('_',' ', $order->payment_status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-3">
                                @if($order->order_status === 'baru')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-midnight text-beige border border-midnight">
                                        <i data-lucide="sparkles" class="w-3.5 h-3.5"></i> Baru
                                    </span>
                                @elseif($order->order_status === 'diproses')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-apricot/90 text-midnight border border-apricot">Diproses</span>
                                @elseif($order->order_status === 'siap' || $order->order_status === 'disajikan')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-custard text-olive border border-olive/60">
                                        <i data-lucide="check" class="w-3.5 h-3.5"></i> Siap
                                    </span>
                                @elseif($order->order_status === 'selesai')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-olive/90 text-beige border border-olive">
                                        <i data-lucide="check" class="w-3.5 h-3.5"></i> Selesai
                                    </span>
                                @elseif($order->order_status === 'dibatalkan')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-custard/60 text-olive border border-olive/40 line-through">
                                        <i data-lucide="x" class="w-3.5 h-3.5"></i> Dibatalkan
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-custard text-olive border border-olive/40">{{ ucfirst($order->order_status) }}</span>
                                @endif
                            </td>
                            <td class="py-4 pr-6 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button onclick="openOrderDetail({{ $order->id }})" class="p-1.5 rounded-full bg-cafe-beige/50 hover:bg-cafe-beige text-cafe-dark transition-colors" title="Lihat Detail Pesanan">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </button>
                                    <button onclick="printOrderReceipt({{ $order->id }})" class="p-1.5 rounded-full bg-cafe-beige/50 hover:bg-cafe-beige text-cafe-dark transition-colors" title="Cetak Nota">
                                        <i data-lucide="printer" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-8 text-center text-xs text-cafe-muted">
                                Tidak ada data pesanan yang sesuai dengan filter atau kata kunci pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-4 sm:p-5 border-t border-cafe-beige/50">
            {{ $orders->links() }}
        </div>
    </div>
</div>

<!-- MODAL DETAIL PESANAN (Pure JS & Tailwind Modal) -->
<div id="order-detail-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 overflow-y-auto hidden">
    <div class="fixed inset-0 bg-midnight/40 backdrop-blur-sm" onclick="closeOrderDetail()"></div>

    <div class="relative w-full max-w-2xl bg-cafe-card rounded-2xl shadow-soft-lg border border-cafe-beige/60 overflow-hidden z-10 my-8">
        <div class="flex items-center justify-between px-6 py-5 border-b border-cafe-beige/50 bg-[#F2F5E2]/40">
            <div>
                <h3 class="font-serif text-xl font-bold text-cafe-dark" id="modal-order-code">Detail Pesanan</h3>
                <p class="text-xs text-cafe-muted mt-0.5" id="modal-order-date">-</p>
            </div>
            <button onclick="closeOrderDetail()" class="w-8 h-8 rounded-full bg-cafe-beige/60 hover:bg-cafe-beige text-cafe-muted hover:text-cafe-dark flex items-center justify-center transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <div class="p-6 max-h-[75vh] overflow-y-auto space-y-6 text-xs text-cafe-text" id="modal-order-content">
            <!-- Dynamic Content loaded via JS -->
            <div class="py-8 text-center text-cafe-muted">Memuat data pesanan...</div>
        </div>
    </div>
</div>

<!-- MODAL BATALKAN PESANAN -->
<div id="cancel-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 overflow-y-auto hidden">
    <div class="fixed inset-0 bg-midnight/40 backdrop-blur-sm" onclick="closeCancelModal()"></div>

    <div class="relative w-full max-w-md bg-cafe-card rounded-2xl shadow-soft-lg border border-cafe-beige/60 overflow-hidden z-10 my-8 p-6 space-y-4">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-2xl bg-cafe-danger/10 text-cafe-danger flex items-center justify-center shrink-0">
                <i data-lucide="alert-triangle" class="w-5 h-5"></i>
            </div>
            <div>
                <h4 class="font-serif text-lg font-bold text-cafe-dark">Batalkan Pesanan</h4>
                <p class="text-xs text-cafe-muted mt-0.5" id="cancel-modal-title">Konfirmasi pembatalan pesanan</p>
            </div>
        </div>

        <form id="cancel-form" method="POST" action="">
            @csrf
            <div class="space-y-1.5 my-3">
                <label class="block text-xs font-semibold text-cafe-dark">Alasan Pembatalan (Wajib diisi):</label>
                <textarea name="cancel_reason" required rows="3" placeholder="Tulis alasan jelas pembatalan pesanan..." class="w-full bg-[#E3DEA4]/50 focus:bg-beige text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-cafe-beige/60">
                <button type="button" onclick="closeCancelModal()" class="px-4 py-2 rounded-full text-xs font-semibold text-cafe-dark hover:bg-cafe-beige/60">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-full bg-cafe-danger hover:bg-cafe-danger/90 text-beige font-semibold text-xs shadow-sm">Konfirmasi Pembatalan</button>
            </div>
        </form>
    </div>
</div>

<!-- PRINTABLE RECEIPT TEMPLATE -->
<div id="printable-receipt" class="hidden font-mono text-xs leading-tight">
    <div class="text-center pb-2 border-b border-midnight">
        @if(\App\Models\CafeSetting::get('receipt_show_logo', '1') == '1')
            <x-logo :show-text="false" size="md" thermal loading="eager" class="mx-auto mb-2" />
        @endif
        <h2 class="font-bold text-sm">{{ $cafeSettings['name'] }}</h2>
        <p class="text-[10px]">{{ $cafeSettings['tagline'] }}</p>
        <p class="text-[10px] mt-1 whitespace-pre-line">{{ $cafeSettings['header'] }}</p>
    </div>

    <div class="py-2 border-b border-midnight space-y-0.5 text-[10px]" id="receipt-meta">
        <!-- receipt details injected via js -->
    </div>

    <div class="py-2 border-b border-midnight space-y-1" id="receipt-items">
        <!-- items injected via js -->
    </div>

    <div class="py-2 border-b border-midnight space-y-0.5 text-[11px]" id="receipt-totals">
        <!-- totals injected via js -->
    </div>

    <div class="text-center pt-3 text-[10px] whitespace-pre-line">
        <p>{{ $cafeSettings['footer'] }}</p>
    </div>
</div>

<script>
    let currentOrderData = null;

    function openOrderDetail(orderId) {
        const modal = document.getElementById('order-detail-modal');
        const content = document.getElementById('modal-order-content');
        modal.classList.remove('hidden');

        fetch(`/admin/pesanan/${orderId}`)
            .then(res => res.json())
            .then(order => {
                currentOrderData = order;
                document.getElementById('modal-order-code').innerText = `Detail Pesanan ${order.order_code}`;
                document.getElementById('modal-order-date').innerText = `Dibuat pada ${new Date(order.created_at).toLocaleString('id-ID')}`;

                let itemsHtml = order.items.map(item => `
                    <tr>
                        <td class="py-2.5 px-3">
                            <p class="font-semibold text-cafe-dark">${item.name}</p>
                            ${item.notes ? `<p class="text-[11px] text-cafe-muted italic">Catatan: "${item.notes}"</p>` : ''}
                        </td>
                        <td class="py-2.5 px-3">Rp ${Number(item.price).toLocaleString('id-ID')}</td>
                        <td class="py-2.5 px-3 font-semibold">${item.quantity}x</td>
                        <td class="py-2.5 px-3 text-right font-bold text-cafe-dark">Rp ${Number(item.subtotal).toLocaleString('id-ID')}</td>
                    </tr>
                `).join('');

                let timelinesHtml = order.timelines.map(t => `
                    <div class="relative pl-4">
                        <div class="absolute -left-[19px] top-1 w-2.5 h-2.5 rounded-full bg-cafe-gold border border-beige"></div>
                        <div class="flex items-baseline justify-between">
                            <span class="font-semibold text-cafe-dark">${t.status}</span>
                            <span class="text-[10px] text-cafe-muted">${new Date(t.logged_at).toLocaleString('id-ID')}</span>
                        </div>
                        <p class="text-cafe-muted text-[11px] mt-0.5">${t.description} ${t.actor ? `(${t.actor})` : ''}</p>
                    </div>
                `).join('');

                content.innerHTML = `
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-4 rounded-2xl bg-cafe-cream/80 border border-cafe-beige/80">
                        <div>
                            <span class="text-[10px] text-cafe-muted uppercase font-semibold">Customer</span>
                            <p class="font-bold text-cafe-dark text-sm mt-0.5">${order.customer_name}</p>
                        </div>
                        <div>
                            <span class="text-[10px] text-cafe-muted uppercase font-semibold">Tipe</span>
                            <p class="font-bold text-cafe-dark text-sm mt-0.5">${order.order_type === 'dine_in' ? 'Meja ' + (order.table_number || '-') : 'Take-away'}</p>
                        </div>
                        <div>
                            <span class="text-[10px] text-cafe-muted uppercase font-semibold">Pembayaran</span>
                            <p class="font-bold text-cafe-dark text-xs mt-0.5 capitalize">${order.payment_status.replace('_', ' ')} (${order.payment_method.toUpperCase()})</p>
                        </div>
                        <div>
                            <span class="text-[10px] text-cafe-muted uppercase font-semibold">Status Pesanan</span>
                            <p class="font-bold text-cafe-dark text-xs mt-0.5 capitalize">${order.order_status}</p>
                        </div>
                    </div>

                    <div>
                        <h4 class="font-serif font-bold text-sm text-cafe-dark mb-2">Item Menu Dipesan</h4>
                        <div class="border border-cafe-beige/60 rounded-2xl overflow-hidden">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-[#F2F5E2]/70 border-b border-cafe-beige text-cafe-muted">
                                    <tr>
                                        <th class="py-2 px-3">Menu</th>
                                        <th class="py-2 px-3">Harga</th>
                                        <th class="py-2 px-3">Qty</th>
                                        <th class="py-2 px-3 text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-cafe-beige/30">
                                    ${itemsHtml}
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-cafe-beige/30 border border-cafe-beige/60 space-y-1.5">
                        <div class="flex justify-between text-xs">
                            <span class="text-cafe-muted">Subtotal:</span>
                            <span class="font-semibold text-cafe-dark">Rp ${Number(order.subtotal).toLocaleString('id-ID')}</span>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="text-cafe-muted">Pajak (10%):</span>
                            <span>Rp ${Number(order.tax).toLocaleString('id-ID')}</span>
                        </div>
                        ${order.discount > 0 ? `
                            <div class="flex justify-between text-xs text-cafe-success">
                                <span>Diskon:</span>
                                <span>-Rp ${Number(order.discount).toLocaleString('id-ID')}</span>
                            </div>
                        ` : ''}
                        <div class="flex justify-between text-sm font-bold pt-2 border-t border-cafe-beige text-cafe-dark">
                            <span>Total Pembayaran:</span>
                            <span>Rp ${Number(order.total).toLocaleString('id-ID')}</span>
                        </div>
                    </div>

                    ${order.cashier_name ? `
                        <div class="p-3 rounded-xl bg-cafe-beige/40 text-xs text-cafe-muted">
                            Diverifikasi oleh kasir: <strong class="text-cafe-dark">${order.cashier_name}</strong>
                            ${order.paid_at ? ` pada ${new Date(order.paid_at).toLocaleString('id-ID')}` : ''}
                        </div>
                    ` : ''}

                    <div>
                        <h4 class="font-serif font-bold text-sm text-cafe-dark mb-2">Riwayat Alur Pesanan (Timeline)</h4>
                        <div class="space-y-3 pl-2 border-l-2 border-cafe-gold/40">
                            ${timelinesHtml}
                        </div>
                    </div>

                    <!-- Admin Status Action Bar -->
                    <div class="pt-4 border-t border-cafe-beige flex flex-wrap items-center justify-between gap-3">
                        <form action="/admin/pesanan/${order.id}/status" method="POST" class="flex items-center gap-2">
                            @csrf
                            <span class="font-semibold text-cafe-dark">Ubah Status:</span>
                            <button name="order_status" value="diproses" class="px-3 py-1.5 rounded-full bg-cafe-beige/60 hover:bg-cafe-beige text-cafe-dark font-medium ${order.order_status === 'diproses' ? 'opacity-50 pointer-events-none' : ''}">Diproses</button>
                            <button name="order_status" value="siap" class="px-3 py-1.5 rounded-full bg-cafe-beige/60 hover:bg-cafe-beige text-cafe-dark font-medium ${order.order_status === 'siap' ? 'opacity-50 pointer-events-none' : ''}">Siap</button>
                            <button name="order_status" value="selesai" class="px-3 py-1.5 rounded-full bg-cafe-beige/60 hover:bg-cafe-beige text-cafe-dark font-medium ${order.order_status === 'selesai' ? 'opacity-50 pointer-events-none' : ''}">Selesai</button>
                        </form>

                        <div class="flex items-center gap-2">
                            ${order.payment_status !== 'dibatalkan' ? `
                                <button onclick="openCancelModal(${order.id}, '${order.order_code}')" class="px-4 py-2 rounded-full bg-cafe-danger/10 hover:bg-cafe-danger/20 text-cafe-danger font-semibold transition-colors">
                                    Batalkan Pesanan
                                </button>
                            ` : ''}
                            <button onclick="printOrderReceipt(${order.id})" class="px-4 py-2 rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream font-semibold transition-colors flex items-center gap-1.5">
                                <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                                <span>Cetak Nota</span>
                            </button>
                        </div>
                    </div>
                `;
                lucide.createIcons();
            });
    }

    function closeOrderDetail() {
        document.getElementById('order-detail-modal').classList.add('hidden');
    }

    function openCancelModal(orderId, orderCode) {
        document.getElementById('cancel-modal-title').innerText = `Membatalkan pesanan ${orderCode}`;
        document.getElementById('cancel-form').action = `/admin/pesanan/${orderId}/cancel`;
        document.getElementById('cancel-modal').classList.remove('hidden');
    }

    function closeCancelModal() {
        document.getElementById('cancel-modal').classList.add('hidden');
    }

    function printOrderReceipt(orderId) {
        fetch(`/admin/pesanan/${orderId}`)
            .then(res => res.json())
            .then(order => {
                document.getElementById('receipt-meta').innerHTML = `
                    <div class="flex justify-between"><span>No: ${order.order_code}</span><span>${order.order_type === 'dine_in' ? 'Meja ' + order.table_number : 'Take-away'}</span></div>
                    <div class="flex justify-between"><span>Tgl: ${new Date(order.created_at).toLocaleString('id-ID')}</span></div>
                    <div>Customer: ${order.customer_name}</div>
                    <div>Kasir: ${order.cashier_name || 'Admin'}</div>
                `;

                document.getElementById('receipt-items').innerHTML = order.items.map(i => `
                    <div class="flex justify-between text-[11px]">
                        <div><span>${i.name}</span><span class="block text-[9px] text-olive">${i.quantity} x Rp ${Number(i.price).toLocaleString('id-ID')}</span></div>
                        <span>Rp ${Number(i.subtotal).toLocaleString('id-ID')}</span>
                    </div>
                `).join('');

                document.getElementById('receipt-totals').innerHTML = `
                    <div class="flex justify-between"><span>Subtotal:</span><span>Rp ${Number(order.subtotal).toLocaleString('id-ID')}</span></div>
                    <div class="flex justify-between"><span>Pajak (10%):</span><span>Rp ${Number(order.tax).toLocaleString('id-ID')}</span></div>
                    ${order.discount > 0 ? `<div class="flex justify-between"><span>Diskon:</span><span>-Rp ${Number(order.discount).toLocaleString('id-ID')}</span></div>` : ''}
                    <div class="flex justify-between font-bold text-xs pt-1 border-t border-dotted border-midnight">
                        <span>TOTAL:</span><span>Rp ${Number(order.total).toLocaleString('id-ID')}</span>
                    </div>
                    <div class="flex justify-between text-[10px] pt-1">
                        <span>Metode: ${order.payment_method.toUpperCase()}</span><span>Status: ${order.payment_status.toUpperCase()}</span>
                    </div>
                `;

                window.print();
            });
    }
</script>
@endsection
