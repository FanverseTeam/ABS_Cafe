@props([
    'items' => [], // array of ['id' => 'all', 'label' => 'Semua', 'icon' => null, 'badge' => null, 'active' => false]
    'id' => null,
])

<div {{ $attributes->merge(['class' => 'flex items-center gap-2 overflow-x-auto no-scrollbar py-1 px-0.5 -mx-1']) }} @if($id) id="{{ $id }}" @endif>
    @foreach($items as $item)
        @php
            $isActive = $item['active'] ?? false;
        @endphp
        <button type="button"
                data-tab="{{ $item['id'] }}"
                class="pill-tab-btn shrink-0 inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-semibold transition-all duration-200 {{ $isActive ? 'bg-[#290024] text-[#F2F5E2] shadow-sm' : 'bg-[#E3DEA4]/50 text-[#775533] border border-[#E3DEA4] hover:bg-[#E3DEA4]' }}">
            @if(!empty($item['icon']))
                <i data-lucide="{{ $item['icon'] }}" class="w-3.5 h-3.5"></i>
            @endif
            <span>{{ $item['label'] }}</span>
            @if(!empty($item['badge']))
                <span class="ml-1 px-1.5 py-0.2 rounded-full text-[10px] {{ $isActive ? 'bg-[#D4954D] text-[#290024]' : 'bg-[#290024]/10 text-[#290024]' }}">
                    {{ $item['badge'] }}
                </span>
            @endif
        </button>
    @endforeach
    {{ $slot }}
</div>
