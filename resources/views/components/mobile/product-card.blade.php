@props([
    'name' => '',
    'price' => 0,
    'image' => null,
    'badge' => null,
    'description' => null,
    'isAvailable' => true,
    'onAdd' => null,
])

<div {{ $attributes->merge(['class' => 'bg-[#FAFCF3] rounded-2xl border border-[#E3DEA4]/70 p-3 shadow-sm flex flex-col justify-between transition-all hover:shadow-md ' . (!$isAvailable ? 'opacity-50 pointer-events-none' : '')]) }}>
    <div>
        <!-- Image Box -->
        <div class="relative w-full aspect-square rounded-xl bg-[#F2F5E2] overflow-hidden mb-2.5 flex items-center justify-center">
            @if($image)
                <img src="{{ $image }}" alt="{{ $name }}" class="w-full h-full object-cover">
            @else
                <div class="w-12 h-12 rounded-full bg-[#E3DEA4]/40 flex items-center justify-center text-[#D4954D]">
                    <i data-lucide="coffee" class="w-6 h-6"></i>
                </div>
            @endif

            @if($badge)
                <span class="absolute top-2 left-2 px-2 py-0.5 rounded-full bg-[#D4954D] text-[#290024] text-[9px] font-bold shadow-sm">
                    {{ $badge }}
                </span>
            @endif
        </div>

        <!-- Info -->
        <h4 class="font-bold text-xs sm:text-sm text-[#290024] line-clamp-1 leading-snug">{{ $name }}</h4>
        @if($description)
            <p class="text-[10px] text-[#775533] mt-0.5 line-clamp-1">{{ $description }}</p>
        @endif
    </div>

    <!-- Price & Add Button -->
    <div class="mt-3 flex items-center justify-between gap-2 pt-2 border-t border-[#E3DEA4]/40">
        <div>
            <span class="text-[9px] text-[#775533] uppercase tracking-wider block">Harga</span>
            <span class="font-serif font-bold text-sm sm:text-base text-[#290024]">
                Rp {{ number_format((float)$price, 0, ',', '.') }}
            </span>
        </div>

        @if($onAdd)
            <button type="button" 
                    onclick="{{ $onAdd }}" 
                    class="w-8 h-8 rounded-full bg-[#290024] hover:bg-[#D4954D] text-[#F2F5E2] hover:text-[#290024] flex items-center justify-center transition-all shadow-sm active:scale-95" 
                    aria-label="Tambah {{ $name }}">
                <i data-lucide="plus" class="w-4 h-4"></i>
            </button>
        @endif
    </div>
</div>
