@props([
    'name' => 'option',
    'value' => '',
    'checked' => false,
    'icon' => null,
    'title' => '',
    'description' => null,
    'badge' => null,
    'id' => null,
])

@php
    $inputId = $id ?? ($name . '-' . \Illuminate\Support\Str::slug($value));
@endphp

<label for="{{ $inputId }}" 
       class="relative flex items-center gap-3.5 p-3.5 rounded-2xl border cursor-pointer transition-all duration-200 select-none bg-[#FAFCF3] border-[#E3DEA4] has-[:checked]:border-[#D4954D] has-[:checked]:bg-[#E3DEA4]/25 has-[:checked]:shadow-sm hover:border-[#D4954D]/60">
    <input type="radio" 
           id="{{ $inputId }}" 
           name="{{ $name }}" 
           value="{{ $value }}" 
           {{ $checked ? 'checked' : '' }}
           {{ $attributes->except(['class']) }}
           class="sr-only peer">

    <!-- Icon Box -->
    @if($icon)
        <div class="w-10 h-10 rounded-2xl bg-[#E3DEA4]/50 text-[#775533] peer-checked:bg-[#290024] peer-checked:text-[#D4954D] flex items-center justify-center shrink-0 transition-colors">
            <i data-lucide="{{ $icon }}" class="w-5 h-5"></i>
        </div>
    @endif

    <!-- Content -->
    <div class="flex-1 min-w-0">
        <div class="flex items-center justify-between gap-2">
            <span class="text-xs sm:text-sm font-bold text-[#290024] peer-checked:text-[#290024] truncate">{{ $title }}</span>
            @if($badge)
                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-[#D4954D]/20 text-[#290024]">{{ $badge }}</span>
            @endif
        </div>
        @if($description)
            <p class="text-[11px] text-[#775533] mt-0.5 leading-snug line-clamp-2">{{ $description }}</p>
        @endif
        {{ $slot }}
    </div>

    <!-- Custom Radio Circle Indicator -->
    <div class="w-5 h-5 rounded-full border-2 border-[#775533]/40 peer-checked:border-[#290024] peer-checked:bg-[#290024] flex items-center justify-center shrink-0 transition-colors">
        <span class="w-2 h-2 rounded-full bg-[#FAFCF3] opacity-0 peer-checked:opacity-100 transition-opacity"></span>
    </div>
</label>
