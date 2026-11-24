@php
    $preserve = collect(request()->query())->except(['periode', 'mulai', 'selesai'])->all();
    $periods = [
        'hari_ini' => 'Hari Ini',
        '7_hari' => '7 Hari',
        '30_hari' => '30 Hari',
        'bulan_ini' => 'Bulan Ini',
    ];
    $baseUrl = request()->url();
@endphp

<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
    <div class="flex items-center gap-2 text-xs text-cafe-muted">
        <i data-lucide="calendar-range" class="w-4 h-4 text-cafe-gold"></i>
        <span>Periode: <strong class="text-cafe-dark">{{ $period->label }}</strong></span>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center gap-2">
        {{-- Dropdown Periode --}}
        <div class="relative flex items-center bg-cafe-beige/50 border border-cafe-beige rounded-full px-3 py-1.5 shadow-2xs">
            <i data-lucide="clock" class="w-3.5 h-3.5 text-cafe-gold mr-2 shrink-0"></i>
            <select onchange="if(this.value) window.location.href=this.value"
                    class="bg-transparent text-xs text-cafe-dark font-semibold focus:outline-none cursor-pointer pr-4 appearance-none">
                @foreach($periods as $key => $label)
                    <option value="{{ $baseUrl . '?' . http_build_query(array_merge($preserve, ['periode' => $key])) }}"
                            {{ $period->key === $key ? 'selected' : '' }}
                            class="bg-[#F2F5E2] text-cafe-dark py-1 font-medium">
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-cafe-muted absolute right-2.5 pointer-events-none"></i>
        </div>

        <form action="{{ $baseUrl }}" method="GET" class="flex items-center justify-between gap-1.5 bg-cafe-card border border-cafe-beige rounded-full px-3 py-1 text-xs">
            @foreach($preserve as $k => $v)
                <input type="hidden" name="{{ $k }}" value="{{ $v }}">
            @endforeach
            <input type="hidden" name="periode" value="kustom">
            <input type="date" name="mulai" value="{{ $period->key === 'kustom' ? $period->start->toDateString() : now()->subDays(6)->toDateString() }}"
                   class="bg-transparent text-[11px] text-cafe-text focus:outline-none cursor-pointer">
            <span class="text-cafe-muted text-[11px]">&ndash;</span>
            <input type="date" name="selesai" value="{{ $period->key === 'kustom' ? $period->end->toDateString() : now()->toDateString() }}"
                   class="bg-transparent text-[11px] text-cafe-text focus:outline-none cursor-pointer">
            <button type="submit" class="px-2.5 py-1 rounded-full bg-cafe-gold hover:bg-cafe-bronze text-[#290024] text-[11px] font-bold transition-colors">
                Terapkan
            </button>
        </form>
    </div>
</div>
