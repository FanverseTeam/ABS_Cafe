@extends('admin.layouts.app')

@section('title', 'Pengaturan Website')
@section('page_title', 'Pengaturan Website')

@section('content')
<div class="space-y-4 lg:space-y-6">
    <!-- Breadcrumb (Desktop only) -->
    <div class="hidden lg:block space-y-4">
        <x-breadcrumb
            :items="[
                ['label' => 'Admin', 'url' => route('admin.website.index'), 'icon' => 'home'],
                ['label' => 'Pengaturan Website'],
            ]"
        />

        <div>
            <h2 class="font-serif text-2xl font-bold text-cafe-dark">Pengaturan Website &amp; Operasional</h2>
            <p class="text-xs text-cafe-muted mt-0.5">Kelola identitas kafe, biaya layanan, dan akun admin</p>
        </div>
    </div>

    {{-- Tabs Navigation for Mobile/Tablet: sticky chip scroller --}}
    <div class="lg:hidden sticky top-16 z-20 bg-[#F2F5E2] pt-2 pb-2 -mx-3.5 px-3.5 sm:-mx-5 sm:px-5">
        @php
            $tabOptions = [
                ['label' => 'Profil', 'val' => 'profil'],
                ['label' => 'Landing Page', 'val' => 'landing'],
                ['label' => 'URL QR Meja', 'val' => 'qr_meja'],
            ];
            $currentWebTab = $tab ?? 'profil';
            $webChips = array_map(function($opt) use ($currentWebTab) {
                return [
                    'label'  => $opt['label'],
                    'href'   => route('admin.website.index', ['tab' => $opt['val']]),
                    'active' => $currentWebTab === $opt['val'],
                ];
            }, $tabOptions);
        @endphp
        <x-mobile.chip-scroller :chips="$webChips" />
    </div>

    <!-- Tabs Navigation (Desktop only) -->
    <div class="hidden lg:flex items-center gap-2 overflow-x-auto border-b border-cafe-beige/60 pb-2 scrollbar-none text-xs font-semibold">
        <a href="{{ route('admin.website.index', ['tab' => 'profil']) }}" class="px-4 py-2 rounded-full whitespace-nowrap transition-all {{ $tab === 'profil' ? 'bg-cafe-dark text-cafe-cream shadow-xs' : 'text-cafe-muted hover:text-cafe-dark hover:bg-cafe-beige/50' }}">
            Profil Cafe
        </a>
        <a href="{{ route('admin.website.index', ['tab' => 'landing']) }}" class="px-4 py-2 rounded-full whitespace-nowrap transition-all {{ $tab === 'landing' ? 'bg-cafe-dark text-cafe-cream shadow-xs' : 'text-cafe-muted hover:text-cafe-dark hover:bg-cafe-beige/50' }}">
            Landing Page
        </a>
        <a href="{{ route('admin.website.index', ['tab' => 'qr_meja']) }}" class="px-4 py-2 rounded-full whitespace-nowrap transition-all {{ $tab === 'qr_meja' ? 'bg-cafe-dark text-cafe-cream shadow-xs' : 'text-cafe-muted hover:text-cafe-dark hover:bg-cafe-beige/50' }}">
            URL QR Meja
        </a>
    </div>

    <!-- TAB 1: PROFIL CAFE -->
    @if($tab === 'profil')
        <div class="bg-cafe-card rounded-2xl p-6 sm:p-8 shadow-soft border border-cafe-beige/40 max-w-3xl">
            <h3 class="font-serif font-bold text-lg text-cafe-dark mb-1">Identitas &amp; Jam Operasional ABS Cafe</h3>
            <p class="text-xs text-cafe-muted mb-6">Informasi ini akan tertera pada struk pembelian, website katalog, dan dokumen resmi.</p>

            <form action="{{ route('admin.website.profile') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="font-semibold text-cafe-dark">Nama Cafe *</label>
                        <input type="text" name="cafe_name" value="{{ $settings['cafe_name'] }}" required class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                    </div>
                    <div class="space-y-1">
                        <label class="font-semibold text-cafe-dark">Tagline *</label>
                        <input type="text" name="cafe_tagline" value="{{ $settings['cafe_tagline'] }}" required class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="font-semibold text-cafe-dark">Upload Logo Website dari Galeri</label>
                    <div class="flex flex-col gap-3 rounded-xl border border-cafe-beige bg-[#F2F5E2]/60 p-3 sm:flex-row sm:items-center">
                        <div id="website-logo-preview" class="shrink-0 flex items-center justify-center">
                            <x-logo size="sm" :show-text="false" />
                        </div>
                        <div class="flex-1 space-y-1">
                            <input type="file" name="cafe_logo" id="cafe_logo_input" accept="image/*,.heic,.heif" onchange="previewWebsiteLogo(this)" class="w-full bg-beige focus:bg-beige text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none cursor-pointer file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-cafe-dark file:text-cafe-cream">
                            <p class="text-[10px] text-cafe-muted">Pilih gambar logo dari galeri (JPG, PNG, WebP, HEIC, SVG). Logo di seluruh website dan header akan otomatis diperbarui.</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="font-semibold text-cafe-dark">Alamat Lengkap</label>
                    <textarea name="cafe_address" rows="2" class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">{{ $settings['cafe_address'] }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="font-semibold text-cafe-dark">Nomor Telepon / WhatsApp</label>
                        <input type="text" name="cafe_phone" value="{{ $settings['cafe_phone'] }}" class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                    </div>
                    <div class="space-y-1">
                        <label class="font-semibold text-cafe-dark">Email Kontak</label>
                        <input type="email" name="cafe_email" value="{{ $settings['cafe_email'] }}" class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                    </div>
                </div>

                <div class="space-y-1 pt-1">
                    <label class="font-semibold text-cafe-dark">Tautan Google Maps</label>
                    <input type="url" name="cafe_maps_url" value="{{ $settings['cafe_maps_url'] ?? '' }}" placeholder="https://maps.app.goo.gl/..." class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                    <p class="text-[10px] text-cafe-muted">Tautan yang dibuka ketika pengunjung mengklik tombol "Lihat di Google Maps".</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                    <div class="space-y-1">
                        <label class="font-semibold text-cafe-dark">Koordinat Latitude (Peta &amp; Schema)</label>
                        <input type="text" name="peta_lat" value="{{ $settings['peta_lat'] ?? '-8.0646338' }}" placeholder="-8.0646338" class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                        @error('peta_lat')<p class="text-[10px] text-midnight mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="space-y-1">
                        <label class="font-semibold text-cafe-dark">Koordinat Longitude (Peta &amp; Schema)</label>
                        <input type="text" name="peta_lng" value="{{ $settings['peta_lng'] ?? '111.9111798' }}" placeholder="111.9111798" class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                        @error('peta_lng')<p class="text-[10px] text-midnight mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                <p class="text-[10px] text-cafe-muted mt-1">Ambil koordinat dari tautan Google Maps bagian !3d dan !4d, bukan angka setelah tanda @ (itu titik tengah peta).</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div class="space-y-1">
                        <label class="font-semibold text-cafe-dark">Jam Buka Hari Kerja (Senin - Kamis)</label>
                        <input type="text" name="hours_weekday" value="{{ $settings['hours_weekday'] }}" placeholder="09:00 - 23:00" class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                    </div>
                    <div class="space-y-1">
                        <label class="font-semibold text-cafe-dark">Jam Buka Akhir Pekan (Jumat - Minggu)</label>
                        <input type="text" name="hours_weekend" value="{{ $settings['hours_weekend'] }}" placeholder="09:00 - 23:00" class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                    <div class="space-y-1">
                        <label class="font-semibold text-cafe-dark">Teks Jam Buka Beranda</label>
                        <input type="text" name="cafe_hours" value="{{ $settings['cafe_hours'] ?? '' }}" placeholder="Buka Setiap Hari 09.00 - 23.00 WIB" class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                    </div>
                    <div class="space-y-1">
                        <label class="font-semibold text-cafe-dark">Batas Order Terakhir (Last Order)</label>
                        <input type="text" name="last_order" value="{{ $settings['last_order'] ?? '' }}" placeholder="22.00 WIB" class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                    </div>
                </div>

                <div class="pt-3 border-t border-cafe-beige/40">
                    <p class="text-xs font-bold text-cafe-dark mb-3 uppercase tracking-wide">WhatsApp</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="font-semibold text-cafe-dark">Nomor WhatsApp</label>
                            <input type="text" name="whatsapp_nomor" id="whatsapp_nomor"
                                   value="{{ $settings['whatsapp_nomor'] ?? '' }}"
                                   placeholder="6289620102010 atau 08962010..."
                                   class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                            @error('whatsapp_nomor')<p class="text-[10px] text-midnight mt-1">{{ $message }}</p>@enderror
                            <p class="text-[10px] text-cafe-muted">Format: 08..., 628..., atau +628... Dikosongkan untuk menyembunyikan tombol WhatsApp.</p>
                        </div>
                        <div class="space-y-1">
                            <label class="font-semibold text-cafe-dark">Pesan Awal Chat</label>
                            <input type="text" name="whatsapp_pesan" id="whatsapp_pesan"
                                   value="{{ $settings['whatsapp_pesan'] ?? '' }}"
                                   placeholder="Halo ABS Cafe, saya ingin bertanya."
                                   maxlength="200"
                                   class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                            @error('whatsapp_pesan')<p class="text-[10px] text-midnight mt-1">{{ $message }}</p>@enderror
                            <p class="text-[10px] text-cafe-muted">Teks yang otomatis muncul saat pengunjung membuka chat. Maks 200 karakter.</p>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-cafe-beige/60 flex justify-end">
                    <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-3 sm:py-2.5 rounded-xl sm:rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream font-semibold text-xs sm:text-sm shadow-md transition-all active:scale-95 cursor-pointer">
                        <i data-lucide="check" class="w-4 h-4 text-cafe-gold"></i>
                        <span>Simpan Profil Cafe</span>
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- TAB: URL QR MEJA -->
    @if($tab === 'qr_meja')
        <div class="space-y-6 max-w-5xl">
            <!-- Header Kartu Info & Shortcut ke Denah Meja -->
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
                    <div class="flex items-center gap-2 self-start md:self-auto">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold bg-[#775533]/10 text-[#775533] border border-[#775533]/20">
                            {{ $tables->count() }} Meja Terdaftar
                        </span>
                        <a href="{{ route('admin.operations.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-cafe-dark hover:bg-cafe-brown text-cafe-cream shadow-xs transition-all">
                            <i data-lucide="grid-3x3" class="w-3.5 h-3.5"></i>
                            <span>Buka Kelola Meja</span>
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
                    <!-- Form Setting Base URL (5 Kolom) -->
                    <div class="lg:col-span-5 space-y-3">
                        <form action="{{ route('admin.website.qr_setting') }}" method="POST" class="space-y-3">
                            @csrf
                            <div>
                                <label for="order_base_url" class="block font-semibold text-cafe-dark text-xs mb-1">
                                    Link Hostingan / Tunnel (Cloudflare, Ngrok, dll.)
                                </label>
                                <div class="relative">
                                    <input type="text"
                                           name="order_base_url"
                                           id="order_base_url"
                                           value="{{ $hostingUrl ?? preg_replace('~/menu$~i', '', rtrim($orderBaseUrl, '/')) }}"
                                           placeholder="{{ url('/') }}"
                                           class="w-full bg-[#E3DEA4]/30 focus:bg-white text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none font-mono text-cafe-dark">
                                </div>
                                <p class="text-[11px] text-cafe-muted mt-1 leading-relaxed">
                                    Cukup masukkan link hostingan saja (tanpa <code class="bg-beige px-1 rounded text-cafe-dark">/...</code> di belakang). Contoh: <code class="bg-beige px-1 rounded text-cafe-dark">https://xxxx.trycloudflare.com</code> atau <code class="bg-beige px-1 rounded text-cafe-dark">https://xxxx.ngrok-free.app</code>. Sistem akan otomatis menambahkan rute menu dan parameter per meja.
                                </p>
                            </div>

                            <div class="flex items-center gap-2 pt-1">
                                <button type="submit" class="px-4 py-2 rounded-xl bg-cafe-dark hover:bg-cafe-brown text-cafe-cream font-semibold text-xs shadow-sm transition-all flex items-center gap-1.5 cursor-pointer">
                                    <i data-lucide="save" class="w-3.5 h-3.5"></i>
                                    <span>Simpan Link Hostingan</span>
                                </button>
                                <button type="button" onclick="document.getElementById('order_base_url').value = '{{ url('/') }}'" class="px-3 py-2 rounded-xl text-xs font-semibold text-cafe-muted hover:text-cafe-dark hover:bg-cafe-beige/40 cursor-pointer">
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
                            $testUrlLanding = $hostingUrl ?? preg_replace('~/menu$~i', '', rtrim($orderBaseUrl, '/'));
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
                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 pt-1">
                                <input type="text" readonly value="{{ $testUrlScan }}" id="test-url-1" class="w-full sm:flex-1 bg-stone-50 text-[11px] font-mono text-cafe-dark p-2 rounded-lg border border-stone-200 truncate select-all">
                                <div class="flex items-center gap-2 shrink-0">
                                    <button type="button" onclick="copyWebText('{{ $testUrlScan }}', 'URL Scan Meja berhasil disalin!')" class="flex-1 sm:flex-initial px-3 py-2 rounded-lg bg-cafe-card hover:bg-cafe-beige text-cafe-dark text-xs font-semibold border border-cafe-beige flex items-center justify-center gap-1 cursor-pointer" title="Salin URL">
                                        <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                                        <span>Salin</span>
                                    </button>
                                    <a href="{{ $testUrlScan }}" target="_blank" class="flex-1 sm:flex-initial px-3 py-2 rounded-lg bg-cafe-dark hover:bg-cafe-brown text-cafe-cream text-xs font-semibold flex items-center justify-center gap-1" title="Buka URL">
                                        <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                                        <span>Buka Tes</span>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- URL 2: Link dari Landing Page (Online / Pilih Meja / Takeaway) -->
                        <div class="bg-white p-3 rounded-xl border border-cafe-beige/60 space-y-1.5 shadow-2xs">
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2">
                                    <span class="w-5 h-5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-bold flex items-center justify-center shrink-0">2</span>
                                    <span class="text-xs font-bold text-cafe-dark">Alur 2: Dari Landing Page (&quot;Pesan Sekarang&quot;)</span>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">Bebas Pilih</span>
                            </div>
                            <p class="text-[11px] text-cafe-muted">
                                Simulasi pelanggan membuka menu dari tombol beranda. Pelanggan bisa memilih nomor meja atau memilih pesan bawa pulang (takeaway).
                            </p>
                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 pt-1">
                                <input type="text" readonly value="{{ $testUrlLanding }}" id="test-url-2" class="w-full sm:flex-1 bg-stone-50 text-[11px] font-mono text-cafe-dark p-2 rounded-lg border border-stone-200 truncate select-all">
                                <div class="flex items-center gap-2 shrink-0">
                                    <button type="button" onclick="copyWebText('{{ $testUrlLanding }}', 'URL Menu Landing Page berhasil disalin!')" class="flex-1 sm:flex-initial px-3 py-2 rounded-lg bg-cafe-card hover:bg-cafe-beige text-cafe-dark text-xs font-semibold border border-cafe-beige flex items-center justify-center gap-1 cursor-pointer" title="Salin URL">
                                        <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                                        <span>Salin</span>
                                    </button>
                                    <a href="{{ $testUrlLanding }}" target="_blank" class="flex-1 sm:flex-initial px-3 py-2 rounded-lg bg-cafe-dark hover:bg-cafe-brown text-cafe-cream text-xs font-semibold flex items-center justify-center gap-1" title="Buka URL">
                                        <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                                        <span>Buka Tes</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- TAB 4: LANDING PAGE -->
    @if($tab === 'landing')
        <div class="space-y-6 max-w-5xl">
            <!-- Header Kartu Info & Shortcut Lihat Landing -->
            <div class="bg-cafe-card rounded-2xl p-5 sm:p-6 shadow-soft border border-cafe-beige/40 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h3 class="font-serif font-bold text-lg text-cafe-dark">Kustomisasi Halaman Landing Page Pelanggan</h3>
                    <p class="text-xs text-cafe-muted mt-1">Ubah foto galeri, judul hero, deskripsi tentang kafe, dan teks keunggulan yang tampil di halaman beranda pelanggan.</p>
                </div>
                <div class="shrink-0 w-full sm:w-auto">
                    <a href="{{ route('pelanggan.beranda') }}" target="_blank" rel="noopener noreferrer" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 sm:py-2.5 rounded-xl sm:rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream text-xs font-semibold shadow-xs transition-all active:scale-95">
                        <span>Lihat Landing Page</span>
                        <i data-lucide="external-link" class="w-3.5 h-3.5 text-cafe-gold"></i>
                    </a>
                </div>
            </div>

            <!-- BAGIAN 1: KELOLA FOTO LANDING PAGE -->
            <div class="bg-cafe-card rounded-2xl p-6 sm:p-8 shadow-soft border border-cafe-beige/40">
                <div class="flex items-center justify-between border-b border-cafe-beige/40 pb-4 mb-6">
                    <div>
                        <h4 class="font-serif font-bold text-base text-cafe-dark">1. Foto &amp; Media Landing Page</h4>
                        <p class="text-xs text-cafe-muted mt-0.5">Format file yang didukung: JPG, PNG, atau WebP (maks. 5 MB per foto). Foto langsung diperbarui di halaman publik.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @php
                        $photoSlotDefinitions = [
                            'hero' => [
                                'title' => 'Foto Latar Hero (Header)',
                                'desc' => 'Tampil sebagai gambar latar belakang bagian atas halaman utama.',
                                'ratio' => 'aspect-[16/9]',
                            ],
                            'hero_cup' => [
                                'title' => 'Foto Cangkir Kopi Hero',
                                'desc' => 'Tampil di kotak sorotan cangkir kopi sisi kanan hero section.',
                                'ratio' => 'aspect-square max-w-[240px]',
                            ],
                            'tentang' => [
                                'title' => 'Foto Tentang Kami',
                                'desc' => 'Tampil dalam bingkai organik melengkung di section Tentang Kami.',
                                'ratio' => 'aspect-[4/3]',
                            ],
                            'suasana' => [
                                'title' => 'Foto Suasana Kafe (Interior)',
                                'desc' => 'Tampil sebagai banner lebar pemandangan suasana kafe.',
                                'ratio' => 'aspect-[16/9]',
                            ],
                        ];
                    @endphp

                    @foreach($photoSlotDefinitions as $slotKey => $def)
                        @php
                            $slotPhoto = $landingPhotos[$slotKey] ?? null;
                        @endphp
                        <div class="rounded-2xl border border-cafe-beige/50 bg-[#F2F5E2]/40 p-5 flex flex-col justify-between space-y-4">
                            <div>
                                <div class="flex items-start justify-between gap-2 mb-2">
                                    <h5 class="font-bold text-xs text-cafe-dark">{{ $def['title'] }}</h5>
                                    @if(!empty($slotPhoto['is_local']))
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            Foto Custom
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                                            Foto Default
                                        </span>
                                    @endif
                                </div>
                                <p class="text-[11px] text-cafe-muted mb-3">{{ $def['desc'] }}</p>

                                <!-- Pratinjau Foto -->
                                <div class="relative w-full {{ $def['ratio'] }} mx-auto rounded-xl overflow-hidden border border-cafe-beige bg-[#290024] shadow-inner flex items-center justify-center">
                                    @if(!empty($slotPhoto['src']))
                                        <img src="{{ $slotPhoto['src'] }}"
                                             alt="{{ $def['title'] }}"
                                             class="w-full h-full object-cover"
                                             onerror="this.style.display='none'">
                                    @else
                                        <span class="text-[11px] text-cafe-cream/60">Belum ada foto</span>
                                    @endif
                                </div>

                                <div class="mt-2 text-[10px] text-cafe-muted">
                                    Rekomendasi ukuran: <span class="font-medium text-cafe-dark">{{ $slotPhoto['suggested_size'] ?? '1200x800' }}</span>
                                </div>
                            </div>

                            <!-- Form Upload & Tombol Reset -->
                            <div class="pt-3 border-t border-cafe-beige/40 space-y-2">
                                <form action="{{ route('admin.website.landing.photo') }}" method="POST" enctype="multipart/form-data" class="space-y-2">
                                    @csrf
                                    <input type="hidden" name="slot" value="{{ $slotKey }}">
                                    <label class="block text-[11px] font-semibold text-cafe-dark">Ganti Foto:</label>
                                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                                        <input type="file"
                                               name="photo"
                                               accept="image/*,.heic,.heif,.HEIC,.HEIF,image/heic,image/heif"
                                               required
                                               class="block w-full text-[11px] text-cafe-muted file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[11px] file:font-semibold file:bg-cafe-dark file:text-cafe-cream hover:file:bg-cafe-brown cursor-pointer bg-beige/40 rounded-xl border border-cafe-beige p-1">
                                        <button type="submit" class="w-full sm:w-auto shrink-0 inline-flex items-center justify-center gap-1.5 px-4 py-2 sm:py-1.5 rounded-xl bg-[#D4954D] hover:bg-[#c2843e] text-[#290024] font-bold text-[11px] transition-all active:scale-95 shadow-xs cursor-pointer">
                                            <i data-lucide="upload" class="w-3.5 h-3.5"></i>
                                            <span>Upload</span>
                                        </button>
                                    </div>
                                </form>

                                @if(!empty($slotPhoto['is_local']))
                                    <form action="{{ route('admin.website.landing.photo.delete', $slotKey) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus foto custom ini dan kembali ke foto bawaan?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center sm:justify-start gap-1 py-1.5 text-[11px] text-rose-700 hover:text-rose-900 font-medium transition-colors">
                                            <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                                            <span class="underline">Reset ke Foto Default</span>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- BAGIAN 1b: KELOLA FOTO GALERI CAROUSEL (MOMENTS WORTH SHARING) -->
            <div class="bg-cafe-card rounded-2xl p-6 sm:p-8 shadow-soft border border-cafe-beige/40">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-cafe-beige/40 pb-4 mb-6">
                    <div>
                        <div class="flex items-center gap-2">
                            <h4 class="font-serif font-bold text-base text-cafe-dark">Galeri Foto Carousel (Moments Worth Sharing)</h4>
                            @if(!empty($isCustomGallery))
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    Custom ({{ count($galleryItems) }} Foto)
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                                    Bawaan Sistem ({{ count($galleryItems) }} Foto)
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-cafe-muted mt-0.5">Foto-foto ini tampil dalam carousel geser kiri-kanan (touch scroll / swipe) di landing page. Anda dapat menambah, mengubah judul, atau menghapus foto.</p>
                    </div>

                    @if(!empty($isCustomGallery))
                        <form action="{{ route('admin.website.landing.gallery.reset') }}" method="POST" onsubmit="return confirm('Kembalikan galeri foto carousel ke default bawaan sistem?');" class="shrink-0">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-rose-50 border border-rose-200 text-rose-700 hover:bg-rose-100 text-[11px] font-semibold transition-colors">
                                <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                                <span>Reset ke Default Bawaan</span>
                            </button>
                        </form>
                    @endif
                </div>

                <!-- Form Tambah Foto Baru ke Galeri Carousel -->
                <div class="mb-8 p-5 rounded-2xl border-2 border-dashed border-[#D4954D]/50 bg-[#E3DEA4]/20">
                    <h5 class="font-bold text-xs text-cafe-dark mb-1 flex items-center gap-1.5">
                        <i data-lucide="plus-circle" class="w-4 h-4 text-cafe-gold"></i>
                        <span>Tambah Foto Baru ke Galeri Carousel</span>
                    </h5>
                    <p class="text-[11px] text-cafe-muted mb-4">Upload foto (rekomendasi rasio 4:5 atau 960x1200 px). Semua format didukung (JPG, PNG, WebP, HEIC, dll. Maks 50 MB).</p>

                    <form id="gallery-add-form" action="{{ route('admin.website.landing.gallery.add') }}" method="POST" enctype="multipart/form-data"
                          onsubmit="const btn = this.querySelector('button[type=submit]'); btn.disabled=true; btn.classList.add('opacity-75'); btn.innerHTML='<span>Mengunggah...</span>';">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                            <div class="sm:col-span-5 space-y-1">
                                <label class="block text-[11px] font-semibold text-cafe-dark">Pilih Berkas Foto</label>
                                <input type="file" name="photo" id="gallery-add-input" accept="image/*,.heic,.heif,.HEIC,.HEIF,image/heic,image/heif" required
                                       onchange="previewGalleryAdd(this)"
                                       class="w-full text-xs text-cafe-dark file:mr-2 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-[11px] file:font-semibold file:bg-cafe-dark file:text-cafe-cream hover:file:bg-cafe-brown cursor-pointer border border-cafe-beige bg-white rounded-xl p-1">
                            </div>
                            <div class="sm:col-span-4 space-y-1">
                                <label class="block text-[11px] font-semibold text-cafe-dark">Keterangan / Judul Foto (Opsional)</label>
                                <input type="text" name="title" placeholder="Contoh: Foto 6 (opsional)"
                                       class="w-full bg-white text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                            </div>
                            <div class="sm:col-span-3">
                                <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 py-2.5 px-4 rounded-xl bg-cafe-dark hover:bg-cafe-brown text-cafe-cream text-xs font-semibold shadow-xs transition-all active:scale-95 cursor-pointer">
                                    <i data-lucide="upload" class="w-3.5 h-3.5 text-cafe-gold"></i>
                                    <span>Unggah ke Galeri</span>
                                </button>
                            </div>
                        </div>

                        <!-- Mini Preview saat file dipilih -->
                        <div id="gallery-add-preview" class="hidden mt-3 pt-3 border-t border-[#D4954D]/30 flex items-center gap-3">
                            <img id="gallery-add-preview-img" src="" alt="Preview" class="w-14 h-14 object-cover rounded-xl border border-cafe-beige shadow-xs">
                            <div class="text-[11px]">
                                <p id="gallery-add-preview-name" class="font-semibold text-cafe-dark truncate max-w-xs"></p>
                                <p id="gallery-add-preview-size" class="text-cafe-muted text-[10px]"></p>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Daftar Kartu Foto Galeri Saat Ini -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                    @foreach($galleryItems as $itemIdx => $gItem)
                        @php
                            $imgSrc = $gItem['image'] ?? '';
                            if ($imgSrc !== '' && !str_starts_with($imgSrc, 'http') && !str_starts_with($imgSrc, '/')) {
                                $imgSrc = asset($imgSrc);
                            }
                        @endphp
                        <div class="rounded-2xl border border-cafe-beige/60 bg-[#F2F5E2]/40 p-3 flex flex-col justify-between space-y-3 group shadow-xs">
                            <div class="space-y-2">
                                <div class="relative w-full aspect-[4/5] rounded-xl overflow-hidden border border-cafe-beige bg-[#290024] shadow-inner">
                                    <img src="{{ $imgSrc }}"
                                         alt="{{ $gItem['title'] ?? 'Galeri' }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                         onerror="this.style.display='none'">
                                    <div class="absolute top-2 left-2 px-2 py-0.5 rounded-full bg-[#000000]/70 text-[#D4954D] text-[10px] font-bold border border-[#D4954D]/40 backdrop-blur-sm">
                                        #{{ $itemIdx + 1 }}
                                    </div>
                                </div>

                                <div class="text-[11px] font-bold text-cafe-dark truncate px-1" title="{{ $gItem['title'] ?? '-' }}">
                                    {{ $gItem['title'] ?: '(Tanpa Judul)' }}
                                </div>
                            </div>

                            <div class="pt-2 border-t border-cafe-beige/40 flex items-center justify-between gap-1">
                                <!-- Trigger Edit Judul Modal/Form -->
                                <button type="button"
                                        onclick="openEditGalleryModal('{{ $gItem['id'] }}', '{{ addslashes($gItem['title'] ?? '') }}')"
                                        class="inline-flex items-center gap-1 text-[11px] font-semibold text-cafe-dark hover:text-cafe-gold transition-colors py-1">
                                    <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                    <span>Ubah</span>
                                </button>

                                <!-- Form Hapus Item -->
                                <form action="{{ route('admin.website.landing.gallery.delete', $gItem['id']) }}" method="POST" onsubmit="return confirm('Hapus foto ini dari galeri carousel?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1 text-[11px] font-semibold text-rose-600 hover:text-rose-800 transition-colors py-1">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                        <span>Hapus</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- BAGIAN 2: KELOLA TEKS KONTEN LANDING PAGE -->
            <div class="bg-cafe-card rounded-2xl p-6 sm:p-8 shadow-soft border border-cafe-beige/40">
                <div class="border-b border-cafe-beige/40 pb-4 mb-6">
                    <h4 class="font-serif font-bold text-base text-cafe-dark">2. Teks &amp; Tulisan Landing Page</h4>
                    <p class="text-xs text-cafe-muted mt-0.5">Ubah kata-kata, slogan, keunggulan, dan deskripsi yang tampil di berbagai bagian beranda.</p>
                </div>

                <form action="{{ route('admin.website.landing') }}" method="POST" class="space-y-6 text-xs">
                    @csrf

                    <!-- HERO SECTION -->
                    <div class="rounded-xl border border-cafe-beige/60 bg-[#F2F5E2]/30 p-4 sm:p-5 space-y-4">
                        <div class="border-b border-cafe-beige/40 pb-2">
                            <h5 class="font-serif font-bold text-sm text-cafe-dark">Bagian Hero (Layar Pembuka)</h5>
                            <p class="text-[11px] text-cafe-muted">Teks utama yang dilihat pelanggan saat pertama kali membuka website.</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="font-semibold text-cafe-dark">Badge / Label Atas</label>
                                <input type="text"
                                       name="hero_badge"
                                       value="{{ $landingSettings['hero_badge'] ?? 'SELAMAT DATANG DI ABS CAFE' }}"
                                       class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                                <p class="text-[10px] text-cafe-muted">Contoh: SELAMAT DATANG DI ABS CAFE</p>
                            </div>
                            <div class="space-y-1">
                                <label class="font-semibold text-cafe-dark">Awalan Judul Utama</label>
                                <input type="text"
                                       name="hero_title_prefix"
                                       value="{{ $landingSettings['hero_title_prefix'] ?? 'Temukan Momen Terbaik di' }}"
                                       class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                                <p class="text-[10px] text-cafe-muted">Teks sebelum nama kafe/highlight.</p>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="font-semibold text-cafe-dark">Sorotan Judul (Gaya Tulisan Tangan / Highlight Emas)</label>
                            <input type="text"
                                   name="hero_title_highlight"
                                   value="{{ $landingSettings['hero_title_highlight'] ?? 'ABS Cafe' }}"
                                   class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none font-medium">
                            <p class="text-[10px] text-cafe-muted">Kata yang diberi aksen font tulisan tangan berwarna emas.</p>
                        </div>

                        <div class="space-y-1">
                            <label class="font-semibold text-cafe-dark">Deskripsi Singkat Hero</label>
                            <textarea name="hero_description"
                                      rows="3"
                                      class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">{{ $landingSettings['hero_description'] ?? '' }}</textarea>
                            <p class="text-[10px] text-cafe-muted">Paragraf pengantar di bawah judul hero.</p>
                        </div>
                    </div>

                    <!-- TENTANG KAMI SECTION -->
                    <div class="rounded-xl border border-cafe-beige/60 bg-[#F2F5E2]/30 p-4 sm:p-5 space-y-4">
                        <div class="border-b border-cafe-beige/40 pb-2">
                            <h5 class="font-serif font-bold text-sm text-cafe-dark">Bagian Tentang Kami</h5>
                            <p class="text-[11px] text-cafe-muted">Cerita singkat dan slogan kafe yang tampil di samping foto berbingkai organik.</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="font-semibold text-cafe-dark">Badge / Label</label>
                                <input type="text"
                                       name="about_badge"
                                       value="{{ $landingSettings['about_badge'] ?? 'TENTANG KAMI' }}"
                                       class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                            </div>
                            <div class="space-y-1">
                                <label class="font-semibold text-cafe-dark">Judul Bagian</label>
                                <input type="text"
                                       name="about_title"
                                       value="{{ $landingSettings['about_title'] ?? 'ABS Cafe' }}"
                                       class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="font-semibold text-cafe-dark">Deskripsi Tentang Kafe</label>
                            <textarea name="about_description"
                                      rows="3"
                                      class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">{{ $landingSettings['about_description'] ?? '' }}</textarea>
                        </div>

                        <div class="space-y-1">
                            <label class="font-semibold text-cafe-dark">Tagline Tulisan Tangan</label>
                            <input type="text"
                                   name="about_tagline"
                                   value="{{ $landingSettings['about_tagline'] ?? 'Good Coffee, Better Moments' }}"
                                   class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                            <p class="text-[10px] text-cafe-muted">Slogan artistik di bawah deskripsi tentang kafe.</p>
                        </div>
                    </div>

                    <!-- EMPAT KEUNGGULAN SECTION -->
                    <div class="rounded-xl border border-cafe-beige/60 bg-[#F2F5E2]/30 p-4 sm:p-5 space-y-4">
                        <div class="border-b border-cafe-beige/40 pb-2">
                            <h5 class="font-serif font-bold text-sm text-cafe-dark">Bagian 4 Keunggulan Kafe</h5>
                            <p class="text-[11px] text-cafe-muted">Empat nilai utama yang tampil dalam barisan ikon lingkaran.</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div class="space-y-1">
                                <label class="font-semibold text-cafe-dark">Keunggulan 1 (Ikon Kopi)</label>
                                <input type="text"
                                       name="feature_1_title"
                                       value="{{ $landingSettings['feature_1_title'] ?? 'Kopi Berkualitas' }}"
                                       class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                            </div>
                            <div class="space-y-1">
                                <label class="font-semibold text-cafe-dark">Keunggulan 2 (Ikon Menu)</label>
                                <input type="text"
                                       name="feature_2_title"
                                       value="{{ $landingSettings['feature_2_title'] ?? 'Menu Lezat' }}"
                                       class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                            </div>
                            <div class="space-y-1">
                                <label class="font-semibold text-cafe-dark">Keunggulan 3 (Ikon Kursi)</label>
                                <input type="text"
                                       name="feature_3_title"
                                       value="{{ $landingSettings['feature_3_title'] ?? 'Suasana Nyaman' }}"
                                       class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                            </div>
                            <div class="space-y-1">
                                <label class="font-semibold text-cafe-dark">Keunggulan 4 (Ikon Komunitas)</label>
                                <input type="text"
                                       name="feature_4_title"
                                       value="{{ $landingSettings['feature_4_title'] ?? 'Cocok untuk Semua' }}"
                                       class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                            </div>
                        </div>
                    </div>

                    <!-- SUASANA & GALERI CAROUSEL SECTION -->
                    <div class="rounded-xl border border-cafe-beige/60 bg-[#F2F5E2]/30 p-4 sm:p-5 space-y-4">
                        <div class="border-b border-cafe-beige/40 pb-2">
                            <h5 class="font-serif font-bold text-sm text-cafe-dark">Bagian Suasana &amp; Galeri Carousel (Moments Worth Sharing)</h5>
                            <p class="text-[11px] text-cafe-muted">Teks judul dan narasi di atas barisan foto carousel yang bisa digeser dengan jari.</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="font-semibold text-cafe-dark">Badge / Label</label>
                                <input type="text"
                                       name="gallery_badge"
                                       value="{{ $landingSettings['gallery_badge'] ?? ($landingSettings['atmosphere_badge'] ?? 'SUASANA & MOMEN') }}"
                                       class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                            </div>
                            <div class="space-y-1">
                                <label class="font-semibold text-cafe-dark">Judul Galeri (Headline Utama)</label>
                                <input type="text"
                                       name="gallery_title"
                                       value="{{ $landingSettings['gallery_title'] ?? ($landingSettings['atmosphere_title'] ?? 'Moments Worth Sharing') }}"
                                       class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="font-semibold text-cafe-dark">Deskripsi Galeri</label>
                            <textarea name="gallery_description"
                                      rows="2"
                                      class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">{{ $landingSettings['gallery_description'] ?? ($landingSettings['atmosphere_description'] ?? 'From your first sip of coffee to meaningful moments with friends, ABS Cafe is where every visit becomes a memory.') }}</textarea>
                        </div>
                    </div>

                    <!-- LOKASI & SOSIAL MEDIA SECTION -->
                    <div class="rounded-xl border border-cafe-beige/60 bg-[#F2F5E2]/30 p-4 sm:p-5 space-y-4">
                        <div class="border-b border-cafe-beige/40 pb-2">
                            <h5 class="font-serif font-bold text-sm text-cafe-dark">Bagian Lokasi &amp; Media Sosial</h5>
                            <p class="text-[11px] text-cafe-muted">Label dan tautan sosial media yang terhubung di bagian bawah halaman.</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="font-semibold text-cafe-dark">Badge Lokasi</label>
                                <input type="text"
                                       name="location_badge"
                                       value="{{ $landingSettings['location_badge'] ?? 'LOKASI' }}"
                                       class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                            </div>
                            <div class="space-y-1">
                                <label class="font-semibold text-cafe-dark">Judul Lokasi</label>
                                <input type="text"
                                       name="location_title"
                                       value="{{ $landingSettings['location_title'] ?? 'Temukan Kami' }}"
                                       class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="font-semibold text-cafe-dark">Judul Bagian Media Sosial</label>
                            <input type="text"
                                   name="social_title"
                                   value="{{ $landingSettings['social_title'] ?? 'Ikuti Kami di Sosial Media' }}"
                                   class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-cafe-beige/40">
                            <div class="space-y-1">
                                <label class="font-semibold text-cafe-dark">Akun Instagram (Username)</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 font-semibold text-cafe-muted">@</span>
                                    <input type="text"
                                           name="cafe_instagram"
                                           value="{{ ltrim($landingSettings['cafe_instagram'] ?? 'abs_tulungagung_24', '@') }}"
                                           placeholder="abs_tulungagung_24"
                                           class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-3 pl-8 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                                </div>
                            </div>
                            <div class="space-y-1">
                                <label class="font-semibold text-cafe-dark">Akun TikTok (Username)</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 font-semibold text-cafe-muted">@</span>
                                    <input type="text"
                                           name="cafe_tiktok"
                                           value="{{ ltrim($landingSettings['cafe_tiktok'] ?? 'abs_tulungagung_24', '@') }}"
                                           placeholder="abs_tulungagung_24"
                                           class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-3 pl-8 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Simpan Teks -->
                    <div class="pt-5 border-t border-cafe-beige/60 flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-between gap-3 sm:gap-4">
                        <div class="flex items-center justify-center sm:justify-start gap-2 text-cafe-muted text-[11px] text-center sm:text-left bg-[#F2F5E2]/60 sm:bg-transparent p-2.5 sm:p-0 rounded-xl">
                            <i data-lucide="info" class="w-4 h-4 text-cafe-gold shrink-0"></i>
                            <span>Perubahan teks langsung tersimpan ke database &amp; cache dibersihkan otomatis.</span>
                        </div>
                        <button type="submit"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-8 py-3.5 sm:py-3 rounded-xl sm:rounded-full bg-[#290024] hover:bg-cafe-brown text-cafe-cream font-bold text-xs sm:text-sm shadow-md hover:shadow-lg transition-all active:scale-[0.98] cursor-pointer">
                            <i data-lucide="check" class="w-4 h-4 text-[#D4954D]"></i>
                            <span>Simpan Perubahan Teks</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Ubah Foto / Keterangan Galeri Carousel -->
        <div id="editGalleryModal" class="fixed inset-0 z-50 bg-[#000000]/70 backdrop-blur-sm hidden items-center justify-center p-4">
            <div class="bg-cafe-card rounded-2xl border border-cafe-beige/60 max-w-md w-full p-6 shadow-xl space-y-4">
                <div class="flex items-center justify-between border-b border-cafe-beige/40 pb-3">
                    <h5 class="font-serif font-bold text-sm text-cafe-dark">Ubah Keterangan / Ganti Foto Galeri</h5>
                    <button type="button" onclick="closeEditGalleryModal()" class="w-7 h-7 rounded-full bg-cafe-beige/60 flex items-center justify-center text-cafe-dark hover:bg-cafe-beige">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
                <form id="editGalleryForm" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
                    @csrf
                    <div class="space-y-1">
                        <label class="font-semibold text-cafe-dark">Judul / Keterangan Foto</label>
                        <input type="text" id="editGalleryTitle" name="title" class="w-full bg-white text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                    </div>
                    <div class="space-y-1">
                        <label class="font-semibold text-cafe-dark">Ganti Berkas Foto (Opsional)</label>
                        <input type="file" name="photo" accept="image/*,.heic,.heif,.HEIC,.HEIF,image/heic,image/heif"
                               class="w-full text-xs text-cafe-dark file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[11px] file:font-semibold file:bg-cafe-dark file:text-cafe-cream border border-cafe-beige bg-white rounded-xl p-1">
                        <p class="text-[10px] text-cafe-muted">Kosongkan jika hanya ingin mengubah judul/keterangan. Semua format foto didukung (JPG, PNG, WebP, HEIC, dll).</p>
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" onclick="closeEditGalleryModal()" class="px-4 py-2 rounded-xl bg-cafe-beige/60 text-cafe-dark font-semibold text-xs">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-cafe-dark hover:bg-cafe-brown text-cafe-cream font-semibold text-xs shadow-xs">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
    <!-- Toast Notifikasi Copy -->
    <div id="copy-web-toast" class="fixed bottom-6 right-6 z-50 px-4 py-2.5 rounded-xl bg-cafe-dark text-cafe-cream text-xs font-semibold shadow-soft-lg border border-cafe-gold/30 flex items-center gap-2 transform translate-y-10 opacity-0 transition-all duration-200 pointer-events-none">
        <i data-lucide="check" class="w-4 h-4 text-cafe-gold"></i>
        <span id="copy-web-toast-msg">Link berhasil disalin!</span>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function copyWebText(text, msg = 'Link berhasil disalin ke clipboard!') {
        navigator.clipboard.writeText(text).then(() => {
            const toast = document.getElementById('copy-web-toast');
            const toastMsg = document.getElementById('copy-web-toast-msg');
            if (toast && toastMsg) {
                toastMsg.innerText = msg;
                toast.classList.remove('translate-y-10', 'opacity-0');
                toast.classList.add('translate-y-0', 'opacity-100');
                setTimeout(() => {
                    toast.classList.remove('translate-y-0', 'opacity-100');
                    toast.classList.add('translate-y-10', 'opacity-0');
                }, 2500);
            }
        }).catch(() => {
            alert('URL: ' + text);
        });
    }

    function openEditGalleryModal(id, title) {
        const modal = document.getElementById('editGalleryModal');
        const form = document.getElementById('editGalleryForm');
        const inputTitle = document.getElementById('editGalleryTitle');
        if (!modal || !form || !inputTitle) return;

        form.action = "{{ url('admin/website/landing/gallery') }}/" + id + "/update";
        inputTitle.value = title;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeEditGalleryModal() {
        const modal = document.getElementById('editGalleryModal');
        if (!modal) return;
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    async function previewGalleryAdd(input) {
        const preview = document.getElementById('gallery-add-preview');
        const img = document.getElementById('gallery-add-preview-img');
        const nameEl = document.getElementById('gallery-add-preview-name');
        const sizeEl = document.getElementById('gallery-add-preview-size');
        if (!preview || !img || !nameEl || !sizeEl) return;

        if (input.files && input.files[0]) {
            let file = input.files[0];
            nameEl.innerText = file.name;
            sizeEl.innerText = (file.size / (1024 * 1024)).toFixed(2) + ' MB';

            if (/\.(heic|heif)$/i.test(file.name) || file.type === 'image/heic' || file.type === 'image/heif') {
                if (typeof window.heic2any === 'function') {
                    try {
                        const blob = await window.heic2any({ blob: file, toType: 'image/jpeg', quality: 0.9 });
                        const resBlob = Array.isArray(blob) ? blob[0] : blob;
                        const newFile = new File([resBlob], file.name.replace(/\.(heic|heif)$/i, '.jpg'), { type: 'image/jpeg' });
                        const dt = new DataTransfer();
                        dt.items.add(newFile);
                        input.files = dt.files;
                        file = newFile;
                    } catch (e) {
                        console.warn('HEIC client preview error:', e);
                    }
                }
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                img.src = e.target.result;
                preview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            preview.classList.add('hidden');
        }
    }

    async function previewWebsiteLogo(input) {
        if (!input || !input.files || !input.files[0]) return;
        let file = input.files[0];

        if (/\.(heic|heif)$/i.test(file.name) || file.type === 'image/heic' || file.type === 'image/heif') {
            if (typeof window.heic2any === 'function') {
                try {
                    const blob = await window.heic2any({ blob: file, toType: 'image/jpeg', quality: 0.9 });
                    const resBlob = Array.isArray(blob) ? blob[0] : blob;
                    file = new File([resBlob], file.name.replace(/\.(heic|heif)$/i, '.jpg'), { type: 'image/jpeg' });
                } catch (e) {
                    console.warn('HEIC preview error:', e);
                }
            }
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const container = document.getElementById('website-logo-preview');
            if (container) {
                container.innerHTML = '<span style="display:inline-flex;width:36px;height:36px;flex-shrink:0;align-items:center;justify-content:center;background-color:transparent;"><img src="' + e.target.result + '" style="display:block;width:100%;height:100%;object-fit:contain;"></span>';
            }
        };
        reader.readAsDataURL(file);
    }
</script>
@endpush
