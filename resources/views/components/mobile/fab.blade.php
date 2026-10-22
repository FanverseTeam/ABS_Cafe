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
    Usage:
      <x-mobile.fab icon="plus" label="Tambah Menu" onclick="openMenuModal()" />
      <x-mobile.fab icon="plus" href="{{ route('...') }}" />
--}}

@php
    $baseClasses = 'lg:hidden fixed z-30 flex items-center justify-center gap-2 shadow-soft-lg transition-all active:scale-95 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#D4954D]/70';
    $positionClasses = 'right-4 bottom-[calc(4.5rem+env(safe-area-inset-bottom)+0.75rem)] sm:right-6';
    $sizeClasses = 'w-14 h-14 rounded-full sm:w-auto sm:h-12 sm:rounded-full sm:px-5';
    $colorClasses = 'bg-[#290024] hover:bg-[#775533] text-[#FAFCF3]';
@endphp

@if($href)
    <a
        href="{{ $href }}"
        @if($id) id="{{ $id }}" @endif
        {{ $attributes->merge(['class' => "$baseClasses $positionClasses $sizeClasses $colorClasses"]) }}
        aria-label="{{ $label ?? 'Tambah' }}"
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
        {{ $attributes->merge(['class' => "$baseClasses $positionClasses $sizeClasses $colorClasses"]) }}
        aria-label="{{ $label ?? 'Tambah' }}"
    >
        <i data-lucide="{{ $icon }}" class="w-5 h-5 shrink-0"></i>
        @if($label)
            <span class="hidden sm:inline text-xs font-semibold whitespace-nowrap">{{ $label }}</span>
        @endif
    </button>
@endif
