@props([
    'items' => [],
])

{{--
    Breadcrumb bersama untuk seluruh halaman back-office.
    Disembunyikan di bawah breakpoint lg (mobile & tablet) dan tetap tampil di desktop.
--}}
<nav {{ $attributes->merge(['class' => 'cafe-breadcrumb hidden lg:flex items-center gap-1.5 text-xs text-cafe-muted']) }} aria-label="Breadcrumb">
    @foreach($items as $item)
        @if(! $loop->first)
            <i data-lucide="chevron-right" class="w-3 h-3 text-cafe-muted/60"></i>
        @endif

        @if(! empty($item['url']))
            <a href="{{ $item['url'] }}" class="hover:text-cafe-dark flex items-center gap-1">
                @if(! empty($item['icon']))
                    <i data-lucide="{{ $item['icon'] }}" class="w-3.5 h-3.5"></i>
                @endif
                <span>{{ $item['label'] }}</span>
            </a>
        @else
            <span class="font-semibold text-cafe-dark">{{ $item['label'] }}</span>
        @endif
    @endforeach
</nav>
