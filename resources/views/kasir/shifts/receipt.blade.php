@extends('kasir.layouts.app')

@section('title', 'Rekap Kas - Shift #'.$shift->id)
@section('page_title', 'Rekap Kas Shift')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft p-5 mb-6">
        <div>
            <h3 class="font-serif font-bold text-cafe-dark">Rekap Kas Shift</h3>
            <p class="text-[11px] text-cafe-muted">{{ $shift->opened_at->translatedFormat('l, d M Y') }} &bull; {{ $shift->user->name ?? Auth::user()->name }}</p>
        </div>
        <button onclick="window.print()" class="inline-flex items-center gap-2 px-5 py-3 rounded-full bg-cafe-dark text-white text-xs font-semibold hover:bg-cafe-brown transition-colors">
            <i data-lucide="printer" class="w-4 h-4"></i>
            Cetak Rekap
        </button>
    </div>

    <div id="printable-receipt" class="bg-white text-black mx-auto rounded-none" style="width:80mm">
        <div class="text-center px-4 pt-6 pb-4 border-b border-black/20">
            <h1 class="font-serif font-bold text-sm">{{ \App\Models\CafeSetting::get('cafe_name', 'ABS Cafe') }}</h1>
            <p class="text-[10px] mt-1 leading-tight">{{ \App\Models\CafeSetting::get('address', '') }}</p>
            <p class="text-[10px]">{{ \App\Models\CafeSetting::get('phone', '') }}</p>
            <p class="text-[10px] mt-2">REKAP KAS SHIFT</p>
        </div>

        <div class="px-4 py-3 text-[11px] space-y-1">
            <div class="flex justify-between"><span>Kasir</span><span class="font-semibold">{{ $shift->user->name ?? Auth::user()->name }}</span></div>
            <div class="flex justify-between"><span>Dibuka</span><span>{{ $shift->opened_at->format('d/m/Y H:i') }}</span></div>
            <div class="flex justify-between"><span>Ditutup</span><span>{{ $shift->closed_at?->format('d/m/Y H:i') }}</span></div>
            @if($shift->notes)
                <div class="flex justify-between"><span>Catatan</span><span class="max-w-[45mm] text-right">{{ $shift->notes }}</span></div>
            @endif
        </div>

        <div class="px-4 py-3 border-t border-dashed border-black/30 text-[11px]">
            <p class="font-bold text-center mb-2">RINCIAN KAS</p>
            <div class="space-y-1">
                <div class="flex justify-between"><span>Kas Awal</span><span>Rp {{ number_format($shift->opening_cash, 0, ',', '.') }}</span></div>
                @foreach($summary['methods'] as $m)
                    <div class="flex justify-between"><span>{{ $m['label'] }} ({{ $m['count'] }}x)</span><span>Rp {{ number_format($m['total'], 0, ',', '.') }}</span></div>
                @endforeach
                <div class="flex justify-between"><span>Kas Masuk</span><span>Rp {{ number_format($summary['cash_in'], 0, ',', '.') }}</span></div>
                <div class="flex justify-between"><span>Kas Keluar</span><span>-Rp {{ number_format($summary['cash_out'], 0, ',', '.') }}</span></div>
                <div class="flex justify-between font-bold border-t border-black/20 pt-1 mt-1"><span>Total Transaksi</span><span>{{ $summary['orders_count'] }}</span></div>
                <div class="flex justify-between border-t border-black/20 pt-1"><span>Penjualan</span><span class="font-bold">Rp {{ number_format($summary['sales'], 0, ',', '.') }}</span></div>
                <div class="flex justify-between"><span>Kas Diharapkan</span><span class="font-bold">Rp {{ number_format($shift->expected_cash, 0, ',', '.') }}</span></div>
                <div class="flex justify-between"><span>Kas Fisik</span><span>Rp {{ number_format($shift->closing_cash, 0, ',', '.') }}</span></div>
                <div class="flex justify-between {{ $shift->cash_difference < 0 ? 'text-red-600' : 'text-green-700' }}">
                    <span>Selisih</span>
                    <span class="font-bold">{{ $shift->cash_difference > 0 ? '+' : '' }}{{ number_format($shift->cash_difference, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        @if($movements->isNotEmpty())
        <div class="px-4 py-3 border-t border-dashed border-black/30 text-[11px]">
            <p class="font-bold text-center mb-2">CATATAN KAS</p>
            <div class="space-y-1">
                @foreach($movements as $rec)
                    <div class="flex justify-between gap-2">
                        <span class="min-w-0">{{ $rec->reason }}</span>
                        <span class="shrink-0">{{ $rec->type === 'masuk' ? '+' : '-' }}Rp {{ number_format($rec->amount, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="px-4 py-6 text-center border-t border-black/20 text-[10px]">
            <p>Terima kasih &bull; {{ now()->format('d/m/Y H:i') }}</p>
        </div>
    </div>
</div>
@endsection