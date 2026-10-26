@props([
    'title' => '',
    'subtitle' => null,
    'price' => null,
    'image' => null,
    'icon' => null,
    'badge' => null,
    'actionIcon' => 'plus', // plus, chevron-right, etc.
    'actionCallback' => null,
    'actionHref' => null,
])

<div {{ $attributes->merge(['class' => 'flex items-center gap-3 p-3 bg-[#F2F5E2] rounded-2xl border border-[#E3DEA4]/60 shadow-sm transition-all hover:border-[#D4954D]/50']) }}>
    <!-- Thumbnail / Icon Box -->
    <div class="w-14 h-14 rounded-2xl bg-[#F2F5E2] overflow-hidden shrink-0 flex items-center justify-center border border-[#E3DEA4]/50">
        @if($image)
            <img src="{{ $image }}" alt="{{ $title }}" class="w-full h-full object-cover">
        @elseif($icon)
            <div class="text-[#D4954D]">
                <i data-lucide="{{ $icon }}" class="w-6 h-6"></i>
            </div>
        @else
            <div class="text-[#775533]">
                <i data-lucide="coffee" class="w-6 h-6"></i>
            </div>
        @endif
    </div>

    <!-- Center Content -->
    <div class="flex-1 min-w-0">
        <div class="flex items-center gap-1.5">
            <h4 class="font-bold text-xs sm:text-sm text-[#290024] truncate leading-tight">{{ $title }}</h4>
            @if($badge)
                <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full bg-[#D4954D] text-[#290024] shrink-0">{{ $badge }}</span>
            @endif
        </div>

        @if($subtitle)
            <p class="text-[11px] text-[#775533] truncate mt-0.5">{{ $subtitle }}</p>
        @endif

        @if($price !== null)
            <p class="font-serif font-bold text-xs sm:text-sm text-[#290024] mt-1">
                Rp {{ number_format((float)$price, 0, ',', '.') }}
            </p>
        @endif

        {{ $slot }}
    </div>

    <!-- Right Action -->
    <div class="shrink-0">
        @if($actionHref)
            <a href="{{ $actionHref }}" class="w-9 h-9 rounded-full bg-[#E3DEA4]/40 hover:bg-[#290024] text-[#290024] hover:text-[#F2F5E2] flex items-center justify-center transition-all shadow-sm" aria-label="Buka {{ $title }}">
                <i data-lucide="{{ $actionIcon }}" class="w-4 h-4"></i>
            </a>
        @elseif($actionCallback)
            <button type="button" onclick="{{ $actionCallback }}" class="w-9 h-9 rounded-full bg-[#290024] hover:bg-[#D4954D] text-[#F2F5E2] hover:text-[#290024] flex items-center justify-center transition-all shadow-sm active:scale-95" aria-label="Aksi {{ $title }}">
                <i data-lucide="{{ $actionIcon }}" class="w-4 h-4"></i>
            </button>
        @endif
    </div>
</div>
