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
                    <button type="submit" class="px-6 py-2.5 rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream font-semibold text-xs shadow-md transition-all">Simpan Pengaturan Pajak</button>
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
                    <button type="submit" class="px-6 py-2.5 rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream font-semibold text-xs shadow-md transition-all">Simpan Akun Saya</button>
                </div>
            </form>
        </div>
    @endif
</div>
@endsection
