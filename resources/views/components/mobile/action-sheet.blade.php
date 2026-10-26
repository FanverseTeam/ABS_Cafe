@props([
    'id'          => 'action-sheet-' . uniqid(),
    'label'       => 'Lainnya',
    'icon'        => 'more-vertical',
    'buttonClass' => '',
])

{{--
    Action Sheet — three-dot menu that opens a bottom-sheet with a list of actions.

    Usage:
      <x-mobile.action-sheet id="menu-actions">
        <button type="button" class="..." onclick="...">Gabungkan Menu</button>
        <button type="button" class="..." onclick="...">Kelola Kategori</button>
      </x-mobile.action-sheet>
--}}

<div class="relative shrink-0">
    <button
        type="button"
        onclick="openBottomSheet('{{ $id }}')"
        aria-label="{{ $label }}"
        aria-controls="{{ $id }}"
        class="flex items-center justify-center w-10 h-10 rounded-full bg-[#F2F5E2] border border-[#E3DEA4] text-[#290024] transition-colors hover:bg-[#E3DEA4]/60 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#D4954D]/70 {{ $buttonClass }}"
    >
        <i data-lucide="{{ $icon }}" class="w-4 h-4 text-[#775533]"></i>
    </button>
</div>

<x-mobile.bottom-sheet :id="$id" :title="$label">
    <div class="space-y-1">
        {{ $slot }}
    </div>
</x-mobile.bottom-sheet>
