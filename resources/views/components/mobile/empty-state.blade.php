@props([
    'icon' => 'inbox',
    'title' => 'Belum Ada Data',
    'message' => 'Data untuk bagian ini masih kosong.',
    'actionLabel' => null,
    'actionCallback' => null,
    'actionHref' => null,
])

<div {{ $attributes->merge(['class' => 'p-8 text-center flex flex-col items-center justify-center bg-[#F2F5E2] rounded-3xl border border-[#E3DEA4]/60']) }}>
    <div class="w-16 h-16 rounded-full bg-[#E3DEA4]/40 text-[#775533] flex items-center justify-center mb-3">
        <i data-lucide="{{ $icon }}" class="w-8 h-8"></i>
    </div>
    <h4 class="font-serif font-bold text-base text-[#290024]">{{ $title }}</h4>
    <p class="text-xs text-[#775533] max-w-xs mt-1 leading-relaxed">{{ $message }}</p>

    @if($actionLabel)
        <div class="mt-4">
            @if($actionHref)
                <a href="{{ $actionHref }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-[#290024] text-[#F2F5E2] text-xs font-semibold hover:bg-[#775533] transition-colors shadow-sm">
                    {{ $actionLabel }}
                </a>
            @elseif($actionCallback)
                <button type="button" onclick="{{ $actionCallback }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-[#290024] text-[#F2F5E2] text-xs font-semibold hover:bg-[#775533] transition-colors shadow-sm">
                    {{ $actionLabel }}
                </button>
            @endif
        </div>
    @endif
</div>
