@props([
    'title' => 'ABS Cafe',
    'subtitle' => null,
    'backUrl' => null,
    'showLogo' => true,
    'homeUrl' => '/',
    'rightAction' => null,
])

<!-- Mobile Top App Bar (only visible below the lg breakpoint) -->
<header class="lg:hidden sticky top-0 z-30 h-16 bg-[#F2F5E2]/95 backdrop-blur-md border-b border-[#E3DEA4]/70 px-4 flex items-center justify-between gap-3 shadow-sm">
    <div class="flex items-center gap-2.5 min-w-0">
        @if($backUrl)
            <a href="{{ $backUrl }}" class="w-10 h-10 -ml-1 rounded-full flex items-center justify-center text-[#290024] hover:bg-[#E3DEA4]/50 active:scale-95 transition-all" aria-label="Kembali">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
        @elseif($showLogo)
            <a href="{{ $homeUrl }}" class="flex items-center gap-2 group" aria-label="Beranda">
                <x-logo :show-text="false" size="sm" loading="eager" class="shrink-0" />
            </a>
        @endif

        <div class="min-w-0">
            <h1 class="font-serif font-bold text-base sm:text-lg text-[#290024] truncate leading-tight">
                {{ $title }}
            </h1>
            @if($subtitle)
                <p class="text-[10px] text-[#775533] truncate leading-none mt-0.5">{{ $subtitle }}</p>
            @endif
        </div>
    </div>

    @if($rightAction)
        <div class="flex items-center gap-1.5 shrink-0">
            {{ $rightAction }}
        </div>
    @endif
</header>
