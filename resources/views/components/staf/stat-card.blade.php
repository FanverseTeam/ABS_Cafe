@props([
    'label',
    'value',
    'tone' => 'cream',
])

@php
    $surface = $tone === 'midnight'
        ? 'border-midnight-200 bg-midnight text-beige'
        : 'border-custard-200 bg-beige-50/95 text-midnight';
    $labelColor = $tone === 'midnight' ? 'text-custard' : 'text-olive';
    $valueColor = $tone === 'midnight' ? 'text-beige' : 'text-midnight';
    $iconSurface = $tone === 'midnight'
        ? 'bg-apricot text-midnight'
        : 'bg-apricot/20 text-midnight';
@endphp

<article {{ $attributes->merge([
    'class' => "rounded-2xl border {$surface} p-3.5 shadow-[0_18px_45px_-24px_rgba(41,0,36,0.45)] backdrop-blur-sm",
]) }}>
    <div class="flex items-center gap-3">
        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full {{ $iconSurface }}">
            {{ $icon }}
        </span>
        <div class="min-w-0">
            <p class="text-[10px] font-semibold uppercase tracking-[0.14em] {{ $labelColor }}">{{ $label }}</p>
            <p class="mt-0.5 truncate text-sm font-bold {{ $valueColor }}">{{ $value }}</p>
        </div>
    </div>
</article>
