@extends('admin.layouts.app')

@section('title', 'Pengaturan Cafe')
@section('page_title', 'Pengaturan')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-1.5 text-xs text-cafe-muted mb-3">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-cafe-dark flex items-center gap-1">
            <i data-lucide="home" class="w-3.5 h-3.5"></i>
            <span>Admin</span>
        </a>
        <i data-lucide="chevron-right" class="w-3 h-3 text-cafe-muted/60"></i>
        <span class="font-semibold text-cafe-dark">Pengaturan Cafe</span>
    </nav>

    <!-- Page Header -->
    <div>
        <h2 class="font-serif text-2xl font-bold text-cafe-dark">Konfigurasi Sistem &amp; Kafe</h2>
        <p class="text-xs text-cafe-muted mt-0.5">Kelola identitas kafe, metode pembayaran, format struk/nota, pajak, dan akun admin</p>
    </div>

    <!-- Tabs Navigation -->
    <div class="flex items-center gap-2 overflow-x-auto border-b border-cafe-beige/60 pb-2 scrollbar-none text-xs font-semibold">
        <a href="{{ route('admin.settings.index', ['tab' => 'profil']) }}" class="px-4 py-2 rounded-full whitespace-nowrap transition-all {{ $tab === 'profil' ? 'bg-cafe-dark text-cafe-cream shadow-xs' : 'text-cafe-muted hover:text-cafe-dark hover:bg-cafe-beige/50' }}">
            Profil Cafe
        </a>
        <a href="{{ route('admin.settings.index', ['tab' => 'pajak']) }}" class="px-4 py-2 rounded-full whitespace-nowrap transition-all {{ $tab === 'pajak' ? 'bg-cafe-dark text-cafe-cream shadow-xs' : 'text-cafe-muted hover:text-cafe-dark hover:bg-cafe-beige/50' }}">
            Pajak &amp; Biaya
        </a>
        <a href="{{ route('admin.settings.index', ['tab' => 'pembayaran']) }}" class="px-4 py-2 rounded-full whitespace-nowrap transition-all {{ $tab === 'pembayaran' ? 'bg-cafe-dark text-cafe-cream shadow-xs' : 'text-cafe-muted hover:text-cafe-dark hover:bg-cafe-beige/50' }}">
            Metode Pembayaran &amp; QRIS
        </a>
        <a href="{{ route('admin.settings.index', ['tab' => 'nota']) }}" class="px-4 py-2 rounded-full whitespace-nowrap transition-all {{ $tab === 'nota' ? 'bg-cafe-dark text-cafe-cream shadow-xs' : 'text-cafe-muted hover:text-cafe-dark hover:bg-cafe-beige/50' }}">
            Format Nota &amp; Struk
        </a>
        <a href="{{ route('admin.settings.index', ['tab' => 'tampilan']) }}" class="px-4 py-2 rounded-full whitespace-nowrap transition-all {{ $tab === 'tampilan' ? 'bg-cafe-dark text-cafe-cream shadow-xs' : 'text-cafe-muted hover:text-cafe-dark hover:bg-cafe-beige/50' }}">
            Tampilan &amp; Banner
        </a>
        <a href="{{ route('admin.settings.index', ['tab' => 'akun']) }}" class="px-4 py-2 rounded-full whitespace-nowrap transition-all {{ $tab === 'akun' ? 'bg-cafe-dark text-cafe-cream shadow-xs' : 'text-cafe-muted hover:text-cafe-dark hover:bg-cafe-beige/50' }}">
            Akun Saya
        </a>
    </div>

    <!-- TAB 1: PROFIL CAFE -->
    @if($tab === 'profil')
        <div class="bg-cafe-card rounded-2xl p-6 sm:p-8 shadow-soft border border-cafe-beige/40 max-w-3xl">
            <h3 class="font-serif font-bold text-lg text-cafe-dark mb-1">Identitas &amp; Jam Operasional ABS Cafe</h3>
            <p class="text-xs text-cafe-muted mb-6">Informasi ini akan tertera pada struk pembelian, website katalog, dan dokumen resmi.</p>

            <form action="{{ route('admin.settings.profile') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="font-semibold text-cafe-dark">Nama Cafe *</label>
                        <input type="text" name="cafe_name" value="{{ $settings['cafe_name'] }}" required class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                    </div>
                    <div class="space-y-1">
                        <label class="font-semibold text-cafe-dark">Tagline *</label>
                        <input type="text" name="cafe_tagline" value="{{ $settings['cafe_tagline'] }}" required class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="font-semibold text-cafe-dark">Logo Website</label>
                    <div class="flex flex-col gap-3 rounded-xl border border-cafe-beige bg-[#F2F5E2]/60 p-3 sm:flex-row sm:items-center">
                        <x-logo size="sm" :show-text="false" />
                        <input type="text" name="cafe_logo" value="{{ $settings['cafe_logo'] }}" placeholder="images/logo.png" class="w-full bg-white focus:bg-white text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                    </div>
                    <p class="text-[10px] text-cafe-muted">Path lokal atau URL gambar. Default: <span class="font-mono">images/logo.png</span>.</p>
                </div>

                <div class="space-y-1">
                    <label class="font-semibold text-cafe-dark">Alamat Lengkap</label>
                    <textarea name="cafe_address" rows="2" class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">{{ $settings['cafe_address'] }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="font-semibold text-cafe-dark">Nomor Telepon / WhatsApp</label>
                        <input type="text" name="cafe_phone" value="{{ $settings['cafe_phone'] }}" class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                    </div>
                    <div class="space-y-1">
                        <label class="font-semibold text-cafe-dark">Email Kontak</label>
                        <input type="email" name="cafe_email" value="{{ $settings['cafe_email'] }}" class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div class="space-y-1">
                        <label class="font-semibold text-cafe-dark">Jam Buka Hari Kerja (Senin - Kamis)</label>
                        <input type="text" name="hours_weekday" value="{{ $settings['hours_weekday'] }}" placeholder="09:00 - 22:00" class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                    </div>
                    <div class="space-y-1">
                        <label class="font-semibold text-cafe-dark">Jam Buka Akhir Pekan (Jumat - Minggu)</label>
                        <input type="text" name="hours_weekend" value="{{ $settings['hours_weekend'] }}" placeholder="09:00 - 23:30" class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                    </div>
                </div>

                <div class="pt-4 border-t border-cafe-beige/60 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream font-semibold text-xs shadow-md transition-all">Simpan Profil Cafe</button>
                </div>
            </form>
        </div>
    @endif

    <!-- TAB 2: PAJAK & BIAYA -->
    @if($tab === 'pajak')
        <div class="bg-cafe-card rounded-2xl p-6 sm:p-8 shadow-soft border border-cafe-beige/40 max-w-2xl">
            <h3 class="font-serif font-bold text-lg text-cafe-dark mb-1">Pengaturan Pajak Daerah &amp; Layanan</h3>
            <p class="text-xs text-cafe-muted mb-6">Atur persentase PB1 resto dan biaya pelayanan kafe.</p>

            <form action="{{ route('admin.settings.tax') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div class="space-y-1">
                    <label class="font-semibold text-cafe-dark">Pajak Restoran / PB1 (%)</label>
                    <div class="relative w-48">
                        <input type="number" name="tax_percent" value="{{ $settings['tax_percent'] }}" min="0" max="100" class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs p-3 pr-8 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 font-bold text-cafe-muted">%</span>
                    </div>
                    <p class="text-[10px] text-cafe-muted">Standar pajak restoran daerah Kabupaten Tulungagung adalah 10%.</p>
                </div>

                <div class="pt-2 border-t border-cafe-beige/40 space-y-3">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" name="service_charge_active" value="1" {{ $settings['service_charge_active'] == '1' ? 'checked' : '' }} class="w-4 h-4 rounded text-cafe-dark accent-[#290024]">
                        <span class="font-semibold text-cafe-dark">Aktifkan Service Charge (Biaya Layanan)</span>
                    </label>

                    <div class="space-y-1 w-48 pl-6">
                        <label class="font-semibold text-cafe-muted">Persentase Service Charge (%)</label>
                        <div class="relative">
                            <input type="number" name="service_charge_percent" value="{{ $settings['service_charge_percent'] }}" min="0" max="100" class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs p-3 pr-8 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 font-bold text-cafe-muted">%</span>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-cafe-beige/60 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream font-semibold text-xs shadow-md transition-all">Simpan Pengaturan Pajak</button>
                </div>
            </form>
        </div>
    @endif

    <!-- TAB 3: PEMBAYARAN & QRIS -->
    @if($tab === 'pembayaran')
        <div class="bg-cafe-card rounded-2xl p-6 sm:p-8 shadow-soft border border-cafe-beige/40 max-w-2xl">
            <h3 class="font-serif font-bold text-lg text-cafe-dark mb-1">Metode Pembayaran Kasir &amp; Gambar QRIS</h3>
            <p class="text-xs text-cafe-muted mb-6">Pilih opsi transaksi yang diaktifkan di POS kasir dan URL gambar QRIS dinamis.</p>

            <form action="{{ route('admin.settings.payment') }}" method="POST" class="space-y-5 text-xs">
                @csrf
                <div class="space-y-3">
                    <label class="font-semibold text-cafe-dark block">Opsi Pembayaran Aktif:</label>

                    <label class="flex items-center gap-3 p-3 rounded-xl bg-cafe-cream/60 border border-cafe-beige cursor-pointer select-none">
                        <input type="checkbox" name="payment_cash" value="1" {{ $settings['payment_cash'] == '1' ? 'checked' : '' }} class="w-4 h-4 rounded text-cafe-dark accent-[#290024]">
                        <div>
                            <span class="font-semibold text-cafe-dark block">Uang Tunai (Cash)</span>
                            <span class="text-[10px] text-cafe-muted">Menerima pembayaran tunai langsung di kasir</span>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-3 rounded-xl bg-cafe-cream/60 border border-cafe-beige cursor-pointer select-none">
                        <input type="checkbox" name="payment_qris" value="1" {{ $settings['payment_qris'] == '1' ? 'checked' : '' }} class="w-4 h-4 rounded text-cafe-dark accent-[#290024]">
                        <div>
                            <span class="font-semibold text-cafe-dark block">QRIS (Gopay, OVO, ShopeePay, BCA, Mandiri)</span>
                            <span class="text-[10px] text-cafe-muted">Scan QR instan dari meja pelanggan atau kasir</span>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-3 rounded-xl bg-cafe-cream/60 border border-cafe-beige cursor-pointer select-none">
                        <input type="checkbox" name="payment_debit" value="1" {{ $settings['payment_debit'] == '1' ? 'checked' : '' }} class="w-4 h-4 rounded text-cafe-dark accent-[#290024]">
                        <div>
                            <span class="font-semibold text-cafe-dark block">Kartu Debit / EDC</span>
                            <span class="text-[10px] text-cafe-muted">Gesek kartu debit perbankan di mesin EDC kasir</span>
                        </div>
                    </label>
                </div>

                <div class="space-y-2 pt-2 border-t border-cafe-beige/40">
                    <label class="font-semibold text-cafe-dark">URL Gambar Barcode QRIS Resmi</label>
                    <input type="url" name="qris_image" value="{{ $settings['qris_image'] }}" class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                    <div class="p-3 bg-white rounded-xl border border-cafe-beige/60 inline-block">
                        <img src="{{ $settings['qris_image'] }}" alt="QRIS ABS Cafe" class="w-36 h-36 mx-auto">
                    </div>
                </div>

                <div class="pt-4 border-t border-cafe-beige/60 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream font-semibold text-xs shadow-md transition-all">Simpan Metode Pembayaran</button>
                </div>
            </form>
        </div>
    @endif

    <!-- TAB 4: FORMAT NOTA & PRATINJAU LANGSUNG -->
    @if($tab === 'nota')
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Form Header & Footer Nota -->
            <div class="bg-cafe-card rounded-2xl p-6 sm:p-8 shadow-soft border border-cafe-beige/40">
                <h3 class="font-serif font-bold text-lg text-cafe-dark mb-1">Format Teks Nota &amp; Struk</h3>
                <p class="text-xs text-cafe-muted mb-6">Teks ini akan dicetak otomatis pada thermal receipt printer kasir.</p>

                <form action="{{ route('admin.settings.receipt') }}" method="POST" class="space-y-4 text-xs">
                    @csrf
                    <div class="space-y-1">
                        <label class="font-semibold text-cafe-dark">Header Nota (Bagian Atas)</label>
                        <textarea name="receipt_header" id="receipt-header-input" rows="3" oninput="updateReceiptLivePreview()" class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none font-mono">{{ $settings['receipt_header'] }}</textarea>
                    </div>

                    <div class="space-y-1">
                        <label class="font-semibold text-cafe-dark">Footer Nota (Bagian Bawah / Ucapan)</label>
                        <textarea name="receipt_footer" id="receipt-footer-input" rows="3" oninput="updateReceiptLivePreview()" class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none font-mono">{{ $settings['receipt_footer'] }}</textarea>
                    </div>

                    <div class="pt-2">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" name="receipt_show_logo" value="1" {{ $settings['receipt_show_logo'] == '1' ? 'checked' : '' }} class="w-4 h-4 rounded text-cafe-dark accent-[#290024]">
                            <span class="font-semibold text-cafe-dark">Tampilkan Logo ABS Cafe di Struk</span>
                        </label>
                    </div>

                    <div class="pt-4 border-t border-cafe-beige/60 flex justify-end">
                        <button type="submit" class="px-6 py-2.5 rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream font-semibold text-xs shadow-md transition-all">Simpan Format Struk</button>
                    </div>
                </form>
            </div>

            <!-- Pratinjau Langsung (Live Preview) Struk Thermal -->
            <div class="bg-cafe-card rounded-2xl p-6 sm:p-8 shadow-soft border border-cafe-beige/40 flex flex-col items-center">
                <span class="text-xs font-semibold text-cafe-muted uppercase tracking-wider mb-3">Pratinjau Langsung Struk Kasir</span>

                <div class="w-72 bg-white p-5 rounded-2xl border border-cafe-beige/80 shadow-md font-mono text-[11px] leading-tight text-black space-y-3">
                    <div class="text-center pb-2 border-b border-dashed border-gray-400">
                        <x-logo :show-text="false" size="md" thermal loading="eager" class="mx-auto mb-2" />
                        <h4 class="font-bold text-xs uppercase">{{ $settings['cafe_name'] }}</h4>
                        <p class="text-[9px]">{{ $settings['cafe_tagline'] }}</p>
                        <p class="text-[9px] mt-1 whitespace-pre-line text-gray-700" id="preview-receipt-header">{{ $settings['receipt_header'] }}</p>
                    </div>

                    <div class="py-1 border-b border-dashed border-gray-400 space-y-0.5 text-[10px]">
                        <div class="flex justify-between"><span>No: #AB261001</span><span>Meja 4</span></div>
                        <div class="flex justify-between"><span>Tgl: {{ date('d/m/Y H:i') }}</span><span>Kasir: Siti</span></div>
                        <div>Customer: Rian Pratama</div>
                    </div>

                    <div class="py-1 border-b border-dashed border-gray-400 space-y-1">
                        <div class="flex justify-between">
                            <div><span>Caramel Latte</span><span class="block text-[9px] text-gray-500">2 x 24.000</span></div>
                            <span>48.000</span>
                        </div>
                        <div class="flex justify-between">
                            <div><span>Butter Croissant</span><span class="block text-[9px] text-gray-500">1 x 18.000</span></div>
                            <span>18.000</span>
                        </div>
                    </div>

                    <div class="py-1 border-b border-dashed border-gray-400 space-y-0.5 text-[10px]">
                        <div class="flex justify-between"><span>Subtotal:</span><span>66.000</span></div>
                        <div class="flex justify-between"><span>Pajak (10%):</span><span>6.600</span></div>
                        <div class="flex justify-between font-bold text-xs pt-1 border-t border-black"><span>TOTAL:</span><span>72.600</span></div>
                        <div class="flex justify-between text-[9px] pt-1"><span>Metode: QRIS</span><span>Status: LUNAS</span></div>
                    </div>

                    <div class="text-center pt-1 text-[9px] whitespace-pre-line text-gray-700" id="preview-receipt-footer">
                        {{ $settings['receipt_footer'] }}
                    </div>
                </div>
            </div>
        </div>

        <script>
            function updateReceiptLivePreview() {
                document.getElementById('preview-receipt-header').innerText = document.getElementById('receipt-header-input').value;
                document.getElementById('preview-receipt-footer').innerText = document.getElementById('receipt-footer-input').value;
            }
        </script>
    @endif

    <!-- TAB 5: TAMPILAN & BANNER -->
    @if($tab === 'tampilan')
        <div class="bg-cafe-card rounded-2xl p-6 sm:p-8 shadow-soft border border-cafe-beige/40 max-w-2xl">
            <h3 class="font-serif font-bold text-lg text-cafe-dark mb-1">Pengaturan Tampilan &amp; Headline Beranda</h3>
            <p class="text-xs text-cafe-muted mb-6">Kelola teks sambutan beranda pelanggan (pengaturan saja).</p>

            <form action="{{ route('admin.settings.display') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div class="space-y-1">
                    <label class="font-semibold text-cafe-dark">Judul Banner Utama (Heading)</label>
                    <input type="text" name="banner_title" value="{{ $settings['banner_title'] }}" required class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                </div>

                <div class="space-y-1">
                    <label class="font-semibold text-cafe-dark">Subjudul / Deskripsi Banner</label>
                    <textarea name="banner_subtitle" rows="2" class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">{{ $settings['banner_subtitle'] }}</textarea>
                </div>

                <div class="pt-4 border-t border-cafe-beige/60 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream font-semibold text-xs shadow-md transition-all">Simpan Tampilan</button>
                </div>
            </form>
        </div>
    @endif

    <!-- TAB 6: AKUN SAYA -->
    @if($tab === 'akun')
        <div class="bg-cafe-card rounded-2xl p-6 sm:p-8 shadow-soft border border-cafe-beige/40 max-w-2xl">
            <h3 class="font-serif font-bold text-lg text-cafe-dark mb-1">Akun Administrator Saya</h3>
            <p class="text-xs text-cafe-muted mb-6">Ubah nama, username login, dan perbarui kata sandi akun administrator.</p>

            <form action="{{ route('admin.settings.account') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="font-semibold text-cafe-dark">Nama Admin *</label>
                        <input type="text" name="name" value="{{ Auth::user()->name }}" required class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                    </div>
                    <div class="space-y-1">
                        <label class="font-semibold text-cafe-dark">Username Admin *</label>
                        <input type="text" name="username" value="{{ Auth::user()->username }}" required class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                    </div>
                </div>

                <div class="pt-2 border-t border-cafe-beige/40 space-y-4">
                    <h4 class="font-semibold text-cafe-dark">Ganti Kata Sandi (Opsional)</h4>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="font-semibold text-cafe-muted">Password Baru</label>
                            <input type="password" name="password" placeholder="Minimal 6 karakter" class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                        </div>
                        <div class="space-y-1">
                            <label class="font-semibold text-cafe-muted">Ulangi Password Baru</label>
                            <input type="password" name="password_confirmation" placeholder="Ulangi password baru" class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-cafe-beige/60 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream font-semibold text-xs shadow-md transition-all">Simpan Akun Saya</button>
                </div>
            </form>
        </div>
    @endif
</div>
@endsection
