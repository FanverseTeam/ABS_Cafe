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
            <h2 class="font-serif text-2xl font-bold text-cafe-dark">Denah Meja &amp; QR Code Pemesanan</h2>
            <p class="text-xs text-cafe-muted mt-0.5">Pantau status meja, atur kapasitas kursi, dan generate QR code scan pesan pelanggan</p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('admin.operations.tables.print_all') }}" target="_blank" class="px-4 py-2.5 rounded-full bg-cafe-card hover:bg-cafe-beige/60 text-cafe-dark border border-cafe-beige/80 font-semibold text-xs shadow-sm transition-all flex items-center gap-2">
                <i data-lucide="printer" class="w-4 h-4 text-cafe-gold"></i>
                <span>Cetak Semua Stand Meja</span>
            </a>

            <button onclick="openTableModal()" class="px-5 py-2.5 rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream font-semibold text-xs shadow-md transition-all flex items-center gap-2">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Tambah Meja Baru</span>
            </button>
        </div>
    </div>

    {{-- Mobile Action Buttons --}}
    <div class="flex lg:hidden items-center gap-2">
        <a href="{{ route('admin.operations.tables.print_all') }}" target="_blank" class="flex-1 py-2 px-3 text-center rounded-xl bg-cafe-card text-cafe-dark border border-cafe-beige/80 font-semibold text-xs shadow-sm flex items-center justify-center gap-1.5">
            <i data-lucide="printer" class="w-3.5 h-3.5 text-cafe-gold"></i>
            <span>Cetak Stand Meja</span>
        </a>
    </div>
    <x-mobile.fab icon="plus" label="Tambah Meja" onclick="openTableModal()" />

    {{-- ============================================================= --}}
    {{-- CARD PENGATURAN URL & PANEL PENGUJIAN 2 CARA PEMESANAN        --}}
    {{-- ============================================================= --}}
    <div class="bg-cafe-card rounded-2xl p-5 sm:p-6 shadow-soft border border-cafe-beige/60 space-y-5">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 pb-4 border-b border-cafe-beige/50">
            <div>
                <h3 class="font-serif text-lg font-bold text-cafe-dark flex items-center gap-2">
                    <i data-lucide="qr-code" class="w-5 h-5 text-cafe-gold"></i>
                    <span>Pengaturan URL &amp; QR Pemesanan Per Meja</span>
                </h3>
                <p class="text-xs text-cafe-muted mt-0.5">
                    Kelola Base URL yang dimasukkan ke dalam QR Code meja dan uji coba 2 alur pemesanan pelanggan.
                </p>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold bg-[#775533]/10 text-[#775533] border border-[#775533]/20 self-start md:self-auto">
                {{ $tables->count() }} Meja Terdaftar
            </span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
            <!-- Form Setting Base URL (5 Kolom) -->
            <div class="lg:col-span-5 space-y-3">
                <form action="{{ route('admin.operations.qr_setting.update') }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label for="order_base_url" class="block font-semibold text-cafe-dark text-xs mb-1">
                            Base URL Menu / Pemesanan
                        </label>
                        <div class="relative">
                            <input type="text"
                                   name="order_base_url"
                                   id="order_base_url"
                                   value="{{ $orderBaseUrl }}"
                                   placeholder="{{ url('/menu') }}"
                                   class="w-full bg-[#E3DEA4]/30 focus:bg-white text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none font-mono text-cafe-dark">
                        </div>
                        <p class="text-[11px] text-cafe-muted mt-1 leading-relaxed">
                            Tips: Saat menguji di HP menggunakan WiFi lokal yang sama, masukkan IP komputer Anda (misal: <code class="bg-beige px-1 rounded text-cafe-dark">http://192.168.1.10:8000/menu</code>).
                        </p>
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <button type="submit" class="px-4 py-2 rounded-xl bg-cafe-dark hover:bg-cafe-brown text-cafe-cream font-semibold text-xs shadow-sm transition-all flex items-center gap-1.5">
                            <i data-lucide="save" class="w-3.5 h-3.5"></i>
                            <span>Simpan URL</span>
                        </button>
                        <button type="button" onclick="document.getElementById('order_base_url').value = '{{ url('/menu') }}'" class="px-3 py-2 rounded-xl text-xs font-semibold text-cafe-muted hover:text-cafe-dark hover:bg-cafe-beige/40">
                            Reset Default
                        </button>
                    </div>
                </form>
            </div>

            <!-- Panel Pengujian 2 URL Pemesanan (7 Kolom) -->
            <div class="lg:col-span-7 space-y-3 bg-beige/50 p-4 rounded-xl border border-cafe-beige/40">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-[#775533] flex items-center gap-1.5">
                        <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                        <span>Panel Pengujian 2 Alur Pemesanan</span>
                    </span>
                    <span class="text-[10px] text-cafe-muted">Klik untuk tes di tab baru</span>
                </div>

                <!-- URL 1: Scan Meja (Dine-in dengan Nomor Meja) -->
                @php
                    $testTableNumber = $tables->first()?->number ?? 1;
                    $testUrlScan = rtrim($orderBaseUrl, '/') . '?meja=' . $testTableNumber;
                    $testUrlLanding = rtrim($orderBaseUrl, '/');
                @endphp
                <div class="bg-white p-3 rounded-xl border border-cafe-beige/60 space-y-1.5 shadow-2xs">
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold flex items-center justify-center shrink-0">1</span>
                            <span class="text-xs font-bold text-cafe-dark">Alur 1: Scan QR di Meja (Otomatis Meja #{{ $testTableNumber }})</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Meja Terkunci</span>
                    </div>
                    <p class="text-[11px] text-cafe-muted">
                        Simulasi pelanggan memindai QR code fisik di meja kafe. Halaman menu langsung terkoneksi ke nomor meja.
                    </p>
                    <div class="flex items-center gap-2 pt-1">
                        <input type="text" readonly value="{{ $testUrlScan }}" id="test-url-1" class="flex-1 bg-stone-50 text-[11px] font-mono text-cafe-dark p-2 rounded-lg border border-stone-200 truncate select-all">
                        <button type="button" onclick="copyText('{{ $testUrlScan }}', 'URL Scan Meja berhasil disalin!')" class="px-3 py-2 rounded-lg bg-cafe-card hover:bg-cafe-beige text-cafe-dark text-xs font-semibold border border-cafe-beige shrink-0 flex items-center gap-1" title="Salin URL">
                            <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                            <span>Salin</span>
                        </button>
                        <a href="{{ $testUrlScan }}" target="_blank" class="px-3 py-2 rounded-lg bg-cafe-dark hover:bg-cafe-brown text-cafe-cream text-xs font-semibold shrink-0 flex items-center gap-1" title="Buka URL">
                            <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                            <span>Buka Tes</span>
                        </a>
                    </div>
                </div>

                <!-- URL 2: Link dari Landing Page (Online / Pilih Meja / Takeaway) -->
                <div class="bg-white p-3 rounded-xl border border-cafe-beige/60 space-y-1.5 shadow-2xs">
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-bold flex items-center justify-center shrink-0">2</span>
                            <span class="text-xs font-bold text-cafe-dark">Alur 2: Dari Landing Page ("Pesan Sekarang")</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">Bebas Pilih</span>
                    </div>
                    <p class="text-[11px] text-cafe-muted">
                        Simulasi pelanggan membuka menu dari tombol beranda. Pelanggan bisa memilih nomor meja atau memilih pesan bawa pulang (takeaway).
                    </p>
                    <div class="flex items-center gap-2 pt-1">
                        <input type="text" readonly value="{{ $testUrlLanding }}" id="test-url-2" class="flex-1 bg-stone-50 text-[11px] font-mono text-cafe-dark p-2 rounded-lg border border-stone-200 truncate select-all">
                        <button type="button" onclick="copyText('{{ $testUrlLanding }}', 'URL Menu Landing Page berhasil disalin!')" class="px-3 py-2 rounded-lg bg-cafe-card hover:bg-cafe-beige text-cafe-dark text-xs font-semibold border border-cafe-beige shrink-0 flex items-center gap-1" title="Salin URL">
                            <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                            <span>Salin</span>
                        </button>
                        <a href="{{ $testUrlLanding }}" target="_blank" class="px-3 py-2 rounded-lg bg-cafe-dark hover:bg-cafe-brown text-cafe-cream text-xs font-semibold shrink-0 flex items-center gap-1" title="Buka URL">
                            <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                            <span>Buka Tes</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables Grid Cards (2 cols on mobile, 3 on tablet, 5 on desktop) -->
    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4 lg:gap-5">
        @forelse($tables as $table)
            @php
                $tableOrderUrl = rtrim($orderBaseUrl, '/') . '?meja=' . $table->number;
            @endphp
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
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#775533]/15 text-[#775533] border border-[#775533]/30">Tersedia</span>
                            @elseif($table->status === 'terpakai')
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#D4954D]/15 text-[#D4954D] border border-[#D4954D]/30 animate-pulse">Terpakai</span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-cafe-muted/15 text-cafe-muted border border-cafe-beige">Nonaktif</span>
                            @endif
                        </div>
                    </div>

                    <!-- Details -->
                    <div class="space-y-1 mb-3 text-xs">
                        <p class="text-cafe-muted flex items-center gap-1.5">
                            <i data-lucide="users" class="w-3.5 h-3.5 text-cafe-gold"></i>
                            <span>Kapasitas: <strong class="text-cafe-dark">{{ $table->capacity }} Orang</strong></span>
                        </p>
                        <p class="text-cafe-muted flex items-center gap-1.5">
                            <i data-lucide="map-pin" class="w-3.5 h-3.5 text-cafe-gold"></i>
                            <span class="truncate">{{ $table->location_notes ?? 'Area Utama' }}</span>
                        </p>
                    </div>

                    <!-- Direct URL Snippet with Quick Copy -->
                    <div class="bg-beige/70 p-1.5 rounded-lg border border-cafe-beige/60 flex items-center justify-between gap-1 mb-3">
                        <span class="text-[10px] font-mono text-cafe-muted truncate select-all pl-1">
                            ?meja={{ $table->number }}
                        </span>
                        <button type="button" onclick="copyText('{{ $tableOrderUrl }}', 'Link Meja #{{ $table->number }} disalin!')" class="p-1 rounded hover:bg-cafe-beige text-cafe-dark transition-colors" title="Salin link lengkap">
                            <i data-lucide="copy" class="w-3 h-3"></i>
                        </button>
                    </div>
                </div>

                <!-- Footer Action Buttons -->
                <div class="pt-3 border-t border-cafe-beige/50 flex items-center justify-between gap-1">
                    <!-- QR Code Button -->
                    <button onclick="openQrModal({{ $table->number }}, {{ $table->id }})" class="inline-flex items-center gap-1 text-[11px] font-bold text-cafe-brown hover:text-cafe-dark px-2.5 py-1.5 rounded-full bg-cafe-beige/60 hover:bg-cafe-beige transition-colors">
                        <i data-lucide="qr-code" class="w-3.5 h-3.5"></i>
                        <span>QR Code</span>
                    </button>

                    <!-- Edit, Print & Delete -->
                    <div class="flex items-center gap-1">
                        <a href="{{ route('admin.operations.tables.print_single', $table->id) }}" target="_blank" class="p-1.5 rounded-full hover:bg-cafe-beige/60 text-cafe-muted hover:text-cafe-dark transition-colors" title="Cetak Stand Meja Ini">
                            <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                        </a>
                        <button onclick="editTableModal({{ json_encode($table) }})" class="p-1.5 rounded-full hover:bg-cafe-beige/60 text-cafe-brown hover:text-cafe-dark transition-colors" title="Edit Meja">
                            <i data-lucide="edit" class="w-3.5 h-3.5"></i>
                        </button>
                        <form action="{{ route('admin.operations.tables.destroy', $table->id) }}" method="POST" onsubmit="return confirm('Hapus meja #{{ $table->number }}?');" class="inline">
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
