@extends('kasir.layouts.app')

@section('title', 'Reservasi & Meja - Kasir')
@section('page_title', 'Reservasi & Meja')

@section('content')
<div class="space-y-6">
    @php
        $activeTab = request('view', 'list');
    @endphp

    <!-- Mobile Pill Switcher: Reservasi | Denah Meja -->
    <div class="lg:hidden flex items-center p-1 bg-[#E3DEA4]/50 rounded-full border border-[#E3DEA4]">
        <button type="button" onclick="switchReservationView('list')" id="tab-btn-list"
                class="flex-1 py-2 rounded-full text-xs font-bold transition-all {{ $activeTab === 'list' ? 'bg-[#290024] text-[#F2F5E2] shadow-sm' : 'text-[#775533] hover:text-[#290024]' }}">
            <i data-lucide="calendar-days" class="w-3.5 h-3.5 inline-block mr-1"></i> Reservasi ({{ $reservations->count() }})
        </button>
        <button type="button" onclick="switchReservationView('tables')" id="tab-btn-tables"
                class="flex-1 py-2 rounded-full text-xs font-bold transition-all {{ $activeTab === 'tables' ? 'bg-[#290024] text-[#F2F5E2] shadow-sm' : 'text-[#775533] hover:text-[#290024]' }}">
            <i data-lucide="grid-3x3" class="w-3.5 h-3.5 inline-block mr-1"></i> Denah Meja
        </button>
    </div>

    <!-- Header: date picker + new reservation -->
    <div class="flex flex-wrap items-center justify-between gap-3">
        <form action="{{ route('kasir.reservations.index') }}" method="GET" class="flex items-center gap-2">
            <input type="date" name="date" value="{{ $date->format('Y-m-d') }}" class="bg-[#F2F5E2] border border-[#E3DEA4] text-xs text-[#290024] px-3.5 py-2 rounded-2xl focus:border-[#D4954D] focus:outline-none">
            <button class="px-4 py-2 rounded-2xl bg-[#290024] text-[#F2F5E2] text-xs font-semibold hover:bg-[#775533] transition-colors">Tampilkan</button>
        </form>
        <button onclick="openCreateModal()" class="hidden sm:inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-[#D4954D] text-[#290024] text-xs font-bold hover:brightness-105 transition-all shadow-md active:scale-95">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Buat Reservasi
        </button>
    </div>

    {{-- Mobile FAB for Buat Reservasi --}}
    <div class="sm:hidden fixed z-30 right-4 sm:right-6 bottom-[calc(4.75rem+env(safe-area-inset-bottom))] landscape:bottom-[calc(3.75rem+env(safe-area-inset-bottom))]">
        <button type="button" onclick="openCreateModal()"
           class="flex items-center gap-2 px-4 py-3 rounded-full bg-[#290024] hover:bg-[#775533] text-[#F2F5E2] border border-[#D4954D]/60 shadow-xl active:scale-95 transition-transform"
           aria-label="Buat Reservasi">
            <i data-lucide="plus" class="w-5 h-5 text-cafe-gold shrink-0"></i>
            <span class="text-xs font-bold whitespace-nowrap">Reservasi</span>
        </button>
    </div>

    <!-- 1. Daftar Reservasi (Ditaruh di Atas) -->
    <div id="section-list" class="{{ $activeTab === 'list' ? '' : 'hidden lg:block' }} bg-[#F2F5E2] rounded-3xl border border-[#E3DEA4]/80 shadow-soft overflow-hidden">
        <div class="px-6 py-4 border-b border-[#E3DEA4]/50 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-2xl bg-[#D4954D]/15 text-[#D4954D] flex items-center justify-center"><i data-lucide="calendar-days" class="w-4 h-4"></i></div>
                <div>
                    <h3 class="font-serif font-bold text-sm text-[#290024]">Reservasi — {{ $date->translatedFormat('l, d M Y') }}</h3>
                    <p class="text-[10px] text-[#775533]">{{ $reservations->count() }} reservasi terdaftar</p>
                </div>
            </div>

            <div class="flex gap-1.5 flex-wrap">
                @php
                    $statusTabs = ['semua' => 'Semua'] + \App\Models\Reservation::STATUSES;
                @endphp
                @foreach($statusTabs as $key => $label)
                    <a href="{{ route('kasir.reservations.index', ['date' => $date->format('Y-m-d'), 'status' => $key]) }}"
                       class="px-3 py-1.5 rounded-full text-[10px] font-semibold transition-all {{ $status === $key ? 'bg-[#290024] text-[#F2F5E2]' : 'bg-[#E3DEA4]/50 text-[#775533] hover:bg-[#E3DEA4]' }}">
                        {{ $label }}{{ isset($counts[$key]) && $key !== 'semua' ? ' ('.$counts[$key].')' : '' }}
                    </a>
                @endforeach
            </div>
        </div>

        <div class="p-5">
            @if($reservations->count() === 0)
                <div class="py-10 text-center">
                    <div class="w-14 h-14 mx-auto rounded-full bg-[#E3DEA4]/40 flex items-center justify-center text-[#775533]">
                        <i data-lucide="calendar-x" class="w-6 h-6"></i>
                    </div>
                    <p class="text-xs text-[#775533] mt-3">Tidak ada reservasi pada tanggal/status ini.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    @foreach($reservations as $reservation)
                        @php $badge = $reservation->statusBadge(); @endphp
                        <div class="p-4 rounded-2xl bg-[#F2F5E2]/80 border border-[#E3DEA4]/60">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <p class="text-xs font-bold text-[#290024]">{{ $reservation->customer_name }}</p>
                                    <p class="text-[10px] text-[#775533] mt-0.5">{{ $reservation->code }} &bull; {{ $reservation->phone ?? '-' }}</p>
                                </div>
                                <span class="text-[9px] font-bold px-2 py-0.5 rounded-full bg-[#D4954D]/15 text-[#290024] border border-[#D4954D]/30 shrink-0">{{ $badge['label'] }}</span>
                            </div>

                            <div class="flex flex-wrap gap-x-4 gap-y-1 mt-3 text-[11px] text-[#775533]">
                                <span class="flex items-center gap-1 font-bold text-[#290024]"><i data-lucide="calendar" class="w-3.5 h-3.5 text-[#D4954D]"></i>{{ $reservation->datetimeRangeLabel() }}</span>
                                <span class="flex items-center gap-1"><i data-lucide="users" class="w-3 h-3 text-[#D4954D]"></i>{{ $reservation->party_size }} orang</span>
                                <span class="flex items-center gap-1"><i data-lucide="armchair" class="w-3 h-3 text-[#D4954D]"></i>{{ $reservation->table ? 'Meja '.$reservation->table->number : 'Belum ditetapkan' }}</span>
                            </div>

                            {{-- Indikator Realtime Status Meja untuk Reservasi Ini --}}
                            <div class="mt-2.5">
                                @if($reservation->isTableOccupiedNow())
                                    <span class="inline-flex items-center gap-1.5 text-[10px] font-bold px-2.5 py-1 rounded-full bg-red-100 text-red-700 border border-red-300">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-ping"></span>
                                        Meja Terisi (Jendela Aktif Realtime)
                                    </span>
                                @elseif($reservation->occupiedWindowStart()->isFuture())
                                    <span class="inline-flex items-center gap-1 text-[10px] font-medium px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-300">
                                        Meja Masih Kosong &bull; Terisi mulai {{ $reservation->occupiedWindowStart()->translatedFormat('d M H:i') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[10px] font-medium px-2.5 py-0.5 rounded-full bg-gray-100 text-gray-600">
                                        Jendela Berakhir &bull; Meja Kembali Kosong
                                    </span>
                                @endif
                            </div>

                            @if($reservation->notes)
                                <p class="text-[10px] text-[#775533] italic mt-2">&ldquo;{{ $reservation->notes }}&rdquo;</p>
                            @endif

                            <div class="flex flex-wrap gap-1.5 mt-3 pt-3 border-t border-[#E3DEA4]/50">
                                @php
                                    $actions = [
                                        'dikonfirmasi' => ['confirm', 'Konfirmasi', 'btn-outline'],
                                        'batal' => ['batal', 'Batal', 'btn-danger'],
                                        'tidak_datang' => ['tidak_datang', 'No-Show', 'btn-danger'],
                                    ];
                                    if ($reservation->status === 'dikonfirmasi') {
                                        $actions['datang'] = ['datang', 'Check-in', 'btn-primary'];
                                    }
                                    if ($reservation->status === 'datang') {
                                        $actions['selesai'] = ['selesai', 'Selesai', 'btn-primary'];
                                    }
                                @endphp
                                @foreach($actions as $nextStatus => [$st, $label, $style])
                                    <form action="{{ route('kasir.reservations.status', $reservation) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="{{ $st }}">
                                        <button class="px-2.5 py-1.5 rounded-full text-[10px] font-bold transition-all {{ $style === 'btn-primary' ? 'bg-[#290024] text-[#F2F5E2] hover:bg-[#775533]' : ($style === 'btn-danger' ? 'bg-[#775533]/10 text-[#775533] border border-[#775533]/30 hover:bg-[#775533]/20' : 'bg-[#E3DEA4] text-[#290024] hover:bg-[#D4954D]') }}">
                                            {{ $label }}
                                        </button>
                                    </form>
                                @endforeach

                                <form action="{{ route('kasir.reservations.destroy', $reservation) }}" method="POST" onsubmit="return confirm('Hapus reservasi {{ $reservation->code }}?')">
                                    @csrf @method('DELETE')
                                    <button class="px-2.5 py-1.5 rounded-full text-[10px] font-bold text-[#775533] hover:text-[#775533] border border-[#E3DEA4] hover:border-[#775533]/40 transition-colors">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- 2. Peta Meja (Ditaruh di Bawah Reservasi) -->
    <div id="section-tables" class="{{ $activeTab === 'tables' ? '' : 'hidden lg:block' }} bg-[#F2F5E2] rounded-3xl border border-[#E3DEA4]/80 shadow-soft overflow-hidden">
        <div class="px-6 py-4 border-b border-cafe-beige/50 flex items-center gap-2.5">
            <div class="w-9 h-9 rounded-2xl bg-cafe-gold/15 text-cafe-gold flex items-center justify-center"><i data-lucide="grid-3x3" class="w-4 h-4"></i></div>
            <div>
                <h3 class="font-serif font-bold text-sm text-cafe-dark">Peta Meja</h3>
                <p class="text-[10px] text-cafe-muted">kondisi real-time &bull; klik meja untuk ubah status</p>
            </div>
        </div>

        <div class="p-5">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 landscape:grid-cols-3 sm:landscape:grid-cols-4 gap-3">
                @foreach($tables as $table)
                    @php
                        $realtimeStatus = $table->realtimeStatus();
                        $badge = $table->statusBadge();
                        $activeRes = $table->activeReservationNow();
                        $upcomingRes = $table->upcomingReservation();
                    @endphp
                    <div class="p-3 rounded-2xl border {{ $realtimeStatus === 'terpakai' ? 'bg-cafe-danger/10 border-cafe-danger/40' : ($realtimeStatus === 'nonaktif' ? 'bg-cafe-beige/30 border-cafe-beige' : 'bg-cafe-success/10 border-cafe-success/40') }}">
                        <div class="flex items-center justify-between">
                            <span class="font-serif font-bold text-cafe-dark text-sm">Meja {{ $table->number }}</span>
                            <i data-lucide="armchair" class="w-4 h-4 {{ $realtimeStatus === 'terpakai' ? 'text-cafe-danger' : ($realtimeStatus === 'nonaktif' ? 'text-cafe-muted' : 'text-cafe-success') }}"></i>
                        </div>
                        <p class="text-[10px] text-cafe-muted mt-0.5">Kapasitas {{ $table->capacity }} {{ $table->orders_count > 0 ? '&bull; '.$table->orders_count.' pesanan' : '' }}</p>
                        
                        <div class="mt-1.5 space-y-1">
                            <span class="inline-block text-[9px] font-bold px-2 py-0.5 rounded-full bg-cafe-{{ $badge['color'] }}/15 text-cafe-{{ $badge['color'] }}">
                                {{ $badge['label'] }}
                            </span>
                            @if($activeRes)
                                <p class="text-[9px] font-bold text-cafe-danger truncate" title="Reservasi: {{ $activeRes->customer_name }} ({{ $activeRes->datetimeRangeLabel() }})">
                                    &bull; Terisi: {{ $activeRes->customer_name }}
                                </p>
                            @elseif($upcomingRes)
                                <p class="text-[9px] font-medium text-[#775533] truncate" title="Dipesan nanti: {{ $upcomingRes->startDateTime()->translatedFormat('d M H:i') }}">
                                    &bull; Nanti: {{ $upcomingRes->startDateTime()->translatedFormat('d M H:i') }}
                                </p>
                            @endif
                        </div>

                        <form action="{{ route('kasir.tables.status', $table) }}" method="POST" class="mt-2">
                            @csrf
                            <select name="status" onchange="this.form.submit()" class="w-full text-[10px] bg-cafe-cream border border-cafe-beige/70 text-cafe-text px-2 py-1.5 rounded-xl focus:border-cafe-gold focus:outline-none">
                                @foreach(['tersedia' => 'Kosong', 'terpakai' => 'Terisi', 'nonaktif' => 'Nonaktif'] as $val => $label)
                                    <option value="{{ $val }}" {{ $realtimeStatus === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Modal / Bottom Sheet Create Reservasi (Layar 9 Referensi) -->
<div id="create-modal" class="hidden fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4">
    <div class="fixed inset-0 bg-[#290024]/60 backdrop-blur-sm" onclick="closeCreateModal()"></div>
    <div class="relative bg-[#F2F5E2] rounded-t-[2rem] sm:rounded-3xl shadow-soft-lg w-full max-w-lg border-t sm:border border-[#E3DEA4] z-10 max-h-[92vh] overflow-y-auto pb-[max(1rem,env(safe-area-inset-bottom))]">
        <!-- Handle for mobile -->
        <div class="sm:hidden w-full pt-3 pb-1 flex justify-center cursor-pointer" onclick="closeCreateModal()">
            <div class="w-12 h-1 bg-[#775533]/30 rounded-full"></div>
        </div>

        <div class="px-6 py-4 border-b border-[#E3DEA4]/60 flex items-center justify-between sticky top-0 bg-[#F2F5E2] z-10">
            <div>
                <h3 class="font-serif font-bold text-base text-[#290024]">Reservasi Meja Baru</h3>
                <p class="text-[10px] text-[#775533]">Isi formulir reservasi tamu</p>
            </div>
            <button onclick="closeCreateModal()" class="w-8 h-8 rounded-full bg-[#E3DEA4]/50 text-[#290024] flex items-center justify-center"><i data-lucide="x" class="w-4 h-4"></i></button>
        </div>

        <form action="{{ route('kasir.reservations.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="sm:col-span-2">
                    <label class="block text-[10px] font-bold text-[#775533] uppercase tracking-wider mb-1">Nama Tamu / Pelanggan *</label>
                    <input type="text" name="customer_name" required class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs text-[#290024] placeholder:text-[#775533]/50 px-3.5 py-2.5 rounded-2xl border border-[#E3DEA4] focus:border-[#D4954D] focus:outline-none" placeholder="Nama lengkap">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-[#775533] uppercase tracking-wider mb-1">Nomor WhatsApp / HP</label>
                    <input type="text" name="phone" class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs text-[#290024] placeholder:text-[#775533]/50 px-3.5 py-2.5 rounded-2xl border border-[#E3DEA4] focus:border-[#D4954D] focus:outline-none" placeholder="08xxxx">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-[#775533] uppercase tracking-wider mb-1">Jumlah Orang *</label>
                    <input type="number" name="party_size" value="2" min="1" max="50" required class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs text-[#290024] px-3.5 py-2.5 rounded-2xl border border-[#E3DEA4] focus:border-[#D4954D] focus:outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-[#775533] uppercase tracking-wider mb-1">Tanggal Mulai *</label>
                    <input type="date" name="reservation_date" id="create_reservation_date" value="{{ $date->format('Y-m-d') }}" required onchange="const end = document.getElementById('create_reservation_end_date'); end.min = this.value; if(end.value < this.value) end.value = this.value;" class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs text-[#290024] px-3.5 py-2.5 rounded-2xl border border-[#E3DEA4] focus:border-[#D4954D] focus:outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-[#775533] uppercase tracking-wider mb-1">Jam Mulai *</label>
                    <input type="time" name="reservation_time" value="19:00" required class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs text-[#290024] px-3.5 py-2.5 rounded-2xl border border-[#E3DEA4] focus:border-[#D4954D] focus:outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-[#775533] uppercase tracking-wider mb-1">Tanggal Selesai (Bisa &gt; 1 Hari)</label>
                    <input type="date" name="reservation_end_date" id="create_reservation_end_date" min="{{ $date->format('Y-m-d') }}" value="{{ $date->format('Y-m-d') }}" class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs text-[#290024] px-3.5 py-2.5 rounded-2xl border border-[#E3DEA4] focus:border-[#D4954D] focus:outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-[#775533] uppercase tracking-wider mb-1">Jam Selesai *</label>
                    <input type="time" name="reservation_end_time" value="21:00" class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs text-[#290024] px-3.5 py-2.5 rounded-2xl border border-[#E3DEA4] focus:border-[#D4954D] focus:outline-none">
                </div>

                <div class="sm:col-span-2 p-2.5 rounded-2xl bg-[#E3DEA4]/50 border border-[#D4954D]/40 text-[11px] text-[#290024]">
                    <div class="flex items-start gap-2">
                        <i data-lucide="info" class="w-4 h-4 text-[#D4954D] shrink-0 mt-0.5"></i>
                        <p class="leading-relaxed">
                            <b>Ketentuan Realtime Meja:</b> Meja tetap <b>kosong</b> sebelum jamnya tiba. Meja otomatis <b>terisi 1 jam sebelum jam mulai</b> dan kembali <b>kosong 1 jam setelah jam selesai</b>. Tanggal selesai dapat diatur lebih dari 1 hari.
                        </p>
                    </div>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-[10px] font-bold text-[#775533] uppercase tracking-wider mb-1">Pilih Meja</label>
                    <select name="table_id" class="w-full bg-[#E3DEA4]/40 text-xs text-[#290024] px-3.5 py-2.5 rounded-2xl border border-[#E3DEA4] focus:border-[#D4954D] focus:outline-none">
                        <option value="">— Dibagikan saat check-in —</option>
                        @foreach($tables as $table)
                            <option value="{{ $table->id }}" {{ $table->status === 'nonaktif' ? 'disabled' : '' }}>Meja {{ $table->number }} (Kapasitas {{ $table->capacity }} orang)</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-[10px] font-bold text-[#775533] uppercase tracking-wider mb-1">Catatan / Permintaan Khusus</label>
                    <textarea name="notes" rows="2" class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs text-[#290024] placeholder:text-[#775533]/50 px-3.5 py-2.5 rounded-2xl border border-[#E3DEA4] focus:border-[#D4954D] focus:outline-none resize-none" placeholder="Misal: Dekat colokan, smoking area..."></textarea>
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-full bg-[#290024] hover:bg-[#775533] text-[#F2F5E2] text-xs font-bold shadow-md transition-all active:scale-[0.98]">
                    <i data-lucide="calendar-plus" class="w-4 h-4 text-[#D4954D]"></i>
                    Simpan Reservasi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openCreateModal() { document.getElementById('create-modal').classList.remove('hidden'); }
function closeCreateModal() { document.getElementById('create-modal').classList.add('hidden'); }

function switchReservationView(view) {
    const secTables = document.getElementById('section-tables');
    const secList = document.getElementById('section-list');
    const btnTables = document.getElementById('tab-btn-tables');
    const btnList = document.getElementById('tab-btn-list');

    if (view === 'tables') {
        secTables.classList.remove('hidden');
        secList.classList.add('hidden');
        secList.classList.add('lg:block');
        btnTables.className = 'flex-1 py-2 rounded-full text-xs font-bold transition-all bg-[#290024] text-[#F2F5E2] shadow-sm';
        btnList.className = 'flex-1 py-2 rounded-full text-xs font-bold transition-all text-[#775533] hover:text-[#290024]';
    } else {
        secTables.classList.add('hidden');
        secTables.classList.add('lg:block');
        secList.classList.remove('hidden');
        btnList.className = 'flex-1 py-2 rounded-full text-xs font-bold transition-all bg-[#290024] text-[#F2F5E2] shadow-sm';
        btnTables.className = 'flex-1 py-2 rounded-full text-xs font-bold transition-all text-[#775533] hover:text-[#290024]';
    }
}
</script>

@endsection