@extends('pelanggan.layouts.app')

@section('title', ($landing['cafe']['name'] ?? 'ABS Cafe') . ' - ' . ($landing['cafe']['tagline'] ?? 'Good Coffee, Better Moments.'))

@section('content')
<!-- ======================================================================= -->
<!-- 1. HERO SECTION                                                         -->
<!-- ======================================================================= -->
<section id="beranda" class="relative min-h-[92vh] sm:min-h-screen flex items-center justify-center pt-24 sm:pt-28 pb-16 overflow-hidden">
    <!-- Foto Latar Hero dengan Overlay Gradasi Midnight Violet -->
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
        <!-- Gradasi Palet Midnight Violet Overlay -->
        <div class="absolute inset-0 bg-gradient-to-b from-[#290024]/90 via-[#290024]/80 to-[#290024]"></div>
        <div class="absolute inset-0 bg-radial from-transparent via-[#290024]/60 to-[#290024]"></div>
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
                       class="inline-flex items-center justify-center gap-2.5 min-h-[44px] px-6 sm:px-7 py-3 rounded-full border border-[#D4954D] bg-[#290024]/60 hover:bg-[#D4954D]/15 text-[#F2F5E2] font-semibold text-xs sm:text-sm tracking-wide transition-all duration-200 hover:-translate-y-0.5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#D4954D] motion-reduce:transform-none">
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
                                 class="w-full h-full object-cover rounded-2xl shadow-[0_16px_40px_rgba(0,0,0,0.6)] border border-[#D4954D]/30 filter drop-shadow-2xl">
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
<section id="tentang-kami" class="relative py-20 sm:py-28 bg-gradient-to-b from-[#290024] via-[#290024] to-[#290024] overflow-hidden">
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
                    <div class="relative w-full h-full overflow-hidden border border-[#D4954D]/50 shadow-[0_16px_50px_rgba(41,0,36,0.7)]"
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
<section class="relative py-14 sm:py-20 bg-[#290024] border-y border-[#775533]/25">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
        <!-- 2x2 di Mobile, 4 Kolom di Tablet & Desktop -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 sm:gap-4 md:divide-x md:divide-[#775533]/40">
            @foreach($landing['features'] as $index => $feature)
                <div class="flex flex-col items-center text-center px-2 sm:px-4 reveal-on-scroll">
                    <!-- Ikon Garis dalam Lingkaran Berbingkai Tipis -->
                    <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full border border-[#D4954D]/70 bg-[#775533]/20 flex items-center justify-center text-[#D4954D] mb-3 shadow-[0_4px_16px_rgba(212,149,77,0.15)] group-hover:scale-110 transition-transform">
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
<!-- 4. SUASANA SECTION                                                      -->
<!-- ======================================================================= -->
<section class="relative py-20 sm:py-28 bg-[#290024] overflow-hidden">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
        <!-- Bingkai Foto Lebar Suasana Kafe -->
        <div class="relative w-full rounded-3xl overflow-hidden border border-[#D4954D]/35 shadow-[0_20px_60px_rgba(41,0,36,0.8)] aspect-[16/10] sm:aspect-[21/9] flex items-center justify-start p-6 sm:p-12 lg:p-16 reveal-on-scroll">
            <!-- Foto Lebar Interior -->
            <div class="absolute inset-0 z-0">
                @if(!empty($landing['atmosphere']['photo']['src']))
                    <img src="{{ $landing['atmosphere']['photo']['src'] }}"
                         alt="{{ $landing['atmosphere']['photo']['alt'] }}"
                         width="{{ $landing['atmosphere']['photo']['width'] }}"
                         height="{{ $landing['atmosphere']['photo']['height'] }}"
                         loading="lazy"
                         decoding="async"
                         referrerpolicy="no-referrer"
                         onerror="this.style.display='none'"
                         class="w-full h-full object-cover object-center filter brightness-[0.55] contrast-[1.05]">
                @else
                    <div class="w-full h-full bg-gradient-to-r from-[#290024] via-[#775533]/40 to-[#290024]"></div>
                @endif
                <!-- Overlay Gradasi Gelap Elegan untuk Memastikan Keterbacaan Teks -->
                <div class="absolute inset-0 bg-gradient-to-r from-[#290024]/95 via-[#290024]/75 to-transparent"></div>
            </div>

            <!-- Konten Teks di Atas Foto -->
            <div class="relative z-10 max-w-lg text-left">
                <!-- Label / Kicker -->
                <span class="inline-block text-[11px] sm:text-xs font-bold tracking-[0.25em] uppercase text-[#D4954D] mb-2">
                    {{ $landing['atmosphere']['badge'] ?? 'SUASANA' }}
                </span>

                <!-- Judul -->
                <h2 class="font-serif text-2xl sm:text-3xl md:text-4xl font-bold text-[#F2F5E2] leading-tight mb-3">
                    {{ $landing['atmosphere']['title'] ?? 'Nyaman & Elegan' }}
                </h2>

                <!-- Deskripsi -->
                <p class="text-xs sm:text-sm md:text-base text-[#E3DEA4] leading-relaxed">
                    {{ $landing['atmosphere']['description'] ?? 'Desain interior kami yang modern dengan sentuhan alami menciptakan suasana yang hangat dan menenangkan.' }}
                </p>
            </div>
        </div>
    </div>
</section>

<!-- ======================================================================= -->
<!-- 5. LOKASI SECTION                                                       -->
<!-- ======================================================================= -->
<section id="lokasi" class="relative py-20 sm:py-28 bg-gradient-to-b from-[#290024] via-[#290024] to-[#290024]">
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
                <div class="space-y-4 mb-8 w-full max-w-md">
                    <!-- Alamat -->
                    <div class="flex items-start gap-3.5">
                        <div class="w-8 h-8 rounded-full bg-[#775533]/25 border border-[#D4954D]/40 flex items-center justify-center text-[#D4954D] shrink-0 mt-0.5">
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
                    <div class="flex items-start gap-3.5">
                        <div class="w-8 h-8 rounded-full bg-[#775533]/25 border border-[#D4954D]/40 flex items-center justify-center text-[#D4954D] shrink-0 mt-0.5">
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
                    <div class="flex items-start gap-3.5">
                        <div class="w-8 h-8 rounded-full bg-[#775533]/25 border border-[#D4954D]/40 flex items-center justify-center text-[#D4954D] shrink-0 mt-0.5">
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
                       class="inline-flex items-center justify-center gap-2.5 min-h-[44px] px-6 py-2.5 rounded-full border border-[#D4954D] bg-[#775533]/20 hover:bg-[#D4954D] hover:text-[#290024] text-[#F2F5E2] font-semibold text-xs sm:text-sm tracking-wide transition-all duration-200 shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#D4954D]">
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
                           class="inline-flex items-center justify-center gap-2.5 min-h-[44px] px-6 py-2.5 rounded-full border border-[#E3DEA4]/60 bg-[#775533]/20 hover:bg-[#E3DEA4]/20 hover:border-[#E3DEA4] text-[#F2F5E2] font-semibold text-xs sm:text-sm tracking-wide transition-all duration-200 shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#E3DEA4]">
                            <svg class="w-4 h-4 fill-current text-[#E3DEA4]" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zm-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884zm8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/>
                            </svg>
                            <span>Chat via WhatsApp</span>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Kartu Peta Bergaya Gelap (Kanan di Desktop, Bawah di Mobile) -->
            <div class="lg:col-span-6 reveal-on-scroll">
                <div class="relative w-full aspect-[4/3] rounded-3xl border border-[#D4954D]/35 bg-[#290024] p-4 sm:p-6 overflow-hidden shadow-[0_16px_50px_rgba(41,0,36,0.7)] flex flex-col justify-between">
                    <!-- Ilustrasi Vektor Peta Gelap Elegan (Inline SVG) -->
                    <svg class="absolute inset-0 w-full h-full opacity-60" viewBox="0 0 500 375" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <!-- Poligon Blok Area Kafe -->
                        <rect width="500" height="375" fill="#290024" />
                        <path d="M30 40 L160 30 L140 180 L20 160 Z" fill="#290024" opacity="0.6" />
                        <path d="M200 50 L340 30 L320 150 L180 160 Z" fill="#290024" opacity="0.6" />
                        <path d="M360 40 L480 30 L460 200 L340 180 Z" fill="#290024" opacity="0.6" />
                        <path d="M40 220 L180 200 L160 340 L30 350 Z" fill="#290024" opacity="0.6" />
                        <path d="M220 210 L380 200 L360 350 L200 340 Z" fill="#290024" opacity="0.6" />

                        <!-- Garis Jalan Raya (Grid Peta) -->
                        <line x1="0" y1="190" x2="500" y2="170" stroke="#775533" stroke-width="12" opacity="0.45" />
                        <line x1="0" y1="190" x2="500" y2="170" stroke="#D4954D" stroke-width="2" stroke-dasharray="8 8" opacity="0.6" />

                        <line x1="170" y1="0" x2="190" y2="375" stroke="#775533" stroke-width="10" opacity="0.4" />
                        <line x1="330" y1="0" x2="350" y2="375" stroke="#775533" stroke-width="8" opacity="0.35" />
                        <line x1="0" y1="80" x2="500" y2="60" stroke="#775533" stroke-width="6" opacity="0.3" />
                        <line x1="0" y1="310" x2="500" y2="290" stroke="#775533" stroke-width="7" opacity="0.35" />

                        <!-- Label Nama Jalan Gaya Peta -->
                        <text x="210" y="160" fill="#E3DEA4" font-size="11" font-weight="600" opacity="0.75" transform="rotate(-2 210 160)">Jl. Letjend Suprapto</text>
                        <text x="180" y="90" fill="#E3DEA4" font-size="9" opacity="0.6" transform="rotate(85 180 90)">Jl. Pahlawan</text>
                        <text x="340" y="90" fill="#E3DEA4" font-size="9" opacity="0.6" transform="rotate(85 340 90)">Jl. Basuki</text>
                    </svg>

                    <!-- Pin Penanda Lokasi ABS Cafe di Tengah Peta -->
                    <div class="relative z-10 m-auto flex flex-col items-center">
                        <div class="relative flex items-center justify-center">
                            <!-- Efek Pulse Cahaya Emas -->
                            <span class="animate-ping absolute inline-flex h-12 w-12 rounded-full bg-[#D4954D] opacity-40"></span>
                            <div class="relative w-12 h-12 rounded-full bg-[#D4954D] text-[#290024] flex items-center justify-center shadow-[0_4px_20px_rgba(212,149,77,0.7)] border-2 border-[#F2F5E2]">
                                <svg class="w-6 h-6 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                    <circle cx="12" cy="10" r="3"></circle>
                                </svg>
                            </div>
                        </div>

                        <!-- Tag Badge Lokasi -->
                        <div class="mt-2.5 px-3.5 py-1 rounded-full bg-[#290024]/90 border border-[#D4954D]/60 backdrop-blur-md shadow-lg">
                            <span class="font-serif font-bold text-xs text-[#F2F5E2] tracking-wider">
                                {{ $landing['cafe']['name'] ?? 'ABS Cafe' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ======================================================================= -->
<!-- 6. MEDIA SOSIAL SECTION                                                 -->
<!-- ======================================================================= -->
<section class="relative py-16 sm:py-20 bg-[#290024] border-t border-[#775533]/25">
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
                   class="inline-flex items-center gap-3 min-h-[44px] px-5 py-2.5 rounded-full bg-[#775533]/15 hover:bg-[#D4954D]/15 border border-[#D4954D]/40 text-[#F2F5E2] hover:text-[#D4954D] transition-all duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#D4954D]">
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
                   class="inline-flex items-center gap-3 min-h-[44px] px-5 py-2.5 rounded-full bg-[#775533]/15 hover:bg-[#D4954D]/15 border border-[#D4954D]/40 text-[#F2F5E2] hover:text-[#D4954D] transition-all duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#D4954D]">
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
                   class="inline-flex items-center gap-3 min-h-[44px] px-5 py-2.5 rounded-full bg-[#775533]/15 hover:bg-[#D4954D]/15 border border-[#D4954D]/40 text-[#F2F5E2] hover:text-[#D4954D] transition-all duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#D4954D]">
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
