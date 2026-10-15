@props([
    'title' => '',
    'value' => '0',
    'icon' => 'activity',
    'trend' => null, // e.g. '+12%' or '-5%'
    'trendUp' => true,
    'subtitle' => null,
])

<div {{ $attributes->merge(['class' => 'bg-[#FAFCF3] p-4 rounded-2xl border border-[#E3DEA4]/70 shadow-sm flex flex-col justify-between']) }}>
    <div class="flex items-center justify-between gap-2">
        <span class="text-xs font-medium text-[#775533] line-clamp-1">{{ $title }}</span>
        <div class="w-8 h-8 rounded-xl bg-[#D4954D]/15 text-[#D4954D] flex items-center justify-center shrink-0">
            <i data-lucide="{{ $icon }}" class="w-4 h-4"></i>
        </div>
    </div>

    <div class="mt-3">
        <p class="font-serif font-bold text-xl sm:text-2xl text-[#290024] tracking-tight leading-none">{{ $value }}</p>
        
        @if($trend !== null || $subtitle)
            <div class="mt-2 flex items-center gap-1.5 flex-wrap">
                @if($trend !== null)
                    <span class="inline-flex items-center text-[10px] font-semibold px-1.5 py-0.5 rounded-full {{ $trendUp ? 'bg-[#4F7A4A]/15 text-[#4F7A4A]' : 'bg-[#B03A2E]/15 text-[#B03A2E]' }}">
                        <i data-lucide="{{ $trendUp ? 'trending-up' : 'trending-down' }}" class="w-3 h-3 mr-0.5"></i>
                        {{ $trend }}
                    </span>
                @endif
                @if($subtitle)
                    <span class="text-[10px] text-[#775533]/80">{{ $subtitle }}</span>
                @endif
            </div>
        @endif
    </div>
</div>
