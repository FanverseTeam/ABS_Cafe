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

    <div class="flex flex-wrap items-center gap-2">
        <div class="inline-flex items-center gap-1 p-1 rounded-full bg-cafe-beige/50 border border-cafe-beige">
            @foreach($periods as $key => $label)
                <a href="{{ $baseUrl . '?' . http_build_query(array_merge($preserve, ['periode' => $key])) }}"
                   class="px-3.5 py-1.5 rounded-full text-xs font-medium transition-all {{ $period->key === $key ? 'bg-cafe-dark text-cafe-cream shadow-sm' : 'text-cafe-muted hover:bg-cafe-beige hover:text-cafe-dark' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <form action="{{ $baseUrl }}" method="GET" class="flex items-center gap-1.5 bg-cafe-card border border-cafe-beige rounded-full px-2 py-1">
            @foreach($preserve as $k => $v)
                <input type="hidden" name="{{ $k }}" value="{{ $v }}">
            @endforeach
            <input type="hidden" name="periode" value="kustom">
            <input type="date" name="mulai" value="{{ $period->key === 'kustom' ? $period->start->toDateString() : now()->subDays(6)->toDateString() }}"
                   class="bg-transparent text-[11px] text-cafe-text focus:outline-none cursor-pointer">
            <span class="text-cafe-muted text-[11px]">&ndash;</span>
            <input type="date" name="selesai" value="{{ $period->key === 'kustom' ? $period->end->toDateString() : now()->toDateString() }}"
                   class="bg-transparent text-[11px] text-cafe-text focus:outline-none cursor-pointer">
            <button type="submit" class="px-2.5 py-1 rounded-full bg-cafe-gold hover:bg-cafe-bronze text-white text-[11px] font-semibold transition-colors">
                Terapkan
            </button>
        </form>
    </div>
</div>
