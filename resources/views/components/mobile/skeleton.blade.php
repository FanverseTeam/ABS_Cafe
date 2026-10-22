@props([
    'count' => 5,
    'type'  => 'list',  // 'list' | 'grid' | 'card'
])

{{--
    Skeleton — loading placeholder rows/cards.

    Usage:
      <x-mobile.skeleton :count="3" type="list" />
      <x-mobile.skeleton :count="6" type="grid" />
--}}

@if($type === 'grid')
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
        @for($i = 0; $i < $count; $i++)
            <div class="bg-[#FAFCF3] rounded-2xl border border-[#E3DEA4]/60 overflow-hidden animate-pulse">
                <div class="h-28 bg-[#E3DEA4]/60"></div>
                <div class="p-3 space-y-2">
                    <div class="h-3 bg-[#E3DEA4]/80 rounded-full w-3/4"></div>
                    <div class="h-2.5 bg-[#E3DEA4]/60 rounded-full w-1/2"></div>
                </div>
            </div>
        @endfor
    </div>
@else
    <div class="space-y-2">
        @for($i = 0; $i < $count; $i++)
            <div class="flex items-center gap-3 p-3 bg-[#FAFCF3] rounded-2xl border border-[#E3DEA4]/60 animate-pulse">
                <div class="w-16 h-16 rounded-xl bg-[#E3DEA4]/60 shrink-0"></div>
                <div class="flex-1 space-y-2">
                    <div class="h-3 bg-[#E3DEA4]/80 rounded-full w-3/4"></div>
                    <div class="h-2.5 bg-[#E3DEA4]/60 rounded-full w-1/2"></div>
                    <div class="h-2.5 bg-[#E3DEA4]/50 rounded-full w-1/3"></div>
                </div>
                <div class="w-8 h-5 bg-[#E3DEA4]/60 rounded-full shrink-0"></div>
            </div>
        @endfor
    </div>
@endif
