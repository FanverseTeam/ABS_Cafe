@props([
    'count' => 5,
    'type'  => 'square-grid',  // 'square-grid' | 'list' | 'card'
])

{{--
    Skeleton — loading placeholder rows/cards.

    Usage:
      <x-mobile.skeleton :count="6" type="square-grid" />
      <x-mobile.skeleton :count="3" type="list" />
--}}

@if($type === 'square-grid' || $type === 'grid')
    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
        @for($i = 0; $i < $count; $i++)
            <div class="bg-[#FAFCF3] rounded-2xl border border-[#E3DEA4]/60 overflow-hidden animate-pulse">
                <div class="aspect-square bg-[#E3DEA4]/60"></div>
                <div class="px-2.5 pt-2 pb-1 space-y-1.5">
                    <div class="h-3 bg-[#E3DEA4]/80 rounded-full w-3/4"></div>
                    <div class="h-2.5 bg-[#E3DEA4]/60 rounded-full w-1/2"></div>
                    <div class="h-2.5 bg-[#E3DEA4]/50 rounded-full w-2/3"></div>
                </div>
                <div class="px-2.5 py-2 border-t border-[#E3DEA4]/40 flex justify-between">
                    <div class="h-2.5 bg-[#E3DEA4]/50 rounded-full w-12"></div>
                    <div class="w-8 h-4 bg-[#E3DEA4]/60 rounded-full"></div>
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
