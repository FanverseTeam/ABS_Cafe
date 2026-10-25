@props([
    'title',
    'backUrl' => null,
    'back' => null,
])

@php
    $backUrl = $backUrl ?? $back ?? url()->previous();
@endphp

<div x-data="{ isScrolled: false }"
     x-init="window.addEventListener('scroll', () => { isScrolled = window.pageYOffset > 5 })"
     class="sticky top-0 z-30 -mx-4 -mt-4 sm:-mx-6 sm:-mt-6 lg:mx-0 lg:mt-0 transition-all duration-200"
     style="padding-top: env(safe-area-inset-top, 0px);">
    <div :class="isScrolled ? 'border-b border-[#775533]/25 shadow-sm' : 'border-b border-transparent'"
         class="h-14 min-h-[56px] w-full bg-[#E3DEA4]/95 backdrop-blur-md lg:rounded-2xl px-4 sm:px-6 lg:px-4 flex items-center gap-2.5 sm:gap-3 transition-colors duration-200">
        <!-- Tombol panah kembali bulat 40px menempel di pojok kiri atas (16px dari tepi), target sentuh >= 44px -->
        <a href="{{ $backUrl }}"
           id="form-back-btn"
           class="relative flex items-center justify-center min-w-[44px] min-h-[44px] -ml-1 text-cafe-dark rounded-full focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-cafe-gold/70 group shrink-0"
           aria-label="Kembali"
           title="Kembali">
            <span class="w-10 h-10 rounded-full bg-cafe-card/85 group-hover:bg-cafe-card group-active:scale-95 text-cafe-dark flex items-center justify-center shadow-xs border border-cafe-beige/80 transition-all">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </span>
        </a>

        <!-- Satu baris judul kecil Noto Serif 18-20px, truncate, sejajar vertikal dengan panah -->
        <div class="min-w-0 flex-1 py-1">
            <h1 class="font-serif text-[18px] sm:text-[19px] font-bold text-cafe-dark truncate leading-tight select-none" title="{{ $title }}">
                {{ $title }}
            </h1>
        </div>
    </div>
</div>
