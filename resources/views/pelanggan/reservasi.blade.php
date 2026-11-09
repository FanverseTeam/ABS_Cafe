@extends('pelanggan.layouts.app')

@section('title', 'Reservasi Meja - ' . ($landing['cafe']['name'] ?? 'ABS Cafe'))
@section('meta_description', 'Reservasi meja di ' . ($landing['cafe']['name'] ?? 'ABS Cafe') . ' secara real-time. Pantau meja yang sedang dipakai atau masih kosong langsung dari ponsel Anda.')

@section('body_class', 'bg-[#F2F5E2] text-[#290024]')
@section('body_style', 'background-color: #F2F5E2 !important; color: #290024 !important;')
@section('main_class', 'bg-[#F2F5E2]')

{{-- Header Khusus Halaman Reservasi Sesuai Desain Figma: Tombol Kembali + Judul di Tengah --}}
@section('header')
<header class="sticky top-0 inset-x-0 z-30 bg-[#F2F5E2]/95 backdrop-blur-md border-b border-[#E3DEA4]/80 transition-all" style="background-color: rgba(242, 245, 226, 0.95) !important;">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 h-14 sm:h-16 flex items-center justify-between">
        <!-- Tombol Kembali ke Beranda -->
        <a href="{{ route('pelanggan.beranda') }}"
           class="w-10 h-10 rounded-full flex items-center justify-center text-[#290024] hover:bg-[#E3DEA4]/50 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#290024]"
           aria-label="Kembali ke Beranda">
            <svg class="w-5 h-5 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M15 18l-6-6 6-6" />
            </svg>
        </a>

        <!-- Judul Tengah: Reservasi Meja -->
        <h1 class="font-serif font-bold text-base sm:text-lg text-[#290024] tracking-tight">
            Reservasi Meja
        </h1>

        <!-- Spacer Kanan (Seimbang dengan tombol kembali) -->
        <div class="w-10 flex justify-end">
            <a href="{{ route('pelanggan.beranda') }}" class="text-xs font-semibold text-[#775533] hover:text-[#290024] hidden sm:block">
                Beranda
            </a>
        </div>
    </div>
</header>
@endsection

@section('content')
<div class="min-h-screen bg-[#F2F5E2] text-[#290024] pb-36 pt-4 sm:pt-6" style="background-color: #F2F5E2 !important;">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 space-y-6">

        <!-- ============================================================== -->
        <!-- 1. HERO BANNER FOTO KAFE & SUBTITLE (SESUAI FIGMA SCREEN 5)     -->
        <!-- ============================================================== -->
        <div class="space-y-3">
            <div class="relative w-full aspect-[16/9] sm:aspect-[21/9] rounded-2xl sm:rounded-3xl overflow-hidden shadow-soft border border-[#E3DEA4]/80 bg-[#F2F5E2]">
                <img src="{{ asset('images/landing-bg-768.webp') }}"
                     alt="Suasana ABS Cafe"
                     class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-[#290024]/60 via-transparent to-transparent sm:hidden"></div>
                <div class="absolute bottom-3 left-4 right-4 sm:hidden text-[#F2F5E2]">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#F2F5E2]/20 backdrop-blur-md border border-[#F2F5E2]/30 uppercase tracking-wider">
                        {{ $landing['cafe']['name'] ?? 'ABS Cafe' }}
                    </span>
                </div>
            </div>

            <!-- Teks Deskripsi Sesuai Figma -->
            <div class="text-center sm:text-left px-1">
                <p class="text-xs sm:text-sm font-medium text-[#775533] leading-relaxed">
                    Reservasi meja untuk pengalaman ngopi yang lebih nyaman
                </p>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- 2. NOTIFIKASI SUKSES (JIKA ADA RESERVASI BARU)                  -->
        <!-- ============================================================== -->
        @if(session('reservation_success'))
            @php $resItem = session('reservation_item'); @endphp
            <div id="sukses-reservasi-card" class="rounded-3xl border-2 border-[#D4954D]/40 bg-[#D4954D]/10 p-5 sm:p-7 shadow-md relative overflow-hidden animate-fade-in text-[#290024]">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-4 border-b border-[#E3DEA4]/60">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-2xl bg-[#D4954D] text-[#290024] flex items-center justify-center shrink-0 shadow-sm">
                            <svg class="w-6 h-6 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-[#775533]">Permohonan Reservasi Diterima!</span>
                            <h3 class="text-lg sm:text-xl font-bold text-[#290024]">Kode: <span class="font-mono text-[#775533]">{{ session('reservation_code') }}</span></h3>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-[#D4954D]/15 text-[#290024] border border-[#D4954D]/30">
                        Menunggu Konfirmasi Kasir
                    </span>
                </div>

                @if($resItem)
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5 py-4 text-xs text-[#775533]">
                        <div>
                            <span class="block text-[#775533]/70 text-[10px] uppercase font-bold tracking-wider mb-0.5">Nama Pemesan</span>
                            <strong class="text-[#290024] text-xs sm:text-sm">{{ $resItem['name'] }}</strong>
                        </div>
                        <div>
                            <span class="block text-[#775533]/70 text-[10px] uppercase font-bold tracking-wider mb-0.5">Nomor WA</span>
                            <strong class="text-[#290024] text-xs sm:text-sm">{{ $resItem['phone'] }}</strong>
                        </div>
                        <div>
                            <span class="block text-[#775533]/70 text-[10px] uppercase font-bold tracking-wider mb-0.5">Jadwal</span>
                            <strong class="text-[#290024] text-xs sm:text-sm">{{ $resItem['date'] }}, {{ $resItem['time'] }} WIB</strong>
                        </div>
                        <div>
                            <span class="block text-[#775533]/70 text-[10px] uppercase font-bold tracking-wider mb-0.5">Meja & Tamu</span>
                            <strong class="text-[#775533] text-xs sm:text-sm">Meja #{{ $resItem['table_number'] }} ({{ $resItem['party_size'] }} Orang)</strong>
                        </div>
                    </div>
                @endif

                <div class="pt-3.5 border-t border-[#E3DEA4]/60 flex flex-wrap items-center justify-between gap-3">
                    <p class="text-xs text-[#775533]">
                        Simpan kode reservasi ini. Anda dapat langsung mengonfirmasi kedatangan ke kasir via WhatsApp.
                    </p>
                    <div class="flex items-center gap-2">
                        @php
                            $waNumber = $landing['cafe']['whatsapp_nomor'] ?? '6289620102010';
                            $waText = urlencode("Halo ABS Cafe, saya ingin konfirmasi reservasi dengan kode " . session('reservation_code') . " atas nama " . ($resItem['name'] ?? '') . " untuk tanggal " . ($resItem['date'] ?? '') . " jam " . ($resItem['time'] ?? ''));
                        @endphp
                        <a href="https://wa.me/{{ $waNumber }}?text={{ $waText }}" target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-[#290024] hover:bg-[#775533] text-[#F2F5E2] text-xs font-semibold transition-all shadow-sm">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.669-.699c.969.54 1.771.82 2.791.82 3.181 0 5.767-2.586 5.768-5.766 0-3.18-2.587-5.766-5.768-5.766m9.969 5.766c0 5.514-4.486 10-10 10-1.823 0-3.535-.494-5.011-1.354l-7.02 1.838 1.87-6.837c-.961-1.536-1.52-3.344-1.52-5.284 0-5.514 4.486-10 10-10 5.514 0 10 4.486 10 10z"/>
                            </svg>
                            Konfirmasi via WhatsApp
                        </a>
                        <button type="button" onclick="document.getElementById('sukses-reservasi-card').remove()" class="px-3 py-1.5 text-xs text-[#775533] hover:text-[#290024]">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- ============================================================== -->
        <!-- 3. PANEL STATUS MEJA REAL-TIME (KONDISI LANGSUNG KAFE)          -->
        <!-- ============================================================== -->
        <div class="bg-[#F2F5E2] border border-[#E3DEA4]/80 rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-soft space-y-4">
            <!-- Header Bar Status Meja -->
            <div class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-[#E3DEA4]/60">
                <div>
                    <h2 class="font-serif font-bold text-sm sm:text-base text-[#290024]">
                        Kondisi Meja Real-Time
                    </h2>
                    <p class="text-[11px] text-[#775533]">
                        Pantau meja yang sedang dipakai atau kosong secara langsung
                    </p>
                </div>

                <!-- Indikator Live & Tombol Refresh -->
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-[#D4954D]/10 text-[#775533] border border-[#D4954D]/30">
                        <span class="relative flex h-1.5 w-1.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#D4954D] opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-[#D4954D]"></span>
                        </span>
                        <span id="sync-status-text">Live Sync</span>
                    </span>
                    <button type="button" id="btn-manual-sync" onclick="fetchTableStatus(true)"
                            title="Segarkan status meja"
                            class="p-1.5 rounded-lg bg-[#E3DEA4]/40 hover:bg-[#E3DEA4] text-[#775533] hover:text-[#290024] transition-colors">
                        <svg id="sync-icon" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Rekapitulasi Jumlah Meja -->
            <div class="grid grid-cols-4 gap-2">
                <div class="px-2.5 py-2 rounded-xl bg-[#D4954D]/10 border border-[#D4954D]/30 text-center">
                    <span class="block text-[10px] text-[#775533] font-semibold uppercase">Kosong</span>
                    <span id="count-tersedia" class="text-base sm:text-lg font-bold text-[#D4954D]">{{ $summary['tersedia'] }}</span>
                </div>
                <div class="px-2.5 py-2 rounded-xl bg-[#290024]/10 border border-[#290024]/30 text-center">
                    <span class="block text-[10px] text-[#290024] font-semibold uppercase">Terisi</span>
                    <span id="count-terpakai" class="text-base sm:text-lg font-bold text-[#290024]">{{ $summary['terpakai'] }}</span>
                </div>
                <div class="px-2.5 py-2 rounded-xl bg-[#775533]/10 border border-[#775533]/30 text-center">
                    <span class="block text-[10px] text-[#775533] font-semibold uppercase">Dipesan</span>
                    <span id="count-dipesan" class="text-base sm:text-lg font-bold text-[#775533]">{{ $summary['dipesan'] }}</span>
                </div>
                <div class="px-2.5 py-2 rounded-xl bg-[#E3DEA4]/20 border border-[#E3DEA4] text-center">
                    <span class="block text-[10px] text-[#775533] font-semibold uppercase">Total</span>
                    <span id="count-total" class="text-base sm:text-lg font-bold text-[#290024]">{{ $summary['total'] }}</span>
                </div>
            </div>

            <!-- Filter Area -->
            <div class="flex items-center gap-2 pt-1 text-xs text-[#775533]">
                <span class="text-[11px]">Area:</span>
                <div class="flex items-center gap-1.5" id="area-filter-container">
                    <button type="button" onclick="filterByArea('all')" class="area-filter-btn px-3 py-1 rounded-full text-xs font-semibold bg-[#290024] text-[#F2F5E2] transition-all shadow-xs">Semua</button>
                    <button type="button" onclick="filterByArea('indoor')" class="area-filter-btn px-3 py-1 rounded-full text-xs font-semibold bg-[#E3DEA4]/40 text-[#775533] hover:bg-[#E3DEA4] transition-all">Indoor</button>
                    <button type="button" onclick="filterByArea('outdoor')" class="area-filter-btn px-3 py-1 rounded-full text-xs font-semibold bg-[#E3DEA4]/40 text-[#775533] hover:bg-[#E3DEA4] transition-all">Outdoor</button>
                </div>
            </div>

            <!-- Grid Kartu Meja Interaktif -->
            <div id="table-cards-grid" class="grid grid-cols-2 sm:grid-cols-3 gap-3 pt-2">
                @forelse($tableStatusList as $t)
                    @php
                        $isAvailable = $t['is_available'];
                        $status = $t['status'];
                        $cardBg = match($status) {
                            'terpakai' => 'border-[#290024]/30 bg-[#290024]/10 text-[#290024]',
                            'dipesan' => 'border-[#775533]/30 bg-[#775533]/10 text-[#775533]',
                            'nonaktif' => 'border-[#E3DEA4] bg-[#E3DEA4]/30 text-[#775533]/50 opacity-60 cursor-not-allowed',
                            default => 'border-[#D4954D]/30 bg-[#D4954D]/10 text-[#290024] hover:border-[#D4954D] hover:shadow-xs cursor-pointer'
                        };
                        $badgeStyle = match($status) {
                            'terpakai' => 'bg-[#290024]/15 text-[#290024] border-[#290024]/30',
                            'dipesan' => 'bg-[#775533]/15 text-[#775533] border-[#775533]/30',
                            'nonaktif' => 'bg-[#E3DEA4]/50 text-[#775533]/60 border-[#E3DEA4]',
                            default => 'bg-[#D4954D]/15 text-[#775533] border-[#D4954D]/30'
                        };
                    @endphp
                    <div class="table-card relative p-3 sm:p-3.5 rounded-2xl border transition-all duration-200 flex flex-col justify-between {{ $cardBg }}"
                         data-table-id="{{ $t['id'] }}"
                         data-table-number="{{ $t['number'] }}"
                         data-table-capacity="{{ $t['capacity'] }}"
                         data-table-area="{{ strtolower($t['area']) }}"
                         data-table-status="{{ $status }}"
                         data-table-available="{{ $isAvailable ? '1' : '0' }}"
                         @if($isAvailable) onclick="selectTableCard({{ $t['id'] }}, {{ $t['number'] }}, {{ $t['capacity'] }})" @endif>

                        <div class="flex items-start justify-between gap-1 mb-1.5">
                            <div>
                                <span class="text-[9px] uppercase font-bold tracking-wider text-[#775533]/80 block">{{ $t['area'] }}</span>
                                <h3 class="font-serif font-bold text-sm sm:text-base text-[#290024]">Meja #{{ $t['number'] }}</h3>
                            </div>
                            <div class="w-7 h-7 rounded-lg bg-[#F2F5E2]/80 border border-current/20 flex items-center justify-center shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <rect x="5" y="4" width="14" height="10" rx="2" />
                                    <path d="M7 14v6M17 14v6" />
                                </svg>
                            </div>
                        </div>

                        <div class="my-1.5 space-y-0.5">
                            <div class="flex items-center gap-1 text-[11px] text-[#775533]">
                                <svg class="w-3 h-3 text-[#775533]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span>Kapasitas: <strong>{{ $t['capacity'] }} Orang</strong></span>
                            </div>
                        </div>

                        <div class="mt-2 pt-2 border-t border-current/15 flex items-center justify-between gap-1">
                            <span class="table-badge inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wide border {{ $badgeStyle }}">
                                @if($status === 'tersedia')
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#D4954D]"></span>
                                @elseif($status === 'terpakai')
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#290024]"></span>
                                @elseif($status === 'dipesan')
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#775533]"></span>
                                @endif
                                <span>{{ $t['status_label'] }}</span>
                            </span>

                            @if($isAvailable)
                                <span class="select-indicator text-[10px] font-semibold text-[#775533] flex items-center gap-0.5">
                                    Pilih
                                    <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                    </svg>
                                </span>
                            @endif
                        </div>

                        <!-- Indikator Centang Terpilih -->
                        <div class="selected-checkmark hidden absolute -top-1.5 -right-1.5 w-5 h-5 rounded-full bg-[#290024] text-[#F2F5E2] items-center justify-center shadow-md border-2 border-[#F2F5E2]">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-8 text-center text-xs text-[#775533]">
                        Belum ada data meja.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- 4. FORMULIR PEMESANAN (SESUAI TATA LETAK MOBILE FIGMA FRAME 5)  -->
        <!-- ============================================================== -->
        <div class="bg-[#F2F5E2] border border-[#E3DEA4]/80 rounded-2xl sm:rounded-3xl p-5 sm:p-7 shadow-soft space-y-4" id="form-reservasi-container">
            <div class="pb-3 border-b border-[#E3DEA4]/60">
                <span class="text-[10px] font-bold uppercase tracking-wider text-[#775533]">Formulir Pelanggan</span>
                <h2 class="font-serif font-bold text-lg text-[#290024]">
                    Lengkapi Detail Reservasi
                </h2>
            </div>

            <!-- Pesan Error Validasi -->
            @if($errors->any())
                <div class="p-3.5 rounded-xl bg-[#290024]/10 border border-[#290024]/20 text-xs text-[#290024]">
                    <strong class="font-bold block mb-1">Periksa kembali data formulir:</strong>
                    <ul class="list-disc list-inside space-y-0.5 text-[#290024]/80">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('pelanggan.reservasi.store') }}" method="POST" id="form-reservasi" class="space-y-4">
                @csrf

                <!-- Tanggal Reservasi (Sesuai Kolom Tanggal Figma) -->
                <div>
                    <label for="reservation_date" class="block text-xs font-semibold text-[#290024] mb-1.5">
                        Tanggal
                    </label>
                    <div class="relative">
                        <input type="date" id="reservation_date" name="reservation_date"
                               value="{{ old('reservation_date', $selectedDate) }}"
                               min="{{ today()->toDateString() }}" required
                               onchange="onDateTimeChanged()"
                               class="w-full px-4 py-3 rounded-xl bg-[#F2F5E2]/80 border border-[#E3DEA4] text-xs text-[#290024] focus:border-[#290024] focus:ring-1 focus:ring-[#290024] focus:outline-none transition-colors">
                    </div>
                </div>

                <!-- Jam Datang -->
                <div>
                    <label for="reservation_time" class="block text-xs font-semibold text-[#290024] mb-1.5">
                        Jam Kedatangan
                    </label>
                    <div class="relative">
                        <select id="reservation_time" name="reservation_time" required
                                onchange="onDateTimeChanged()"
                                class="w-full px-4 py-3 rounded-xl bg-[#F2F5E2]/80 border border-[#E3DEA4] text-xs text-[#290024] focus:border-[#290024] focus:ring-1 focus:ring-[#290024] focus:outline-none transition-colors">
                            @foreach($timeSlots as $slot)
                                <option value="{{ $slot }}" {{ old('reservation_time', substr($selectedTime, 0, 5)) === $slot ? 'selected' : '' }}>
                                    {{ $slot }} WIB
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Jumlah Orang (Sesuai Kolom Jumlah Orang Figma) -->
                <div>
                    <label for="party_size" class="block text-xs font-semibold text-[#290024] mb-1.5">
                        Jumlah Orang
                    </label>
                    <div class="relative">
                        <select id="party_size" name="party_size" required
                                onchange="onPartySizeChanged()"
                                class="w-full px-4 py-3 rounded-xl bg-[#F2F5E2]/80 border border-[#E3DEA4] text-xs text-[#290024] focus:border-[#290024] focus:ring-1 focus:ring-[#290024] focus:outline-none transition-colors">
                            @for($i = 1; $i <= 20; $i++)
                                <option value="{{ $i }}" {{ old('party_size', 2) == $i ? 'selected' : '' }}>
                                    {{ $i }} Orang
                                </option>
                            @endfor
                        </select>
                    </div>
                </div>

                <!-- Pilih Meja (Sesuai Kolom Pilih Meja Figma) -->
                <div>
                    <label for="table_id" class="block text-xs font-semibold text-[#290024] mb-1.5">
                        Pilih Meja
                    </label>
                    <div class="relative">
                        <select id="table_id" name="table_id"
                                onchange="onTableSelectDropdownChanged()"
                                class="w-full px-4 py-3 rounded-xl bg-[#F2F5E2]/80 border border-[#E3DEA4] text-xs text-[#290024] focus:border-[#290024] focus:ring-1 focus:ring-[#290024] focus:outline-none transition-colors">
                            <option value="">Pilih Otomatis Meja Kosong</option>
                            @foreach($tables as $tbl)
                                <option value="{{ $tbl->id }}" data-capacity="{{ $tbl->capacity }}" {{ old('table_id') == $tbl->id ? 'selected' : '' }}>
                                    Meja #{{ $tbl->number }} (Kapasitas {{ $tbl->capacity }} orang)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Banner Info Meja Terpilih -->
                    <div id="selected-table-banner" class="hidden mt-2 p-2.5 rounded-xl bg-[#D4954D]/10 border border-[#D4954D]/30 text-xs text-[#290024] flex items-center justify-between">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-[#D4954D]"></span>
                            <span>Meja Terpilih: <strong id="selected-table-text" class="font-bold">Meja #1</strong></span>
                        </div>
                        <button type="button" onclick="clearSelectedTable()" class="text-[10px] text-[#775533] hover:text-[#290024] font-semibold underline">
                            Ganti
                        </button>
                    </div>
                </div>

                <!-- Nama Lengkap -->
                <div>
                    <label for="customer_name" class="block text-xs font-semibold text-[#290024] mb-1.5">
                        Nama Lengkap
                    </label>
                    <input type="text" id="customer_name" name="customer_name"
                           value="{{ old('customer_name') }}" required maxlength="100"
                           placeholder="Nama pemesan..."
                           class="w-full px-4 py-3 rounded-xl bg-[#F2F5E2]/80 border border-[#E3DEA4] text-xs text-[#290024] placeholder-[#775533]/50 focus:border-[#290024] focus:ring-1 focus:ring-[#290024] focus:outline-none transition-colors">
                </div>

                <!-- Nomor WhatsApp -->
                <div>
                    <label for="phone" class="block text-xs font-semibold text-[#290024] mb-1.5">
                        Nomor WhatsApp / Telepon
                    </label>
                    <input type="tel" id="phone" name="phone"
                           value="{{ old('phone') }}" required maxlength="20"
                           placeholder="Contoh: 081234567890"
                           class="w-full px-4 py-3 rounded-xl bg-[#F2F5E2]/80 border border-[#E3DEA4] text-xs text-[#290024] placeholder-[#775533]/50 focus:border-[#290024] focus:ring-1 focus:ring-[#290024] focus:outline-none transition-colors">
                </div>

                <!-- Catatan Tambahan -->
                <div>
                    <label for="notes" class="block text-xs font-semibold text-[#290024] mb-1.5">
                        Catatan Khusus <span class="text-[#775533]/70 font-normal">(Opsional)</span>
                    </label>
                    <textarea id="notes" name="notes" rows="2" maxlength="500"
                              placeholder="Contoh: Acara ulang tahun, dekat colokan listrik..."
                              class="w-full px-4 py-3 rounded-xl bg-[#F2F5E2]/80 border border-[#E3DEA4] text-xs text-[#290024] placeholder-[#775533]/50 focus:border-[#290024] focus:ring-1 focus:ring-[#290024] focus:outline-none transition-colors resize-none">{{ old('notes') }}</textarea>
                </div>

                <!-- Tombol Desktop (Di mobile akan ada sticky bottom bar sesuai Figma) -->
                <div class="hidden sm:block pt-3">
                    <button type="submit"
                            class="w-full py-3.5 px-6 rounded-full bg-[#290024] hover:bg-[#290024]/90 text-[#F2F5E2] font-bold text-sm tracking-wide transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                        <span>Pesan Sekarang</span>
                        <svg class="w-4 h-4 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </button>
                </div>
            </form>
        </div>

        <!-- ============================================================== -->
        <!-- 5. CEK STATUS RESERVASI SEBELUMNYA                             -->
        <!-- ============================================================== -->
        <div class="bg-[#F2F5E2] border border-[#E3DEA4]/80 rounded-2xl sm:rounded-3xl p-4 sm:p-5 shadow-soft">
            <h3 class="font-serif font-bold text-xs sm:text-sm text-[#290024] mb-1">
                Cek Status Reservasi Anda
            </h3>
            <p class="text-[11px] text-[#775533] mb-3">
                Masukkan kode reservasi atau nomor WhatsApp untuk melihat status kedatangan.
            </p>
            <form action="{{ route('pelanggan.reservasi.check') }}" method="GET" class="flex gap-2">
                <input type="text" name="q" value="{{ $searchCode }}" placeholder="Kode RSV-... atau No. WA"
                       class="flex-1 px-3.5 py-2 rounded-xl bg-[#F2F5E2]/80 border border-[#E3DEA4] text-xs text-[#290024] placeholder-[#775533]/50 focus:border-[#290024] focus:outline-none">
                <button type="submit" class="px-4 py-2 rounded-xl bg-[#290024] text-[#F2F5E2] text-xs font-semibold hover:bg-[#290024]/90 transition-colors shrink-0">
                    Cek
                </button>
            </form>

            @if($searchCode !== '')
                <div class="mt-3 pt-3 border-t border-[#E3DEA4]/60">
                    @if($foundReservation)
                        <div class="p-3 rounded-xl bg-[#D4954D]/10 border border-[#D4954D]/30 text-xs">
                            <div class="flex items-center justify-between gap-2 mb-1">
                                <span class="font-mono font-bold text-[#290024]">{{ $foundReservation->code }}</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-[#D4954D]/15 text-[#290024] border border-[#D4954D]/30">
                                    {{ $foundReservation->statusLabel() }}
                                </span>
                            </div>
                            <p class="text-[#290024]">Nama: <strong>{{ $foundReservation->customer_name }}</strong></p>
                            <p class="text-[#775533]">Jadwal: {{ $foundReservation->datetimeLabel() }} &bull; Meja #{{ $foundReservation->table?->number ?? '-' }}</p>
                        </div>
                    @else
                        <p class="text-xs text-[#290024]/70">Reservasi "{{ $searchCode }}" tidak ditemukan.</p>
                    @endif
                </div>
            @endif
        </div>

    </div>

    <!-- ============================================================== -->
    <!-- 6. STICKY BOTTOM BAR KHUSUS MOBILE (SESUAI FIGMA FRAME 5)      -->
    <!-- ============================================================== -->
    <div class="sm:hidden fixed bottom-0 inset-x-0 z-40 bg-[#F2F5E2]/95 backdrop-blur-md border-t border-[#E3DEA4] p-4 shadow-[0_-4px_15px_rgba(41,0,36,0.08)]" style="background-color: rgba(242, 245, 226, 0.95) !important;">
        <button type="button"
                onclick="const f = document.getElementById('form-reservasi'); if (f.checkValidity()) { f.submit(); } else { f.reportValidity(); }"
                class="w-full py-3.5 px-6 rounded-full bg-[#290024] active:bg-[#290024]/90 text-[#F2F5E2] font-bold text-sm tracking-wide shadow-md flex items-center justify-center gap-2">
            <span>Pesan Sekarang</span>
            <svg class="w-4 h-4 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="5" y1="12" x2="19" y2="12"></line>
                <polyline points="12 5 19 12 12 19"></polyline>
            </svg>
        </button>
    </div>
</div>
@endsection

@section('footer')
<footer class="bg-[#F2F5E2] border-t border-[#E3DEA4]/80 py-6 sm:py-8 text-center text-xs text-[#775533]" style="background-color: #F2F5E2 !important;">
    <div class="max-w-4xl mx-auto px-4 space-y-1.5">
        <p class="font-serif font-bold text-[#290024]">{{ $landing['cafe']['name'] ?? 'ABS Cafe' }}</p>
        <p class="text-[11px] text-[#775533]/80">{{ $landing['cafe']['tagline'] ?? 'Good Coffee, Better Moments.' }}</p>
        <p class="text-[10px] text-[#775533]/60">&copy; {{ date('Y') }} Hak cipta dilindungi.</p>
    </div>
</footer>
@endsection

@push('scripts')
<script>
    const STATUS_ENDPOINT = "{{ route('pelanggan.reservasi.tables') }}";
    const POLL_INTERVAL = 10000;
    let pollTimer = null;
    let selectedTableId = null;

    document.addEventListener('DOMContentLoaded', function () {
        const tableDropdown = document.getElementById('table_id');
        if (tableDropdown && tableDropdown.value) {
            const opt = tableDropdown.options[tableDropdown.selectedIndex];
            highlightSelectedTableCard(tableDropdown.value, opt.text);
        }
        startRealtimePolling();
    });

    function startRealtimePolling() {
        if (pollTimer) clearInterval(pollTimer);
        pollTimer = setInterval(function () {
            fetchTableStatus(false);
        }, POLL_INTERVAL);
    }

    function fetchTableStatus(isManual = false) {
        const dateInput = document.getElementById('reservation_date');
        const timeInput = document.getElementById('reservation_time');
        const syncIcon = document.getElementById('sync-icon');
        const syncText = document.getElementById('sync-status-text');

        const date = dateInput ? dateInput.value : '';
        const time = timeInput ? timeInput.value : '';

        if (syncIcon) syncIcon.classList.add('animate-spin');
        if (syncText) syncText.textContent = 'Sync...';

        const url = `${STATUS_ENDPOINT}?date=${encodeURIComponent(date)}&time=${encodeURIComponent(time)}`;

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data && data.success) {
                updateTableCardsUI(data.tables, data.summary);
                if (syncText) syncText.textContent = `Live: ${data.timestamp}`;
            }
        })
        .catch(err => {
            console.error('Gagal sinkron status meja:', err);
            if (syncText) syncText.textContent = 'Error sync';
        })
        .finally(() => {
            if (syncIcon) syncIcon.classList.remove('animate-spin');
        });
    }

    function updateTableCardsUI(tables, summary) {
        if (summary) {
            const countTersedia = document.getElementById('count-tersedia');
            const countTerpakai = document.getElementById('count-terpakai');
            const countDipesan = document.getElementById('count-dipesan');
            const countTotal = document.getElementById('count-total');

            if (countTersedia) countTersedia.textContent = summary.tersedia;
            if (countTerpakai) countTerpakai.textContent = summary.terpakai;
            if (countDipesan) countDipesan.textContent = summary.dipesan;
            if (countTotal) countTotal.textContent = summary.total;
        }

        if (!tables || !Array.isArray(tables)) return;

        tables.forEach(t => {
            const card = document.querySelector(`.table-card[data-table-id="${t.id}"]`);
            if (!card) return;

            card.setAttribute('data-table-status', t.status);
            card.setAttribute('data-table-available', t.is_available ? '1' : '0');

            card.className = 'table-card relative p-3 sm:p-3.5 rounded-2xl border transition-all duration-200 flex flex-col justify-between';

            const badge = card.querySelector('.table-badge');
            const selectIndicator = card.querySelector('.select-indicator');

            if (t.status === 'tersedia') {
                card.classList.add('border-[#D4954D]/40', 'bg-[#D4954D]/10', 'text-[#775533]', 'hover:border-[#290024]', 'hover:shadow-xs', 'cursor-pointer');
                card.onclick = () => selectTableCard(t.id, t.number, t.capacity);

                if (badge) {
                    badge.className = 'table-badge inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wide border bg-[#D4954D]/15 text-[#775533] border-[#D4954D]/30';
                    badge.innerHTML = `<span class="w-1.5 h-1.5 rounded-full bg-[#D4954D]"></span><span>${t.status_label}</span>`;
                }
                if (selectIndicator) selectIndicator.style.display = 'flex';
            } else if (t.status === 'terpakai') {
                card.classList.add('border-[#290024]/30', 'bg-[#290024]/10', 'text-[#290024]');
                card.onclick = null;

                if (badge) {
                    badge.className = 'table-badge inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wide border bg-[#290024]/15 text-[#290024] border-[#290024]/30';
                    badge.innerHTML = `<span class="w-1.5 h-1.5 rounded-full bg-[#290024]"></span><span>${t.status_label}</span>`;
                }
                if (selectIndicator) selectIndicator.style.display = 'none';

                if (selectedTableId === t.id) {
                    clearSelectedTable();
                    alert(`Perhatian: Meja #${t.number} baru saja terisi oleh pesanan pelanggan.`);
                }
            } else if (t.status === 'dipesan') {
                card.classList.add('border-[#775533]/30', 'bg-[#775533]/10', 'text-[#775533]');
                card.onclick = null;

                if (badge) {
                    badge.className = 'table-badge inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wide border bg-[#775533]/15 text-[#775533] border-[#775533]/30';
                    badge.innerHTML = `<span class="w-1.5 h-1.5 rounded-full bg-[#775533]"></span><span>${t.status_label}</span>`;
                }
                if (selectIndicator) selectIndicator.style.display = 'none';

                if (selectedTableId === t.id) {
                    clearSelectedTable();
                    alert(`Perhatian: Meja #${t.number} sudah dipesan untuk jam tersebut.`);
                }
            } else {
                card.classList.add('border-[#E3DEA4]', 'bg-[#E3DEA4]/30', 'text-[#775533]/50', 'opacity-60', 'cursor-not-allowed');
                card.onclick = null;
                if (selectIndicator) selectIndicator.style.display = 'none';
            }

            if (selectedTableId === t.id) {
                card.classList.add('ring-2', 'ring-[#290024]', 'border-[#290024]');
                const chk = card.querySelector('.selected-checkmark');
                if (chk) {
                    chk.classList.remove('hidden');
                    chk.classList.add('flex');
                }
            }
        });
    }

    function selectTableCard(tableId, tableNumber, capacity) {
        const partySizeSelect = document.getElementById('party_size');
        const currentParty = partySizeSelect ? parseInt(partySizeSelect.value, 10) : 1;

        if (currentParty > capacity) {
            const konfirmasi = confirm(`Kapasitas Meja #${tableNumber} maksimal ${capacity} orang, sedangkan Anda memilih ${currentParty} orang. Apakah ingin menyesuaikan jumlah tamu menjadi ${capacity} orang?`);
            if (konfirmasi && partySizeSelect) {
                partySizeSelect.value = capacity;
            } else {
                return;
            }
        }

        const tableDropdown = document.getElementById('table_id');
        if (tableDropdown) {
            tableDropdown.value = tableId;
        }

        highlightSelectedTableCard(tableId, `Meja #${tableNumber} (${capacity} Orang)`);

        const formEl = document.getElementById('form-reservasi-container');
        if (formEl && window.innerWidth < 1024) {
            formEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    function highlightSelectedTableCard(tableId, labelText) {
        selectedTableId = parseInt(tableId, 10);

        document.querySelectorAll('.table-card').forEach(c => {
            c.classList.remove('ring-2', 'ring-[#290024]', 'border-[#290024]');
            const chk = c.querySelector('.selected-checkmark');
            if (chk) {
                chk.classList.add('hidden');
                chk.classList.remove('flex');
            }
        });

        const activeCard = document.querySelector(`.table-card[data-table-id="${tableId}"]`);
        if (activeCard) {
            activeCard.classList.add('ring-2', 'ring-[#290024]', 'border-[#290024]');
            const chk = activeCard.querySelector('.selected-checkmark');
            if (chk) {
                chk.classList.remove('hidden');
                chk.classList.add('flex');
            }
        }

        const banner = document.getElementById('selected-table-banner');
        const text = document.getElementById('selected-table-text');
        if (banner && text) {
            text.textContent = labelText;
            banner.classList.remove('hidden');
        }
    }

    function clearSelectedTable() {
        selectedTableId = null;
        const tableDropdown = document.getElementById('table_id');
        if (tableDropdown) tableDropdown.value = '';

        document.querySelectorAll('.table-card').forEach(c => {
            c.classList.remove('ring-2', 'ring-[#290024]', 'border-[#290024]');
            const chk = c.querySelector('.selected-checkmark');
            if (chk) {
                chk.classList.add('hidden');
                chk.classList.remove('flex');
            }
        });

        const banner = document.getElementById('selected-table-banner');
        if (banner) banner.classList.add('hidden');
    }

    function onTableSelectDropdownChanged() {
        const tableDropdown = document.getElementById('table_id');
        if (!tableDropdown) return;

        const val = tableDropdown.value;
        if (!val) {
            clearSelectedTable();
        } else {
            const opt = tableDropdown.options[tableDropdown.selectedIndex];
            highlightSelectedTableCard(val, opt.text);
        }
    }

    function onDateTimeChanged() {
        fetchTableStatus(true);
    }

    function onPartySizeChanged() {
        if (!selectedTableId) return;

        const partySelect = document.getElementById('party_size');
        const activeCard = document.querySelector(`.table-card[data-table-id="${selectedTableId}"]`);

        if (partySelect && activeCard) {
            const cap = parseInt(activeCard.getAttribute('data-table-capacity'), 10);
            const party = parseInt(partySelect.value, 10);
            if (party > cap) {
                alert(`Peringatan: Jumlah tamu (${party} orang) melebihi kapasitas Meja #${activeCard.getAttribute('data-table-number')} (maksimal ${cap} orang).`);
            }
        }
    }

    function filterByArea(area) {
        document.querySelectorAll('.area-filter-btn').forEach(b => {
            b.className = 'area-filter-btn px-3 py-1 rounded-full text-xs font-semibold bg-[#E3DEA4]/40 text-[#775533] hover:bg-[#E3DEA4] transition-all';
        });

        const targetBtn = event ? event.target : null;
        if (targetBtn) {
            targetBtn.className = 'area-filter-btn px-3 py-1 rounded-full text-xs font-semibold bg-[#290024] text-[#F2F5E2] transition-all shadow-xs';
        }

        const cards = document.querySelectorAll('.table-card');
        cards.forEach(card => {
            const cardArea = card.getAttribute('data-table-area');
            if (area === 'all' || cardArea === area) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }
</script>
@endpush
