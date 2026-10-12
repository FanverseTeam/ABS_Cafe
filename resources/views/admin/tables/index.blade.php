@extends('admin.layouts.app')

@section('title', 'Manajemen Meja Kafe')
@section('page_title', 'Meja')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-1.5 text-xs text-cafe-muted mb-3">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-cafe-dark flex items-center gap-1">
            <i data-lucide="home" class="w-3.5 h-3.5"></i>
            <span>Admin</span>
        </a>
        <i data-lucide="chevron-right" class="w-3 h-3 text-cafe-muted/60"></i>
        <span class="font-semibold text-cafe-dark">Denah &amp; Meja</span>
    </nav>

    <!-- Page Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-serif text-2xl font-bold text-cafe-dark">Denah Meja &amp; QR Code Pemesanan</h2>
            <p class="text-xs text-cafe-muted mt-0.5">Pantau status meja, atur kapasitas kursi, dan generate QR code scan pesan pelanggan</p>
        </div>

        <button onclick="openTableModal()" class="px-5 py-2.5 rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream font-semibold text-xs shadow-md transition-all flex items-center gap-2">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Tambah Meja Baru</span>
        </button>
    </div>

    <!-- Tables Grid Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-5">
        @forelse($tables as $table)
            <div class="bg-cafe-card rounded-2xl p-5 shadow-soft hover:shadow-soft-lg border border-cafe-beige/50 flex flex-col justify-between transition-all relative overflow-hidden group">
                <!-- Status Top Border Indicator -->
                <div class="absolute top-0 left-0 right-0 h-1.5 {{ $table->status === 'tersedia' ? 'bg-cafe-success' : ($table->status === 'terpakai' ? 'bg-cafe-warning' : 'bg-cafe-muted') }}"></div>

                <div>
                    <!-- Header Card: Nomor & Status -->
                    <div class="flex items-start justify-between mb-3">
                        <div class="w-12 h-12 rounded-2xl bg-cafe-beige/70 flex flex-col items-center justify-center text-cafe-dark shadow-inner">
                            <span class="text-[9px] uppercase tracking-wider text-cafe-muted font-bold">Meja</span>
                            <span class="font-serif font-black text-lg leading-none text-cafe-dark">{{ $table->number }}</span>
                        </div>

                        <div>
                            @if($table->status === 'tersedia')
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#4F7A4A]/15 text-[#4F7A4A] border border-[#4F7A4A]/30">Tersedia</span>
                            @elseif($table->status === 'terpakai')
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#C98A2B]/15 text-[#C98A2B] border border-[#C98A2B]/30 animate-pulse">Terpakai</span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-cafe-muted/15 text-cafe-muted border border-cafe-beige">Nonaktif</span>
                            @endif
                        </div>
                    </div>

                    <!-- Details -->
                    <div class="space-y-1 mb-4 text-xs">
                        <p class="text-cafe-muted flex items-center gap-1.5">
                            <i data-lucide="users" class="w-3.5 h-3.5 text-cafe-gold"></i>
                            <span>Kapasitas: <strong class="text-cafe-dark">{{ $table->capacity }} Orang</strong></span>
                        </p>
                        <p class="text-cafe-muted flex items-center gap-1.5">
                            <i data-lucide="map-pin" class="w-3.5 h-3.5 text-cafe-gold"></i>
                            <span class="truncate">{{ $table->location_notes ?? 'Area Utama' }}</span>
                        </p>
                    </div>
                </div>

                <!-- Footer Action Buttons -->
                <div class="pt-3 border-t border-cafe-beige/50 flex items-center justify-between">
                    <!-- QR Code Button -->
                    <button onclick="openQrModal({{ $table->number }})" class="inline-flex items-center gap-1 text-[11px] font-bold text-cafe-brown hover:text-cafe-dark px-2.5 py-1 rounded-full bg-cafe-beige/60 hover:bg-cafe-beige transition-colors">
                        <i data-lucide="qr-code" class="w-3.5 h-3.5"></i>
                        <span>QR Code</span>
                    </button>

                    <!-- Edit & Delete -->
                    <div class="flex items-center gap-1">
                        <button onclick="editTableModal({{ json_encode($table) }})" class="p-1.5 rounded-full hover:bg-cafe-beige/60 text-cafe-brown hover:text-cafe-dark transition-colors" title="Edit Meja">
                            <i data-lucide="edit" class="w-3.5 h-3.5"></i>
                        </button>
                        <form action="{{ route('admin.tables.destroy', $table->id) }}" method="POST" onsubmit="return confirm('Hapus meja #{{ $table->number }}?');" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 rounded-full hover:bg-cafe-danger/10 text-cafe-danger transition-colors" title="Hapus Meja">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-5 p-12 text-center bg-cafe-card/50 rounded-2xl border border-dashed border-cafe-beige/80">
                <i data-lucide="grid-3x3" class="w-10 h-10 text-cafe-muted mx-auto mb-2"></i>
                <h4 class="font-serif font-bold text-base text-cafe-dark">Belum Ada Meja Terdaftar</h4>
                <p class="text-xs text-cafe-muted mt-1">Tambahkan nomor meja kafe Anda sekarang.</p>
            </div>
        @endforelse
    </div>
</div>

<!-- MODAL QR CODE MEJA -->
<div id="qr-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 overflow-y-auto hidden">
    <div class="fixed inset-0 bg-[#3B1E14]/40 backdrop-blur-sm" onclick="closeQrModal()"></div>

    <div class="relative w-full max-w-sm bg-cafe-card rounded-2xl shadow-soft-lg border border-cafe-beige/60 overflow-hidden z-10 my-8 p-6 text-center space-y-4">
        <div>
            <h3 class="font-serif text-xl font-bold text-cafe-dark" id="qr-modal-title">QR Code Meja</h3>
            <p class="text-xs text-cafe-muted mt-0.5">Scan untuk memesan langsung dari meja kafe.</p>
        </div>

        <div class="p-4 bg-white rounded-2xl border border-cafe-beige/80 inline-block shadow-inner mx-auto">
            <img id="qr-modal-image" src="" alt="QR Code" class="w-48 h-48 mx-auto">
        </div>

        <p class="text-[11px] text-cafe-muted font-mono" id="qr-modal-url"></p>

        <div class="flex items-center justify-center gap-3 pt-2">
            <a id="qr-modal-download" href="" download="" target="_blank" class="px-5 py-2.5 rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream font-semibold text-xs shadow-md transition-all flex items-center gap-1.5">
                <i data-lucide="download" class="w-4 h-4"></i>
                <span>Unduh QR Image</span>
            </a>
            <button type="button" onclick="closeQrModal()" class="px-4 py-2 rounded-full text-xs font-semibold text-cafe-dark hover:bg-cafe-beige/60">Tutup</button>
        </div>
    </div>
</div>

<!-- MODAL FORM TAMBAH / EDIT MEJA -->
<div id="table-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 overflow-y-auto hidden">
    <div class="fixed inset-0 bg-[#3B1E14]/40 backdrop-blur-sm" onclick="closeTableModal()"></div>

    <div class="relative w-full max-w-md bg-cafe-card rounded-2xl shadow-soft-lg border border-cafe-beige/60 overflow-hidden z-10 my-8">
        <div class="flex items-center justify-between px-6 py-5 border-b border-cafe-beige/50 bg-[#F6EDE0]/40">
            <div>
                <h3 class="font-serif text-lg font-bold text-cafe-dark" id="table-modal-title">Tambah Meja Baru</h3>
                <p class="text-xs text-cafe-muted mt-0.5">Atur penomoran meja, kapasitas tempat duduk, dan area.</p>
            </div>
            <button onclick="closeTableModal()" class="w-8 h-8 rounded-full bg-cafe-beige/60 hover:bg-cafe-beige text-cafe-muted hover:text-cafe-dark flex items-center justify-center">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="table-form" method="POST" action="{{ route('admin.tables.store') }}" class="p-6 space-y-4 text-xs">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="font-semibold text-cafe-dark">Nomor Meja *</label>
                    <input type="number" name="number" id="tbl-number" required min="1" placeholder="misal: 11" class="w-full bg-[#EFE0CC]/40 focus:bg-white text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                </div>
                <div class="space-y-1">
                    <label class="font-semibold text-cafe-dark">Kapasitas (Orang) *</label>
                    <input type="number" name="capacity" id="tbl-capacity" value="4" required min="1" class="w-full bg-[#EFE0CC]/40 focus:bg-white text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                </div>
            </div>

            <div class="space-y-1">
                <label class="font-semibold text-cafe-dark">Status Meja *</label>
                <select name="status" id="tbl-status" required class="w-full bg-[#EFE0CC]/40 focus:bg-white text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                    <option value="tersedia">Tersedia (Kosong)</option>
                    <option value="terpakai">Terpakai (Ada Tamu)</option>
                    <option value="nonaktif">Nonaktif (Rusak / Sedang Diperbaiki)</option>
                </select>
            </div>

            <div class="space-y-1">
                <label class="font-semibold text-cafe-dark">Catatan Lokasi / Area</label>
                <input type="text" name="location_notes" id="tbl-location" placeholder="misal: Lantai 1 Dekat Jendela, Outdoor Taman..." class="w-full bg-[#EFE0CC]/40 focus:bg-white text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-cafe-beige">
                <button type="button" onclick="closeTableModal()" class="px-5 py-2.5 rounded-full text-xs font-semibold text-cafe-dark hover:bg-cafe-beige/60">Batal</button>
                <button type="submit" class="px-6 py-2.5 rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream font-semibold text-xs shadow-md transition-all">Simpan Meja</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openQrModal(number) {
        const orderUrl = `https://abscafe.com/order?table=${number}`;
        const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodeURIComponent(orderUrl)}`;

        document.getElementById('qr-modal-title').innerText = `QR Code Meja #${number}`;
        document.getElementById('qr-modal-image').src = qrUrl;
        document.getElementById('qr-modal-url').innerText = orderUrl;
        document.getElementById('qr-modal-download').href = qrUrl;
        document.getElementById('qr-modal').classList.remove('hidden');
    }

    function closeQrModal() {
        document.getElementById('qr-modal').classList.add('hidden');
    }

    function openTableModal() {
        document.getElementById('table-modal-title').innerText = 'Tambah Meja Baru';
        document.getElementById('table-form').action = "{{ route('admin.tables.store') }}";
        document.getElementById('tbl-number').value = '';
        document.getElementById('tbl-capacity').value = '4';
        document.getElementById('tbl-status').value = 'tersedia';
        document.getElementById('tbl-location').value = '';
        document.getElementById('table-modal').classList.remove('hidden');
    }

    function editTableModal(table) {
        document.getElementById('table-modal-title').innerText = `Edit Meja #${table.number}`;
        document.getElementById('table-form').action = `/admin/meja/${table.id}/update`;
        document.getElementById('tbl-number').value = table.number;
        document.getElementById('tbl-capacity').value = table.capacity;
        document.getElementById('tbl-status').value = table.status;
        document.getElementById('tbl-location').value = table.location_notes || '';
        document.getElementById('table-modal').classList.remove('hidden');
    }

    function closeTableModal() {
        document.getElementById('table-modal').classList.add('hidden');
    }
</script>
@endsection
