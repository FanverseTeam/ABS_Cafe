@extends('pelanggan.layouts.app')

@section('title', ($landing['cafe']['name'] ?? 'ABS Cafe') . ' - ' . ($landing['cafe']['tagline'] ?? 'Good Coffee, Better Moments.'))

@section('body_class', 'bg-[#000000] text-[#F2F5E2]')
@section('body_style', 'background-color: #000000 !important; color: #F2F5E2 !important;')

@section('content')
@php
    $introLogo = \App\Models\CafeSetting::get('cafe_logo', 'images/logo.png');
    $introLogoUrl = \Illuminate\Support\Str::startsWith($introLogo, ['http://', 'https://']) ? $introLogo : asset(ltrim($introLogo, '/'));
@endphp
<style>
    @keyframes absIntroLogoIn {
        0% {
            opacity: 0;
            transform: scale(0.78);
        }
        70% {
            opacity: 1;
            transform: scale(1.04);
        }
        100% {
            opacity: 1;
            transform: scale(1);
        }
    }

    @keyframes absIntroTextIn {
        0% {
            opacity: 0;
            transform: translateY(10px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes absIntroRingDraw {
        0% {
            stroke-dashoffset: 290;
        }
        100% {
            stroke-dashoffset: 0;
        }
    }

    #pelanggan-intro {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: radial-gradient(circle at center, rgba(212, 149, 77, 0.12) 0%, #000000 62%);
        background-color: #000000;
        transition: opacity 0.42s cubic-bezier(0.22, 1, 0.36, 1), transform 0.42s cubic-bezier(0.22, 1, 0.36, 1);
    }

    #pelanggan-intro.is-leaving {
        opacity: 0;
        transform: scale(1.03);
        pointer-events: none;
    }

    #pelanggan-intro .abs-intro-logo {
        animation: absIntroLogoIn 0.5s cubic-bezier(0.22, 1, 0.36, 1) both;
    }

    #pelanggan-intro .abs-intro-ring-progress {
        stroke-dasharray: 290;
        stroke-dashoffset: 290;
        animation: absIntroRingDraw 1.85s cubic-bezier(0.22, 1, 0.36, 1) 0.1s forwards;
    }

    #pelanggan-intro .abs-intro-title {
        animation: absIntroTextIn 0.45s cubic-bezier(0.22, 1, 0.36, 1) 0.12s both;
    }
</style>
<div id="pelanggan-intro" aria-hidden="true">
    <div class="abs-intro-logo relative flex items-center justify-center w-28 h-28 sm:w-32 sm:h-32 mb-4">
        <svg class="absolute inset-0 w-full h-full -rotate-90 pointer-events-none" viewBox="0 0 100 100" aria-hidden="true">
            <circle cx="50" cy="50" r="46" fill="none" stroke="rgba(212, 149, 77, 0.18)" stroke-width="2" />
            <circle class="abs-intro-ring-progress" cx="50" cy="50" r="46" fill="none" stroke="#D4954D" stroke-width="2.5" stroke-linecap="round" />
        </svg>
        <img src="{{ $introLogoUrl }}"
             alt="ABS Cafe"
             class="w-16 h-16 sm:w-20 sm:h-20 object-contain">
    </div>
    <div class="abs-intro-title text-center">
        <p class="font-serif text-2xl sm:text-3xl font-bold tracking-wide text-[#F2F5E2]">
            {{ $landing['cafe']['name'] ?? 'ABS Cafe' }}
        </p>
    </div>
    <div class="abs-intro-title absolute bottom-6 inset-x-0 flex justify-center px-4 opacity-80 scale-90 pointer-events-none">
        <x-team-watermark theme="dark" class="py-0" />
    </div>
</div>
<script>
    (function () {
        var intro = document.getElementById('pelanggan-intro');
        if (!intro) return;

        var reducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        var alreadySeen = false;
        try {
            alreadySeen = sessionStorage.getItem('abs_pelanggan_intro_seen') === '1';
        } catch (e) {}

        if (reducedMotion || alreadySeen) {
            intro.style.display = 'none';
            return;
        }

        var dismissed = false;
        function dismissIntro() {
            if (dismissed) return;
            dismissed = true;
            try {
                sessionStorage.setItem('abs_pelanggan_intro_seen', '1');
            } catch (e) {}
            intro.classList.add('is-leaving');
            setTimeout(function () {
                intro.style.display = 'none';
            }, 440);
        }

        intro.addEventListener('click', dismissIntro);
        setTimeout(dismissIntro, 2000);
    })();
</script>

<!-- ======================================================================= -->
<!-- 1. HERO SECTION                                                         -->
<!-- ======================================================================= -->
<section id="beranda" class="relative min-h-[84vh] sm:min-h-[92vh] flex items-center justify-center pt-24 sm:pt-28 pb-14 sm:pb-20 overflow-hidden bg-[#000000]">
    <!-- Foto Latar Hero dengan Overlay Gradasi Gelap -->
    <div class="absolute inset-0 z-0">
        @if(!empty($landing['hero']['bg_photo']['src']))
            <img src="{{ $landing['hero']['bg_photo']['src'] }}"
                 alt="{{ $landing['hero']['bg_photo']['alt'] }}"
                 width="{{ $landing['hero']['bg_photo']['width'] }}"
                 height="{{ $landing['hero']['bg_photo']['height'] }}"
                 fetchpriority="high"
                 decoding="async"
                 referrerpolicy="no-referrer"
                 onerror="this.style.display='none'"
                 class="w-full h-full object-cover object-center filter brightness-[0.45] contrast-[1.05] scale-105 transition-transform duration-1000 ease-out motion-reduce:transition-none">
        @endif
        <!-- Gradasi Palet Hitam Overlay -->
        <div class="absolute inset-0 bg-gradient-to-b from-[#000000]/90 via-[#000000]/80 to-[#000000]"></div>
        <div class="absolute inset-0 bg-radial from-transparent via-[#000000]/60 to-[#000000]"></div>
    </div>

    <!-- Konten Hero -->
    <div class="relative z-10 mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 w-full">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 sm:gap-12 items-center">
            <!-- Kolom Teks Hero (Kiri di Desktop, Atas di Mobile) -->
            <div class="lg:col-span-7 flex flex-col items-start text-left reveal-on-scroll">
                <!-- Label / Kicker -->
                <span class="inline-block text-[11px] sm:text-xs font-bold tracking-[0.25em] uppercase text-[#D4954D] mb-3">
                    {{ $landing['hero']['badge'] ?? 'SELAMAT DATANG DI ABS CAFE' }}
                </span>

                <!-- Judul Utama -->
                <h1 class="font-serif text-3xl sm:text-4xl md:text-5xl lg:text-[3.25rem] font-bold text-[#F2F5E2] leading-[1.15] mb-2">
                    {{ $landing['hero']['title_prefix'] ?? 'Temukan Momen Terbaik di' }}
                    <span class="font-script text-[#D4954D] text-4xl sm:text-5xl md:text-6xl lg:text-[4rem] font-normal block mt-1 tracking-normal">
                        {{ $landing['hero']['title_highlight'] ?? 'ABS Cafe' }}
                    </span>
                </h1>

                <!-- Deskripsi Hero -->
                <p class="text-xs sm:text-sm md:text-base text-[#E3DEA4] leading-relaxed max-w-xl my-4">
                    {{ $landing['hero']['description'] ?? 'Nikmati kopi berkualitas, makanan lezat, dan suasana nyaman yang cocok untuk bekerja, belajar, atau sekadar bersantai bersama orang-orang tercinta.' }}
                </p>

                <!-- Tombol Aksi (Pesan Sekarang & Reservasi) -->
                <div class="flex flex-wrap items-center gap-3 sm:gap-4 mt-2">
                    <!-- Tombol Pesan Sekarang -->
                    <a href="{{ route('pelanggan.menu') }}"
                       class="inline-flex items-center justify-center gap-2.5 min-h-[44px] px-6 sm:px-7 py-3 rounded-full bg-[#D4954D] hover:bg-[#D4954D] text-[#290024] font-semibold text-xs sm:text-sm tracking-wide transition-all duration-200 shadow-[0_6px_20px_rgba(212,149,77,0.35)] hover:shadow-[0_8px_25px_rgba(212,149,77,0.45)] hover:-translate-y-0.5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#F2F5E2] motion-reduce:transform-none">
                        <!-- Ikon Sendok-Garpu -->
                        <svg class="w-4 h-4 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M18 2v6a3 3 0 0 1-3 3 3 3 0 0 1-3-3V2"></path>
                            <path d="M15 2v16"></path>
                            <path d="M6 2v4a3 3 0 0 0 3 3 3 3 0 0 0 3-3V2"></path>
                            <path d="M9 2v16"></path>
                        </svg>
                        <span>Pesan Sekarang</span>
                        <!-- Panah Kanan -->
                        <svg class="w-4 h-4 stroke-current ml-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </a>

                    <!-- Tombol Reservasi -->
                    <a href="{{ route('pelanggan.reservasi') }}"
                       class="inline-flex items-center justify-center gap-2.5 min-h-[44px] px-6 sm:px-7 py-3 rounded-full border border-[#D4954D] bg-[#050604] hover:bg-[#D4954D]/15 text-[#F2F5E2] font-semibold text-xs sm:text-sm tracking-wide transition-all duration-200 hover:-translate-y-0.5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#D4954D] motion-reduce:transform-none">
                        <!-- Ikon Kalender -->
                        <svg class="w-4 h-4 stroke-current text-[#D4954D]" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        <span>Reservasi</span>
                        <!-- Panah Kanan -->
                        <svg class="w-4 h-4 stroke-current text-[#D4954D] ml-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Kolom Cangkir Kopi Hero (Kanan di Desktop, Bawah di Mobile) -->
            <div class="lg:col-span-5 flex justify-center lg:justify-end mt-4 lg:mt-0 reveal-on-scroll">
                <div class="relative w-64 h-64 sm:w-80 sm:h-80 md:w-96 md:h-96">
                    <!-- Glow Emas di Belakang Cangkir -->
                    <div class="absolute inset-0 bg-gradient-to-tr from-[#D4954D]/25 via-[#775533]/20 to-transparent rounded-full filter blur-2xl transform scale-90"></div>

                    @if(!empty($landing['hero']['cup_photo']['src']))
                        <div class="relative w-full h-full rounded-3xl overflow-hidden p-2">
                            <img src="{{ $landing['hero']['cup_photo']['src'] }}"
                                 alt="{{ $landing['hero']['cup_photo']['alt'] }}"
                                 width="{{ $landing['hero']['cup_photo']['width'] }}"
                                 height="{{ $landing['hero']['cup_photo']['height'] }}"
                                 loading="eager"
                                 decoding="async"
                                 referrerpolicy="no-referrer"
                                 onerror="this.style.display='none'"
                                 class="w-full h-full object-cover rounded-2xl shadow-[0_16px_40px_rgba(0,0,0,0.6)] border border-[#A09B90]/40 filter drop-shadow-2xl">
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ======================================================================= -->
<!-- 2. TENTANG KAMI SECTION                                                 -->
<!-- ======================================================================= -->
<section id="tentang-kami" class="relative py-16 sm:py-24 bg-[#000000] overflow-hidden">
    <!-- Ornamen Daun Garis Halus di Latar Belakang (Inline SVG) -->
    <div class="pointer-events-none absolute -top-10 -left-10 w-48 h-48 opacity-15 text-[#D4954D]" aria-hidden="true">
        <svg viewBox="0 0 160 160" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20 140 Q 60 80 120 30" />
            <path d="M120 30 Q 140 50 120 70 Q 100 50 120 30 Z" />
            <path d="M85 65 Q 110 75 95 95 Q 75 80 85 65 Z" />
            <path d="M55 95 Q 75 105 65 125 Q 45 110 55 95 Z" />
        </svg>
    </div>

    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 sm:gap-14 items-center">
            <!-- Teks Tentang Kami (Kiri) -->
            <div class="lg:col-span-6 flex flex-col items-start text-left reveal-on-scroll">
                <!-- Label / Kicker -->
                <span class="inline-block text-[11px] sm:text-xs font-bold tracking-[0.25em] uppercase text-[#D4954D] mb-2">
                    {{ $landing['about']['badge'] ?? 'TENTANG KAMI' }}
                </span>

                <!-- Judul Besar -->
                <h2 class="font-serif text-3xl sm:text-4xl md:text-5xl font-bold text-[#F2F5E2] leading-tight mb-4">
                    {{ $landing['about']['title'] ?? 'ABS Cafe' }}
                </h2>

                <!-- Deskripsi Paragraf -->
                <p class="text-xs sm:text-sm md:text-base text-[#E3DEA4] leading-relaxed mb-6">
                    {{ $landing['about']['description'] ?? 'ABS Cafe adalah tempat yang dirancang untuk memberikan pengalaman menikmati kopi terbaik dengan suasana yang hangat dan nyaman. Kami percaya, setiap cangkir kopi bisa menjadi awal dari cerita yang indah.' }}
                </p>

                <!-- Tagline Tulisan Tangan Script & Ornamen Daun Garis -->
                <div class="flex items-center gap-4 mt-2">
                    <p class="font-script text-[#D4954D] text-3xl sm:text-4xl md:text-5xl leading-tight">
                        {{ $landing['about']['tagline'] ?? 'Good Coffee, Better Moments' }}
                    </p>
                    <div class="w-16 sm:w-24 h-12 text-[#D4954D] shrink-0 opacity-75" aria-hidden="true">
                        <svg viewBox="0 0 100 50" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="w-full h-full">
                            <path d="M5 45 Q 45 35 90 10" />
                            <path d="M90 10 Q 98 22 88 30 Q 78 20 90 10 Z" />
                            <path d="M55 24 Q 70 30 62 40 Q 48 34 55 24 Z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Foto Bingkai Organik (Kanan) -->
            <div class="lg:col-span-6 flex justify-center lg:justify-end reveal-on-scroll">
                <div class="relative w-full max-w-md sm:max-w-lg aspect-[4/3] sm:aspect-[5/4]">
                    <!-- Bingkai Daun Botani di Sisi Kanan Atas -->
                    <div class="pointer-events-none absolute -top-8 -right-8 w-28 h-28 sm:w-36 sm:h-36 text-[#D4954D] opacity-80 z-20" aria-hidden="true">
                        <svg viewBox="0 0 140 140" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="w-full h-full">
                            <path d="M130 10 Q 70 50 10 130" />
                            <path d="M130 10 Q 140 35 115 45 Q 105 20 130 10 Z" />
                            <path d="M95 35 Q 115 55 90 70 Q 75 45 95 35 Z" />
                            <path d="M60 65 Q 85 80 65 100 Q 45 80 60 65 Z" />
                        </svg>
                    </div>

                    <!-- Foto Kafe dengan Bingkai Organik Melengkung Asimetris -->
                    <div class="relative w-full h-full overflow-hidden border border-[#A09B90]/60 shadow-[0_16px_50px_rgba(41,0,36,0.7)]"
                         style="border-radius: 65% 35% 55% 45% / 45% 55% 45% 55%;">
                        @if(!empty($landing['about']['photo']['src']))
                            <img src="{{ $landing['about']['photo']['src'] }}"
                                 alt="{{ $landing['about']['photo']['alt'] }}"
                                 width="{{ $landing['about']['photo']['width'] }}"
                                 height="{{ $landing['about']['photo']['height'] }}"
                                 loading="lazy"
                                 decoding="async"
                                 referrerpolicy="no-referrer"
                                 onerror="this.style.display='none'"
                                 class="w-full h-full object-cover filter brightness-[0.85] contrast-[1.05] hover:scale-105 transition-transform duration-700 ease-out motion-reduce:transition-none">
                        @else
                            <div class="w-full h-full bg-gradient-to-tr from-[#290024] to-[#775533]/40"></div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-[#290024]/60 via-transparent to-transparent pointer-events-none"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ======================================================================= -->
<!-- 3. EMPAT KEUNGGULAN SECTION                                             -->
<!-- ======================================================================= -->
<section class="relative py-16 sm:py-24 bg-[#000000] border-y border-[#A09B90]/30">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
        <!-- 2x2 di Mobile, 4 Kolom di Tablet & Desktop -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-5">
            @foreach($landing['features'] as $index => $feature)
                <div class="flex flex-col items-center text-center p-5 sm:p-6 rounded-2xl bg-[#050604] border border-[#A09B90]/30 reveal-on-scroll">
                    <!-- Ikon Garis dalam Lingkaran Berbingkai Tipis -->
                    <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full border border-[#A09B90]/80 bg-[#050604] flex items-center justify-center text-[#D4954D] mb-3 shadow-[0_4px_16px_rgba(212,149,77,0.15)] group-hover:scale-110 transition-transform">
                        @if($feature['icon'] === 'coffee')
                            <!-- Ikon Kopi -->
                            <svg class="w-6 h-6 sm:w-7 sm:h-7 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M18 8h1a4 4 0 0 1 0 8h-1"></path>
                                <path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"></path>
                                <line x1="6" y1="1" x2="6" y2="4"></line>
                                <line x1="10" y1="1" x2="10" y2="4"></line>
                                <line x1="14" y1="1" x2="14" y2="4"></line>
                            </svg>
                        @elseif($feature['icon'] === 'utensils')
                            <!-- Ikon Menu Lezat / Sendok Garpu -->
                            <svg class="w-6 h-6 sm:w-7 sm:h-7 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M18 2v6a3 3 0 0 1-3 3 3 3 0 0 1-3-3V2"></path>
                                <path d="M15 2v16"></path>
                                <path d="M6 2v4a3 3 0 0 0 3 3 3 3 0 0 0 3-3V2"></path>
                                <path d="M9 2v16"></path>
                            </svg>
                        @elseif($feature['icon'] === 'armchair')
                            <!-- Ikon Suasana Nyaman / Sofa -->
                            <svg class="w-6 h-6 sm:w-7 sm:h-7 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M19 9V6a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v3"></path>
                                <path d="M3 11v5a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-5a2 2 0 0 0-4 0v2H7v-2a2 2 0 0 0-4 0Z"></path>
                                <path d="M5 18v2"></path>
                                <path d="M19 18v2"></path>
                            </svg>
                        @else
                            <!-- Ikon Cocok untuk Semua / Users -->
                            <svg class="w-6 h-6 sm:w-7 sm:h-7 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        @endif
                    </div>

                    <!-- Label Keunggulan -->
                    <span class="font-semibold text-xs sm:text-sm text-[#F2F5E2] tracking-wide">
                        {{ $feature['title'] }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ======================================================================= -->
<!-- 4. GALERI SUASANA SECTION (AUTO-SCROLL FILMSTRIP) -->
<!-- ======================================================================= -->
<section id="suasana" class="relative py-16 sm:py-24 bg-[#000000] overflow-hidden">
    <!-- Header -->
    <div class="relative mx-auto max-w-3xl px-4 sm:px-6 text-center mb-10 sm:mb-14 reveal-on-scroll">
        @if(!empty($landing['gallery']['badge']))
            <span class="inline-block text-[11px] sm:text-xs font-bold tracking-[0.25em] uppercase text-[#D4954D] mb-3">
                {{ $landing['gallery']['badge'] }}
            </span>
        @endif

        <h2 class="font-serif text-3xl sm:text-4xl md:text-5xl font-bold text-[#F2F5E2] leading-tight mb-4 tracking-tight">
            {{ $landing['gallery']['title'] ?? 'Moments Worth Sharing' }}
        </h2>

        <p class="text-xs sm:text-sm md:text-base text-[#E3DEA4] leading-relaxed max-w-xl mx-auto">
            {{ $landing['gallery']['description'] ?? 'From your first sip of coffee to meaningful moments with friends, ABS Cafe is where every visit becomes a memory.' }}
        </p>
    </div>

    @php
        $galleryList = $landing['gallery']['items'] ?? [];
    @endphp

    @if(count($galleryList) > 0)
    <!-- Filmstrip: auto-scroll tanpa henti, dua salinan untuk seamless loop -->
    <div class="gallery-marquee-wrapper overflow-hidden w-full" aria-hidden="true">
        <div class="gallery-marquee-track flex gap-3 sm:gap-4 w-max">
            {{-- Salinan 1 --}}
            @foreach($galleryList as $gItem)
                <div class="gallery-film-card shrink-0 w-[52vw] sm:w-[300px] md:w-[340px] h-[38vw] sm:h-[220px] md:h-[250px] rounded-2xl overflow-hidden bg-[#120010]">
                    <img src="{{ $gItem['image'] }}"
                         alt="{{ $gItem['alt'] ?? ($gItem['title'] ?? 'Suasana ABS Cafe') }}"
                         loading="lazy"
                         decoding="async"
                         class="w-full h-full object-cover object-center pointer-events-none select-none">
                </div>
            @endforeach
            {{-- Salinan 2 (untuk seamless loop) --}}
            @foreach($galleryList as $gItem)
                <div class="gallery-film-card shrink-0 w-[52vw] sm:w-[300px] md:w-[340px] h-[38vw] sm:h-[220px] md:h-[250px] rounded-2xl overflow-hidden bg-[#120010]">
                    <img src="{{ $gItem['image'] }}"
                         alt="{{ $gItem['alt'] ?? ($gItem['title'] ?? 'Suasana ABS Cafe') }}"
                         loading="lazy"
                         decoding="async"
                         class="w-full h-full object-cover object-center pointer-events-none select-none">
                </div>
            @endforeach
        </div>
    </div>
    @endif
</section>

<!-- ======================================================================= -->
<!-- 4a. SIGNATURE 3D DRINK SHOWCASE                                         -->
<!-- ======================================================================= -->
@php
    $oreoImage = asset('images/pelanggan/menu landing/oreo-spanish-latte-3d.png');
    $regalImage = asset('images/pelanggan/menu landing/spanish-regal-latte-3d.png');
    $showcaseDrinks = [
        ['name' => 'Oreo Spanish Latte', 'img' => $oreoImage],
        ['name' => 'Spanish Regal Latte', 'img' => $regalImage],
        ['name' => 'Oreo Spanish Latte', 'img' => $oreoImage],
        ['name' => 'Spanish Regal Latte', 'img' => $regalImage],
        ['name' => 'Oreo Spanish Latte', 'img' => $oreoImage],
        ['name' => 'Spanish Regal Latte', 'img' => $regalImage],
    ];
@endphp
<style>
    .drink-carousel-stage {
        position: relative;
        width: 100%;
        height: 310px;
        perspective: 1100px;
        transform-style: preserve-3d;
        touch-action: pan-y;
        cursor: grab;
        user-select: none;
    }

    .drink-carousel-stage.is-dragging {
        cursor: grabbing;
    }

    @media (min-width: 640px) {
        .drink-carousel-stage {
            height: 370px;
        }
    }

    @media (min-width: 1024px) {
        .drink-carousel-stage {
            height: 400px;
        }
    }

    .drink-carousel-item {
        position: absolute;
        left: 50%;
        top: 50%;
        transform-style: preserve-3d;
        will-change: transform, opacity;
        cursor: pointer;
    }

    .drink-carousel-item.is-active {
        cursor: grab;
    }

    .drink-carousel-stage.is-dragging .drink-carousel-item {
        cursor: grabbing;
    }

    .drink-3d {
        --w: 145px;
        --rx: 0deg;
        --ry: 0deg;
        --sx: 50%;
        position: relative;
        width: var(--w);
        aspect-ratio: 465 / 865;
        perspective: 900px;
        pointer-events: none;
    }

    @media (min-width: 640px) {
        .drink-3d {
            --w: 175px;
        }
    }

    @media (min-width: 1024px) {
        .drink-3d {
            --w: 190px;
        }
    }

    .drink-3d__float {
        position: absolute;
        inset: 0;
    }

    .drink-carousel-item.is-active .drink-3d__float {
        animation: drink-float 4s ease-in-out infinite;
    }

    .drink-3d__tilt {
        position: absolute;
        inset: 0;
        transform: rotateX(var(--rx)) rotateY(var(--ry));
        transform-style: preserve-3d;
        will-change: transform;
    }

    .drink-3d__img {
        width: 100%;
        height: 100%;
        display: block;
        object-fit: contain;
        user-select: none;
        -webkit-user-drag: none;
        filter: drop-shadow(0 18px 22px rgba(0, 0, 0, 0.78));
    }

    /* Efek kilau & gelembung hanya muncul di dalam bentuk gelas */
    .drink-3d__fx {
        position: absolute;
        inset: 0;
        overflow: hidden;
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.35s ease;
        -webkit-mask: var(--img) center / contain no-repeat;
                mask: var(--img) center / contain no-repeat;
    }

    .drink-carousel-item.is-active .drink-3d__fx {
        opacity: 1;
    }

    .drink-3d__sheen {
        position: absolute;
        inset: 0;
        background: linear-gradient(105deg,
            transparent calc(var(--sx) - 18%),
            rgba(255, 255, 255, 0.5) var(--sx),
            transparent calc(var(--sx) + 18%));
        mix-blend-mode: soft-light;
    }

    .drink-3d__bubble {
        position: absolute;
        bottom: 6%;
        width: var(--s);
        height: var(--s);
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.55);
        box-shadow: inset -1px -1px 2px rgba(0, 0, 0, 0.15);
        opacity: 0;
        animation: drink-rise var(--d) linear var(--delay) infinite;
    }

    .drink-3d__shadow {
        position: absolute;
        left: 10%;
        right: 10%;
        bottom: -5%;
        height: 7%;
        border-radius: 50%;
        background: radial-gradient(closest-side, rgba(212, 149, 77, 0.4), rgba(0, 0, 0, 0.65) 55%, transparent);
        opacity: 0.45;
        pointer-events: none;
    }

    .drink-carousel-item.is-active .drink-3d__shadow {
        animation: drink-shadow 4s ease-in-out infinite;
    }

    @keyframes drink-float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-9px); }
    }

    @keyframes drink-shadow {
        0%, 100% { transform: scale(1); opacity: 0.9; }
        50% { transform: scale(0.86); opacity: 0.55; }
    }

    @keyframes drink-rise {
        0% { transform: translate(0, 0); opacity: 0; }
        15% { opacity: 0.8; }
        100% { transform: translate(var(--dx), -330px); opacity: 0; }
    }

    @media (prefers-reduced-motion: reduce) {
        .drink-carousel-item.is-active .drink-3d__float,
        .drink-carousel-item.is-active .drink-3d__shadow,
        .drink-3d__bubble {
            animation: none;
        }
    }
</style>

<section id="signature-showcase" class="relative py-16 sm:py-24 bg-[#000000] border-t border-[#A09B90]/30 overflow-hidden">
    <!-- Studio Spotlight -->
    <div class="pointer-events-none absolute inset-0"
         style="background: radial-gradient(ellipse 65% 58% at 50% 52%, rgba(212, 149, 77, 0.14) 0%, rgba(41, 0, 36, 0.22) 44%, #000000 78%);"></div>

    <div class="relative z-10 mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center max-w-2xl mx-auto mb-6 sm:mb-8 reveal-on-scroll">
            <span class="inline-block text-[11px] sm:text-xs font-bold tracking-[0.25em] uppercase text-[#D4954D] mb-2">
                SIGNATURE SHOWCASE
            </span>
        </div>

        <!-- 3D Swipeable Carousel Stage (Tanpa Panah) -->
        <div id="drink-3d-stage" class="drink-carousel-stage mx-auto">
            @foreach($showcaseDrinks as $idx => $drink)
                <div class="drink-carousel-item"
                     data-drink-index="{{ $idx }}"
                     data-drink-name="{{ $drink['name'] }}">
                    <div class="drink-3d" style="--img: url('{{ $drink['img'] }}');">
                        <div class="drink-3d__shadow" aria-hidden="true"></div>
                        <div class="drink-3d__float">
                            <div class="drink-3d__tilt">
                                <img class="drink-3d__img"
                                     src="{{ $drink['img'] }}"
                                     alt="{{ $drink['name'] }}"
                                     loading="lazy"
                                     draggable="false">
                                <div class="drink-3d__fx" aria-hidden="true">
                                    <div class="drink-3d__sheen"></div>
                                    <span class="drink-3d__bubble" style="left:30%;--s:5px;--d:5s;--delay:0s;--dx:8px"></span>
                                    <span class="drink-3d__bubble" style="left:52%;--s:4px;--d:6.5s;--delay:1.5s;--dx:-10px"></span>
                                    <span class="drink-3d__bubble" style="left:68%;--s:6px;--d:7s;--delay:3s;--dx:6px"></span>
                                    <span class="drink-3d__bubble" style="left:42%;--s:3px;--d:5.5s;--delay:4s;--dx:-6px"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Active Drink Info + Dots + CTA -->
        <div class="text-center mt-4 sm:mt-6 space-y-3">
            <div>
                <h3 id="drink-3d-title" class="font-serif text-xl sm:text-2xl font-bold text-[#F2F5E2]">{{ $showcaseDrinks[2]['name'] ?? 'Oreo Spanish Latte' }}</h3>
            </div>
            <div id="drink-3d-dots" class="flex items-center justify-center gap-2 pt-1">
                @foreach($showcaseDrinks as $idx => $drink)
                    <button type="button"
                            data-dot-index="{{ $idx }}"
                            aria-label="Pilih minuman {{ $idx + 1 }}"
                            class="h-1.5 rounded-full transition-all duration-300 bg-[#A09B90]/40 w-1.5"></button>
                @endforeach
            </div>
            <div class="pt-1">
                <a href="{{ route('pelanggan.menu') }}"
                   class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-[#D4954D] hover:bg-[#E3DEA4] text-[#290024] font-bold text-xs sm:text-sm transition-all shadow-[0_6px_20px_rgba(212,149,77,0.3)] active:scale-95">
                    <span>Lihat Semua Menu</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
            </div>
        </div>
    </div>
</section>

<script>
    (function () {
        var stage = document.getElementById('drink-3d-stage');
        if (!stage) return;

        var items = Array.prototype.slice.call(stage.querySelectorAll('.drink-carousel-item'));
        var dots = Array.prototype.slice.call(document.querySelectorAll('#drink-3d-dots [data-dot-index]'));
        var titleEl = document.getElementById('drink-3d-title');
        var total = items.length;
        if (!total) return;

        var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        var currentPos = Math.floor(total / 2);
        var targetPos = currentPos;

        // State untuk efek 3D tilt gelas aktif
        var tx = 0, ty = 0, cx = 0, cy = 0;
        var isHovering = false;
        var isDragging = false;
        var dragStartX = 0;
        var dragStartPos = 0;
        var dragMoved = false;
        var lastPointerX = 0;
        var velocity = 0;
        var t0 = performance.now();
        var autoTimer = null;

        function mod(n, m) {
            return ((n % m) + m) % m;
        }

        function getSpread() {
            if (window.innerWidth < 640) return 125;
            if (window.innerWidth < 1024) return 170;
            return 205;
        }

        function updateDotsAndInfo(activeIdx) {
            var activeItem = items[activeIdx];
            if (activeItem && titleEl) {
                titleEl.textContent = activeItem.getAttribute('data-drink-name') || '';
            }
            dots.forEach(function (dot, idx) {
                if (idx === activeIdx) {
                    dot.classList.add('w-6', 'bg-[#D4954D]');
                    dot.classList.remove('w-1.5', 'bg-[#A09B90]/40');
                } else {
                    dot.classList.remove('w-6', 'bg-[#D4954D]');
                    dot.classList.add('w-1.5', 'bg-[#A09B90]/40');
                }
            });
        }

        var lastActiveIdx = -1;

        function renderCarousel(now) {
            // Smooth spring/lerp posisi carousel
            if (!isDragging) {
                currentPos += (targetPos - currentPos) * 0.14;
            }

            var spreadX = getSpread();
            var activeIdx = mod(Math.round(currentPos), total);

            if (activeIdx !== lastActiveIdx) {
                lastActiveIdx = activeIdx;
                updateDotsAndInfo(activeIdx);
            }

            // Smooth tilt & sheen untuk gelas aktif
            if (!isHovering && !isDragging && !reduce) {
                var t = (now - t0) / 1000;
                tx = Math.sin(t * 0.8) * 0.45;
                ty = Math.cos(t * 0.6) * 0.15;
            }
            cx += (tx - cx) * 0.1;
            cy += (ty - cy) * 0.1;

            var maxTilt = 16;

            items.forEach(function (item, idx) {
                var offset = idx - currentPos;
                // Wrap melingkar terpendek
                offset = mod(offset + total / 2, total) - total / 2;

                var abs = Math.abs(offset);
                var translateX = offset * spreadX;
                var translateZ = (1 - Math.min(abs, 2.5)) * 85 - abs * 65;
                var rotateY = offset * -24;
                var scale = Math.max(0.62, 1 - abs * 0.17);
                var opacity = Math.max(0.28, 1 - abs * 0.34);
                var brightness = Math.max(0.5, 1 - abs * 0.22);

                item.style.transform =
                    'translate(-50%, -50%) translate3d(' +
                    translateX.toFixed(1) + 'px, 0px, ' +
                    translateZ.toFixed(1) + 'px) rotateY(' +
                    rotateY.toFixed(2) + 'deg) scale(' +
                    scale.toFixed(3) + ')';
                item.style.opacity = opacity.toFixed(3);
                item.style.zIndex = Math.round(30 - abs * 5);
                item.style.filter = abs > 0.25 ? 'brightness(' + brightness.toFixed(2) + ')' : 'none';

                var isActive = idx === activeIdx && abs < 0.45;
                item.classList.toggle('is-active', isActive);

                var drinkEl = item.querySelector('.drink-3d');
                if (drinkEl) {
                    if (isActive) {
                        drinkEl.style.setProperty('--ry', (cx * maxTilt).toFixed(2) + 'deg');
                        drinkEl.style.setProperty('--rx', (-cy * maxTilt * 0.6).toFixed(2) + 'deg');
                        drinkEl.style.setProperty('--sx', (50 + cx * 45).toFixed(1) + '%');
                    } else {
                        drinkEl.style.setProperty('--ry', '0deg');
                        drinkEl.style.setProperty('--rx', '0deg');
                        drinkEl.style.setProperty('--sx', '50%');
                    }
                }
            });

            requestAnimationFrame(renderCarousel);
        }

        function resetAutoRotate() {
            if (autoTimer) clearInterval(autoTimer);
            if (reduce) return;
            autoTimer = setInterval(function () {
                if (!isDragging && !isHovering) {
                    targetPos = Math.round(targetPos) + 1;
                }
            }, 3500);
        }

        // Pointer drag (mouse & jari) + tilt saat hover
        stage.addEventListener('pointerdown', function (e) {
            if (e.button !== 0 && e.pointerType === 'mouse') return;
            isDragging = true;
            dragMoved = false;
            dragStartX = e.clientX;
            lastPointerX = e.clientX;
            dragStartPos = currentPos;
            velocity = 0;
            stage.classList.add('is-dragging');
            if (stage.setPointerCapture) {
                stage.setPointerCapture(e.pointerId);
            }
            if (autoTimer) clearInterval(autoTimer);
        });

        stage.addEventListener('pointermove', function (e) {
            var r = stage.getBoundingClientRect();
            var nx = ((e.clientX - r.left) / r.width - 0.5) * 2;
            var ny = ((e.clientY - r.top) / r.height - 0.5) * 2;

            if (isDragging) {
                var dx = e.clientX - dragStartX;
                if (Math.abs(dx) > 6) dragMoved = true;
                velocity = e.clientX - lastPointerX;
                lastPointerX = e.clientX;
                var pxPerItem = getSpread();
                currentPos = dragStartPos - dx / pxPerItem;
                targetPos = currentPos;
            } else if (e.pointerType === 'mouse') {
                isHovering = true;
                tx = Math.max(-1, Math.min(1, nx));
                ty = Math.max(-1, Math.min(1, ny));
            }
        });

        function endDrag(e) {
            if (!isDragging) return;
            isDragging = false;
            stage.classList.remove('is-dragging');

            if (!dragMoved) {
                // Klik pada salah satu item samping -> pindah ke item itu
                var clickedItem = e.target && e.target.closest ? e.target.closest('.drink-carousel-item') : null;
                if (clickedItem) {
                    var clickedIdx = parseInt(clickedItem.getAttribute('data-drink-index'), 10);
                    var offset = mod(clickedIdx - Math.round(currentPos) + total / 2, total) - total / 2;
                    targetPos = Math.round(currentPos) + offset;
                } else {
                    targetPos = Math.round(currentPos);
                }
            } else {
                // Momentum ringan saat swipe cepat
                var flick = Math.max(-0.8, Math.min(0.8, -velocity * 0.02));
                targetPos = Math.round(currentPos + flick);
            }
            resetAutoRotate();
        }

        stage.addEventListener('pointerup', endDrag);
        stage.addEventListener('pointercancel', endDrag);

        stage.addEventListener('pointerleave', function () {
            isHovering = false;
            if (!isDragging) {
                tx = 0;
                ty = 0;
            }
        });

        // Klik dot indikator
        dots.forEach(function (dot) {
            dot.addEventListener('click', function () {
                var dotIdx = parseInt(dot.getAttribute('data-dot-index'), 10);
                var offset = mod(dotIdx - Math.round(currentPos) + total / 2, total) - total / 2;
                targetPos = Math.round(currentPos) + offset;
                resetAutoRotate();
            });
        });

        requestAnimationFrame(renderCarousel);
        resetAutoRotate();
    })();
</script>

<!-- ======================================================================= -->
<!-- 4b. MENU TERFAVORIT (BEST OF TASTE - 3 MENU PENJUALAN TERBANYAK)        -->
<!-- ======================================================================= -->
@if(isset($topFavorites) && $topFavorites->count() > 0)
<section id="favorit" class="relative py-16 sm:py-24 bg-[#000000] border-t border-[#A09B90]/30 overflow-hidden">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="relative mx-auto max-w-3xl px-4 sm:px-6 text-center mb-12 sm:mb-16 reveal-on-scroll">
            <span class="inline-block text-[11px] sm:text-xs font-bold tracking-[0.25em] uppercase text-[#D4954D] mb-3">
                MENU TERFAVORIT
            </span>
            <h2 class="font-serif text-3xl sm:text-4xl md:text-5xl font-bold text-[#F2F5E2] leading-tight tracking-wide mb-3 uppercase">
                BEST OF TASTE
            </h2>
            <div class="w-16 h-0.5 bg-[#A09B90]/50 mx-auto mb-4"></div>
            <p class="text-xs sm:text-sm md:text-base text-[#E3DEA4] leading-relaxed max-w-xl mx-auto">
                Tiga racikan dan hidangan yang paling sering dipesan pelanggan setia ABS Cafe setiap harinya.
            </p>
        </div>

        <!-- 3 Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8">
            @foreach($topFavorites as $fav)
                <div class="bg-[#050604] rounded-3xl border border-[#A09B90]/40 overflow-hidden flex flex-col justify-between hover:border-[#D4954D]/60 transition-all duration-300 group shadow-[0_16px_40px_rgba(0,0,0,0.85)] reveal-on-scroll">
                    <div>
                        <!-- Photo Container -->
                        <div class="relative w-full aspect-[4/3] bg-[#120010] overflow-hidden">
                            <img src="{{ $fav->imageUrl() }}"
                                 alt="{{ $fav->name }}"
                                 loading="lazy"
                                 decoding="async"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">

                            @if($fav->category)
                                <span class="absolute top-3 left-3 bg-[#050604]/80 backdrop-blur-md text-[#D4954D] border border-[#A09B90]/40 text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                                    {{ $fav->category->name }}
                                </span>
                            @endif
                        </div>

                        <!-- Content -->
                        <div class="p-6 sm:p-7">
                            <h3 class="font-serif text-xl sm:text-2xl font-bold text-[#F2F5E2] mb-2.5 group-hover:text-[#D4954D] transition-colors line-clamp-1">
                                {{ $fav->name }}
                            </h3>
                            <p class="text-xs sm:text-sm text-[#E3DEA4]/80 leading-relaxed line-clamp-3">
                                {{ $fav->description ?: 'Pilihan menu terfavorit pelanggan ABS Cafe dengan cita rasa istimewa dan kualitas bahan terbaik.' }}
                            </p>
                        </div>
                    </div>

                    <!-- Footer Action Link -->
                    <div class="px-6 pb-6 sm:px-7 sm:pb-7 pt-2">
                        <a href="{{ route('pelanggan.menu', ['q' => $fav->name]) }}"
                           class="inline-flex items-center gap-2 text-xs font-bold tracking-[0.18em] uppercase text-[#F2F5E2] group-hover:text-[#D4954D] transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-[#D4954D] rounded">
                            <span>LIHAT MENU</span>
                            <span class="transform group-hover:translate-x-1 transition-transform duration-200">&rarr;</span>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- ======================================================================= -->
<!-- 5. LOKASI SECTION                                                       -->
<!-- ======================================================================= -->
<section id="lokasi" class="relative py-16 sm:py-24 bg-[#000000]">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 sm:gap-14 items-center">
            <!-- Informasi Lokasi (Kiri) -->
            <div class="lg:col-span-6 flex flex-col items-start text-left reveal-on-scroll">
                <!-- Label / Kicker -->
                <span class="inline-block text-[11px] sm:text-xs font-bold tracking-[0.25em] uppercase text-[#D4954D] mb-2">
                    {{ $landing['location']['badge'] ?? 'LOKASI' }}
                </span>

                <!-- Judul -->
                <h2 class="font-serif text-3xl sm:text-4xl md:text-5xl font-bold text-[#F2F5E2] leading-tight mb-6">
                    {{ $landing['location']['title'] ?? 'Temukan Kami' }}
                </h2>

                <!-- Daftar Informasi Alamat, Jam, Telepon -->
                <div class="space-y-3 mb-8 w-full max-w-md">
                    <!-- Alamat -->
                    <div class="flex items-start gap-3.5 p-4 rounded-2xl bg-[#050604] border border-[#A09B90]/30">
                        <div class="w-8 h-8 rounded-full bg-[#050604] border border-[#A09B90]/50 flex items-center justify-center text-[#D4954D] shrink-0 mt-0.5">
                            <svg class="w-4 h-4 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                        </div>
                        <div class="text-left">
                            <p class="text-xs sm:text-sm text-[#F2F5E2] font-medium leading-relaxed break-words">
                                {{ $landing['cafe']['address'] ?? 'Jl. Letjend Suprapto No.44, Kepatihan, Kec. Tulungagung, Kabupaten Tulungagung, Jawa Timur 66219' }}
                            </p>
                        </div>
                    </div>

                    <!-- Jam Operasional -->
                    <div class="flex items-start gap-3.5 p-4 rounded-2xl bg-[#050604] border border-[#A09B90]/30">
                        <div class="w-8 h-8 rounded-full bg-[#050604] border border-[#A09B90]/50 flex items-center justify-center text-[#D4954D] shrink-0 mt-0.5">
                            <svg class="w-4 h-4 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                        </div>
                        <div class="text-left">
                            <p class="text-xs sm:text-sm text-[#E3DEA4] leading-relaxed">
                                {{ $landing['cafe']['hours'] ?? 'Buka Setiap Hari 09.00 - 23.00 WIB' }}
                                @if(!empty($landing['cafe']['last_order']))
                                    <span class="block text-[11px] sm:text-xs text-[#E3DEA4]/75">(Batas Order Terakhir: {{ $landing['cafe']['last_order'] }})</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- Nomor Telepon -->
                    <div class="flex items-start gap-3.5 p-4 rounded-2xl bg-[#050604] border border-[#A09B90]/30">
                        <div class="w-8 h-8 rounded-full bg-[#050604] border border-[#A09B90]/50 flex items-center justify-center text-[#D4954D] shrink-0 mt-0.5">
                            <svg class="w-4 h-4 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                            </svg>
                        </div>
                        <div class="text-left">
                            <a href="tel:{{ $landing['cafe']['phone_tel'] ?? '+6289620102010' }}" class="text-xs sm:text-sm text-[#E3DEA4] hover:text-[#D4954D] leading-relaxed transition-colors inline-flex items-center gap-1 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-[#D4954D] rounded">
                                {{ $landing['cafe']['phone'] ?? '0896-2010-2010' }}
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Tombol Tindakan: Google Maps & Chat WhatsApp -->
                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ $landing['cafe']['maps_url'] ?? 'https://maps.app.goo.gl/mvEr6sWCacKH5j677' }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="inline-flex items-center justify-center gap-2.5 min-h-[44px] px-6 py-2.5 rounded-full border border-[#D4954D] bg-[#050604] hover:bg-[#D4954D] hover:text-[#290024] text-[#F2F5E2] font-semibold text-xs sm:text-sm tracking-wide transition-all duration-200 shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#D4954D]">
                        <svg class="w-4 h-4 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                        <span>Lihat di Google Maps</span>
                    </a>

                    @if(!empty($landing['cafe']['whatsapp_url']))
                        <a href="{{ $landing['cafe']['whatsapp_url'] }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="inline-flex items-center justify-center gap-2.5 min-h-[44px] px-6 py-2.5 rounded-full border border-[#A09B90]/60 bg-[#050604] hover:bg-[#E3DEA4]/20 hover:border-[#E3DEA4] text-[#F2F5E2] font-semibold text-xs sm:text-sm tracking-wide transition-all duration-200 shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#E3DEA4]">
                            <svg class="w-4 h-4 fill-current text-[#E3DEA4]" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zm-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884zm8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/>
                            </svg>
                            <span>Chat via WhatsApp</span>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Kartu Google Maps Asli (Kanan di Desktop, Bawah di Mobile) -->
            <div class="lg:col-span-6 reveal-on-scroll">
                <div class="relative w-full aspect-[4/3] min-h-[320px] sm:min-h-[400px] rounded-3xl border border-[#A09B90]/40 bg-[#050604] overflow-hidden shadow-[0_16px_50px_rgba(0,0,0,0.8)] group">
                    <!-- Google Maps Real Interactive Embed -->
                    <iframe
                        src="{{ $landing['cafe']['maps_embed_url'] ?? ('https://maps.google.com/maps?q=' . ($landing['cafe']['peta_lat'] ?? '-8.0646338') . ',' . ($landing['cafe']['peta_lng'] ?? '111.9111798') . '&hl=id&z=17&output=embed') }}"
                        class="w-full h-full border-0 rounded-3xl"
                        style="border:0;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        title="Peta Lokasi {{ $landing['cafe']['name'] ?? 'ABS Cafe' }}">
                    </iframe>

                    <!-- Tag Badge Lokasi Cafe (Pojok Kiri Atas) -->
                    <div class="pointer-events-none absolute top-3 sm:top-4 left-3 sm:left-4 z-10 flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#050604]/90 border border-[#A09B90]/60 backdrop-blur-md shadow-lg">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#D4954D] opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-[#D4954D]"></span>
                        </span>
                        <span class="font-serif font-bold text-xs text-[#F2F5E2] tracking-wider">
                            {{ $landing['cafe']['name'] ?? 'ABS Cafe' }}
                        </span>
                    </div>

                    <!-- Tombol Cepat: Buka di Google Maps (Pojok Kanan Bawah) -->
                    <div class="absolute bottom-3 sm:bottom-4 right-3 sm:right-4 z-10">
                        <a href="{{ $landing['cafe']['maps_url'] ?? 'https://maps.app.goo.gl/mvEr6sWCacKH5j677' }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-[#050604]/95 hover:bg-[#D4954D] text-[#E3DEA4] hover:text-[#290024] border border-[#A09B90]/70 backdrop-blur-md text-xs font-semibold shadow-lg transition-all duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#D4954D]">
                            <svg class="w-3.5 h-3.5 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <polygon points="3 11 22 2 13 21 11 13 3 11"></polygon>
                            </svg>
                            <span>Buka Peta</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ======================================================================= -->
<!-- 5b. RESERVASI MEJA REAL-TIME BANNER                                    -->
<!-- ======================================================================= -->
<section class="relative py-16 sm:py-24 bg-[#000000] border-t border-[#A09B90]/25 overflow-hidden">
    <div class="relative mx-auto max-w-4xl px-4 sm:px-6 reveal-on-scroll">
        <!-- Kotak Banner Reservasi Bergaya Elevated Card -->
        <div class="relative p-8 sm:p-12 md:p-14 rounded-3xl bg-[#050604] border border-[#A09B90]/40 shadow-[0_20px_50px_rgba(0,0,0,0.85)] text-center overflow-hidden">
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-[#D4954D]/10 via-transparent to-transparent pointer-events-none"></div>
            
            <div class="relative z-10">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#D4954D]/15 border border-[#A09B90]/40 text-[#F2F5E2] text-xs font-semibold tracking-wider uppercase mb-4">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#D4954D] opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-[#D4954D]"></span>
                    </span>
                    Ketersediaan Meja Real-Time
                </div>
                <h2 class="font-serif text-2xl sm:text-3xl md:text-4xl font-bold text-[#F2F5E2] mb-4">
                    Ingin Bersantai Tanpa Khawatir Kehabisan Tempat?
                </h2>
                <p class="text-xs sm:text-sm md:text-base text-[#E3DEA4] leading-relaxed max-w-2xl mx-auto mb-8">
                    Pantau denah meja kafe secara langsung. Ketahui meja yang sedang terisi atau masih kosong secara real-time, lalu amankan reservasi Anda hanya dalam beberapa klik.
                </p>
                <div class="flex flex-wrap items-center justify-center gap-4">
                    <a href="{{ route('pelanggan.reservasi') }}"
                       class="inline-flex items-center justify-center gap-2.5 min-h-[48px] px-8 py-3.5 rounded-full bg-[#D4954D] hover:bg-[#D4954D]/90 text-[#290024] font-bold text-sm tracking-wide transition-all duration-200 shadow-[0_6px_20px_rgba(212,149,77,0.35)] hover:shadow-[0_8px_25px_rgba(212,149,77,0.45)] hover:-translate-y-0.5">
                        <svg class="w-4 h-4 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        <span>Lihat Meja & Reservasi Sekarang</span>
                        <svg class="w-4 h-4 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ======================================================================= -->
<!-- 6. MEDIA SOSIAL SECTION                                                 -->
<!-- ======================================================================= -->
<section class="relative py-16 sm:py-24 bg-[#000000] border-t border-[#A09B90]/30">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 text-center reveal-on-scroll">
        <!-- Judul Bagian Sosial Media -->
        <h3 class="font-serif text-xl sm:text-2xl md:text-3xl font-bold text-[#F2F5E2] mb-6 sm:mb-8">
            {{ $landing['social']['title'] ?? 'Ikuti Kami di Sosial Media' }}
        </h3>

        <!-- Tautan Akun Media Sosial (Instagram, TikTok & WhatsApp) -->
        <div class="flex flex-wrap items-center justify-center gap-4 sm:gap-8">
            @if(!empty($landing['cafe']['instagram']))
                <a href="{{ $landing['cafe']['instagram_url'] ?? '#' }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="inline-flex items-center gap-3 min-h-[44px] px-5 py-2.5 rounded-full bg-[#050604] hover:bg-[#D4954D]/15 border border-[#A09B90]/50 text-[#F2F5E2] hover:text-[#D4954D] transition-all duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#D4954D]">
                    <!-- Ikon Instagram SVG -->
                    <svg class="w-5 h-5 stroke-current text-[#D4954D]" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                        <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                        <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                    </svg>
                    <span class="text-xs sm:text-sm font-semibold tracking-wide">{{ '@' . ltrim($landing['cafe']['instagram'], '@') }}</span>
                </a>
            @endif

            @if(!empty($landing['cafe']['tiktok']))
                <a href="{{ $landing['cafe']['tiktok_url'] ?? '#' }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="inline-flex items-center gap-3 min-h-[44px] px-5 py-2.5 rounded-full bg-[#050604] hover:bg-[#D4954D]/15 border border-[#A09B90]/50 text-[#F2F5E2] hover:text-[#D4954D] transition-all duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#D4954D]">
                    <!-- Ikon TikTok SVG -->
                    <svg class="w-5 h-5 stroke-current text-[#D4954D]" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5"></path>
                    </svg>
                    <span class="text-xs sm:text-sm font-semibold tracking-wide">{{ '@' . ltrim($landing['cafe']['tiktok'], '@') }}</span>
                </a>
            @endif

            @if(!empty($landing['cafe']['whatsapp_url']))
                <a href="{{ $landing['cafe']['whatsapp_url'] }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="inline-flex items-center gap-3 min-h-[44px] px-5 py-2.5 rounded-full bg-[#050604] hover:bg-[#D4954D]/15 border border-[#A09B90]/50 text-[#F2F5E2] hover:text-[#D4954D] transition-all duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#D4954D]">
                    <!-- Ikon WhatsApp SVG -->
                    <svg class="w-5 h-5 fill-current text-[#D4954D]" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zm-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884zm8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/>
                    </svg>
                    <span class="text-xs sm:text-sm font-semibold tracking-wide">{{ $landing['cafe']['whatsapp_display'] ?: $landing['cafe']['whatsapp_nomor'] }}</span>
                </a>
            @endif
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    // Smooth scroll animations with IntersectionObserver
    document.addEventListener('DOMContentLoaded', function () {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-revealed');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.15,
            rootMargin: '0px 0px -40px 0px'
        });

        document.querySelectorAll('.reveal-on-scroll').forEach(el => {
            observer.observe(el);
        });
    });
</script>
<style>
    /* ── Marquee filmstrip gallery ── */
    @keyframes gallery-marquee {
        0%   { transform: translateX(0); }
        100% { transform: translateX(-50%); }
    }

    .gallery-marquee-track {
        animation: gallery-marquee 28s linear infinite;
        will-change: transform;
    }

    /* Jeda saat hover (opsional tapi smooth UX) */
    .gallery-marquee-wrapper:hover .gallery-marquee-track {
        animation-play-state: paused;
    }

    /* Jika user prefer reduced motion → matikan animasi */
    @media (prefers-reduced-motion: reduce) {
        .gallery-marquee-track {
            animation: none;
        }
    }

    /* ── Reveal on scroll ── */
    .reveal-on-scroll {
        opacity: 0;
        transform: translateY(20px);
        transition: opacity 0.7s cubic-bezier(0.16, 1, 0.3, 1), transform 0.7s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .reveal-on-scroll.is-revealed {
        opacity: 1;
        transform: translateY(0);
    }
    @media (prefers-reduced-motion: reduce) {
        .reveal-on-scroll {
            opacity: 1 !important;
            transform: none !important;
            transition: none !important;
        }
    }
</style>
@endpush
