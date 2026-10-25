<footer class="relative bg-[#290024] border-t border-[#775533]/30 text-[#E3DEA4] pt-12 pb-8 overflow-hidden">
    <!-- Ornamen Daun Garis di Sudut Kiri Bawah (SVG Inline Dekoratif) -->
    <div class="pointer-events-none absolute -bottom-6 -left-6 w-32 h-32 opacity-20 sm:opacity-30 text-[#D4954D]" aria-hidden="true">
        <svg viewBox="0 0 120 120" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="w-full h-full">
            <path d="M10 110 Q 50 70 80 20" />
            <path d="M80 20 Q 95 35 80 50 Q 65 35 80 20 Z" />
            <path d="M60 45 Q 75 55 65 68 Q 50 58 60 45 Z" />
            <path d="M42 68 Q 55 75 48 88 Q 35 80 42 68 Z" />
            <path d="M68 62 Q 82 70 78 82 Q 62 76 68 62 Z" />
        </svg>
    </div>

    <!-- Ornamen Daun Garis di Sudut Kanan Bawah (SVG Inline Dekoratif) -->
    <div class="pointer-events-none absolute -bottom-6 -right-6 w-32 h-32 opacity-20 sm:opacity-30 text-[#D4954D] -scale-x-100" aria-hidden="true">
        <svg viewBox="0 0 120 120" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="w-full h-full">
            <path d="M10 110 Q 50 70 80 20" />
            <path d="M80 20 Q 95 35 80 50 Q 65 35 80 20 Z" />
            <path d="M60 45 Q 75 55 65 68 Q 50 58 60 45 Z" />
            <path d="M42 68 Q 55 75 48 88 Q 35 80 42 68 Z" />
            <path d="M68 62 Q 82 70 78 82 Q 62 76 68 62 Z" />
        </svg>
    </div>

    <div class="relative mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 flex flex-col items-center text-center">
        <!-- Logo Kecil & Tagline -->
        <a href="{{ route('pelanggan.beranda') }}" class="group flex flex-col items-center gap-2 mb-4 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#D4954D] rounded-2xl p-2" aria-label="Beranda ABS Cafe">
            <x-logo :show-text="false" size="sm" class="group-hover:scale-105 transition-transform" />
            <span class="font-serif text-lg font-bold tracking-wider text-[#F2F5E2]">
                {{ $landing['cafe']['name'] ?? 'ABS CAFE' }}
            </span>
            <span class="text-xs text-[#D4954D] italic font-serif">
                {{ $landing['cafe']['tagline'] ?? 'Good Coffee, Better Moments.' }}
            </span>
        </a>

        <!-- Alamat Singkat -->
        <p class="text-xs text-[#E3DEA4]/80 max-w-md mx-auto mb-6 leading-relaxed break-words">
            {{ $landing['cafe']['address'] ?? 'Jl. Letjend Suprapto No.44, Kepatihan, Kec. Tulungagung, Kabupaten Tulungagung, Jawa Timur 66219' }}
        </p>

        <!-- Garis Pemisah Halus -->
        <div class="w-24 h-px bg-[#D4954D]/30 mb-6"></div>

        <!-- Copyright -->
        <div class="text-xs text-[#E3DEA4]/60">
            <p>
                &copy; {{ date('Y') }} {{ $landing['cafe']['name'] ?? 'ABS Cafe' }}. Hak cipta dilindungi.
            </p>
        </div>
    </div>
</footer>
