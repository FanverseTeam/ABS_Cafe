@props([
    'label'    => null,
    'icon'     => 'plus',
    'onclick'  => null,
    'href'     => null,
    'id'       => null,
])

{{--
    Floating Action Button (FAB)
    - Mobile  : Circular button (56px), icon only, positioned above bottom nav
    - Tablet  : Pill button with optional label (sm:flex-row)
    - Smart scroll: auto-hides smoothly when scrolling down, reappears when scrolling up
    - Position: calc(64px + env(safe-area-inset-bottom) + 16px)
--}}

@php
    $btnClasses = 'flex items-center justify-center gap-2 shadow-soft-lg transition-all active:scale-95 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#D4954D]/70 w-14 h-14 rounded-full sm:w-auto sm:h-12 sm:rounded-full sm:px-5 bg-[#290024] hover:bg-[#775533] text-[#F2F5E2]';
@endphp

<div
    x-data="{ showFab: true, lastScroll: 0 }"
    x-init="lastScroll = window.pageYOffset"
    @scroll.window="
        let current = window.pageYOffset;
        if (current <= 60) {
            showFab = true;
        } else if (current > lastScroll + 8) {
            showFab = false;
        } else if (current < lastScroll - 8) {
            showFab = true;
        }
        lastScroll = current;
    "
    class="lg:hidden fixed z-30 right-4 sm:right-6 transition-all duration-300"
    :class="showFab ? 'translate-y-0 opacity-100 scale-100 pointer-events-auto' : 'translate-y-16 opacity-0 scale-75 pointer-events-none'"
    style="bottom: calc(64px + env(safe-area-inset-bottom, 0px) + 16px);"
>
    @if($href)
        <a
            href="{{ $href }}"
            @if($id) id="{{ $id }}" @endif
            {{ $attributes->merge(['class' => $btnClasses]) }}
            aria-label="{{ $label ?? 'Aksi' }}"
        >
            <i data-lucide="{{ $icon }}" class="w-5 h-5 shrink-0"></i>
            @if($label)
                <span class="hidden sm:inline text-xs font-semibold whitespace-nowrap">{{ $label }}</span>
            @endif
        </a>
    @else
        <button
            type="button"
            @if($onclick) onclick="{{ $onclick }}" @endif
            @if($id) id="{{ $id }}" @endif
            {{ $attributes->merge(['class' => $btnClasses]) }}
            aria-label="{{ $label ?? 'Aksi' }}"
        >
            <i data-lucide="{{ $icon }}" class="w-5 h-5 shrink-0"></i>
            @if($label)
                <span class="hidden sm:inline text-xs font-semibold whitespace-nowrap">{{ $label }}</span>
            @endif
        </button>
    @endif
</div>
