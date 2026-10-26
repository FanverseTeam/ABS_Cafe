@php
    $isHabis = $menu->isHabis();
    $priceLabel = $menu->customerPriceLabel();
    $imageUrl = $menu->imageUrl();
    $menuPayload = [
        'id' => $menu->id,
        'name' => $menu->name,
        'description' => $menu->description ?: '',
        'price' => (float) $menu->price,
        'image' => $imageUrl,
        'is_habis' => $isHabis,
        'price_label' => $priceLabel,
        'variants' => $menu->variants->map(fn ($v) => [
            'id' => $v->id,
            'name' => $v->name,
            'size' => $v->size,
            'display_name' => $v->displayName(),
            'price' => (float) $v->price,
            'price_label' => 'Rp ' . number_format((float) $v->price, 0, ',', '.'),
            'is_available' => (bool) $v->is_available,
            'is_default' => (bool) $v->is_default,
            'is_habis' => $v->isHabis(),
        ])->values()->all(),
    ];
@endphp

<!-- [UPLOAD-LOKAL #1] Kartu Menu Pelanggan ABS Cafe -->
<div class="bg-beige rounded-2xl border border-[#E3DEA4]/60 p-2.5 sm:p-3 shadow-xs hover:shadow-md transition-all duration-200 flex flex-col justify-between group">
    <div>
        <!-- Thumbnail Foto Persegi (Square) -->
        <div class="relative w-full aspect-square rounded-xl overflow-hidden bg-[#E3DEA4] border border-[#E3DEA4]/40 mb-2 sm:mb-2.5 flex items-center justify-center">
            @if($imageUrl)
                <img src="{{ $imageUrl }}"
                     alt="{{ $menu->name }}"
                     loading="lazy"
                     decoding="async"
                     onerror="this.classList.add('hidden'); this.nextElementSibling.classList.remove('hidden');"
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300 {{ $isHabis ? 'opacity-45 grayscale-[30%]' : '' }}">
                <!-- Placeholder Cadangan Saat Gambar Gagal Dimuat -->
                <div class="hidden absolute inset-0 bg-gradient-to-br from-[#775533]/20 via-[#D4954D]/15 to-[#E3DEA4]/30 flex items-center justify-center text-[#775533]">
                    <svg class="w-8 h-8 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M18 8h1a4 4 0 0 1 0 8h-1"></path>
                        <path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"></path>
                        <line x1="6" y1="1" x2="6" y2="4"></line>
                        <line x1="10" y1="1" x2="10" y2="4"></line>
                        <line x1="14" y1="1" x2="14" y2="4"></line>
                    </svg>
                </div>
            @else
                <!-- Placeholder Gradasi Palet ABS Cafe -->
                <div class="w-full h-full bg-gradient-to-br from-[#775533]/20 via-[#D4954D]/15 to-[#E3DEA4]/30 flex items-center justify-center text-[#775533]">
                    <svg class="w-8 h-8 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M18 8h1a4 4 0 0 1 0 8h-1"></path>
                        <path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"></path>
                        <line x1="6" y1="1" x2="6" y2="4"></line>
                        <line x1="10" y1="1" x2="10" y2="4"></line>
                        <line x1="14" y1="1" x2="14" y2="4"></line>
                    </svg>
                </div>
            @endif

            <!-- Badge Habis Jika Menu Tidak Tersedia -->
            @if($isHabis)
                <span class="absolute top-2 left-2 px-2 py-0.5 rounded-md bg-[#290024]/85 text-[#E3DEA4] text-[9px] sm:text-[10px] font-bold uppercase tracking-wider backdrop-blur-xs shadow-xs">
                    Habis
                </span>
            @endif
        </div>

        <!-- Nama Menu -->
        <h3 class="font-serif font-bold text-xs sm:text-sm text-[#290024] line-clamp-1 leading-snug group-hover:text-[#775533] transition-colors"
            title="{{ $menu->name }}">
            {{ $menu->name }}
        </h3>
    </div>

    <!-- Baris Harga & Tombol Tambah Bulat -->
    <div class="mt-2.5 pt-1.5 flex items-center justify-between gap-1">
        <!-- Label Harga -->
        <span class="font-sans font-semibold text-[11px] sm:text-xs text-[#775533] truncate">
            {{ $priceLabel }}
        </span>

        <!-- Tombol Tambah Bulat Midnight Violet -->
        @if($isHabis)
            <button type="button"
                    disabled
                    aria-label="Menu {{ $menu->name }} sedang habis"
                    class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-[#E3DEA4]/50 text-[#775533]/40 flex items-center justify-center cursor-not-allowed opacity-40 shrink-0">
                <svg class="w-3.5 h-3.5 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
            </button>
        @else
            <button type="button"
                    @click="openBottomSheet({{ json_encode($menuPayload, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) }})"
                    aria-label="Pilih varian dan tambah {{ $menu->name }} ke keranjang"
                    class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-[#290024] text-[#E3DEA4] hover:bg-[#D4954D] hover:text-[#290024] active:scale-90 flex items-center justify-center shrink-0 transition-all shadow-xs focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#290024]">
                <svg class="w-3.5 h-3.5 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
            </button>
        @endif
    </div>
</div>
