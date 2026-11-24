<div class="space-y-6">

    <!-- Flash Message Notification -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-800 text-xs font-semibold flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600 shrink-0"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">
                <i data-lucide="x" class="w-3.5 h-3.5"></i>
            </button>
        </div>
    @endif

    <!-- Page Header & Action (Desktop only) -->
    <div class="hidden lg:flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-serif text-2xl font-bold text-cafe-dark">Denah &amp; Kelola Meja</h2>
            <p class="text-xs text-cafe-muted mt-0.5">Kelola kapasitas kursi, denah nomor meja, dan cetak stand QR code</p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('admin.website.index', ['tab' => 'qr_meja']) }}" class="px-4 py-2.5 rounded-full bg-cafe-card hover:bg-cafe-beige/60 text-cafe-dark border border-cafe-beige/80 font-semibold text-xs shadow-sm transition-all flex items-center gap-2">
                <i data-lucide="settings" class="w-4 h-4 text-cafe-gold"></i>
                <span>Pengaturan URL QR</span>
            </a>

            <a href="{{ route('admin.operations.tables.print_all') }}" target="_blank" class="px-4 py-2.5 rounded-full bg-cafe-card hover:bg-cafe-beige/60 text-cafe-dark border border-cafe-beige/80 font-semibold text-xs shadow-sm transition-all flex items-center gap-2">
                <i data-lucide="printer" class="w-4 h-4 text-cafe-gold"></i>
                <span>Cetak Semua Stand Meja</span>
            </a>

            <button onclick="openTableModal()" class="px-5 py-2.5 rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream font-semibold text-xs shadow-md transition-all flex items-center gap-2 cursor-pointer">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Tambah Meja Baru</span>
            </button>
        </div>
    </div>

    {{-- Mobile Action Buttons --}}
    <div class="flex lg:hidden items-center gap-2">
        <a href="{{ route('admin.website.index', ['tab' => 'qr_meja']) }}" class="flex-1 py-2 px-3 text-center rounded-xl bg-cafe-card text-cafe-dark border border-cafe-beige/80 font-semibold text-xs shadow-sm flex items-center justify-center gap-1.5">
            <i data-lucide="settings" class="w-3.5 h-3.5 text-cafe-gold"></i>
            <span>URL QR</span>
        </a>
        <a href="{{ route('admin.operations.tables.print_all') }}" target="_blank" class="flex-1 py-2 px-3 text-center rounded-xl bg-cafe-card text-cafe-dark border border-cafe-beige/80 font-semibold text-xs shadow-sm flex items-center justify-center gap-1.5">
            <i data-lucide="printer" class="w-3.5 h-3.5 text-cafe-gold"></i>
            <span>Cetak Stand</span>
        </a>
    </div>
    <x-mobile.fab icon="plus" label="Tambah Meja" onclick="openTableModal()" />

    <!-- Tables Grid Cards (1 col on narrow mobile, 2 cols on mobile, 3 on tablet/laptop, 4-5 on large desktop) -->
    <div class="grid grid-cols-1 min-[420px]:grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-3 sm:gap-4 lg:gap-5">
        @forelse($tables as $table)
            @php
                $tableOrderUrl = rtrim($orderBaseUrl, '/') . '?meja=' . $table->number;
            @endphp
            <div class="bg-cafe-card rounded-2xl shadow-soft hover:shadow-soft-lg border border-cafe-beige/50 flex flex-col justify-between transition-all relative overflow-hidden group min-h-[310px] sm:min-h-[340px]">
                <!-- Status Top Border Indicator -->
                <div class="absolute top-0 left-0 right-0 h-1.5 {{ $table->status === 'tersedia' ? 'bg-cafe-success' : ($table->status === 'terpakai' ? 'bg-cafe-warning' : 'bg-cafe-muted') }}"></div>

                <div class="p-5 sm:p-6 flex-1 flex flex-col">
                    <!-- Header Card: Nomor & Status Badge -->
                    <div class="flex items-start justify-between mb-5">
                        <div class="w-16 h-16 rounded-2xl bg-[#290024] text-[#F2F5E2] flex flex-col items-center justify-center shadow-md border border-[#D4954D]/30 shrink-0">
                            <span class="text-[9px] uppercase tracking-widest text-[#D4954D] font-bold">Meja</span>
                            <span class="font-serif font-black text-2xl leading-none text-[#F2F5E2] mt-0.5">{{ $table->number }}</span>
                        </div>

                        <div>
                            @if($table->status === 'tersedia')
                                <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-[#775533]/15 text-[#775533] border border-[#775533]/30 shadow-xs flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    <span>Tersedia</span>
                                </span>
                            @elseif($table->status === 'terpakai')
                                <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-[#D4954D]/15 text-[#D4954D] border border-[#D4954D]/30 shadow-xs flex items-center gap-1.5 animate-pulse">
                                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                    <span>Terpakai</span>
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-cafe-muted/15 text-cafe-muted border border-cafe-beige shadow-xs">Nonaktif</span>
                            @endif
                        </div>
                    </div>

                    <!-- Details: Kapasitas & Lokasi -->
                    <div class="space-y-2.5 my-4 text-xs">
                        <div class="flex items-center justify-between p-2.5 rounded-xl bg-cafe-card border border-cafe-beige/40">
                            <span class="text-cafe-muted flex items-center gap-2">
                                <i data-lucide="users" class="w-4 h-4 text-cafe-gold shrink-0"></i>
                                <span>Kapasitas Kursi</span>
                            </span>
                            <strong class="text-cafe-dark text-xs">{{ $table->capacity }} Orang</strong>
                        </div>
                        <div class="flex items-center justify-between p-2.5 rounded-xl bg-cafe-card border border-cafe-beige/40">
                            <span class="text-cafe-muted flex items-center gap-2">
                                <i data-lucide="map-pin" class="w-4 h-4 text-cafe-gold shrink-0"></i>
                                <span>Lokasi</span>
                            </span>
                            <span class="truncate font-semibold text-cafe-dark text-xs max-w-[140px] text-right">{{ $table->location_notes ?? 'Lantai 1' }}</span>
                        </div>
                    </div>

                    <!-- Direct URL Snippet with Quick Copy -->
                    <div class="bg-beige/80 p-2 rounded-xl border border-cafe-beige/70 flex items-center justify-between gap-1.5 mb-1 mt-auto">
                        <span class="text-[10px] font-mono text-cafe-muted truncate select-all pl-1.5">
                            ?meja={{ $table->number }}
                        </span>
                        <button type="button" onclick="copyText('{{ $tableOrderUrl }}', 'Link Meja #{{ $table->number }} disalin!')" class="p-1.5 rounded-lg hover:bg-cafe-beige text-cafe-dark transition-colors shrink-0 bg-cafe-card shadow-xs" title="Salin link lengkap">
                            <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>
                </div>

                <!-- Footer Action Buttons -->
                <div class="p-4 sm:px-6 bg-cafe-card border-t border-cafe-beige/50 flex items-center justify-between gap-2">
                    <!-- QR Code Button -->
                    <button onclick="openQrModal({{ $table->number }}, {{ $table->id }})" class="inline-flex items-center gap-1.5 text-xs font-bold text-cafe-dark hover:text-[#D4954D] px-3 py-1.5 rounded-full bg-cafe-beige/60 hover:bg-cafe-beige transition-all shadow-2xs">
                        <i data-lucide="qr-code" class="w-3.5 h-3.5 text-cafe-gold"></i>
                        <span>QR Code</span>
                    </button>

                    <!-- Edit, Print & Delete -->
                    <div class="flex items-center gap-1 shrink-0">
                        <a href="{{ route('admin.operations.tables.print_single', $table->id) }}" target="_blank" class="p-2 rounded-xl hover:bg-cafe-beige/60 text-cafe-muted hover:text-cafe-dark transition-colors" title="Cetak Stand Meja Ini">
                            <i data-lucide="printer" class="w-4 h-4"></i>
                        </a>
                        <button onclick="editTableModal({{ json_encode($table) }})" class="p-2 rounded-xl hover:bg-cafe-beige/60 text-cafe-brown hover:text-cafe-dark transition-colors" title="Edit Meja">
                            <i data-lucide="edit" class="w-4 h-4"></i>
                        </button>
                        <form action="{{ route('admin.operations.tables.destroy', $table->id) }}" method="POST" onsubmit="return confirm('Hapus meja #{{ $table->number }}?');" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 rounded-xl hover:bg-cafe-danger/10 text-cafe-danger transition-colors" title="Hapus Meja">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
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
    <div class="fixed inset-0 bg-[#290024]/40 backdrop-blur-sm" onclick="closeQrModal()"></div>

    <div class="relative w-full max-w-sm bg-cafe-card rounded-2xl shadow-soft-lg border border-cafe-beige/60 overflow-hidden z-10 my-8 p-6 text-center space-y-4">
        <div>
            <h3 class="font-serif text-xl font-bold text-cafe-dark" id="qr-modal-title">QR Code Meja</h3>
            <p class="text-xs text-cafe-muted mt-0.5">Scan untuk memesan langsung dari meja kafe.</p>
        </div>

        <div class="p-4 bg-beige rounded-2xl border border-cafe-beige/80 inline-block shadow-inner mx-auto">
            <img id="qr-modal-image" src="" alt="QR Code" class="w-52 h-52 mx-auto">
        </div>

        <div class="bg-white p-2.5 rounded-xl border border-cafe-beige/60 flex items-center justify-between gap-2 text-left">
            <span class="text-[11px] text-cafe-dark font-mono truncate select-all" id="qr-modal-url"></span>
            <button type="button" onclick="copyModalUrl()" class="px-2.5 py-1 rounded bg-cafe-card hover:bg-cafe-beige text-cafe-dark text-[11px] font-semibold border border-cafe-beige shrink-0">
                Salin
            </button>
        </div>

        <div class="flex flex-col gap-2 pt-2">
            <div class="flex items-center justify-center gap-2">
                <a id="qr-modal-download" href="" download="" target="_blank" class="flex-1 px-4 py-2.5 rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream font-semibold text-xs shadow-md transition-all flex items-center justify-center gap-1.5">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    <span>Unduh PNG</span>
                </a>
                <a id="qr-modal-print" href="" target="_blank" class="flex-1 px-4 py-2.5 rounded-full bg-cafe-card hover:bg-cafe-beige text-cafe-dark border border-cafe-beige font-semibold text-xs shadow-sm transition-all flex items-center justify-center gap-1.5">
                    <i data-lucide="printer" class="w-4 h-4 text-cafe-gold"></i>
                    <span>Cetak Stand</span>
                </a>
            </div>
            <button type="button" onclick="closeQrModal()" class="w-full py-2 rounded-full text-xs font-semibold text-cafe-muted hover:text-cafe-dark hover:bg-cafe-beige/40">Tutup</button>
        </div>
    </div>
</div>

<!-- MODAL FORM TAMBAH / EDIT MEJA -->
<div id="table-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 overflow-y-auto hidden">
    <div class="fixed inset-0 bg-[#290024]/40 backdrop-blur-sm" onclick="closeTableModal()"></div>

    <div class="relative w-full max-w-md bg-cafe-card rounded-2xl shadow-soft-lg border border-cafe-beige/60 overflow-hidden z-10 my-8">
        <div class="flex items-center justify-between px-6 py-5 border-b border-cafe-beige/50 bg-[#F2F5E2]/40">
            <div>
                <h3 class="font-serif text-lg font-bold text-cafe-dark" id="table-modal-title">Tambah Meja Baru</h3>
                <p class="text-xs text-cafe-muted mt-0.5">Atur penomoran meja, kapasitas tempat duduk, dan area.</p>
            </div>
            <button onclick="closeTableModal()" class="w-8 h-8 rounded-full bg-cafe-beige/60 hover:bg-cafe-beige text-cafe-muted hover:text-cafe-dark flex items-center justify-center">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="table-form" method="POST" action="{{ route('admin.operations.tables.store') }}" class="p-6 space-y-4 text-xs">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="font-semibold text-cafe-dark">Nomor Meja *</label>
                    <input type="number" name="number" id="tbl-number" required min="1" placeholder="misal: 11" class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                </div>
                <div class="space-y-1">
                    <label class="font-semibold text-cafe-dark">Kapasitas (Orang) *</label>
                    <input type="number" name="capacity" id="tbl-capacity" value="4" required min="1" class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                </div>
            </div>

            <div class="space-y-1">
                <label class="font-semibold text-cafe-dark">Status Meja *</label>
                <select name="status" id="tbl-status" required class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                    <option value="tersedia">Tersedia (Kosong)</option>
                    <option value="terpakai">Terpakai (Ada Tamu)</option>
                    <option value="nonaktif">Nonaktif (Rusak / Sedang Diperbaiki)</option>
                </select>
            </div>

            <div class="space-y-1">
                <label class="font-semibold text-cafe-dark">Catatan Lokasi / Area</label>
                <input type="text" name="location_notes" id="tbl-location" placeholder="misal: Lantai 1 Dekat Jendela, Outdoor Taman..." class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-cafe-beige">
                <button type="button" onclick="closeTableModal()" class="px-5 py-2.5 rounded-full text-xs font-semibold text-cafe-dark hover:bg-cafe-beige/60">Batal</button>
                <button type="submit" class="px-6 py-2.5 rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream font-semibold text-xs shadow-md transition-all">Simpan Meja</button>
            </div>
        </form>
    </div>
</div>

<!-- TOAST FEEDBACK COPY -->
<div id="copy-toast" class="fixed bottom-6 right-6 z-50 px-4 py-2.5 rounded-xl bg-cafe-dark text-cafe-cream text-xs font-semibold shadow-soft-lg border border-cafe-gold/30 flex items-center gap-2 transform translate-y-10 opacity-0 transition-all duration-200 pointer-events-none">
    <i data-lucide="check" class="w-4 h-4 text-cafe-gold"></i>
    <span id="copy-toast-msg">Link berhasil disalin!</span>
</div>

<script>
    const orderBaseUrlConfig = "{{ rtrim($orderBaseUrl, '/') }}";

    function copyText(text, msg = 'Link berhasil disalin ke clipboard!') {
        navigator.clipboard.writeText(text).then(() => {
            const toast = document.getElementById('copy-toast');
            const toastMsg = document.getElementById('copy-toast-msg');
            toastMsg.innerText = msg;
            toast.classList.remove('translate-y-10', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');
            setTimeout(() => {
                toast.classList.remove('translate-y-0', 'opacity-100');
                toast.classList.add('translate-y-10', 'opacity-0');
            }, 2500);
        }).catch(err => {
            alert('URL: ' + text);
        });
    }

    function copyModalUrl() {
        const url = document.getElementById('qr-modal-url').innerText;
        copyText(url, 'URL Meja berhasil disalin!');
    }

    function openQrModal(number, tableId) {
        const orderUrl = `${orderBaseUrlConfig}?meja=${number}`;
        const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=350x350&data=${encodeURIComponent(orderUrl)}`;

        document.getElementById('qr-modal-title').innerText = `QR Code Meja #${number}`;
        document.getElementById('qr-modal-image').src = qrUrl;
        document.getElementById('qr-modal-url').innerText = orderUrl;
        document.getElementById('qr-modal-download').href = qrUrl;
        document.getElementById('qr-modal-download').setAttribute('download', `QR_Meja_${number}_ABSCafe.png`);

        if (tableId) {
            document.getElementById('qr-modal-print').href = `/admin/operasional/meja/${tableId}/cetak-qr`;
            document.getElementById('qr-modal-print').style.display = 'flex';
        } else {
            document.getElementById('qr-modal-print').style.display = 'none';
        }

        document.getElementById('qr-modal').classList.remove('hidden');
    }

    function closeQrModal() {
        document.getElementById('qr-modal').classList.add('hidden');
    }

    function openTableModal() {
        document.getElementById('table-modal-title').innerText = 'Tambah Meja Baru';
        document.getElementById('table-form').action = "{{ route('admin.operations.tables.store') }}";
        document.getElementById('tbl-number').value = '';
        document.getElementById('tbl-capacity').value = '4';
        document.getElementById('tbl-status').value = 'tersedia';
        document.getElementById('tbl-location').value = '';
        document.getElementById('table-modal').classList.remove('hidden');
    }

    function editTableModal(table) {
        document.getElementById('table-modal-title').innerText = `Edit Meja #${table.number}`;
        document.getElementById('table-form').action = `/admin/operasional/meja/${table.id}/update`;
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
