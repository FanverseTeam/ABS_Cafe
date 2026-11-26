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

    <div class="flex flex-col sm:flex-row sm:items-center gap-2.5 sm:gap-4">
        {{-- Dropdown Periode (Digeser agak ke kiri dengan margin kanan) --}}
        <div class="relative flex items-center bg-cafe-beige/50 border border-cafe-beige rounded-full px-3 py-1.5 shadow-2xs sm:mr-1">
            <i data-lucide="clock" class="w-3.5 h-3.5 text-cafe-gold mr-2 shrink-0"></i>
            <select onchange="if(this.value) window.location.href=this.value"
                    class="bg-transparent text-xs text-cafe-dark font-semibold focus:outline-none cursor-pointer pr-5 appearance-none">
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

        {{-- Form Rentang Tanggal Terpisah (Renggang full kiri sampai kanan) --}}
        <form action="{{ $baseUrl }}" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 text-xs w-full sm:w-auto">
            @foreach($preserve as $k => $v)
                <input type="hidden" name="{{ $k }}" value="{{ $v }}">
            @endforeach
            <input type="hidden" name="periode" value="kustom">

            <div class="flex items-center gap-2 w-full sm:w-auto">
                {{-- Tanggal Awal Terpisah dengan Border (Full width di mobile) --}}
                <div class="flex-1 sm:flex-initial flex items-center justify-center bg-cafe-card border border-cafe-beige rounded-full px-3 py-1.5 shadow-2xs">
                    <input type="date" name="mulai"
                           value="{{ $period->key === 'kustom' ? $period->start->toDateString() : now()->subDays(6)->toDateString() }}"
                           class="w-full bg-transparent text-[11px] font-medium text-cafe-text text-center focus:outline-none cursor-pointer">
                </div>

                {{-- Strip Terpisah --}}
                <span class="text-cafe-muted font-bold text-xs shrink-0">&ndash;</span>

                {{-- Tanggal Akhir Terpisah dengan Border yang sama (Full width di mobile) --}}
                <div class="flex-1 sm:flex-initial flex items-center justify-center bg-cafe-card border border-cafe-beige rounded-full px-3 py-1.5 shadow-2xs">
                    <input type="date" name="selesai"
                           value="{{ $period->key === 'kustom' ? $period->end->toDateString() : now()->toDateString() }}"
                           class="w-full bg-transparent text-[11px] font-medium text-cafe-text text-center focus:outline-none cursor-pointer">
                </div>
            </div>

            {{-- Tombol Terapkan Terpisah (Full width di mobile, inline di desktop) --}}
            <button type="submit"
                    class="w-full sm:w-auto px-4 py-1.5 rounded-full bg-cafe-gold hover:bg-cafe-bronze text-[#290024] text-xs font-bold shadow-2xs transition-all active:scale-95 text-center shrink-0">
                Terapkan
            </button>
        </form>
    </div>
</div>
