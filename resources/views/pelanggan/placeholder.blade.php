@extends('pelanggan.layouts.app')

@section('title', $title . ' - ' . ($landing['cafe']['name'] ?? 'ABS Cafe'))

@section('body_class', 'bg-[#F2F5E2] text-[#290024]')
@section('body_style', 'background-color: #F2F5E2 !important; color: #290024 !important;')
@section('main_class', 'bg-[#F2F5E2]')

@section('content')
<section class="min-h-[75vh] flex items-center justify-center pt-24 pb-16 px-4 sm:px-6 bg-[#F2F5E2]" style="background-color: #F2F5E2 !important;">
    <div class="max-w-md w-full mx-auto text-center p-8 rounded-3xl bg-[#F2F5E2] border border-[#E3DEA4] shadow-soft">
        <!-- Badge -->
        <span class="inline-flex items-center px-3.5 py-1 rounded-full text-[11px] font-bold tracking-widest uppercase bg-[#E3DEA4]/40 text-[#775533] border border-[#E3DEA4] mb-4">
            Segera Hadir
        </span>

        <!-- Judul -->
        <h1 class="font-serif text-2xl sm:text-3xl font-bold text-[#290024] mb-3">
            {{ $title }}
        </h1>

        <!-- Subtitle -->
        <p class="text-sm text-[#775533] leading-relaxed mb-8">
            {{ $subtitle }}
        </p>

        <!-- Tombol Kembali ke Beranda -->
        <a href="{{ route('pelanggan.beranda') }}"
           class="inline-flex items-center justify-center gap-2 min-h-[44px] px-6 py-2.5 rounded-full bg-[#290024] hover:bg-[#D4954D] hover:text-[#290024] text-[#F2F5E2] font-semibold text-xs sm:text-sm tracking-wide transition-all duration-200 shadow-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#290024]">
            <svg class="w-4 h-4 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            <span>Kembali ke Beranda</span>
        </a>
    </div>
</section>
@endsection

@section('footer')
<footer class="bg-[#F2F5E2] border-t border-[#E3DEA4]/80 py-6 sm:py-8 text-center text-xs text-[#775533]" style="background-color: #F2F5E2 !important;">
    <div class="max-w-4xl mx-auto px-4 space-y-1.5">
        <p class="font-serif font-bold text-[#290024]">{{ $landing['cafe']['name'] ?? 'ABS Cafe' }}</p>
        <p class="text-[11px] text-[#775533]/80">{{ $landing['cafe']['tagline'] ?? 'Good Coffee, Better Moments.' }}</p>
        <p class="text-[10px] text-[#775533]/60">&copy; {{ date('Y') }} Hak cipta dilindungi.</p>
    </div>
</footer>
@endsection
