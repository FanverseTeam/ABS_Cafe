@props([
    'theme' => 'light',
    'class' => '',
])

@php
    $isDark = $theme === 'dark';
@endphp

<div {{ $attributes->merge(['class' => "w-full flex items-center justify-center py-3 print:hidden select-none {$class}"]) }}>
    <div @class([
        'inline-flex items-center gap-2.5 px-3.5 py-1.5 rounded-full border backdrop-blur-xs text-xs transition-all duration-200 group shadow-xs',
        'border-[#775533]/40 bg-[#000000]/60 text-[#E3DEA4]/80 hover:text-[#F2F5E2] hover:border-[#D4954D]/60' => $isDark,
        'border-[#E3DEA4] bg-[#F2F5E2]/90 text-[#775533] hover:text-[#290024] hover:border-[#D4954D]/50' => !$isDark,
    ])>
        <div @class([
            'w-5 h-5 sm:w-6 sm:h-6 rounded-full overflow-hidden border shrink-0 bg-[#F2F5E2] flex items-center justify-center ring-1',
            'border-[#D4954D]/50 ring-white/10' => $isDark,
            'border-[#775533]/30 ring-black/5' => !$isDark,
        ])>
            <img src="{{ asset('images/logo-team.jpeg') }}"
                 alt="Logo Favnverse Team"
                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                 loading="lazy">
        </div>
        <div class="flex items-center gap-1.5 tracking-normal">
            <span class="text-[11px] sm:text-xs">By <strong class="font-semibold text-inherit">Favnverse team</strong></span>
            <span class="text-[10px] sm:text-[11px] opacity-40">&bull;</span>
            <span @class([
                'text-[10px] sm:text-[11px] font-medium',
                'text-[#D4954D]' => $isDark,
                'text-[#775533]' => !$isDark,
            ])>SMKN 1 Boyolangu</span>
        </div>
    </div>
</div>
