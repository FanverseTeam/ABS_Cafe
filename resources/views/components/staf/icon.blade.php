@props([
    'name',
    'class' => 'h-5 w-5',
])

<svg {{ $attributes->merge([
    'class' => $class,
    'xmlns' => 'http://www.w3.org/2000/svg',
    'viewBox' => '0 0 24 24',
    'fill' => 'none',
    'stroke' => 'currentColor',
    'stroke-width' => '1.8',
    'stroke-linecap' => 'round',
    'stroke-linejoin' => 'round',
    'aria-hidden' => 'true',
]) }}>
    @switch($name)
        @case('arrow-right')
            <path d="M5 12h14" />
            <path d="m13 6 6 6-6 6" />
            @break

        @case('arrow-down')
            <path d="M12 5v14" />
            <path d="m6 13 6 6 6-6" />
            @break

        @case('settings')
            <path d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z" />
            <path d="M19.4 15a1.7 1.7 0 0 0 .34 1.88l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06A1.7 1.7 0 0 0 15 19.4a1.7 1.7 0 0 0-1 .6 1.7 1.7 0 0 0-.4 1.1V21a2 2 0 1 1-4 0v-.09A1.7 1.7 0 0 0 8.6 19.4a1.7 1.7 0 0 0-1.88.34l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.7 1.7 0 0 0 4.6 15a1.7 1.7 0 0 0-.6-1 1.7 1.7 0 0 0-1.1-.4H3a2 2 0 1 1 0-4h.09A1.7 1.7 0 0 0 4.6 8.6a1.7 1.7 0 0 0-.34-1.88l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.7 1.7 0 0 0 9 4.6a1.7 1.7 0 0 0 1-.6 1.7 1.7 0 0 0 .4-1.1V3a2 2 0 1 1 4 0v.09A1.7 1.7 0 0 0 15.4 4.6a1.7 1.7 0 0 0 1.88-.34l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.7 1.7 0 0 0 19.4 9c.1.38.3.72.6 1 .3.26.7.4 1.1.4H21a2 2 0 1 1 0 4h-.09a1.7 1.7 0 0 0-1.51.6Z" />
            @break

        @case('chart')
            <path d="M4 20V4" />
            <path d="M4 20h16" />
            <path d="M8 16v-5" />
            <path d="M12 16V8" />
            <path d="M16 16v-3" />
            <path d="m7 8 4-3 3 2 5-4" />
            @break

        @case('cash')
            <rect x="3" y="6" width="18" height="12" rx="2" />
            <circle cx="12" cy="12" r="2.5" />
            <path d="M7 9.5h.01M17 14.5h.01" />
            @break

        @case('receipt')
            <path d="M6 3h12a1 1 0 0 1 1 1v18l-3-2-4 2-4-2-3 2V4a1 1 0 0 1 1-1Z" />
            <path d="M9 8h6M9 12h6M9 16h3" />
            @break

        @case('box')
            <path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Z" />
            <path d="m4.5 7.8 7.5 4.3 7.5-4.3" />
            <path d="M12 12.1V21" />
            @break

        @case('shield-check')
            <path d="M12 3 5 6v5c0 4.5 3 8.5 7 10 4-1.5 7-5.5 7-10V6l-7-3Z" />
            <path d="m9 12 2 2 4-4" />
            @break

        @case('sparkles')
            <path d="m12 3 1.2 3.3L16.5 7.5l-3.3 1.2L12 12l-1.2-3.3L7.5 7.5l3.3-1.2L12 3Z" />
            <path d="m19 14 .7 1.8 1.8.7-1.8.7L19 19l-.7-1.8-1.8-.7 1.8-.7L19 14Z" />
            <path d="m5 14 .6 1.5 1.4.5-1.4.5L5 18l-.6-1.5L3 16l1.4-.5L5 14Z" />
            @break

        @case('coffee')
            <path d="M4 8h13v5a5 5 0 0 1-5 5H9a5 5 0 0 1-5-5V8Z" />
            <path d="M17 9h1.8a2.7 2.7 0 0 1 0 5.4H17" />
            <path d="M7 4c0 1 1 1.4 1 2.4M11 4c0 1 1 1.4 1 2.4M15 4c0 1 1 1.4 1 2.4" />
            <path d="M3 21h16" />
            @break
    @endswitch
</svg>
