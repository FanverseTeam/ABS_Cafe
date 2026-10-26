@extends('kasir.layouts.app')

@section('title', 'Mulai Shift - Kasir')
@section('page_title', 'Mulai Shift')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft-lg overflow-hidden">
        <div class="bg-[#290024] text-[#F2F5E2] p-6 sm:p-8 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-tr from-[#D4954D]/20 to-transparent"></div>
            <div class="relative z-10">
                <div class="w-12 h-12 rounded-2xl bg-[#D4954D]/20 text-[#D4954D] flex items-center justify-center border border-[#D4954D]/30">
                    <i data-lucide="play" class="w-5 h-5"></i>
                </div>
                <h2 class="font-serif text-2xl font-bold mt-4">Mulai Shift Kasir</h2>
                <p class="text-xs text-[#F2F5E2]/70 mt-1 leading-relaxed">
                    Kas awal diisi otomatis dari laci kas. Anda dapat langsung mulai melayani pesanan pelanggan.
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
                    <p class="text-[11px] text-cafe-muted">Shift dimulai: {{ now()->translatedFormat('l, d M Y • H:i') }} WIB</p>
                </div>
            </div>

            <!-- Kas awal otomatis (read-only) -->
            <div class="p-5 rounded-2xl bg-cafe-cream/80 border border-cafe-beige/80 space-y-2">
                <label class="block text-xs font-semibold text-cafe-muted uppercase tracking-wider">Kas Awal di Laci (Otomatis)</label>
                <div class="font-serif text-3xl font-bold text-cafe-dark">
                    Rp {{ number_format($openingCash, 0, ',', '.') }}
                </div>
                <div class="flex items-start gap-2 pt-1 text-xs text-cafe-brown bg-cafe-beige/35 p-3 rounded-xl border border-cafe-beige/60">
                    <i data-lucide="info" class="w-4 h-4 text-cafe-gold shrink-0 mt-0.5"></i>
                    <span class="leading-relaxed">{{ $sourceText }}</span>
                </div>
            </div>

            <!-- Catatan -->
            <div>
                <label class="block text-xs font-semibold text-cafe-text mb-2">Catatan Shift (opsional)</label>
                <textarea name="notes" rows="2" placeholder="Misal: shift pagi, persiapan buka cafe" class="w-full bg-[#E3DEA4]/50 focus:bg-beige text-xs text-cafe-text placeholder:text-cafe-muted/60 px-4 py-3 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none focus:ring-2 focus:ring-cafe-gold/20 transition-all resize-none">{{ old('notes') }}</textarea>
                @error('notes')<p class="text-[11px] text-cafe-danger mt-1.5">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-full bg-cafe-dark text-beige font-semibold text-xs sm:text-sm shadow-md hover:bg-cafe-brown transition-colors">
                    <i data-lucide="play" class="w-4 h-4"></i>
                    Mulai Shift
                </button>
                <a href="{{ route('kasir.dashboard') }}" class="px-5 py-3.5 rounded-full bg-cafe-beige/60 text-cafe-brown text-xs font-semibold hover:bg-cafe-beige transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection