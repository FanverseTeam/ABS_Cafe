@extends('kasir.layouts.app')

@section('title', 'Buka Shift - Kasir')
@section('page_title', 'Buka Shift')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft-lg overflow-hidden">
        <div class="bg-[#290024] text-[#F2F5E2] p-6 sm:p-8 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-tr from-[#D4954D]/20 to-transparent"></div>
            <div class="relative z-10">
                <div class="w-12 h-12 rounded-2xl bg-[#D4954D]/20 text-[#D4954D] flex items-center justify-center border border-[#D4954D]/30">
                    <i data-lucide="play" class="w-5 h-5"></i>
                </div>
                <h2 class="font-serif text-2xl font-bold mt-4">Buka Shift Baru</h2>
                <p class="text-xs text-[#F2F5E2]/70 mt-1 leading-relaxed">
                    Isi jumlah kas awal di laci. Anda dapat mencatat pengeluaran/pemasukan kas tambahan selama shift berjalan.
                </p>
            </div>
        </div>

        <form action="{{ route('kasir.shift.open.store') }}" method="POST" class="p-6 sm:p-8 space-y-6">
            @csrf

            <!-- Info kasir -->
            <div class="flex items-center gap-3 p-4 rounded-2xl bg-cafe-cream border border-cafe-beige/60">
                <div class="w-10 h-10 rounded-full bg-cafe-gold text-cafe-dark font-serif font-bold flex items-center justify-center text-sm">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div>
                    <p class="text-xs font-bold text-cafe-dark">{{ Auth::user()->name }}</p>
                    <p class="text-[11px] text-cafe-muted">Shift dimulai hari ini, {{ now()->translatedFormat('l, d M Y • H:i') }} WIB</p>
                </div>
            </div>

            <!-- Kas awal -->
            <div>
                <label class="block text-xs font-semibold text-cafe-text mb-2">Jumlah Kas Awal (Rp)</label>
                <div class="relative">
                    <i data-lucide="banknote" class="w-4 h-4 text-cafe-muted absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                    <input type="number" name="opening_cash" id="opening_cash" value="{{ old('opening_cash', 100000) }}" step="1000" min="0" required
                        class="w-full bg-[#E3DEA4]/50 focus:bg-white text-lg font-serif font-bold text-cafe-dark pl-11 pr-4 py-3.5 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none focus:ring-2 focus:ring-cafe-gold/20 transition-all">
                </div>
                @error('opening_cash')<p class="text-[11px] text-cafe-danger mt-1.5">{{ $message }}</p>@enderror
            </div>

            <!-- Quick amounts -->
            <div class="flex flex-wrap gap-2">
                @foreach([100000, 200000, 300000, 500000] as $amount)
                    <button type="button" onclick="document.getElementById('opening_cash').value = {{ $amount }}"
                        class="px-4 py-2 rounded-full bg-cafe-beige/50 border border-cafe-beige text-[11px] font-semibold text-cafe-brown hover:bg-cafe-gold/20 hover:border-cafe-gold transition-colors">
                        Rp {{ number_format($amount, 0, ',', '.') }}
                    </button>
                @endforeach
            </div>

            <!-- Catatan -->
            <div>
                <label class="block text-xs font-semibold text-cafe-text mb-2">Catatan (opsional)</label>
                <textarea name="notes" rows="2" placeholder="Misal: shift pagi, persiapan bahan" class="w-full bg-[#E3DEA4]/50 focus:bg-white text-xs text-cafe-text placeholder:text-cafe-muted/60 px-4 py-3 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none focus:ring-2 focus:ring-cafe-gold/20 transition-all resize-none">{{ old('notes') }}</textarea>
                @error('notes')<p class="text-[11px] text-cafe-danger mt-1.5">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-full bg-cafe-dark text-white font-semibold text-xs sm:text-sm shadow-md hover:bg-cafe-brown transition-colors">
                    <i data-lucide="play" class="w-4 h-4"></i>
                    Buka Shift Sekarang
                </button>
                <a href="{{ route('kasir.dashboard') }}" class="px-5 py-3.5 rounded-full bg-cafe-beige/60 text-cafe-brown text-xs font-semibold hover:bg-cafe-beige transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection