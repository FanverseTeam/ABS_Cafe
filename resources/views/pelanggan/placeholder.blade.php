@extends('pelanggan.layouts.app')

@section('title', $title . ' - ' . ($landing['cafe']['name'] ?? 'ABS Cafe'))

@section('content')
<section class="min-h-[75vh] flex items-center justify-center pt-28 pb-16 px-4 sm:px-6">
    <div class="max-w-md w-full mx-auto text-center p-8 rounded-3xl bg-[#775533]/15 border border-[#D4954D]/25 backdrop-blur-md shadow-[0_8px_32px_rgba(41,0,36,0.35)]">
        <!-- Badge -->
        <span class="inline-flex items-center px-3.5 py-1 rounded-full text-[11px] font-bold tracking-widest uppercase bg-[#D4954D]/20 text-[#D4954D] border border-[#D4954D]/40 mb-4">
            Segera Hadir
        </span>

        <!-- Judul -->
        <h1 class="font-serif text-2xl sm:text-3xl font-bold text-[#F2F5E2] mb-3">
            {{ $title }}
        </h1>

        <!-- Subtitle -->
        <p class="text-sm text-[#E3DEA4] leading-relaxed mb-8">
            {{ $subtitle }}
        </p>

        <!-- Tombol Kembali ke Beranda -->
        <a href="{{ route('pelanggan.beranda') }}"
           class="inline-flex items-center justify-center gap-2 min-h-[44px] px-6 py-2.5 rounded-full bg-[#D4954D] hover:bg-[#D4954D] text-[#290024] font-semibold text-xs sm:text-sm tracking-wide transition-all duration-200 shadow-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#F2F5E2]">
            <svg class="w-4 h-4 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            <span>Kembali ke Beranda</span>
        </a>
    </div>
</section>
@endsection
