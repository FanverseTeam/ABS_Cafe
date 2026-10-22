@props([
    'menu',
    'toggleAvailableRoute',
    'editCallback'      => null,
    'deleteCallback'    => null,
    'duplicateRoute'    => null,
])

{{--
    Menu List Item — compact list card for mobile/tablet Manajemen Menu.
    - Baris penuh lebar, tinggi ±88px, rounded-2xl
    - Kiri: thumbnail 64px membulat, pita Best Seller di sudut thumbnail
    - Tengah: flex-1, min-w-0, nama maks 2 baris, kategori Olive Wood, harga satu baris
    - Kanan: badge status Tersedia/Habis di atas, toggle di bawah (touch target >= 44px)
    - Ketuk baris membuka bottom sheet aksi
--}}

@php
    $sheetId   = 'menu-action-' . $menu->id;
    $isBest    = (bool) $menu->is_best_seller;
    $isAvail   = (bool) $menu->is_available;
    $menuJson  = Illuminate\Support\Js::from($menu->load('variants'));
@endphp

<div {{ $attributes->merge(['class' => 'flex items-center gap-3 p-3 bg-[#FAFCF3] rounded-2xl border border-[#E3DEA4]/60 shadow-sm transition-all hover:border-[#290024]/20 min-h-[88px]']) }}>
    {{-- Thumbnail with Best Seller ribbon in the corner --}}
    <button
        type="button"
        onclick="openBottomSheet('{{ $sheetId }}')"
        class="relative w-16 h-16 rounded-xl overflow-hidden bg-[#E3DEA4]/40 shrink-0 text-left focus:outline-none focus-visible:ring-2 focus-visible:ring-[#D4954D]/70"
        aria-label="Aksi untuk {{ $menu->name }}"
        aria-controls="{{ $sheetId }}"
    >
        <img
            src="{{ $menu->image }}"
            alt="{{ $menu->name }}"
            class="w-full h-full object-cover"
            loading="lazy"
        >
        @if($isBest)
            <span class="absolute top-1 left-1 bg-[#D4954D] text-[#290024] text-[8px] font-bold px-1.5 py-0.5 rounded shadow-xs leading-none">
                Best Seller
            </span>
        @endif
    </button>

    {{-- Center content --}}
    <button
        type="button"
        onclick="openBottomSheet('{{ $sheetId }}')"
        class="flex-1 min-w-0 text-left focus:outline-none"
        aria-controls="{{ $sheetId }}"
        aria-label="Aksi untuk {{ $menu->name }}"
    >
        <h3 class="font-bold text-xs sm:text-sm text-[#290024] line-clamp-2 leading-tight">
            {{ $menu->name }}
        </h3>
        <p class="text-[11px] text-[#775533] mt-0.5 truncate font-medium">
            {{ $menu->category?->name }}
        </p>
        <p class="font-semibold text-xs text-[#290024] mt-1 whitespace-nowrap tabular-nums">
            {{ $menu->priceRangeLabel() }}
        </p>
    </button>

    {{-- Right: status badge on top + toggle on bottom (touch target >= 44px) --}}
    <div class="flex flex-col items-end justify-center gap-1.5 shrink-0" onclick="event.stopPropagation()">
        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full leading-tight {{ $isAvail ? 'bg-[#4F7A4A]/10 text-[#4F7A4A]' : 'bg-[#B03A2E]/10 text-[#B03A2E]' }}">
            {{ $isAvail ? 'Tersedia' : 'Habis' }}
        </span>

        <form action="{{ $toggleAvailableRoute }}" method="POST">
            @csrf
            <button
                type="submit"
                class="min-w-[44px] min-h-[44px] -my-2 -mr-2 flex items-center justify-center p-1 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#D4954D]/70 rounded-full"
                title="{{ $isAvail ? 'Tandai Habis' : 'Tandai Tersedia' }}"
                aria-label="{{ $isAvail ? 'Tandai Habis' : 'Tandai Tersedia' }} {{ $menu->name }}"
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
                <span>Ubah Menu</span>
            </button>
        @endif

        {{-- Toggle Availability --}}
        <form action="{{ $toggleAvailableRoute }}" method="POST">
            @csrf
            <button
                type="submit"
                class="flex items-center gap-3 w-full px-4 py-3.5 rounded-2xl text-sm font-semibold text-[#290024] hover:bg-[#E3DEA4]/50 transition-colors text-left min-h-[48px]"
            >
                <i data-lucide="{{ $isAvail ? 'eye-off' : 'eye' }}" class="w-5 h-5 text-[#775533] shrink-0"></i>
                <span>{{ $isAvail ? 'Nonaktifkan (Tandai Habis)' : 'Aktifkan (Tandai Tersedia)' }}</span>
            </button>
        </form>

        {{-- Duplicate --}}
        @if($duplicateRoute)
            <form action="{{ $duplicateRoute }}" method="POST">
                @csrf
                <button
                    type="submit"
                    class="flex items-center gap-3 w-full px-4 py-3.5 rounded-2xl text-sm font-semibold text-[#290024] hover:bg-[#E3DEA4]/50 transition-colors text-left min-h-[48px]"
                >
                    <i data-lucide="copy" class="w-5 h-5 text-[#775533] shrink-0"></i>
                    <span>Duplikat Menu</span>
                </button>
            </form>
        @endif

        {{-- Resep & HPP --}}
        <a
            href="{{ route('admin.catalog.menu.recipes', $menu->id) }}"
            class="flex items-center gap-3 w-full px-4 py-3.5 rounded-2xl text-sm font-semibold text-[#290024] hover:bg-[#E3DEA4]/50 transition-colors text-left min-h-[48px]"
        >
            <i data-lucide="scale" class="w-5 h-5 text-[#775533] shrink-0"></i>
            <span>Resep &amp; HPP</span>
        </a>

        <div class="h-px bg-[#E3DEA4]/60 my-2"></div>

        {{-- Delete --}}
        @if($deleteCallback)
            <button
                type="button"
                onclick="closeBottomSheet('{{ $sheetId }}'); {{ $deleteCallback }}({{ $menu->id }}, '{{ addslashes($menu->name) }}');"
                class="flex items-center gap-3 w-full px-4 py-3.5 rounded-2xl text-sm font-semibold text-[#B03A2E] hover:bg-[#B03A2E]/10 transition-colors text-left min-h-[48px]"
            >
                <i data-lucide="trash-2" class="w-5 h-5 text-[#B03A2E] shrink-0"></i>
                <span>Hapus Menu</span>
            </button>
        @endif
    </div>
</x-mobile.bottom-sheet>
