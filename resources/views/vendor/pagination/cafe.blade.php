@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi Halaman" class="flex flex-col sm:flex-row items-center justify-between gap-3 text-xs w-full">
        {{-- Kiri: Keterangan Hasil --}}
        <div class="text-cafe-muted text-center sm:text-left order-2 sm:order-1">
            <span>Menampilkan</span>
            @if ($paginator->firstItem())
                <span class="font-bold text-cafe-dark">{{ $paginator->firstItem() }}</span>
                <span>-</span>
                <span class="font-bold text-cafe-dark">{{ $paginator->lastItem() }}</span>
            @else
                <span class="font-bold text-cafe-dark">{{ $paginator->count() }}</span>
            @endif
            <span>dari</span>
            <span class="font-bold text-cafe-dark">{{ $paginator->total() }}</span>
            <span>data</span>
        </div>

        {{-- Kanan / Tengah: Tombol Nomor Halaman & Panah --}}
        <div class="flex items-center gap-1.5 order-1 sm:order-2 flex-wrap justify-center">
            {{-- Tombol Sebelumnya (<) --}}
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" aria-label="Halaman Sebelumnya" class="inline-flex items-center justify-center h-8 w-8 rounded-xl bg-cafe-card/50 text-cafe-muted/50 border border-cafe-beige/60 cursor-not-allowed select-none">
                    <i data-lucide="chevron-left" class="w-4 h-4"></i>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Halaman Sebelumnya" class="inline-flex items-center justify-center h-8 w-8 rounded-xl bg-cafe-card text-cafe-dark border border-cafe-beige/80 hover:bg-cafe-beige/60 font-medium transition-all active:scale-95 shadow-2xs">
                    <i data-lucide="chevron-left" class="w-4 h-4"></i>
                </a>
            @endif

            {{-- Nomor Halaman --}}
            @foreach ($elements as $element)
                {{-- Separator Tiga Titik --}}
                @if (is_string($element))
                    <span class="inline-flex items-center justify-center h-8 w-8 text-cafe-muted select-none">
                        {{ $element }}
                    </span>
                @endif

                {{-- Array Link Halaman --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="inline-flex items-center justify-center h-8 w-8 rounded-xl bg-cafe-dark text-[#F2F5E2] font-bold shadow-xs select-none">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" class="inline-flex items-center justify-center h-8 w-8 rounded-xl bg-cafe-card text-cafe-dark border border-cafe-beige/80 hover:bg-cafe-beige/60 font-medium transition-all active:scale-95 shadow-2xs" aria-label="Ke halaman {{ $page }}">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Tombol Berikutnya (>) --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Halaman Berikutnya" class="inline-flex items-center justify-center h-8 w-8 rounded-xl bg-cafe-card text-cafe-dark border border-cafe-beige/80 hover:bg-cafe-beige/60 font-medium transition-all active:scale-95 shadow-2xs">
                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                </a>
            @else
                <span aria-disabled="true" aria-label="Halaman Berikutnya" class="inline-flex items-center justify-center h-8 w-8 rounded-xl bg-cafe-card/50 text-cafe-muted/50 border border-cafe-beige/60 cursor-not-allowed select-none">
                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                </span>
            @endif
        </div>
    </nav>
@endif
