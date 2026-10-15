@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Top Welcome Banner & Quick Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-gradient-to-r from-[#290024] via-[#3D1236] to-[#775533] text-cafe-cream p-6 sm:p-8 rounded-3xl shadow-soft-lg relative overflow-hidden">
        <!-- Line art aesthetic circles -->
        <div class="absolute -top-10 -right-10 w-44 h-44 rounded-full border border-cafe-gold/20 pointer-events-none"></div>
        <div class="absolute top-1/2 -right-20 w-60 h-60 rounded-full border border-cafe-gold/10 pointer-events-none"></div>

        <div class="relative z-10">
            <span class="text-xs font-medium text-cafe-gold uppercase tracking-wider block mb-1">
                {{ \Carbon\Carbon::now()->translatedFormat('l, j F Y') }}
            </span>
            <h2 class="font-serif text-2xl sm:text-3xl font-bold tracking-tight text-[#FAFCF3]">
                Selamat datang, {{ Auth::user()->name }}! 👋
            </h2>
            <p class="text-xs sm:text-sm text-cafe-cream/80 mt-1 max-w-xl">
                Ikhtisar operasional ABS Cafe Tulungagung hari ini. Terdapat <strong>{{ $pendingPaymentsCount }} pesanan</strong> menunggu pembayaran dan <strong>{{ $lowStockMenus->count() }} menu</strong> dengan stok menipis.
            </p>
        </div>

        <div class="flex items-center gap-3 relative z-10 shrink-0">
            <a href="{{ route('admin.menu.index') }}" class="px-4 sm:px-5 py-2.5 rounded-full bg-cafe-gold text-cafe-dark hover:opacity-95 font-semibold text-xs shadow-md transition-all flex items-center gap-1.5">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Kelola Menu</span>
            </a>
            <a href="{{ route('admin.orders.index') }}" class="px-4 sm:px-5 py-2.5 rounded-full bg-white/15 hover:bg-white/25 text-[#FAFCF3] font-semibold text-xs border border-white/20 transition-all flex items-center gap-1.5">
                <span>Semua Pesanan</span>
                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>
    </div>

    <!-- 4 Stat Cards (2 Kolom di Mobile Sesuai Panduan) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6">
        <!-- Stat 1: Pesanan Hari Ini -->
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

        <!-- Stat 2: Pendapatan Hari Ini -->
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

        <!-- Stat 3: Menunggu Pembayaran -->
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

        <!-- Stat 4: Menu Stok Menipis -->
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

    <!-- Charts Row: Penjualan 7 Hari -->
    <div>
        <!-- Revenue Line/Area Chart (Full Width) -->
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

    <!-- Bottom Row: Top 5 Menu & Pesanan Terbaru & Peringatan Stok -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Top 5 Menu Terlaris -->
        <div class="bg-cafe-card rounded-2xl p-6 shadow-soft border border-cafe-beige/40">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-serif text-lg font-bold text-cafe-dark">Menu Terlaris</h3>
                <a href="{{ route('admin.menu.index') }}" class="text-xs font-semibold text-cafe-brown hover:text-cafe-dark">
                    Lihat Semua &rarr;
                </a>
            </div>

            <div class="space-y-3">
                @foreach($topMenus as $idx => $menu)
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

        <!-- Pesanan Terbaru -->
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

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-cafe-beige text-cafe-muted font-semibold">
                                <th class="pb-3 pl-2">Kode Order</th>
                                <th class="pb-3">Customer</th>
                                <th class="pb-3">Meja/Tipe</th>
                                <th class="pb-3">Total</th>
                                <th class="pb-3">Status</th>
                                <th class="pb-3 text-right pr-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-cafe-beige/30">
                            @foreach($recentOrders as $rOrd)
                                <tr class="hover:bg-cafe-cream/60 transition-colors">
                                    <td class="py-3 pl-2 font-mono font-medium text-cafe-dark">{{ $rOrd->order_code }}</td>
                                    <td class="py-3 font-medium text-cafe-text">{{ $rOrd->customer_name }}</td>
                                    <td class="py-3 text-cafe-muted">{{ $rOrd->order_type === 'dine_in' ? 'Meja ' . $rOrd->table_number : 'Take-away' }}</td>
                                    <td class="py-3 font-semibold text-cafe-dark">Rp {{ number_format($rOrd->total, 0, ',', '.') }}</td>
                                    <td class="py-3">
                                        @if($rOrd->payment_status === 'lunas')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-[#4F7A4A]/15 text-[#4F7A4A] border border-[#4F7A4A]/30">Lunas</span>
                                        @elseif($rOrd->payment_status === 'menunggu_pembayaran')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-[#C98A2B]/15 text-[#C98A2B] border border-[#C98A2B]/30">Menunggu</span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-[#B03A2E]/15 text-[#B03A2E] border border-[#B03A2E]/30">Batal</span>
                                        @endif
                                    </td>
                                    <td class="py-3 text-right pr-2">
                                        <a href="{{ route('admin.orders.index', ['search' => $rOrd->order_code]) }}" class="inline-flex items-center gap-1 text-xs font-semibold text-cafe-brown hover:text-cafe-dark px-2.5 py-1 rounded-full bg-cafe-beige/50 hover:bg-cafe-beige transition-colors">
                                            <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                            <span>Lihat</span>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Panel Peringatan Stok Ringkas -->
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
                    @forelse($lowStockMenus->take(3) as $ls)
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
</div>
@endsection

@push('scripts')
<script>
    // Revenue Line/Area Chart with Chart.js
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
                        label: function(context) {
                            return 'Pendapatan: Rp ' + context.parsed.y.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
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
