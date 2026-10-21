@props([
    'label' => '',
    'value' => null,
    'fullWidth' => false,
])

<div class="{{ $fullWidth ? 'col-span-2' : '' }} min-w-0" data-card-field>
    <span class="block text-[10px] font-semibold text-[#775533] uppercase tracking-wider mb-0.5 truncate">{{ $label }}</span>
    <div class="text-xs font-medium text-cafe-dark break-words">
        {{ $value ?? $slot }}
    </div>
</div>
