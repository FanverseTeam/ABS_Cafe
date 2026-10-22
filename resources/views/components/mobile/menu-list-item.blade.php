@props([
    'menu',
    'toggleAvailableRoute',
    'editCallback'      => null,
    'deleteCallback'    => null,
    'duplicateRoute'    => null,
])

{{--
    Menu List Item — compact list card for mobile Manajemen Menu.
    Shows thumbnail (64px), name (2-line), category, price, best-seller ribbon,
    availability badge, and toggle form. Tapping the row body opens an action sheet.

    Usage:
      <x-mobile.menu-list-item
          :menu="$menu"
          :toggle-available-route="route('admin.catalog.menu.toggle.available', $menu->id)"
          edit-callback="editMenuModal"
          delete-callback="openDeleteMenuModal"
          :duplicate-route="route('admin.catalog.menu.duplicate', $menu->id)"
      />
--}}

@php
    $sheetId   = 'menu-action-' . $menu->id;
    $isBest    = $menu->is_best_seller;
    $isAvail   = $menu->is_available;
    $stockLow  = $menu->stock > 0 && $menu->stock <= $menu->min_stock;
    $stockOut  = $menu->stock <= 0;
    $menuJson  = Illuminate\Support\Js::from($menu->load('variants'));
@endphp

{{-- Row card --}}
<div class="flex items-center gap-3 p-3 bg-[#FAFCF3] rounded-2xl border border-[#E3DEA4]/60 shadow-sm transition-all hover:border-[#290024]/20 active:bg-[#F2F5E2]">

    {{-- Thumbnail with Best Seller ribbon --}}
    <button
        type="button"
        onclick="openBottomSheet('{{ $sheetId }}')"
        class="relative w-16 h-16 rounded-xl overflow-hidden bg-[#E3DEA4]/40 shrink-0 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#D4954D]/70"
        aria-label="Buka aksi untuk {{ $menu->name }}"
        aria-controls="{{ $sheetId }}"
    >
        <img
            src="{{ $menu->image }}"
            alt="{{ $menu->name }}"
            class="w-full h-full object-cover"
            loading="lazy"
        >
        @if($isBest)
            <span class="absolute bottom-0 left-0 right-0 bg-[#D4954D] text-[#290024] text-[8px] font-bold text-center py-0.5 leading-none">
                Best Seller
            </span>
        @endif
    </button>

    {{-- Main content --}}
    <button
        type="button"
        onclick="openBottomSheet('{{ $sheetId }}')"
        class="flex-1 min-w-0 text-left focus-visible:outline-none"
        aria-controls="{{ $sheetId }}"
    >
        <p class="font-bold text-sm text-[#290024] line-clamp-2 leading-tight">{{ $menu->name }}</p>
        <p class="text-[11px] text-[#775533] mt-0.5 truncate">{{ $menu->category?->name }}</p>
        <p class="font-semibold text-xs text-[#290024] mt-1">{{ $menu->priceRangeLabel() }}</p>
    </button>

    {{-- Right side: status badge + toggle --}}
    <div class="flex flex-col items-end gap-2 shrink-0">
        {{-- Availability status badge --}}
        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full leading-tight {{ $isAvail ? 'bg-[#4F7A4A]/10 text-[#4F7A4A]' : 'bg-[#B03A2E]/10 text-[#B03A2E]' }}">
            {{ $isAvail ? 'Tersedia' : 'Habis' }}
        </span>

        {{-- Availability toggle (min 48px touch target area via padding) --}}
        <form action="{{ $toggleAvailableRoute }}" method="POST">
            @csrf
            <button
                type="submit"
                class="flex items-center gap-1.5 py-1 px-1 rounded-full transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#D4954D]/70"
                title="{{ $isAvail ? 'Nonaktifkan' : 'Aktifkan' }}"
                aria-label="{{ $isAvail ? 'Nonaktifkan' : 'Aktifkan' }} {{ $menu->name }}"
            >
                <div class="w-9 h-5 rounded-full p-0.5 transition-colors {{ $isAvail ? 'bg-[#4F7A4A]' : 'bg-[#775533]/30' }}">
                    <div class="w-4 h-4 rounded-full bg-white shadow-sm transition-transform {{ $isAvail ? 'translate-x-4' : 'translate-x-0' }}"></div>
                </div>
            </button>
        </form>
    </div>
</div>

{{-- Action bottom sheet for this menu item --}}
<x-mobile.bottom-sheet :id="$sheetId" :title="$menu->name" :subtitle="$menu->category?->name">
    <div class="space-y-1">
        {{-- Edit --}}
        @if($editCallback)
            <button
                type="button"
                onclick="closeBottomSheet('{{ $sheetId }}'); {{ $editCallback }}({{ $menuJson }});"
                class="flex items-center gap-3 w-full px-4 py-3.5 rounded-2xl text-sm font-semibold text-[#290024] hover:bg-[#E3DEA4]/50 transition-colors text-left min-h-[48px]"
            >
                <i data-lucide="edit" class="w-5 h-5 text-[#775533] shrink-0"></i>
                <span>Ubah</span>
            </button>
        @endif

        {{-- Duplicate --}}
        @if($duplicateRoute)
            <form action="{{ $duplicateRoute }}" method="POST">
                @csrf
                <button
                    type="submit"
                    class="flex items-center gap-3 w-full px-4 py-3.5 rounded-2xl text-sm font-semibold text-[#290024] hover:bg-[#E3DEA4]/50 transition-colors text-left min-h-[48px]"
                >
                    <i data-lucide="copy" class="w-5 h-5 text-[#775533] shrink-0"></i>
                    <span>Duplikasi</span>
                </button>
            </form>
        @endif

        {{-- Toggle availability --}}
        <form action="{{ $toggleAvailableRoute }}" method="POST">
            @csrf
            <button
                type="submit"
                class="flex items-center gap-3 w-full px-4 py-3.5 rounded-2xl text-sm font-semibold transition-colors text-left min-h-[48px] {{ $isAvail ? 'text-[#C98A2B] hover:bg-[#C98A2B]/10' : 'text-[#4F7A4A] hover:bg-[#4F7A4A]/10' }}"
            >
                <i data-lucide="{{ $isAvail ? 'eye-off' : 'eye' }}" class="w-5 h-5 shrink-0"></i>
                <span>{{ $isAvail ? 'Nonaktifkan (Habis)' : 'Aktifkan (Tersedia)' }}</span>
            </button>
        </form>

        {{-- Separator --}}
        <div class="h-px bg-[#E3DEA4]/60 my-1"></div>

        {{-- Delete --}}
        @if($deleteCallback)
            <button
                type="button"
                onclick="closeBottomSheet('{{ $sheetId }}'); {{ $deleteCallback }}({{ $menu->id }}, '{{ addslashes($menu->name) }}');"
                class="flex items-center gap-3 w-full px-4 py-3.5 rounded-2xl text-sm font-semibold text-[#B03A2E] hover:bg-[#B03A2E]/10 transition-colors text-left min-h-[48px]"
            >
                <i data-lucide="trash-2" class="w-5 h-5 shrink-0"></i>
                <span>Hapus</span>
            </button>
        @endif
    </div>
</x-mobile.bottom-sheet>
