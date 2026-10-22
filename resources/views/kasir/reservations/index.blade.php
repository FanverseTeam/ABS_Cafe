@extends('kasir.layouts.app')

@section('title', 'Reservasi & Meja - Kasir')
@section('page_title', 'Reservasi & Meja')

@section('content')
<div class="space-y-6">
    <!-- Mobile Pill Switcher: Denah Meja | Reservasi (Sesuai Referensi Layar 9) -->
    <div class="lg:hidden flex items-center p-1 bg-[#E3DEA4]/50 rounded-full border border-[#E3DEA4]">
        <button type="button" onclick="switchReservationView('tables')" id="tab-btn-tables" class="flex-1 py-2 rounded-full text-xs font-bold transition-all bg-[#290024] text-[#FAFCF3] shadow-sm">
            Denah Meja
        </button>
        <button type="button" onclick="switchReservationView('list')" id="tab-btn-list" class="flex-1 py-2 rounded-full text-xs font-bold transition-all text-[#775533] hover:text-[#290024]">
            Reservasi ({{ $reservations->count() }})
        </button>
    </div>

    <!-- Header: date picker + new reservation -->
    <div class="flex flex-wrap items-center justify-between gap-3">
        <form action="{{ route('kasir.reservations.index') }}" method="GET" class="flex items-center gap-2">
            <input type="date" name="date" value="{{ $date->format('Y-m-d') }}" class="bg-[#FAFCF3] border border-[#E3DEA4] text-xs text-[#290024] px-3.5 py-2 rounded-2xl focus:border-[#D4954D] focus:outline-none">
            <button class="px-4 py-2 rounded-2xl bg-[#290024] text-[#FAFCF3] text-xs font-semibold hover:bg-[#775533] transition-colors">Tampilkan</button>
        </form>
        <button onclick="openCreateModal()" class="hidden lg:inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-[#D4954D] text-[#290024] text-xs font-bold hover:brightness-105 transition-all shadow-md active:scale-95">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Buat Reservasi
        </button>
    </div>

    {{-- Mobile FAB for Buat Reservasi --}}
    <x-mobile.fab icon="plus" label="Reservasi" onclick="openCreateModal()" />

    <!-- Peta Meja -->
    <div id="section-tables" class="bg-[#FAFCF3] rounded-3xl border border-[#E3DEA4]/80 shadow-soft overflow-hidden">
        <div class="px-6 py-4 border-b border-cafe-beige/50 flex items-center gap-2.5">
            <div class="w-9 h-9 rounded-2xl bg-cafe-gold/15 text-cafe-gold flex items-center justify-center"><i data-lucide="grid-3x3" class="w-4 h-4"></i></div>
            <div>
                <h3 class="font-serif font-bold text-sm text-cafe-dark">Peta Meja</h3>
                <p class="text-[10px] text-cafe-muted">kondisi real-time &bull; klik meja untuk ubah status</p>
            </div>
        </div>

        <div class="p-5">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3">
                @foreach($tables as $table)
                    @php $badge = $table->statusBadge(); @endphp
                    <div class="p-3 rounded-2xl border {{ $table->status === 'terpakai' ? 'bg-cafe-danger/10 border-cafe-danger/40' : ($table->status === 'nonaktif' ? 'bg-cafe-beige/30 border-cafe-beige' : 'bg-cafe-success/10 border-cafe-success/40') }}">
                        <div class="flex items-center justify-between">
                            <span class="font-serif font-bold text-cafe-dark text-sm">Meja {{ $table->number }}</span>
                            <i data-lucide="armchair" class="w-4 h-4 {{ $table->status === 'terpakai' ? 'text-cafe-danger' : ($table->status === 'nonaktif' ? 'text-cafe-muted' : 'text-cafe-success') }}"></i>
                        </div>
                        <p class="text-[10px] text-cafe-muted mt-0.5">Kapasitas {{ $table->capacity }} {{ $table->orders_count > 0 ? '&bull; '.$table->orders_count.' pesanan' : '' }}</p>
                        <span class="inline-block mt-1.5 text-[9px] font-bold px-2 py-0.5 rounded-full bg-cafe-{{ $badge['color'] }}/15 text-cafe-{{ $badge['color'] }}">{{ $badge['label'] }}</span>
                        <form action="{{ route('kasir.tables.status', $table) }}" method="POST" class="mt-2">
                            @csrf
                            <select name="status" onchange="this.form.submit()" class="w-full text-[10px] bg-cafe-cream border border-cafe-beige/70 text-cafe-text px-2 py-1.5 rounded-xl focus:border-cafe-gold focus:outline-none">
                                @foreach(['tersedia' => 'Kosong', 'terpakai' => 'Terisi', 'nonaktif' => 'Nonaktif'] as $val => $label)
                                    <option value="{{ $val }}" {{ $table->status === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Daftar Reservasi -->
    <div id="section-list" class="bg-[#FAFCF3] rounded-3xl border border-[#E3DEA4]/80 shadow-soft overflow-hidden">
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
                       class="px-3 py-1.5 rounded-full text-[10px] font-semibold transition-all {{ $status === $key ? 'bg-[#290024] text-[#FAFCF3]' : 'bg-[#E3DEA4]/50 text-[#775533] hover:bg-[#E3DEA4]' }}">
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
                                <span class="flex items-center gap-1"><i data-lucide="clock" class="w-3 h-3 text-[#D4954D]"></i>{{ substr($reservation->reservation_time, 0, 5) }}</span>
                                <span class="flex items-center gap-1"><i data-lucide="users" class="w-3 h-3 text-[#D4954D]"></i>{{ $reservation->party_size }} orang</span>
                                <span class="flex items-center gap-1"><i data-lucide="armchair" class="w-3 h-3 text-[#D4954D]"></i>{{ $reservation->table ? 'Meja '.$reservation->table->number : 'Belum ditetapkan' }}</span>
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
                                        <button class="px-2.5 py-1.5 rounded-full text-[10px] font-bold transition-all {{ $style === 'btn-primary' ? 'bg-[#290024] text-[#FAFCF3] hover:bg-[#775533]' : ($style === 'btn-danger' ? 'bg-[#B03A2E]/10 text-[#B03A2E] border border-[#B03A2E]/30 hover:bg-[#B03A2E]/20' : 'bg-[#E3DEA4] text-[#290024] hover:bg-[#D4954D]') }}">
                                            {{ $label }}
                                        </button>
                                    </form>
                                @endforeach

                                <form action="{{ route('kasir.reservations.destroy', $reservation) }}" method="POST" onsubmit="return confirm('Hapus reservasi {{ $reservation->code }}?')">
                                    @csrf @method('DELETE')
                                    <button class="px-2.5 py-1.5 rounded-full text-[10px] font-bold text-[#775533] hover:text-[#B03A2E] border border-[#E3DEA4] hover:border-[#B03A2E]/40 transition-colors">
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
</div>

<!-- Modal / Bottom Sheet Create Reservasi (Layar 9 Referensi) -->
<div id="create-modal" class="hidden fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4">
    <div class="fixed inset-0 bg-[#290024]/60 backdrop-blur-sm" onclick="closeCreateModal()"></div>
    <div class="relative bg-[#FAFCF3] rounded-t-[2rem] sm:rounded-3xl shadow-soft-lg w-full max-w-lg border-t sm:border border-[#E3DEA4] z-10 max-h-[92vh] overflow-y-auto pb-[max(1rem,env(safe-area-inset-bottom))]">
        <!-- Handle for mobile -->
        <div class="sm:hidden w-full pt-3 pb-1 flex justify-center cursor-pointer" onclick="closeCreateModal()">
            <div class="w-12 h-1 bg-[#775533]/30 rounded-full"></div>
        </div>

        <div class="px-6 py-4 border-b border-[#E3DEA4]/60 flex items-center justify-between sticky top-0 bg-[#FAFCF3] z-10">
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
                    <input type="text" name="customer_name" required class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs text-[#290024] placeholder:text-[#775533]/50 px-3.5 py-2.5 rounded-2xl border border-[#E3DEA4] focus:border-[#D4954D] focus:outline-none" placeholder="Nama lengkap">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-[#775533] uppercase tracking-wider mb-1">Nomor WhatsApp / HP</label>
                    <input type="text" name="phone" class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs text-[#290024] placeholder:text-[#775533]/50 px-3.5 py-2.5 rounded-2xl border border-[#E3DEA4] focus:border-[#D4954D] focus:outline-none" placeholder="08xxxx">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-[#775533] uppercase tracking-wider mb-1">Jumlah Orang *</label>
                    <input type="number" name="party_size" value="2" min="1" max="50" required class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs text-[#290024] px-3.5 py-2.5 rounded-2xl border border-[#E3DEA4] focus:border-[#D4954D] focus:outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-[#775533] uppercase tracking-wider mb-1">Tanggal *</label>
                    <input type="date" name="reservation_date" value="{{ $date->format('Y-m-d') }}" required class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs text-[#290024] px-3.5 py-2.5 rounded-2xl border border-[#E3DEA4] focus:border-[#D4954D] focus:outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-[#775533] uppercase tracking-wider mb-1">Jam Datang *</label>
                    <input type="time" name="reservation_time" value="19:00" required class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs text-[#290024] px-3.5 py-2.5 rounded-2xl border border-[#E3DEA4] focus:border-[#D4954D] focus:outline-none">
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
                    <textarea name="notes" rows="2" class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs text-[#290024] placeholder:text-[#775533]/50 px-3.5 py-2.5 rounded-2xl border border-[#E3DEA4] focus:border-[#D4954D] focus:outline-none resize-none" placeholder="Misal: Dekat colokan, smoking area..."></textarea>
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-full bg-[#290024] hover:bg-[#775533] text-[#FAFCF3] text-xs font-bold shadow-md transition-all active:scale-[0.98]">
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
        btnTables.className = 'flex-1 py-2 rounded-full text-xs font-bold transition-all bg-[#290024] text-[#FAFCF3] shadow-sm';
        btnList.className = 'flex-1 py-2 rounded-full text-xs font-bold transition-all text-[#775533] hover:text-[#290024]';
    } else {
        secTables.classList.add('hidden');
        secList.classList.remove('hidden');
        btnList.className = 'flex-1 py-2 rounded-full text-xs font-bold transition-all bg-[#290024] text-[#FAFCF3] shadow-sm';
        btnTables.className = 'flex-1 py-2 rounded-full text-xs font-bold transition-all text-[#775533] hover:text-[#290024]';
    }
}
</script>

@endsection