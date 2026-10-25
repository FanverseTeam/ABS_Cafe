@extends('kasir.layouts.app')

@section('title', 'Dashboard Shift - Kasir')
@section('page_title', 'Dashboard Shift')

@section('content')
<div class="space-y-6">
    <!-- Banner Shift -->
    @if(! $shift)
        <div class="w-full max-w-[900px] mx-auto lg:max-w-none bg-gradient-to-r from-[#290024] via-[#3D1236] to-[#775533] text-cafe-cream rounded-2xl lg:rounded-3xl p-5 md:p-6 lg:p-8 relative shadow-soft-lg">
            <!-- Background & line art decoration isolated with overflow-hidden -->
            <div class="absolute inset-0 rounded-2xl lg:rounded-3xl overflow-hidden pointer-events-none z-0" aria-hidden="true">
                <div class="absolute inset-0 opacity-15 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=900&auto=format&fit=crop&q=80')"></div>
                <div class="absolute inset-0 bg-gradient-to-r from-[#290024] via-[#3D1236]/90 to-transparent"></div>
                <div class="absolute -top-10 -right-10 w-44 h-44 rounded-full border border-cafe-gold/20 opacity-40"></div>
            </div>

            <div class="relative z-10 max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 text-cafe-gold text-xs font-medium border border-cafe-gold/30 mb-2">
                    <i data-lucide="wallet" class="w-3.5 h-3.5"></i>
                    <span>Shift Belum Dibuka</span>
                </div>
                <h2 class="font-serif leading-[1.25] break-words text-[#FAFCF3]">
                    <span class="block lg:inline text-xs sm:text-sm lg:text-3xl font-normal lg:font-bold text-cafe-cream/85 lg:text-[#FAFCF3] mb-0.5 lg:mb-0">Mulai hari kerja Anda, </span>
                    <span class="block lg:inline text-[22px] sm:text-[24px] md:text-[28px] lg:text-3xl font-bold tracking-tight text-cafe-gold lg:text-[#FAFCF3]">
                        {{ Auth::user()->name }}
                    </span>
                </h2>
                <p class="text-sm leading-[1.5] text-cafe-beige/85 mt-2 max-w-lg">
                    Transaksi kasir hanya bisa diproses setelah shift dibuka. Siapkan kas awal, lalu mulai menerima pesanan & pembayaran.
                </p>
                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <a href="{{ route('kasir.shift.open') }}" class="inline-flex items-center justify-center gap-2 h-11 px-5 rounded-full bg-cafe-gold text-cafe-dark hover:bg-[#c4853d] font-semibold text-xs sm:text-sm shadow-md transition-all">
                        <i data-lucide="play" class="w-4 h-4 shrink-0"></i>
                        <span>Buka Shift Sekarang</span>
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- Quick Action: Pesan Manual untuk Customer -->
    <div class="bg-gradient-to-r from-[#290024] via-[#3D1236] to-[#775533] text-[#FAFCF3] rounded-2xl sm:rounded-3xl p-4 sm:p-5 shadow-soft-lg flex flex-col sm:flex-row items-center justify-between gap-3 border border-[#D4954D]/30">
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-[#D4954D] text-[#290024] flex items-center justify-center shrink-0 shadow">
                <i data-lucide="plus-circle" class="w-5 h-5 sm:w-6 sm:h-6"></i>
            </div>
            <div class="min-w-0">
                <div class="flex items-center gap-2">
                    <h3 class="font-serif font-bold text-sm sm:text-base text-[#FAFCF3] truncate">Pesan Manual untuk Customer</h3>
                    <span class="text-[9px] bg-[#D4954D] text-[#290024] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider">Kasir Mobile</span>
                </div>
                <p class="text-[11px] text-[#E3DEA4] truncate">Catat pesanan pelanggan langsung di kasir atau pesan antar meja (Dine In & Take Away)</p>
            </div>
        </div>
        <a href="{{ route('kasir.pos.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-full bg-[#D4954D] hover:bg-[#c4853d] text-[#290024] text-xs font-bold shadow-md transition-all active:scale-95 shrink-0">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Buat Pesanan Sekarang</span>
        </a>
    </div>

    <!-- Stat Cards -->
    @if($shift && $summary)
        <div class="grid grid-cols-2 md:grid-cols-4 landscape:grid-cols-4 gap-3 sm:gap-4">
            <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 p-5 shadow-soft">
                <div class="flex items-center justify-between">
                    <p class="text-[11px] font-semibold text-cafe-muted uppercase tracking-wide">Penjualan Shift</p>
                    <div class="w-9 h-9 rounded-2xl bg-cafe-gold/15 text-cafe-gold flex items-center justify-center"><i data-lucide="trending-up" class="w-4 h-4"></i></div>
                </div>
                <p class="font-serif text-xl sm:text-2xl font-bold text-cafe-dark mt-2">Rp {{ number_format($summary['sales'], 0, ',', '.') }}</p>
                <p class="text-[11px] text-cafe-muted mt-1">{{ $summary['orders_count'] }} transaksi lunas</p>
            </div>

            <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 p-5 shadow-soft">
                <div class="flex items-center justify-between">
                    <p class="text-[11px] font-semibold text-cafe-muted uppercase tracking-wide">Kas Fisik</p>
                    <div class="w-9 h-9 rounded-2xl bg-cafe-success/15 text-cafe-success flex items-center justify-center"><i data-lucide="banknote" class="w-4 h-4"></i></div>
                </div>
                <p class="font-serif text-xl sm:text-2xl font-bold text-cafe-dark mt-2">Rp {{ number_format($summary['expected_cash'], 0, ',', '.') }}</p>
                <p class="text-[11px] text-cafe-muted mt-1">Termasuk Rp {{ number_format($shift->opening_cash, 0, ',', '.') }} kas awal</p>
            </div>

            <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 p-5 shadow-soft">
                <div class="flex items-center justify-between">
                    <p class="text-[11px] font-semibold text-cafe-muted uppercase tracking-wide">Belum Dibayar</p>
                    <div class="w-9 h-9 rounded-2xl bg-cafe-warning/15 text-cafe-warning flex items-center justify-center"><i data-lucide="clock" class="w-4 h-4"></i></div>
                </div>
                <p class="font-serif text-xl sm:text-2xl font-bold text-cafe-dark mt-2">{{ $summary['unpaid_count'] }}</p>
                <p class="text-[11px] text-cafe-muted mt-1">pesanan menunggu pembayaran</p>
            </div>

            <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 p-5 shadow-soft">
                <div class="flex items-center justify-between">
                    <p class="text-[11px] font-semibold text-cafe-muted uppercase tracking-wide">Sedang Diproses</p>
                    <div class="w-9 h-9 rounded-2xl bg-cafe-info/15 text-cafe-info flex items-center justify-center"><i data-lucide="utensils" class="w-4 h-4"></i></div>
                </div>
                <p class="font-serif text-xl sm:text-2xl font-bold text-cafe-dark mt-2">{{ $summary['active_count'] }}</p>
                <p class="text-[11px] text-cafe-muted mt-1">pesanan aktif di dapur</p>
            </div>
        </div>
    @endif

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
                                <div class="w-10 h-10 rounded-2xl bg-white border border-cafe-beige flex items-center justify-center text-cafe-gold shrink-0">
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
                                <form action="{{ route('kasir.orders.accept', $order) }}" method="POST">
                                    @csrf
                                    <button class="mt-1 px-3.5 py-1.5 rounded-full bg-cafe-success text-white text-[11px] font-semibold hover:bg-cafe-success/90 transition-colors">
                                        <i data-lucide="check" class="w-3 h-3 inline-block -mt-0.5"></i> Terima
                                    </button>
                                </form>
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
                            <div class="h-10 rounded-xl {{ $table->status === 'terpakai' ? 'bg-cafe-danger text-white' : 'bg-cafe-warning text-white' }} flex items-center justify-center font-serif font-bold">
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
                                <p class="text-[11px] text-cafe-muted truncate">{{ substr($reservation->reservation_time, 0, 5) }} &bull; {{ $reservation->party_size }} orang {{ $reservation->table ? '&bull; Meja '.$reservation->table->number : '' }}</p>
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