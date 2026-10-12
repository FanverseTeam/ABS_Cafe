@extends('kasir.layouts.app')

@section('title', 'Reservasi & Meja - Kasir')
@section('page_title', 'Reservasi & Meja')

@section('content')
<div class="space-y-6">
    <!-- Header: date picker + new reservation -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <form action="{{ route('kasir.reservations.index') }}" method="GET" class="flex items-center gap-2">
            <input type="date" name="date" value="{{ $date->format('Y-m-d') }}" class="bg-cafe-card border border-cafe-beige text-xs text-cafe-text px-4 py-2.5 rounded-2xl focus:border-cafe-gold focus:outline-none focus:ring-2 focus:ring-cafe-gold/20">
            <button class="px-4 py-2.5 rounded-2xl bg-cafe-dark text-white text-xs font-semibold hover:bg-cafe-brown transition-colors">Tampilkan</button>
        </form>
        <button onclick="openCreateModal()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-cafe-gold text-cafe-dark text-xs font-bold hover:bg-[#B87C3C] transition-colors shadow-md">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Buat Reservasi
        </button>
    </div>

    <!-- Peta Meja -->
    <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft overflow-hidden">
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
    <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft overflow-hidden">
        <div class="px-6 py-4 border-b border-cafe-beige/50 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-2xl bg-cafe-info/15 text-cafe-info flex items-center justify-center"><i data-lucide="calendar-days" class="w-4 h-4"></i></div>
                <div>
                    <h3 class="font-serif font-bold text-sm text-cafe-dark">Reservasi — {{ $date->translatedFormat('l, d M Y') }}</h3>
                    <p class="text-[10px] text-cafe-muted">{{ $reservations->count() }} reservasi</p>
                </div>
            </div>

            <div class="flex gap-1.5 flex-wrap">
                @php
                    $statusTabs = ['semua' => 'Semua'] + \App\Models\Reservation::STATUSES;
                @endphp
                @foreach($statusTabs as $key => $label)
                    <a href="{{ route('kasir.reservations.index', ['date' => $date->format('Y-m-d'), 'status' => $key]) }}"
                       class="px-3 py-1.5 rounded-full text-[10px] font-semibold {{ $status === $key ? 'bg-cafe-dark text-white' : 'bg-cafe-beige/50 text-cafe-brown hover:bg-cafe-beige' }}">
                        {{ $label }}{{ isset($counts[$key]) && $key !== 'semua' ? ' ('.$counts[$key].')' : '' }}
                    </a>
                @endforeach
            </div>
        </div>

        <div class="p-5">
            @if($reservations->count() === 0)
                <div class="py-10 text-center">
                    <div class="w-14 h-14 mx-auto rounded-full bg-cafe-cream flex items-center justify-center text-cafe-muted">
                        <i data-lucide="calendar-x" class="w-6 h-6"></i>
                    </div>
                    <p class="text-xs text-cafe-muted mt-3">Tidak ada reservasi pada tanggal/status ini.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    @foreach($reservations as $reservation)
                        @php $badge = $reservation->statusBadge(); @endphp
                        <div class="p-4 rounded-2xl bg-cafe-cream/70 border border-cafe-beige/50">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <p class="text-xs font-bold text-cafe-dark">{{ $reservation->customer_name }}</p>
                                    <p class="text-[10px] text-cafe-muted mt-0.5">{{ $reservation->code }} &bull; {{ $reservation->phone ?? '-' }}</p>
                                </div>
                                <span class="text-[9px] font-bold px-2 py-1 rounded-full bg-cafe-{{ $badge['color'] }}/15 text-cafe-{{ $badge['color'] }} border border-cafe-{{ $badge['color'] }}/30 shrink-0">{{ $badge['label'] }}</span>
                            </div>

                            <div class="flex flex-wrap gap-x-4 gap-y-1 mt-3 text-[11px] text-cafe-muted">
                                <span class="flex items-center gap-1"><i data-lucide="clock" class="w-3 h-3 text-cafe-gold"></i>{{ substr($reservation->reservation_time, 0, 5) }}</span>
                                <span class="flex items-center gap-1"><i data-lucide="users" class="w-3 h-3 text-cafe-gold"></i>{{ $reservation->party_size }} orang</span>
                                <span class="flex items-center gap-1"><i data-lucide="armchair" class="w-3 h-3 text-cafe-gold"></i>{{ $reservation->table ? 'Meja '.$reservation->table->number : 'Belum ditetapkan' }}</span>
                            </div>

                            @if($reservation->notes)
                                <p class="text-[10px] text-cafe-muted italic mt-2">&ldquo;{{ $reservation->notes }}&rdquo;</p>
                            @endif

                            <div class="flex flex-wrap gap-1.5 mt-3 pt-3 border-t border-cafe-beige/40">
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
                                        <button class="px-2.5 py-1.5 rounded-full text-[10px] font-bold transition-colors {{ $style === 'btn-primary' ? 'bg-cafe-dark text-white hover:bg-cafe-brown' : ($style === 'btn-danger' ? 'bg-cafe-danger/10 text-cafe-danger border border-cafe-danger/30 hover:bg-cafe-danger/20' : 'bg-cafe-beige/60 text-cafe-brown hover:bg-cafe-beige') }}">
                                            {{ $label }}
                                        </button>
                                    </form>
                                @endforeach

                                <form action="{{ route('kasir.reservations.destroy', $reservation) }}" method="POST" onsubmit="return confirm('Hapus reservasi {{ $reservation->code }}?')">
                                    @csrf @method('DELETE')
                                    <button class="px-2.5 py-1.5 rounded-full text-[10px] font-bold text-cafe-muted hover:text-cafe-danger border border-cafe-beige hover:border-cafe-danger/40 transition-colors">
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

<!-- Modal Create -->
<div id="create-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-[#290024]/60 backdrop-blur-sm" onclick="closeCreateModal()"></div>
    <div class="relative bg-cafe-card rounded-3xl shadow-soft-lg w-full max-w-lg border border-cafe-beige/60 animate-in fade-in max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-cafe-beige/50 flex items-center justify-between sticky top-0 bg-cafe-card">
            <div>
                <h3 class="font-serif font-bold text-sm text-cafe-dark">Buat Reservasi</h3>
                <p class="text-[10px] text-cafe-muted">kode reservasi dibuat otomatis</p>
            </div>
            <button onclick="closeCreateModal()" class="p-1.5 rounded-full bg-cafe-cream text-cafe-muted hover:text-cafe-dark"><i data-lucide="x" class="w-4 h-4"></i></button>
        </div>

        <form action="{{ route('kasir.reservations.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="sm:col-span-2">
                    <label class="block text-[10px] font-semibold text-cafe-muted uppercase tracking-wide mb-1.5">Nama Pelanggan *</label>
                    <input type="text" name="customer_name" required class="w-full bg-[#E3DEA4]/50 focus:bg-white text-xs text-cafe-text placeholder:text-cafe-muted/60 px-4 py-3 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none focus:ring-2 focus:ring-cafe-gold/20" placeholder="Nama lengkap">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-cafe-muted uppercase tracking-wide mb-1.5">No. HP</label>
                    <input type="text" name="phone" class="w-full bg-[#E3DEA4]/50 focus:bg-white text-xs text-cafe-text placeholder:text-cafe-muted/60 px-4 py-3 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none focus:ring-2 focus:ring-cafe-gold/20" placeholder="08xxxx">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-cafe-muted uppercase tracking-wide mb-1.5">Jumlah Orang *</label>
                    <input type="number" name="party_size" value="2" min="1" max="50" required class="w-full bg-[#E3DEA4]/50 focus:bg-white text-xs text-cafe-text px-4 py-3 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none focus:ring-2 focus:ring-cafe-gold/20">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-cafe-muted uppercase tracking-wide mb-1.5">Tanggal *</label>
                    <input type="date" name="reservation_date" value="{{ $date->format('Y-m-d') }}" required class="w-full bg-[#E3DEA4]/50 focus:bg-white text-xs text-cafe-text px-4 py-3 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none focus:ring-2 focus:ring-cafe-gold/20">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-cafe-muted uppercase tracking-wide mb-1.5">Jam *</label>
                    <input type="time" name="reservation_time" value="19:00" required class="w-full bg-[#E3DEA4]/50 focus:bg-white text-xs text-cafe-text px-4 py-3 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none focus:ring-2 focus:ring-cafe-gold/20">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-[10px] font-semibold text-cafe-muted uppercase tracking-wide mb-1.5">Meja</label>
                    <select name="table_id" class="w-full bg-[#E3DEA4]/50 text-xs text-cafe-text px-4 py-3 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none focus:ring-2 focus:ring-cafe-gold/20">
                        <option value="">— Dibagikan saat check-in —</option>
                        @foreach($tables as $table)
                            <option value="{{ $table->id }}" {{ $table->status === 'nonaktif' ? 'disabled' : '' }}>Meja {{ $table->number }} (Kap {{ $table->capacity }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-[10px] font-semibold text-cafe-muted uppercase tracking-wide mb-1.5">Catatan</label>
                    <textarea name="notes" rows="2" class="w-full bg-[#E3DEA4]/50 focus:bg-white text-xs text-cafe-text placeholder:text-cafe-muted/60 px-4 py-3 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none focus:ring-2 focus:ring-cafe-gold/20 resize-none" placeholder="Catatan khusus pelanggan"></textarea>
                </div>
            </div>

            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-full bg-cafe-dark text-white text-xs font-bold hover:bg-cafe-brown transition-colors">
                <i data-lucide="calendar-plus" class="w-4 h-4"></i>
                Simpan Reservasi
            </button>
        </form>
    </div>
</div>

<script>
function openCreateModal() { document.getElementById('create-modal').classList.remove('hidden'); }
function closeCreateModal() { document.getElementById('create-modal').classList.add('hidden'); }
</script>
@endsection