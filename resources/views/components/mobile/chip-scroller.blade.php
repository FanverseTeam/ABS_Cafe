@props([
    'chips'        => [],   // array of ['label', 'href', 'active', 'count' (optional)]
    'id'           => 'chip-scroller-' . uniqid(),
    'fadedEdges'   => true,
])

{{--
    Chip Scroller — horizontal scroll row of filter chips with fade effect at edges.
    Active chip is scrolled into view on page load via JS.

    Usage:
      <x-mobile.chip-scroller :chips="[
          ['label' => 'Semua', 'href' => route('...', ['category_id' => 'all']), 'active' => !request('category_id')],
          ['label' => $cat->name, 'count' => 5, 'href' => route('...'), 'active' => request('category_id') == $cat->id],
      ]" />
--}}

<div id="{{ $id }}" class="relative {{ $fadedEdges ? 'chip-scroller-wrap' : '' }}">
    @if($fadedEdges)
    <style>
        #{{ $id }} .chip-scroller-inner::before,
        #{{ $id }} .chip-scroller-inner::after {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            width: 24px;
            z-index: 1;
            pointer-events: none;
        }
        #{{ $id }}-container::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 24px;
            background: linear-gradient(to right, #F2F5E2, transparent);
            z-index: 1;
            pointer-events: none;
        }
        #{{ $id }}-container::after {
            content: '';
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            width: 24px;
            background: linear-gradient(to left, #F2F5E2, transparent);
            z-index: 1;
            pointer-events: none;
        }
    </style>
    @endif

    <div
        id="{{ $id }}-container"
        class="relative overflow-hidden"
    >
        <div
            id="{{ $id }}-inner"
            class="flex items-center gap-2 overflow-x-auto scroll-smooth py-1"
            style="-webkit-overflow-scrolling: touch; scrollbar-width: none; -ms-overflow-style: none;"
        >
            @foreach($chips as $chip)
                @php
                    $isActive = $chip['active'] ?? false;
                @endphp
                <a
                    href="{{ $chip['href'] }}"
                    data-chip-active="{{ $isActive ? 'true' : 'false' }}"
                    class="flex-shrink-0 px-4 py-2 rounded-full text-xs font-semibold whitespace-nowrap transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#D4954D]/70 {{ $isActive ? 'bg-[#290024] text-[#F2F5E2] shadow-sm' : 'bg-[#F2F5E2] border border-[#E3DEA4] text-[#290024] hover:bg-[#E3DEA4]/60' }}"
                >
                    {{ $chip['label'] }}@if(isset($chip['count']))<span class="ml-1 opacity-70">{{ $chip['count'] }}</span>@endif
                </a>
            @endforeach
        </div>
    </div>
</div>

<script>
    (function () {
        const inner = document.getElementById('{{ $id }}-inner');
        if (!inner) { return; }
        const active = inner.querySelector('[data-chip-active="true"]');
        if (active) {
            setTimeout(function () {
                active.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
            }, 80);
        }
    })();
</script>
