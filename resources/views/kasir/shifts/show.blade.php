@extends('kasir.layouts.app')

@section('title', 'Detail Shift #'.$shift->id.' - Kasir')
@section('page_title', 'Detail Rekap Shift')

@section('content')
<div class="space-y-6">
    <!-- Header Summary -->
    <div class="bg-[#290024] text-[#F2F5E2] rounded-3xl p-6 sm:p-8 relative overflow-hidden shadow-soft-lg">
        <div class="absolute inset-0 bg-gradient-to-tr from-[#D4954D]/20 to-transparent"></div>
        <div class="relative z-10 flex flex-col gap-6">
            <div class="flex flex-col sm:flex-row items-start justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 text-[#D4954D] text-xs font-medium border border-[#D4954D]/20">
                        <i data-lucide="wallet" class="w-3.5 h-3.5"></i>
                        Shift #{{ $shift->id }} &bull; {{ $shift->status === 'tutup' ? 'Selesai' : 'Berlangsung' }}
                    </div>
                    <h2 class="font-serif text-2xl font-bold mt-4">{{ $shift->opened_at->translatedFormat('l, d M Y') }}</h2>
                    <p class="text-xs text-[#F2F5E2]/70 mt-1">
                        {{ $shift->opened_at->format('H:i') }} sampai {{ $shift->closed_at?->format('H:i') }} &bull; Kasir {{ $shift->user->name ?? '-' }}
                    </p>
                </div>
                <a href="{{ route('kasir.shift.receipt', $shift) }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-full bg-white/10 text-[#F2F5E2] text-xs font-semibold border border-white/20 hover:bg-white/20 transition-colors shrink-0">
                    <i data-lucide="printer" class="w-4 h-4"></i>
                    Lihat / Cetak Rekap
                </a>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-5 gap-3">
                <div class="p-4 rounded-2xl bg-white/10 border border-white/10 text-center">
                    <p class="text-[10px] text-[#F2F5E2]/70 uppercase tracking-wide">Transaksi</p>
                    <p class="font-serif text-xl font-bold text-[#D4954D] mt-1">{{ $summary['orders_count'] }}</p>
                </div>
                <div class="p-4 rounded-2xl bg-white/10 border border-white/10 text-center">
                    <p class="text-[10px] text-[#F2F5E2]/70 uppercase tracking-wide">Penjualan</p>
                    <p class="font-serif text-xl font-bold text-[#D4954D] mt-1">Rp {{ number_format($summary['sales'], 0, ',', '.') }}</p>
                </div>
                <div class="p-4 rounded-2xl bg-white/10 border border-white/10 text-center">
                    <p class="text-[10px] text-[#F2F5E2]/70 uppercase tracking-wide">Kas Diharapkan</p>
                    <p class="font-serif text-xl font-bold text-[#D4954D] mt-1">Rp {{ number_format($shift->expected_cash, 0, ',', '.') }}</p>
                </div>
                <div class="p-4 rounded-2xl bg-white/10 border border-white/10 text-center">
                    <p class="text-[10px] text-[#F2F5E2]/70 uppercase tracking-wide">Kas Fisik</p>
                    <p class="font-serif text-xl font-bold text-[#D4954D] mt-1">Rp {{ number_format($shift->closing_cash, 0, ',', '.') }}</p>
                </div>
                <div class="p-4 rounded-2xl bg-white/10 border border-white/10 text-center">
                    <p class="text-[10px] text-[#F2F5E2]/70 uppercase tracking-wide">Selisih</p>
                    <p class="font-serif text-xl font-bold mt-1 {{ $shift->cash_difference < 0 ? 'text-[#E58B80]' : 'text-[#A8C39B]' }}">
                        {{ $shift->cash_difference > 0 ? '+' : '' }}{{ number_format($shift->cash_difference, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Pesanan dalam shift -->
        <div class="lg:col-span-2 bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft overflow-hidden">
            <div class="px-6 py-4 border-b border-cafe-beige/50">
                <h3 class="font-serif font-bold text-sm text-cafe-dark">Pesanan pada Shift Ini</h3>
            </div>
            <div class="p-5">
                <x-responsive-table>
                    <x-slot:headers>
                        <th class="py-2.5 pr-4 font-semibold text-left">Kode</th>
                        <th class="py-2.5 pr-4 font-semibold text-left">Pelanggan</th>
                        <th class="py-2.5 pr-4 font-semibold text-left">Metode</th>
                        <th class="py-2.5 pr-4 font-semibold text-left">Status</th>
                        <th class="py-2.5 pr-4 font-semibold text-right">Total</th>
                    </x-slot:headers>

                    @forelse($orders as $order)
                        <x-responsive-table.row>
                            <x-slot:title>
                                {{ $order->order_code }}
                            </x-slot:title>

                            <x-slot:subtitle>
                                {{ $order->customer_name }}
                            </x-slot:subtitle>

                            <x-slot:badge>
                                @php $badge = $order->paymentStatusBadge(); @endphp
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-cafe-{{ $badge['color'] }}/15 text-cafe-{{ $badge['color'] }}">{{ $badge['label'] }}</span>
                            </x-slot:badge>

                            <x-slot:fields>
                                <x-responsive-table.field label="Metode" :value="$order->paymentMethodLabel()" />
                                <x-responsive-table.field label="Total" :value="'Rp ' . number_format($order->total, 0, ',', '.')" is-highlight="true" />
                            </x-slot:fields>

                            <x-slot:desktop>
                                <td class="hidden md:table-cell py-3 pr-4 font-semibold text-cafe-dark">{{ $order->order_code }}</td>
                                <td class="hidden md:table-cell py-3 pr-4 text-cafe-muted">{{ $order->customer_name }}</td>
                                <td class="hidden md:table-cell py-3 pr-4">{{ $order->paymentMethodLabel() }}</td>
                                <td class="hidden md:table-cell py-3 pr-4">
                                    <span class="text-[10px] font-bold px-2 py-1 rounded-full bg-cafe-{{ $badge['color'] }}/15 text-cafe-{{ $badge['color'] }}">{{ $badge['label'] }}</span>
                                </td>
                                <td class="hidden md:table-cell py-3 text-right font-serif font-bold text-cafe-dark">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                            </x-slot:desktop>
                        </x-responsive-table.row>
                    @empty
                        <x-responsive-table.empty col-span="5" message="Belum ada pesanan pada shift ini." />
                    @endforelse
                </x-responsive-table>
            </div>
        </div>

        <!-- Catatan Kas -->
        <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft overflow-hidden">
            <div class="px-6 py-4 border-b border-cafe-beige/50">
                <h3 class="font-serif font-bold text-sm text-cafe-dark">Catatan Kas</h3>
            </div>
            <div class="p-5">
                @if($movements->isEmpty())
                    <div class="py-8 text-center">
                        <div class="w-14 h-14 mx-auto rounded-full bg-cafe-cream flex items-center justify-center text-cafe-muted">
                            <i data-lucide="list-checks" class="w-6 h-6"></i>
                        </div>
                        <p class="text-xs text-cafe-muted mt-3">Tidak ada catatan kas masuk/keluar pada shift ini.</p>
                    </div>
                @else
                    <div class="divide-y divide-cafe-beige/30">
                        @foreach($movements as $rec)
                            <div class="py-3 flex items-center justify-between gap-3">
                                <div class="min-w-0 flex items-start gap-2.5">
                                    <i data-lucide="{{ $rec->type === 'masuk' ? 'download' : 'upload' }}" class="w-4 h-4 mt-0.5 {{ $rec->type === 'masuk' ? 'text-cafe-info' : 'text-cafe-warning' }}"></i>
                                    <div class="min-w-0">
                                        <p class="text-xs font-semibold text-cafe-dark">{{ $rec->reason }}</p>
                                        <p class="text-[10px] text-cafe-muted">{{ $rec->user->name ?? '-' }} &bull; {{ $rec->created_at->format('d/m/y H:i') }}</p>
                                    </div>
                                </div>
                                <span class="text-xs font-bold shrink-0 {{ $rec->type === 'masuk' ? 'text-cafe-info' : 'text-cafe-warning' }}">
                                    {{ $rec->type === 'masuk' ? '+' : '-' }}Rp {{ number_format($rec->amount, 0, ',', '.') }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="flex items-center justify-end">
        <a href="{{ route('kasir.shift.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-cafe-beige/60 text-cafe-brown text-xs font-semibold hover:bg-cafe-beige transition-colors">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
            Kembali ke Riwayat Shift
        </a>
    </div>
</div>
@endsection