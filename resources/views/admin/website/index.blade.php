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
            <p class="text-xs text-cafe-muted mt-0.5">Kelola identitas kafe, pajak, dan akun admin</p>
        </div>
    </div>

    {{-- Tabs Navigation for Mobile/Tablet: sticky chip scroller --}}
    <div class="lg:hidden sticky top-16 z-20 bg-[#F2F5E2] pt-2 pb-2 -mx-3.5 px-3.5 sm:-mx-5 sm:px-5">
        @php
            $tabOptions = [
                ['label' => 'Profil', 'val' => 'profil'],
                ['label' => 'Landing Page', 'val' => 'landing'],
                ['label' => 'Pajak & Biaya', 'val' => 'pajak'],
                ['label' => 'Akun Saya', 'val' => 'akun'],
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
        <a href="{{ route('admin.website.index', ['tab' => 'pajak']) }}" class="px-4 py-2 rounded-full whitespace-nowrap transition-all {{ $tab === 'pajak' ? 'bg-cafe-dark text-cafe-cream shadow-xs' : 'text-cafe-muted hover:text-cafe-dark hover:bg-cafe-beige/50' }}">
            Pajak &amp; Biaya
        </a>
        <a href="{{ route('admin.website.index', ['tab' => 'akun']) }}" class="px-4 py-2 rounded-full whitespace-nowrap transition-all {{ $tab === 'akun' ? 'bg-cafe-dark text-cafe-cream shadow-xs' : 'text-cafe-muted hover:text-cafe-dark hover:bg-cafe-beige/50' }}">
            Akun Saya
        </a>
    </div>

    <!-- TAB 1: PROFIL CAFE -->
    @if($tab === 'profil')
        <div class="bg-cafe-card rounded-2xl p-6 sm:p-8 shadow-soft border border-cafe-beige/40 max-w-3xl">
            <h3 class="font-serif font-bold text-lg text-cafe-dark mb-1">Identitas &amp; Jam Operasional ABS Cafe</h3>
            <p class="text-xs text-cafe-muted mb-6">Informasi ini akan tertera pada struk pembelian, website katalog, dan dokumen resmi.</p>

            <form action="{{ route('admin.website.profile') }}" method="POST" class="space-y-4 text-xs">
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
                    <label class="font-semibold text-cafe-dark">Logo Website</label>
                    <div class="flex flex-col gap-3 rounded-xl border border-cafe-beige bg-[#F2F5E2]/60 p-3 sm:flex-row sm:items-center">
                        <x-logo size="sm" :show-text="false" />
                        <input type="text" name="cafe_logo" value="{{ $settings['cafe_logo'] }}" placeholder="images/logo.png" class="w-full bg-beige focus:bg-beige text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                    </div>
                    <p class="text-[10px] text-cafe-muted">Path lokal atau URL gambar. Default: <span class="font-mono">images/logo.png</span>.</p>
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

    <!-- TAB 2: PAJAK & BIAYA -->
    @if($tab === 'pajak')
        <div class="bg-cafe-card rounded-2xl p-6 sm:p-8 shadow-soft border border-cafe-beige/40 max-w-2xl">
            <h3 class="font-serif font-bold text-lg text-cafe-dark mb-1">Pengaturan Pajak Daerah &amp; Layanan</h3>
            <p class="text-xs text-cafe-muted mb-6">Atur persentase PB1 resto dan biaya pelayanan kafe.</p>

            <form action="{{ route('admin.website.tax') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div class="space-y-1">
                    <label class="font-semibold text-cafe-dark">Pajak Restoran / PB1 (%)</label>
                    <div class="relative w-48">
                        <input type="number" name="tax_percent" value="{{ $settings['tax_percent'] }}" min="0" max="100" class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-3 pr-8 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
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
                            <input type="number" name="service_charge_percent" value="{{ $settings['service_charge_percent'] }}" min="0" max="100" class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-3 pr-8 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 font-bold text-cafe-muted">%</span>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-cafe-beige/60 flex justify-end">
                    <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-3 sm:py-2.5 rounded-xl sm:rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream font-semibold text-xs sm:text-sm shadow-md transition-all active:scale-95 cursor-pointer">
                        <i data-lucide="check" class="w-4 h-4 text-cafe-gold"></i>
                        <span>Simpan Pengaturan Pajak</span>
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- TAB 3: AKUN SAYA -->
    @if($tab === 'akun')
        <div class="bg-cafe-card rounded-2xl p-6 sm:p-8 shadow-soft border border-cafe-beige/40 max-w-2xl">
            <h3 class="font-serif font-bold text-lg text-cafe-dark mb-1">Akun Administrator Saya</h3>
            <p class="text-xs text-cafe-muted mb-6">Ubah nama, username login, dan perbarui kata sandi akun administrator.</p>

            <form action="{{ route('admin.website.account') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="font-semibold text-cafe-dark">Nama Admin *</label>
                        <input type="text" name="name" value="{{ Auth::user()->name }}" required class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                    </div>
                    <div class="space-y-1">
                        <label class="font-semibold text-cafe-dark">Username Admin *</label>
                        <input type="text" name="username" value="{{ Auth::user()->username }}" required class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                    </div>
                </div>

                <div class="pt-2 border-t border-cafe-beige/40 space-y-4">
                    <h4 class="font-semibold text-cafe-dark">Ganti Kata Sandi (Opsional)</h4>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="font-semibold text-cafe-muted">Password Baru</label>
                            <input type="password" name="password" placeholder="Minimal 6 karakter" class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                        </div>
                        <div class="space-y-1">
                            <label class="font-semibold text-cafe-muted">Ulangi Password Baru</label>
                            <input type="password" name="password_confirmation" placeholder="Ulangi password baru" class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-cafe-beige/60 flex justify-end">
                    <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-3 sm:py-2.5 rounded-xl sm:rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream font-semibold text-xs sm:text-sm shadow-md transition-all active:scale-95 cursor-pointer">
                        <i data-lucide="check" class="w-4 h-4 text-cafe-gold"></i>
                        <span>Simpan Akun Saya</span>
                    </button>
                </div>
            </form>
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
                                               accept="image/jpeg,image/png,image/webp"
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

                    <!-- SUASANA KAFE SECTION -->
                    <div class="rounded-xl border border-cafe-beige/60 bg-[#F2F5E2]/30 p-4 sm:p-5 space-y-4">
                        <div class="border-b border-cafe-beige/40 pb-2">
                            <h5 class="font-serif font-bold text-sm text-cafe-dark">Bagian Suasana Kafe</h5>
                            <p class="text-[11px] text-cafe-muted">Teks overlay di atas foto banner suasana kafe.</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="font-semibold text-cafe-dark">Badge / Label</label>
                                <input type="text"
                                       name="atmosphere_badge"
                                       value="{{ $landingSettings['atmosphere_badge'] ?? 'SUASANA' }}"
                                       class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                            </div>
                            <div class="space-y-1">
                                <label class="font-semibold text-cafe-dark">Judul Suasana</label>
                                <input type="text"
                                       name="atmosphere_title"
                                       value="{{ $landingSettings['atmosphere_title'] ?? 'Nyaman & Elegan' }}"
                                       class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="font-semibold text-cafe-dark">Deskripsi Suasana</label>
                            <textarea name="atmosphere_description"
                                      rows="2"
                                      class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">{{ $landingSettings['atmosphere_description'] ?? '' }}</textarea>
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
    @endif
</div>
@endsection
