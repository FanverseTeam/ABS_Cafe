@props([
    'id'            => 'filter-sheet-' . uniqid(),
    'badgeCount'    => 0,
    'icon'          => 'sliders-horizontal',
    'label'         => 'Filter & Urut',
    'buttonClass'   => '',
])

{{--
    Filter Sheet — opens a bottom-sheet with sorting / filtering options.
    Renders a compact icon button with optional active-filter badge.

    Usage:
      <x-mobile.filter-sheet id="menu-filter" :badge-count="$activeFilters">
        <!-- slot: sheet body content (radio groups, toggles, etc.) -->
        <x-slot:footer>
          <div class="grid grid-cols-2 gap-3">
            <button>Atur Ulang</button>
            <button>Terapkan</button>
          </div>
        </x-slot:footer>
      </x-mobile.filter-sheet>
--}}

<div class="relative shrink-0">
    {{-- Trigger button --}}
    <button
        type="button"
        onclick="openBottomSheet('{{ $id }}')"
        aria-label="{{ $label }}"
        aria-controls="{{ $id }}"
        class="relative flex items-center gap-1.5 h-10 px-3 rounded-full bg-[#FAFCF3] border border-[#E3DEA4] text-[#290024] text-xs font-semibold transition-colors hover:bg-[#E3DEA4]/60 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#D4954D]/70 {{ $buttonClass }}"
    >
        <i data-lucide="{{ $icon }}" class="w-4 h-4 shrink-0 text-[#775533]"></i>
        <span class="hidden sm:inline">{{ $label }}</span>

        @if($badgeCount > 0)
            <span class="absolute -top-1 -right-1 min-w-4 h-4 rounded-full bg-[#290024] text-[#FAFCF3] text-[9px] font-bold flex items-center justify-center px-1 leading-none">
                {{ $badgeCount > 9 ? '9+' : $badgeCount }}
            </span>
        @endif
    </button>
</div>

{{-- Bottom Sheet --}}
<x-mobile.bottom-sheet :id="$id" :title="$label">
    <x-slot:footer>
        @isset($footer)
            {{ $footer }}
        @endisset
    </x-slot:footer>

    {{ $slot }}
</x-mobile.bottom-sheet>
