@extends('kasir.layouts.app')

@section('title', 'Dashboard Shift - Kasir')
@section('page_title', 'Dashboard Shift')

@section('content')
<div class="space-y-6">
    <!-- Banner Shift -->
    @if(! $shift)
        <div class="bg-[#290024] text-[#F2F5E2] rounded-3xl p-6 sm:p-8 relative overflow-hidden shadow-soft-lg">
            <div class="absolute inset-0 opacity-20 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=900&auto=format&fit=crop&q=80')"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-[#290024] via-[#290024]/90 to-transparent"></div>
            <div class="relative z-10 max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 text-[#D4954D] text-xs font-medium border border-[#D4954D]/20">
                    <i data-lucide="wallet" class="w-3.5 h-3.5"></i>
                    <span>Shift Belum Dibuka</span>
                </div>
                <h2 class="font-serif text-2xl sm:text-3xl font-bold mt-4">Mulai hari kerja Anda, <span class="text-[#D4954D] italic">{{ Auth::user()->name }}</span>!</h2>
                <p class="text-xs sm:text-sm text-[#F2F5E2]/80 mt-2 max-w-lg leading-relaxed">
                    Transaksi kasir hanya bisa diproses setelah shift dibuka. Siapkan kas awal, lalu mulai menerima pesanan & pembayaran.
                </p>
                <a href="{{ route('kasir.shift.open') }}" class="mt-6 inline-flex items-center gap-2 px-6 py-3 rounded-full bg-[#D4954D] text-[#290024] font-bold text-xs sm:text-sm shadow-md hover:bg-[#B87C3C] transition-colors">
                    <i data-lucide="play" class="w-4 h-4"></i>
                    Buka Shift Sekarang
                </a>
            </div>
        </div>
    @endif

    <!-- Stat Cards -->
    @if($shift && $summary)
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
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