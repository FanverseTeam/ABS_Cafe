@extends('kasir.layouts.app')

@section('title', 'Dashboard Kasir - ABS Cafe')
@section('page_title', 'Dashboard Kasir')

@section('content')
<div class="space-y-6">
<<<<<<< HEAD
    <!-- Kartu Pilih Penjaga Kasir yang Bertugas -->
    <div class="w-full max-w-[900px] mx-auto lg:max-w-none bg-gradient-to-r from-[#290024] via-[#290024] to-[#775533] text-cafe-cream rounded-2xl lg:rounded-3xl p-5 md:p-6 lg:p-8 relative shadow-soft-lg">
        <div class="absolute inset-0 rounded-2xl lg:rounded-3xl overflow-hidden pointer-events-none z-0" aria-hidden="true">
            <div class="absolute inset-0 opacity-15 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=900&auto=format&fit=crop&q=80')"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-[#290024] via-[#290024]/90 to-transparent"></div>
            <div class="absolute -top-10 -right-10 w-44 h-44 rounded-full border border-cafe-gold/20 opacity-40"></div>
        </div>

        <div class="relative z-10">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="max-w-xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-beige/10 text-cafe-gold text-xs font-medium border border-cafe-gold/30 mb-2">
                        <i data-lucide="user-check" class="w-3.5 h-3.5"></i>
                        <span>Penjaga Kasir yang Bertugas</span>
                    </div>
                    <h2 class="font-serif leading-[1.25] break-words text-[#F2F5E2]">
                        <span class="block lg:inline text-xs sm:text-sm lg:text-2xl font-normal lg:font-bold text-cafe-cream/85 lg:text-[#F2F5E2] mb-0.5 lg:mb-0">Siapa yang jaga kasir hari ini?</span>
                    </h2>
                    <p class="text-sm leading-[1.5] text-cafe-beige/85 mt-1 max-w-lg">
                        Pilih nama penjaga kasir yang sedang bertugas. Nama ini akan tampil di struk dan rekap penjualan.
                    </p>
                </div>

                <form action="{{ route('kasir.set_penjaga') }}" method="POST" class="flex items-end gap-3 w-full lg:w-auto">
                    @csrf
                    <div class="flex-1 lg:w-60">
                        <label for="penjaga_kasir_name" class="block text-[11px] text-[#E3DEA4] font-semibold mb-1.5 uppercase tracking-wide">Nama Penjaga</label>
                        @if($penjagaList->isNotEmpty())
                            <select name="penjaga_kasir_name" id="penjaga_kasir_name"
                                    class="w-full h-11 px-4 rounded-full bg-beige/10 border border-cafe-gold/40 text-[#F2F5E2] text-sm font-medium focus:border-cafe-gold focus:ring-1 focus:ring-cafe-gold/50 transition-all appearance-none cursor-pointer"
                                    style="background-image: url('data:image/svg+xml;charset=utf-8,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2224%22 height=%2224%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%23D4954D%22 stroke-width=%222%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22%3E%3Cpath d=%22m6 9 6 6 6-6%22/%3E%3C/svg%3E'); background-position: right 12px center; background-repeat: no-repeat; background-size: 18px; padding-right: 40px;"
                                    onchange="this.form.submit()">
                                <option value="" disabled {{ !$activePenjaga ? 'selected' : '' }} class="text-cafe-dark bg-[#F2F5E2]">-- Pilih Penjaga Kasir --</option>
                                @foreach($penjagaList as $penjaga)
                                    <option value="{{ $penjaga->name }}" {{ $activePenjaga === $penjaga->name ? 'selected' : '' }} class="text-cafe-dark bg-[#F2F5E2]">
                                        {{ $penjaga->name }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <div class="px-3.5 py-2.5 rounded-full bg-cafe-warning/15 border border-cafe-warning/30 text-cafe-warning text-xs font-semibold">
                                Belum ada penjaga dari Admin
                            </div>
                        @endif
                    </div>
                </form>
            </div>

            @if($activePenjaga)
                <div class="mt-4 flex items-center gap-2 px-3 py-2 rounded-xl bg-cafe-gold/15 border border-cafe-gold/30 w-fit">
                    <span class="relative flex h-2.5 w-2.5 shrink-0">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-cafe-gold opacity-60"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-cafe-gold"></span>
                    </span>
                    <span class="text-xs font-semibold text-cafe-gold">Penjaga aktif: {{ $activePenjaga }}</span>
                </div>
            @else
                <div class="mt-4 flex items-center gap-2 px-3 py-2 rounded-xl bg-cafe-warning/15 border border-cafe-warning/30 w-fit">
                    <i data-lucide="alert-triangle" class="w-3.5 h-3.5 text-cafe-warning"></i>
                    <span class="text-xs font-semibold text-cafe-warning">Belum pilih penjaga kasir. Silakan pilih dari dropdown di atas.</span>
                </div>
            @endif
        </div>
    </div>
=======
    {{-- Banner Shift dihilangkan — shift berjalan otomatis tanpa perlu dibuka manual --}}
>>>>>>> 6ac93a624e631e5773e51745fdfc766f062753de

    <!-- Quick Action: Pesan Manual untuk Customer -->
    <div class="bg-gradient-to-r from-[#290024] via-[#290024] to-[#775533] text-[#F2F5E2] rounded-2xl sm:rounded-3xl p-4 sm:p-5 shadow-soft-lg flex flex-col sm:flex-row items-center justify-between gap-3 border border-[#D4954D]/30">
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-[#D4954D] text-[#290024] flex items-center justify-center shrink-0 shadow">
                <i data-lucide="plus-circle" class="w-5 h-5 sm:w-6 sm:h-6"></i>
            </div>
            <div class="min-w-0">
                <div class="flex items-center gap-2">
                    <h3 class="font-serif font-bold text-sm sm:text-base text-[#F2F5E2] truncate">Pesan Manual untuk Customer</h3>
                    <span class="text-[9px] bg-[#D4954D] text-[#290024] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider">Kasir Mobile</span>
                </div>
                <p class="text-[11px] text-[#E3DEA4] truncate">Catat pesanan pelanggan langsung di kasir atau pesan antar meja (Dine In & Take Away)</p>
            </div>
        </div>
        @if($activePenjaga)
            <a href="{{ route('kasir.pos.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-full bg-[#D4954D] hover:bg-[#D4954D] text-[#290024] text-xs font-bold shadow-md transition-all active:scale-95 shrink-0">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Buat Pesanan Sekarang</span>
            </a>
        @else
            <button type="button" onclick="document.getElementById('penjaga_kasir_name')?.focus(); alert('Pilih nama penjaga kasir yang bertugas terlebih dahulu pada form di atas!');" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-full bg-[#E3DEA4] text-[#290024] text-xs font-bold shadow-md transition-all active:scale-95 shrink-0">
                <i data-lucide="alert-triangle" class="w-4 h-4 text-[#775533]"></i>
                <span>Pilih Penjaga Kasir Dahulu</span>
            </button>
        @endif
    </div>

    <!-- Mobile & Tablet Menu Shortcuts Grid (Layar Operasional Lengkap) -->
    <div class="lg:hidden space-y-2">
        <div class="flex items-center justify-between px-1">
            <h3 class="font-serif font-bold text-xs sm:text-sm text-cafe-dark flex items-center gap-1.5">
                <i data-lucide="grid" class="w-4 h-4 text-cafe-gold"></i>
                Menu Operasional Kasir
            </h3>
            <span class="text-[10px] text-cafe-muted">Akses Cepat</span>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 sm:gap-3">
            <a href="{{ route('kasir.pos.index') }}" class="bg-cafe-card rounded-2xl border border-cafe-beige/70 p-3 sm:p-3.5 flex items-center gap-2.5 hover:bg-cafe-beige/40 active:scale-[0.98] transition-all shadow-soft">
                <div class="w-9 h-9 rounded-xl bg-cafe-dark text-cafe-gold flex items-center justify-center shrink-0">
                    <i data-lucide="calculator" class="w-4 h-4"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-cafe-dark truncate">POS / Pesan</p>
                    <p class="text-[10px] text-cafe-muted truncate">Input order kasir</p>
                </div>
            </a>
            <a href="{{ route('kasir.orders.index') }}" class="bg-cafe-card rounded-2xl border border-cafe-beige/70 p-3 sm:p-3.5 flex items-center gap-2.5 hover:bg-cafe-beige/40 active:scale-[0.98] transition-all shadow-soft">
                <div class="w-9 h-9 rounded-xl bg-cafe-gold/20 text-[#290024] flex items-center justify-center shrink-0">
                    <i data-lucide="inbox" class="w-4 h-4 text-cafe-gold"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-cafe-dark truncate">Pesanan Masuk</p>
                    <p class="text-[10px] text-cafe-muted truncate">Dapur & Web</p>
                </div>
            </a>
            <a href="{{ route('kasir.payments.index') }}" class="bg-cafe-card rounded-2xl border border-cafe-beige/70 p-3 sm:p-3.5 flex items-center gap-2.5 hover:bg-cafe-beige/40 active:scale-[0.98] transition-all shadow-soft">
                <div class="w-9 h-9 rounded-xl bg-cafe-warning/15 text-cafe-warning flex items-center justify-center shrink-0">
                    <i data-lucide="receipt" class="w-4 h-4"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-cafe-dark truncate">Pembayaran</p>
                    <p class="text-[10px] text-cafe-muted truncate">Tagihan & Struk</p>
                </div>
            </a>
            <a href="{{ route('kasir.reservations.index') }}" class="bg-cafe-card rounded-2xl border border-cafe-beige/70 p-3 sm:p-3.5 flex items-center gap-2.5 hover:bg-cafe-beige/40 active:scale-[0.98] transition-all shadow-soft">
                <div class="w-9 h-9 rounded-xl bg-cafe-info/15 text-cafe-info flex items-center justify-center shrink-0">
                    <i data-lucide="calendar-days" class="w-4 h-4"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-cafe-dark truncate">Reservasi & Meja</p>
                    <p class="text-[10px] text-cafe-muted truncate">Denah & Tamu</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Stat Cards Harian -->
    <div class="grid grid-cols-2 md:grid-cols-4 landscape:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 p-5 shadow-soft">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-semibold text-cafe-muted uppercase tracking-wide">Penjualan Hari Ini</p>
                <div class="w-9 h-9 rounded-2xl bg-cafe-gold/15 text-cafe-gold flex items-center justify-center"><i data-lucide="trending-up" class="w-4 h-4"></i></div>
            </div>
            <p class="font-serif text-xl sm:text-2xl font-bold text-cafe-dark mt-2">Rp {{ number_format($todaySales, 0, ',', '.') }}</p>
            <p class="text-[11px] text-cafe-muted mt-1">{{ $todayOrdersCount }} transaksi lunas</p>
        </div>

        <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 p-5 shadow-soft">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-semibold text-cafe-muted uppercase tracking-wide">{{ $activePenjaga ? 'Penjualan ' . $activePenjaga : 'Penjaga Kasir' }}</p>
                <div class="w-9 h-9 rounded-2xl bg-cafe-success/15 text-cafe-success flex items-center justify-center"><i data-lucide="user-check" class="w-4 h-4"></i></div>
            </div>
            <p class="font-serif text-xl sm:text-2xl font-bold text-cafe-dark mt-2">Rp {{ number_format($attendantSales, 0, ',', '.') }}</p>
            <p class="text-[11px] text-cafe-muted mt-1">{{ $attendantOrdersCount }} transaksi oleh penjaga</p>
        </div>

        <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 p-5 shadow-soft">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-semibold text-cafe-muted uppercase tracking-wide">Belum Dibayar</p>
                <div class="w-9 h-9 rounded-2xl bg-cafe-warning/15 text-cafe-warning flex items-center justify-center"><i data-lucide="clock" class="w-4 h-4"></i></div>
            </div>
            <p class="font-serif text-xl sm:text-2xl font-bold text-cafe-dark mt-2">{{ $unpaidCount }}</p>
            <p class="text-[11px] text-cafe-muted mt-1">pesanan menunggu pembayaran</p>
        </div>

        <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 p-5 shadow-soft">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-semibold text-cafe-muted uppercase tracking-wide">Sedang Diproses</p>
                <div class="w-9 h-9 rounded-2xl bg-cafe-info/15 text-cafe-info flex items-center justify-center"><i data-lucide="utensils" class="w-4 h-4"></i></div>
            </div>
            <p class="font-serif text-xl sm:text-2xl font-bold text-cafe-dark mt-2">{{ $activeCount }}</p>
            <p class="text-[11px] text-cafe-muted mt-1">pesanan aktif di dapur</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Kolom Kiri: Pesanan Web Baru + Meja Terisi -->
        <div class="xl:col-span-2 space-y-6">
            <!-- Pesanan Web Baru -->
            <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft overflow-hidden">
                <div class="px-6 py-4 border-b border-cafe-beige/50 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-2xl bg-cafe-info/15 text-cafe-info flex items-center justify-center"><i data-lucide="globe" class="w-4 h-4"></i></div>
                        <div>
                            <h3 class="font-serif font-bold text-sm text-cafe-dark">Pesanan Web Baru</h3>
                            <p class="text-[10px] text-cafe-muted">perlu diterima kasir</p>
                        </div>
                    </div>
                    <a href="{{ route('kasir.orders.index', ['tab' => 'web']) }}" class="text-xs font-semibold text-cafe-brown hover:text-cafe-dark flex items-center gap-1">
                        Kelola <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>

                <div class="p-5">
                    @forelse($newWebOrders as $order)
                        <div class="flex items-center justify-between gap-3 p-3 rounded-2xl bg-cafe-cream/70 border border-cafe-beige/50 mb-2.5 last:mb-0">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-2xl bg-beige border border-cafe-beige flex items-center justify-center text-cafe-gold shrink-0">
                                    <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-cafe-dark">{{ $order->order_code }}</p>
                                    <p class="text-[11px] text-cafe-muted truncate">{{ $order->customer_name }} &bull; {{ $order->orderTypeLabel() }} {{ $order->table_number ? '&bull; Meja '.$order->table_number : '' }}</p>
                                    <p class="text-[10px] text-cafe-info font-medium mt-0.5">{{ $order->items->count() }} item &bull; {{ $order->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="text-sm font-serif font-bold text-cafe-dark">Rp {{ number_format($order->total, 0, ',', '.') }}</p>
                                @if($activePenjaga)
                                    <form action="{{ route('kasir.orders.accept', $order) }}" method="POST">
                                        @csrf
                                        <button class="mt-1 px-3.5 py-1.5 rounded-full bg-cafe-success text-beige text-[11px] font-semibold hover:bg-cafe-success/90 transition-colors">
                                            <i data-lucide="check" class="w-3 h-3 inline-block -mt-0.5"></i> Terima
                                        </button>
                                    </form>
                                @else
                                    <button type="button" onclick="document.getElementById('penjaga_kasir_name')?.focus(); alert('Wajib memilih nama penjaga kasir yang bertugas di bagian atas sebelum menerima pesanan!');" class="mt-1 px-3.5 py-1.5 rounded-full bg-cafe-beige/70 text-cafe-dark text-[11px] font-semibold hover:bg-cafe-beige transition-colors">
                                        <i data-lucide="alert-triangle" class="w-3 h-3 inline-block -mt-0.5 text-cafe-warning"></i> Pilih Penjaga
                                    </button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center">
                            <div class="w-14 h-14 mx-auto rounded-full bg-cafe-cream flex items-center justify-center text-cafe-muted">
                                <i data-lucide="inbox" class="w-6 h-6"></i>
                            </div>
                            <p class="text-xs text-cafe-muted mt-3">Tidak ada pesanan Web baru saat ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Meja Terisi -->
            <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft overflow-hidden">
                <div class="px-6 py-4 border-b border-cafe-beige/50 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-2xl bg-cafe-danger/15 text-cafe-danger flex items-center justify-center"><i data-lucide="grid-3x3" class="w-4 h-4"></i></div>
                        <div>
                            <h3 class="font-serif font-bold text-sm text-cafe-dark">Meja Terisi</h3>
                            <p class="text-[10px] text-cafe-muted">kondisi meja saat ini</p>
                        </div>
                    </div>
                    <a href="{{ route('kasir.reservations.index') }}" class="text-xs font-semibold text-cafe-brown hover:text-cafe-dark flex items-center gap-1">
                        Reservasi <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>

                <div class="p-5 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                    @forelse($occupiedTables as $table)
                        <div class="p-3 rounded-2xl border {{ $table->status === 'terpakai' ? 'bg-cafe-danger/10 border-cafe-danger/30' : 'bg-cafe-warning/10 border-cafe-warning/30' }}">
                            <div class="h-10 rounded-xl {{ $table->status === 'terpakai' ? 'bg-cafe-danger text-beige' : 'bg-cafe-warning text-beige' }} flex items-center justify-center font-serif font-bold">
                                {{ $table->number }}
                            </div>
                            <p class="text-[11px] font-semibold text-cafe-dark mt-2">{{ $table->statusBadge()['label'] }}</p>
                            <p class="text-[10px] text-cafe-muted">Kap {{ $table->capacity }}</p>
                        </div>
                    @empty
                        <div class="col-span-full py-6 text-center">
                            <div class="w-14 h-14 mx-auto rounded-full bg-cafe-cream flex items-center justify-center text-cafe-muted">
                                <i data-lucide="armchair" class="w-6 h-6"></i>
                            </div>
                            <p class="text-xs text-cafe-muted mt-3">Semua meja kosong. Pesanan via <a href="{{ route('kasir.pos.index') }}" class="text-cafe-gold font-semibold hover:underline">POS</a> akan mengisinya otomatis.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Menunggu Bayar + Reservasi Hari Ini -->
        <div class="space-y-6">
            <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft overflow-hidden">
                <div class="px-6 py-4 border-b border-cafe-beige/50 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-2xl bg-cafe-warning/15 text-cafe-warning flex items-center justify-center"><i data-lucide="hourglass" class="w-4 h-4"></i></div>
                        <div>
                            <h3 class="font-serif font-bold text-sm text-cafe-dark">Menunggu Pembayaran</h3>
                            <p class="text-[10px] text-cafe-muted">segera diproses</p>
                        </div>
                    </div>
                    <a href="{{ route('kasir.payments.index') }}" class="text-xs font-semibold text-cafe-brown hover:text-cafe-dark flex items-center gap-1">
                        Bayar <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>

                <div class="p-5 divide-y divide-cafe-beige/30">
                    @forelse($waitingOrders as $order)
                        <a href="{{ route('kasir.payments.checkout', $order) }}" class="py-3 flex items-center justify-between gap-3 group">
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-cafe-dark group-hover:text-cafe-gold transition-colors">{{ $order->order_code }}</p>
                                <p class="text-[11px] text-cafe-muted truncate">{{ $order->customer_name }} &bull; {{ $order->orderTypeLabel() }} {{ $order->table_number ? 'Meja '.$order->table_number : '' }}</p>
                            </div>
                            <p class="text-xs font-serif font-bold text-cafe-dark shrink-0">Rp {{ number_format($order->total, 0, ',', '.') }}</p>
                        </a>
                    @empty
                        <div class="py-8 text-center">
                            <div class="w-14 h-14 mx-auto rounded-full bg-cafe-cream flex items-center justify-center text-cafe-muted">
                                <i data-lucide="receipt" class="w-6 h-6"></i>
                            </div>
                            <p class="text-xs text-cafe-muted mt-3">Tidak ada tagihan pending.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft overflow-hidden">
                <div class="px-6 py-4 border-b border-cafe-beige/50 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-2xl bg-cafe-gold/15 text-cafe-gold flex items-center justify-center"><i data-lucide="calendar-days" class="w-4 h-4"></i></div>
                        <div>
                            <h3 class="font-serif font-bold text-sm text-cafe-dark">Reservasi Hari Ini</h3>
                            <p class="text-[10px] text-cafe-muted">{{ now()->translatedFormat('l, d M Y') }}</p>
                        </div>
                    </div>
                    <a href="{{ route('kasir.reservations.index') }}" class="text-xs font-semibold text-cafe-brown hover:text-cafe-dark flex items-center gap-1">
                        Kelola <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>

                <div class="p-5 divide-y divide-cafe-beige/30">
                    @forelse($todayReservations as $reservation)
                        <div class="py-3 flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-cafe-dark">{{ $reservation->customer_name }}</p>
                                <p class="text-[11px] text-cafe-muted truncate">{{ $reservation->datetimeRangeLabel() }} &bull; {{ $reservation->party_size }} orang {{ $reservation->table ? '&bull; Meja '.$reservation->table->number : '' }}</p>
                            </div>
                            @php $badge = $reservation->statusBadge(); @endphp
                            <span class="text-[10px] font-bold px-2 py-1 rounded-full bg-cafe-{{ $badge['color'] }}/15 text-cafe-{{ $badge['color'] }} border border-cafe-{{ $badge['color'] }}/30 shrink-0">
                                {{ $badge['label'] }}
                            </span>
                        </div>
                    @empty
                        <div class="py-8 text-center">
                            <div class="w-14 h-14 mx-auto rounded-full bg-cafe-cream flex items-center justify-center text-cafe-muted">
                                <i data-lucide="calendar-off" class="w-6 h-6"></i>
                            </div>
                            <p class="text-xs text-cafe-muted mt-3">Belum ada reservasi hari ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection