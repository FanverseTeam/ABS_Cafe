@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')
<div class="space-y-4 lg:space-y-8">
    <!-- ACTIVE ADMIN DASHBOARD: welcome banner for the always-on modules. -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-gradient-to-r from-[#290024] via-[#3D1236] to-[#775533] text-cafe-cream p-4.5 sm:p-6 lg:p-8 rounded-2xl sm:rounded-3xl shadow-soft-lg relative overflow-hidden">
        <!-- Line art aesthetic circles -->
        <div class="absolute -top-10 -right-10 w-44 h-44 rounded-full border border-cafe-gold/20 pointer-events-none"></div>
        <div class="absolute top-1/2 -right-20 w-60 h-60 rounded-full border border-cafe-gold/10 pointer-events-none"></div>

        <div class="relative z-10">
            <span class="text-xs font-medium text-cafe-gold uppercase tracking-wider block mb-1">
                {{ \Carbon\Carbon::now()->translatedFormat('l, j F Y') }}
            </span>
            <h2 class="font-serif text-2xl sm:text-3xl font-bold tracking-tight text-[#FAFCF3]">
                Selamat datang, {{ Auth::user()->name }}!
            </h2>
            <p class="text-xs sm:text-sm text-cafe-cream/80 mt-1 max-w-xl">
                @if ($legacyAdmin)
                    Ikhtisar operasional ABS Cafe Tulungagung hari ini. Terdapat <strong>{{ $pendingPaymentsCount }} pesanan</strong> menunggu pembayaran dan <strong>{{ $lowStockCount }} menu</strong> dengan stok menipis.
                @else
                    Ikhtisar operasional ABS Cafe Tulungagung hari ini. Ada <strong>{{ $activePromoCount }} promo aktif</strong> dan <strong>{{ $lowStockCount }} menu</strong> yang perlu diperhatikan stoknya.
                @endif
            </p>
        </div>

        <div class="flex items-center gap-3 relative z-10 shrink-0">
            <a href="{{ route('admin.catalog.index', ['tab' => 'menu']) }}" class="px-4 sm:px-5 py-2.5 rounded-full bg-cafe-gold text-cafe-dark hover:opacity-95 font-semibold text-xs shadow-md transition-all flex items-center gap-1.5">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Kelola Menu</span>
            </a>

            @if ($legacyAdmin)
                <!-- LEGACY ADMIN: order shortcut only exists while the switch is on. -->
                <a href="{{ route('admin.orders.index') }}" class="px-4 sm:px-5 py-2.5 rounded-full bg-white/15 hover:bg-white/25 text-[#FAFCF3] font-semibold text-xs border border-white/20 transition-all flex items-center gap-1.5">
                    <span>Semua Pesanan</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </a>
            @endif
        </div>
    </div>

    <!-- ACTIVE ADMIN DASHBOARD: stats from Dashboard + six active modules only. -->
    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-3 sm:gap-5">
        <a href="{{ route('admin.catalog.index', ['tab' => 'menu']) }}" class="bg-cafe-card rounded-2xl p-4 sm:p-5 shadow-soft hover:shadow-soft-lg transition-all border border-cafe-beige/40 flex flex-col justify-between group">
            <div class="flex items-center justify-between gap-2">
                <p class="text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-cafe-muted">Total Menu</p>
                <div class="w-9 h-9 rounded-xl bg-cafe-beige/80 flex items-center justify-center text-cafe-dark">
                    <i data-lucide="coffee" class="w-4 h-4"></i>
                </div>
            </div>
            <h3 class="mt-3 text-2xl sm:text-3xl font-serif font-bold text-cafe-text">{{ $totalMenus }}</h3>
            <p class="text-[10px] text-cafe-muted mt-0.5 group-hover:text-cafe-brown">Lihat katalog &rarr;</p>
        </a>

        <a href="{{ route('admin.catalog.index', ['tab' => 'stock']) }}" class="bg-cafe-card rounded-2xl p-4 sm:p-5 shadow-soft hover:shadow-soft-lg transition-all border border-cafe-beige/40 flex flex-col justify-between group">
            <div class="flex items-center justify-between gap-2">
                <p class="text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-cafe-muted">Menu Stok Menipis/Habis</p>
                <div class="w-9 h-9 rounded-xl bg-cafe-warning/15 flex items-center justify-center text-cafe-warning">
                    <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                </div>
            </div>
            <h3 class="mt-3 text-2xl sm:text-3xl font-serif font-bold text-cafe-text">{{ $lowStockCount }}</h3>
            <p class="text-[10px] text-cafe-muted mt-0.5 group-hover:text-cafe-brown">Perlu restock &rarr;</p>
        </a>

        <a href="{{ route('admin.vouchers.index') }}" class="bg-cafe-card rounded-2xl p-4 sm:p-5 shadow-soft hover:shadow-soft-lg transition-all border border-cafe-beige/40 flex flex-col justify-between group">
            <div class="flex items-center justify-between gap-2">
                <p class="text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-cafe-muted">Promo Aktif</p>
                <div class="w-9 h-9 rounded-xl bg-cafe-gold/20 flex items-center justify-center text-cafe-brown">
                    <i data-lucide="percent" class="w-4 h-4"></i>
                </div>
            </div>
            <h3 class="mt-3 text-2xl sm:text-3xl font-serif font-bold text-cafe-text">{{ $activePromoCount }}</h3>
            <p class="text-[10px] text-cafe-muted mt-0.5 group-hover:text-cafe-brown">Kelola promo &rarr;</p>
        </a>

        <a href="{{ route('admin.staff.index') }}" class="bg-cafe-card rounded-2xl p-4 sm:p-5 shadow-soft hover:shadow-soft-lg transition-all border border-cafe-beige/40 flex flex-col justify-between group">
            <div class="flex items-center justify-between gap-2">
                <p class="text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-cafe-muted">Staf Aktif</p>
                <div class="w-9 h-9 rounded-xl bg-cafe-success/15 flex items-center justify-center text-cafe-success">
                    <i data-lucide="users" class="w-4 h-4"></i>
                </div>
            </div>
            <h3 class="mt-3 text-2xl sm:text-3xl font-serif font-bold text-cafe-text">{{ $activeStaffCount }}</h3>
            <p class="text-[10px] text-cafe-muted mt-0.5 group-hover:text-cafe-brown">Kelola staf &rarr;</p>
        </a>

        <a href="{{ route('admin.operations.index', ['tab' => 'meja']) }}" class="bg-cafe-card rounded-2xl p-4 sm:p-5 shadow-soft hover:shadow-soft-lg transition-all border border-cafe-beige/40 flex flex-col justify-between group col-span-2 md:col-span-1">
            <div class="flex items-center justify-between gap-2">
                <p class="text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-cafe-muted">Jumlah Meja</p>
                <div class="w-9 h-9 rounded-xl bg-cafe-info/15 flex items-center justify-center text-cafe-info">
                    <i data-lucide="grid-3x3" class="w-4 h-4"></i>
                </div>
            </div>
            <h3 class="mt-3 text-2xl sm:text-3xl font-serif font-bold text-cafe-text">{{ $totalTables }}</h3>
            <p class="text-[10px] text-cafe-muted mt-0.5 group-hover:text-cafe-brown">Atur meja &rarr;</p>
        </a>
    </div>

    <!-- ACTIVE ADMIN DASHBOARD: quick actions to active management pages. -->
    <div>
        <div class="flex items-center justify-between mb-3">
            <div>
                <h3 class="font-serif text-lg font-bold text-cafe-dark">Pintasan Cepat</h3>
                <p class="text-xs text-cafe-muted">Aksi yang paling sering dilakukan admin.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <a href="{{ route('admin.catalog.index', ['tab' => 'menu']) }}" class="flex items-center gap-3 p-4 rounded-2xl bg-cafe-card border border-cafe-beige/50 shadow-soft hover:shadow-soft-lg hover:border-cafe-gold/50 transition-all group">
                <span class="w-10 h-10 rounded-xl bg-cafe-gold/20 text-cafe-brown flex items-center justify-center shrink-0">
                    <i data-lucide="plus-circle" class="w-5 h-5"></i>
                </span>
                <span class="min-w-0">
                    <span class="block text-sm font-semibold text-cafe-dark">Tambah Menu</span>
                    <span class="block text-[11px] text-cafe-muted">Buka katalog menu</span>
                </span>
                <i data-lucide="arrow-right" class="w-4 h-4 text-cafe-muted ml-auto group-hover:text-cafe-brown"></i>
            </a>

            <a href="{{ route('admin.vouchers.index') }}" class="flex items-center gap-3 p-4 rounded-2xl bg-cafe-card border border-cafe-beige/50 shadow-soft hover:shadow-soft-lg hover:border-cafe-gold/50 transition-all group">
                <span class="w-10 h-10 rounded-xl bg-cafe-gold/20 text-cafe-brown flex items-center justify-center shrink-0">
                    <i data-lucide="ticket-plus" class="w-5 h-5"></i>
                </span>
                <span class="min-w-0">
                    <span class="block text-sm font-semibold text-cafe-dark">Tambah Promo</span>
                    <span class="block text-[11px] text-cafe-muted">Buat voucher baru</span>
                </span>
                <i data-lucide="arrow-right" class="w-4 h-4 text-cafe-muted ml-auto group-hover:text-cafe-brown"></i>
            </a>

            <a href="{{ route('admin.staff.index') }}" class="flex items-center gap-3 p-4 rounded-2xl bg-cafe-card border border-cafe-beige/50 shadow-soft hover:shadow-soft-lg hover:border-cafe-gold/50 transition-all group">
                <span class="w-10 h-10 rounded-xl bg-cafe-gold/20 text-cafe-brown flex items-center justify-center shrink-0">
                    <i data-lucide="user-plus" class="w-5 h-5"></i>
                </span>
                <span class="min-w-0">
                    <span class="block text-sm font-semibold text-cafe-dark">Tambah Staf</span>
                    <span class="block text-[11px] text-cafe-muted">Kelola akses pengguna</span>
                </span>
                <i data-lucide="arrow-right" class="w-4 h-4 text-cafe-muted ml-auto group-hover:text-cafe-brown"></i>
            </a>
        </div>
    </div>

    <!-- ACTIVE ADMIN DASHBOARD: activity log + stock warnings. -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-cafe-card rounded-2xl p-6 shadow-soft border border-cafe-beige/40">
            <div class="flex items-center justify-between gap-3 mb-4">
                <div>
                    <h3 class="font-serif text-lg font-bold text-cafe-dark">Aktivitas Terbaru</h3>
                    <p class="text-xs text-cafe-muted">5 catatan terakhir dari Log Aktivitas.</p>
                </div>
                <a href="{{ route('admin.activity.index') }}" class="text-xs font-semibold text-cafe-brown hover:text-cafe-dark whitespace-nowrap">
                    Lihat Semua &rarr;
                </a>
            </div>

            <div class="space-y-3">
                @forelse ($recentActivities as $log)
                    <div class="flex items-start gap-3 p-3 rounded-2xl bg-cafe-cream/60 border border-cafe-beige/40">
                        <span class="w-8 h-8 rounded-full bg-cafe-gold/20 text-cafe-brown flex items-center justify-center shrink-0">
                            <i data-lucide="history" class="w-4 h-4"></i>
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold text-cafe-dark leading-snug">{{ $log->activity }}</p>
                            <p class="text-[10px] text-cafe-muted mt-0.5">
                                {{ $log->user_name }} &bull; {{ ucfirst($log->role) }} &bull; {{ $log->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-xs text-cafe-muted">
                        Belum ada aktivitas tercatat.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="bg-cafe-card rounded-2xl p-6 shadow-soft border border-cafe-beige/40">
            <div class="flex items-center justify-between gap-3 mb-4">
                <div>
                    <h3 class="font-serif text-lg font-bold text-cafe-dark">Peringatan Stok Menipis</h3>
                    <p class="text-xs text-cafe-muted">Data dari Stok &amp; Supplier untuk menu yang menipis atau habis.</p>
                </div>
                <a href="{{ route('admin.catalog.index', ['tab' => 'stock']) }}" class="text-xs font-semibold text-cafe-brown hover:text-cafe-dark whitespace-nowrap">
                    Kelola Stok &rarr;
                </a>
            </div>

            <div class="space-y-3">
                @forelse ($lowStockMenus->take(5) as $menu)
                    <div class="flex items-center justify-between gap-3 p-3 rounded-2xl bg-cafe-cream/60 border border-cafe-beige/40">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-cafe-dark truncate">{{ $menu->name }}</p>
                            <p class="text-[10px] text-cafe-muted">
                                Sisa <strong class="{{ $menu->stock <= 0 ? 'text-cafe-danger' : 'text-cafe-warning' }}">{{ $menu->stock }}</strong> {{ $menu->unit }} (Min: {{ $menu->min_stock }})
                            </p>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold shrink-0 {{ $menu->stock <= 0 ? 'bg-cafe-danger/15 text-cafe-danger' : 'bg-cafe-warning/15 text-cafe-warning' }}">
                            {{ $menu->stock <= 0 ? 'Habis' : 'Menipis' }}
                        </span>
                    </div>
                @empty
                    <div class="py-8 text-center text-xs text-cafe-muted">
                        Semua stok menu berada dalam kondisi aman.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    @if ($legacyAdmin)
        <!-- LEGACY ADMIN: order, revenue, chart, and recent-order widgets return only when the switch is on. -->
        <div>
            <div class="flex items-center gap-2 mb-3">
                <span class="px-2.5 py-1 rounded-full bg-cafe-warning/15 text-cafe-warning text-[10px] font-bold uppercase tracking-wider">Legacy</span>
                <h3 class="font-serif text-lg font-bold text-cafe-dark">Ringkasan Pesanan</h3>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6">
                <!-- LEGACY ADMIN: Stat 1: Pesanan Hari Ini -->
                <div class="bg-cafe-card rounded-2xl p-4 sm:p-6 shadow-soft hover:shadow-soft-lg transition-all border border-cafe-beige/40 flex flex-col justify-between relative overflow-hidden">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-cafe-muted truncate">Pesanan Hari Ini</p>
                        <div class="w-8 h-8 sm:w-11 sm:h-11 rounded-xl sm:rounded-2xl bg-cafe-beige/80 flex items-center justify-center text-cafe-dark shadow-xs shrink-0">
                            <i data-lucide="shopping-cart" class="w-4 h-4 sm:w-5 sm:h-5 stroke-[2.2]"></i>
                        </div>
                    </div>
                    <div class="mt-2 sm:mt-3">
                        <h3 class="text-lg sm:text-2xl lg:text-3xl font-serif font-bold text-cafe-text tracking-tight">{{ $ordersTodayCount }}</h3>
                        <div class="flex items-center gap-1.5 mt-1 text-[10px] sm:text-xs">
                            <span class="font-medium text-cafe-success bg-cafe-success/10 px-1.5 py-0.5 rounded-full">↑ 14.2%</span>
                            <span class="text-cafe-muted hidden sm:inline">transaksi</span>
                        </div>
                    </div>
                </div>

                <!-- LEGACY ADMIN: Stat 2: Pendapatan Hari Ini -->
                <div class="bg-cafe-card rounded-2xl p-4 sm:p-6 shadow-soft hover:shadow-soft-lg transition-all border border-cafe-beige/40 flex flex-col justify-between relative overflow-hidden">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-cafe-muted truncate">Pendapatan</p>
                        <div class="w-8 h-8 sm:w-11 sm:h-11 rounded-xl sm:rounded-2xl bg-cafe-beige/80 flex items-center justify-center text-cafe-dark shadow-xs shrink-0">
                            <i data-lucide="dollar-sign" class="w-4 h-4 sm:w-5 sm:h-5 stroke-[2.2]"></i>
                        </div>
                    </div>
                    <div class="mt-2 sm:mt-3">
                        <h3 class="text-base sm:text-2xl lg:text-3xl font-serif font-bold text-cafe-text tracking-tight truncate">Rp {{ number_format($revenueToday, 0, ',', '.') }}</h3>
                        <div class="flex items-center gap-1.5 mt-1 text-[10px] sm:text-xs">
                            <span class="font-medium text-cafe-success bg-cafe-success/10 px-1.5 py-0.5 rounded-full">↑ 8.5%</span>
                            <span class="text-cafe-muted hidden sm:inline">lunas</span>
                        </div>
                    </div>
                </div>

                <!-- LEGACY ADMIN: Stat 3: Menunggu Pembayaran -->
                <div class="bg-cafe-card rounded-2xl p-4 sm:p-6 shadow-soft hover:shadow-soft-lg transition-all border border-cafe-beige/40 flex flex-col justify-between relative overflow-hidden">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-cafe-muted truncate">Menunggu Bayar</p>
                        <div class="w-8 h-8 sm:w-11 sm:h-11 rounded-xl sm:rounded-2xl bg-cafe-beige/80 flex items-center justify-center text-cafe-dark shadow-xs shrink-0">
                            <i data-lucide="clock" class="w-4 h-4 sm:w-5 sm:h-5 stroke-[2.2]"></i>
                        </div>
                    </div>
                    <div class="mt-2 sm:mt-3">
                        <h3 class="text-lg sm:text-2xl lg:text-3xl font-serif font-bold text-cafe-text tracking-tight">{{ $pendingPaymentsCount }}</h3>
                        <div class="flex items-center gap-1.5 mt-1 text-[10px] sm:text-xs">
                            <span class="font-medium text-cafe-warning bg-cafe-warning/10 px-1.5 py-0.5 rounded-full">Verifikasi</span>
                        </div>
                    </div>
                </div>

                <!-- LEGACY ADMIN: Stat 4: Menu Stok Menipis -->
                <div class="bg-cafe-card rounded-2xl p-4 sm:p-6 shadow-soft hover:shadow-soft-lg transition-all border border-cafe-beige/40 flex flex-col justify-between relative overflow-hidden">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-cafe-muted truncate">Stok Menipis</p>
                        <div class="w-8 h-8 sm:w-11 sm:h-11 rounded-xl sm:rounded-2xl bg-cafe-beige/80 flex items-center justify-center text-cafe-dark shadow-xs shrink-0">
                            <i data-lucide="alert-triangle" class="w-4 h-4 sm:w-5 sm:h-5 stroke-[2.2]"></i>
                        </div>
                    </div>
                    <div class="mt-2 sm:mt-3">
                        <h3 class="text-lg sm:text-2xl lg:text-3xl font-serif font-bold text-cafe-text tracking-tight">{{ $lowStockMenus->count() }} Menu</h3>
                        <div class="flex items-center gap-1.5 mt-1 text-[10px] sm:text-xs">
                            <span class="font-medium text-cafe-danger bg-cafe-danger/10 px-1.5 py-0.5 rounded-full">Restock</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- LEGACY ADMIN: sales chart. -->
        <div>
            <div class="bg-cafe-card rounded-2xl p-6 shadow-soft border border-cafe-beige/40">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
                    <div>
                        <h3 class="font-serif text-lg font-bold text-cafe-dark">Tren Penjualan &amp; Pendapatan</h3>
                        <p class="text-xs text-cafe-muted">Performa nominal penjualan 7 hari terakhir</p>
                    </div>
                    <span class="text-xs font-semibold px-3 py-1 bg-cafe-beige/60 text-cafe-dark rounded-full">
                        Minggu Ini
                    </span>
                </div>

                <div class="h-64 sm:h-72 w-full relative">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>

        <!-- LEGACY ADMIN: top menus, latest orders, and legacy stock panel. -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="bg-cafe-card rounded-2xl p-6 shadow-soft border border-cafe-beige/40">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-serif text-lg font-bold text-cafe-dark">Menu Terlaris</h3>
                    <a href="{{ route('admin.menu.index') }}" class="text-xs font-semibold text-cafe-brown hover:text-cafe-dark">
                        Lihat Semua &rarr;
                    </a>
                </div>

                <div class="space-y-3">
                    @foreach ($topMenus as $idx => $menu)
                        <div class="flex items-center justify-between gap-3 p-2.5 rounded-2xl hover:bg-cafe-beige/30 transition-colors">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="font-serif font-bold text-xs text-cafe-gold w-4 text-center">#{{ $idx + 1 }}</span>
                                <img src="{{ $menu->image }}" alt="{{ $menu->name }}" class="w-11 h-11 rounded-xl object-cover bg-cafe-beige shrink-0 shadow-xs">
                                <div class="min-w-0">
                                    <p class="text-xs font-semibold text-cafe-dark truncate">{{ $menu->name }}</p>
                                    <p class="text-[11px] text-cafe-muted">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                                </div>
                            </div>
                            <span class="text-xs font-bold text-cafe-dark bg-cafe-beige/60 px-2 py-0.5 rounded-full shrink-0">
                                {{ $menu->sold_count }} terjual
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="lg:col-span-2 bg-cafe-card rounded-2xl p-6 shadow-soft border border-cafe-beige/40 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="font-serif text-lg font-bold text-cafe-dark">Pesanan Terbaru</h3>
                            <p class="text-xs text-cafe-muted">Transaksi paling akhir yang masuk ke sistem</p>
                        </div>
                        <a href="{{ route('admin.orders.index') }}" class="text-xs font-semibold text-cafe-brown hover:text-cafe-dark">
                            Semua Pesanan &rarr;
                        </a>
                    </div>

                    <x-responsive-table>
                        <x-slot:headers>
                            <th class="pb-3 pl-2 text-left">Kode Order</th>
                            <th class="pb-3 text-left">Customer</th>
                            <th class="pb-3 text-left">Meja/Tipe</th>
                            <th class="pb-3 text-left">Total</th>
                            <th class="pb-3 text-left">Status</th>
                            <th class="pb-3 text-right pr-2">Aksi</th>
                        </x-slot:headers>

                        @foreach ($recentOrders as $rOrd)
                            <x-responsive-table.row>
                                <x-slot:title>
                                    <span class="font-mono">{{ $rOrd->order_code }}</span>
                                </x-slot:title>

                                <x-slot:subtitle>
                                    {{ $rOrd->customer_name }}
                                </x-slot:subtitle>

                                <x-slot:badge>
                                    @if ($rOrd->payment_status === 'lunas')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-[#4F7A4A]/15 text-[#4F7A4A] border border-[#4F7A4A]/30">Lunas</span>
                                    @elseif ($rOrd->payment_status === 'menunggu_pembayaran')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-[#C98A2B]/15 text-[#C98A2B] border border-[#C98A2B]/30">Menunggu</span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-[#B03A2E]/15 text-[#B03A2E] border border-[#B03A2E]/30">Batal</span>
                                    @endif
                                </x-slot:badge>

                                <x-slot:fields>
                                    <x-responsive-table.field label="Tipe" :value="$rOrd->order_type === 'dine_in' ? 'Meja ' . $rOrd->table_number : 'Take-away'" />
                                    <x-responsive-table.field label="Total" :value="'Rp ' . number_format($rOrd->total, 0, ',', '.')" is-highlight="true" />
                                </x-slot:fields>

                                <x-slot:actions>
                                    <a href="{{ route('admin.orders.index', ['search' => $rOrd->order_code]) }}" class="inline-flex items-center gap-1 text-xs font-semibold text-cafe-brown hover:text-cafe-dark px-3 py-1.5 rounded-full bg-cafe-beige/50 hover:bg-cafe-beige transition-colors">
                                        <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                        <span>Lihat</span>
                                    </a>
                                </x-slot:actions>

                                <x-slot:desktop>
                                    <td class="hidden md:table-cell py-3 pl-2 font-mono font-medium text-cafe-dark">{{ $rOrd->order_code }}</td>
                                    <td class="hidden md:table-cell py-3 font-medium text-cafe-text">{{ $rOrd->customer_name }}</td>
                                    <td class="hidden md:table-cell py-3 text-cafe-muted">{{ $rOrd->order_type === 'dine_in' ? 'Meja ' . $rOrd->table_number : 'Take-away' }}</td>
                                    <td class="hidden md:table-cell py-3 font-semibold text-cafe-dark">Rp {{ number_format($rOrd->total, 0, ',', '.') }}</td>
                                    <td class="hidden md:table-cell py-3">
                                        @if ($rOrd->payment_status === 'lunas')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-[#4F7A4A]/15 text-[#4F7A4A] border border-[#4F7A4A]/30">Lunas</span>
                                        @elseif ($rOrd->payment_status === 'menunggu_pembayaran')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-[#C98A2B]/15 text-[#C98A2B] border border-[#C98A2B]/30">Menunggu</span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-[#B03A2E]/15 text-[#B03A2E] border border-[#B03A2E]/30">Batal</span>
                                        @endif
                                    </td>
                                    <td class="hidden md:table-cell py-3 text-right pr-2">
                                        <a href="{{ route('admin.orders.index', ['search' => $rOrd->order_code]) }}" class="inline-flex items-center gap-1 text-xs font-semibold text-cafe-brown hover:text-cafe-dark px-2.5 py-1 rounded-full bg-cafe-beige/50 hover:bg-cafe-beige transition-colors">
                                            <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                            <span>Lihat</span>
                                        </a>
                                    </td>
                                </x-slot:desktop>
                            </x-responsive-table.row>
                        @endforeach
                    </x-responsive-table>
                </div>

                <!-- LEGACY ADMIN: stock panel with its old standalone link. -->
                <div class="mt-6 pt-4 border-t border-cafe-beige/50">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <i data-lucide="alert-triangle" class="w-4 h-4 text-cafe-warning"></i>
                            <h4 class="font-serif text-sm font-bold text-cafe-dark">Peringatan Stok di Bawah Batas Minimum</h4>
                        </div>
                        <a href="{{ route('admin.stock.index') }}" class="text-xs font-semibold text-cafe-brown hover:text-cafe-dark">
                            Kelola Stok &rarr;
                        </a>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        @forelse ($lowStockMenus->take(3) as $ls)
                            <div class="p-3 rounded-2xl bg-cafe-cream/80 border border-cafe-beige/80 flex items-center justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-cafe-dark truncate">{{ $ls->name }}</p>
                                    <p class="text-[11px] text-cafe-muted">Sisa: <strong class="text-cafe-danger">{{ $ls->stock }}</strong> {{ $ls->unit }} (Min: {{ $ls->min_stock }})</p>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $ls->stock <= 0 ? 'bg-cafe-danger/20 text-cafe-danger' : 'bg-cafe-warning/20 text-cafe-warning' }}">
                                    {{ $ls->stock <= 0 ? 'Habis' : 'Menipis' }}
                                </span>
                            </div>
                        @empty
                            <div class="col-span-3 py-3 text-center text-xs text-cafe-muted">
                                Semua stok menu kafe saat ini berada dalam kondisi aman.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@if ($legacyAdmin)
    @push('scripts')
    <script>
        // LEGACY ADMIN: sales chart only initializes when the legacy switch is on.
        const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
        const gradient = ctxRevenue.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(41, 0, 36, 0.25)');
        gradient.addColorStop(1, 'rgba(41, 0, 36, 0.0)');

        new Chart(ctxRevenue, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: {!! json_encode($chartRevenue) !!},
                    borderColor: '#290024',
                    borderWidth: 2.5,
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#D4954D',
                    pointBorderColor: '#290024',
                    pointRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return 'Pendapatan: Rp ' + context.parsed.y.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (value) {
                                return 'Rp' + (value / 1000).toLocaleString('id-ID') + 'k';
                            },
                            font: { size: 10 }
                        },
                        grid: { color: 'rgba(227, 222, 164, 0.6)' }
                    },
                    x: {
                        ticks: { font: { size: 10 } },
                        grid: { display: false }
                    }
                }
            }
        });
    </script>
    @endpush
@endif
