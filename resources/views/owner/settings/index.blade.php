@extends('owner.layouts.app')

@section('title', 'Pengaturan Bisnis')
@section('page_title', 'Pengaturan Bisnis')

@section('content')
    <div class="space-y-6">
        <div>
            <h2 class="font-serif text-2xl sm:text-3xl font-bold text-cafe-dark">Pengaturan Bisnis</h2>
            <p class="text-xs text-cafe-muted mt-1">Konfigurasi target, pajak, jam operasional, dan preferensi laporan ABS Cafe.</p>
        </div>

        <div class="p-4 rounded-2xl bg-cafe-gold/10 border border-cafe-gold/30 flex items-start gap-3">
            <i data-lucide="info" class="w-4 h-4 text-cafe-bronze shrink-0 mt-0.5"></i>
            <p class="text-xs text-cafe-brown leading-relaxed">
                Beberapa pengaturan (pajak, service charge, jam operasional, dan format nota) terhubung dengan modul Admin.
                Perubahan di sini akan langsung memengaruhi data di kedua portal.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <!-- Cafe Info (read-only) -->
            <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft p-5 space-y-3">
                <h3 class="font-serif font-bold text-cafe-dark flex items-center gap-2"><i data-lucide="store" class="w-4 h-4 text-cafe-gold"></i> Profil Usaha</h3>
                <div class="text-xs space-y-2">
                    <div><p class="text-cafe-muted text-[10px] uppercase tracking-wide">Nama</p><p class="font-semibold text-cafe-dark">{{ $cafe['name'] }}</p></div>
                    <div><p class="text-cafe-muted text-[10px] uppercase tracking-wide">Tagline</p><p class="text-cafe-text">{{ $cafe['tagline'] }}</p></div>
                    <div><p class="text-cafe-muted text-[10px] uppercase tracking-wide">Alamat</p><p class="text-cafe-text">{{ $cafe['address'] ?: '-' }}</p></div>
                    <div><p class="text-cafe-muted text-[10px] uppercase tracking-wide">Telepon</p><p class="text-cafe-text">{{ $cafe['phone'] ?: '-' }}</p></div>
                    <div><p class="text-cafe-muted text-[10px] uppercase tracking-wide">Email</p><p class="text-cafe-text">{{ $cafe['email'] ?: '-' }}</p></div>
                </div>
                <a href="{{ route('owner.profile.index') }}" class="inline-flex items-center gap-1 text-xs font-semibold text-cafe-brown hover:text-cafe-dark transition-colors">
                    Kelola akun saya <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>

            <!-- Settings Form -->
            <form action="{{ route('owner.settings.update') }}" method="POST" class="lg:col-span-2 space-y-4">
                @csrf

                <!-- Target -->
                <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft p-5 space-y-4">
                    <h3 class="font-serif font-bold text-cafe-dark flex items-center gap-2"><i data-lucide="target" class="w-4 h-4 text-cafe-gold"></i> Target & Margin</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-cafe-text">Target Harian (Rp)</label>
                            <input type="number" step="1000" min="0" name="target_daily_sales" value="{{ old('target_daily_sales', $settings['target_daily_sales']) }}" class="w-full bg-cafe-cream/60 focus:bg-white text-sm px-4 py-2.5 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-cafe-text">Target Bulanan (Rp)</label>
                            <input type="number" step="1000" min="0" name="target_monthly_sales" value="{{ old('target_monthly_sales', $settings['target_monthly_sales']) }}" class="w-full bg-cafe-cream/60 focus:bg-white text-sm px-4 py-2.5 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-cafe-text">Margin Laba (%)</label>
                            <input type="number" step="0.1" min="0" max="100" name="profit_margin_percent" value="{{ old('profit_margin_percent', $settings['profit_margin_percent']) }}" class="w-full bg-cafe-cream/60 focus:bg-white text-sm px-4 py-2.5 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all">
                        </div>
                    </div>
                </div>

                <!-- Tax & service -->
                <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft p-5 space-y-4">
                    <h3 class="font-serif font-bold text-cafe-dark flex items-center gap-2"><i data-lucide="landmark" class="w-4 h-4 text-cafe-gold"></i> Pajak & Biaya Layanan</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-cafe-text">Pajak (%)</label>
                            <input type="number" step="0.1" min="0" max="100" name="tax_percent" value="{{ old('tax_percent', $settings['tax_percent']) }}" class="w-full bg-cafe-cream/60 focus:bg-white text-sm px-4 py-2.5 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-cafe-text">Service Charge (%)</label>
                            <input type="number" step="0.1" min="0" max="100" name="service_charge_percent" value="{{ old('service_charge_percent', $settings['service_charge_percent']) }}" class="w-full bg-cafe-cream/60 focus:bg-white text-sm px-4 py-2.5 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-cafe-text">Pembulatan Harga</label>
                            <select name="price_rounding" class="w-full bg-cafe-cream/60 focus:bg-white text-sm px-4 py-2.5 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all">
                                @foreach(['none' => 'Tanpa Pembulatan', '100' => 'Ke Rp 100', '500' => 'Ke Rp 500', '1000' => 'Ke Rp 1.000'] as $val => $label)
                                    <option value="{{ $val }}" {{ $settings['price_rounding'] === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <label class="flex items-center gap-2 cursor-pointer text-xs text-cafe-text select-none">
                        <input type="checkbox" name="service_charge_active" value="1" {{ $settings['service_charge_active'] == '1' ? 'checked' : '' }} class="w-4 h-4 rounded accent-[#290024]">
                        <span>Aktifkan service charge</span>
                    </label>
                </div>

                <!-- Operational -->
                <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft p-5 space-y-4">
                    <h3 class="font-serif font-bold text-cafe-dark flex items-center gap-2"><i data-lucide="clock" class="w-4 h-4 text-cafe-gold"></i> Operasional</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-cafe-text">Jam Hari Kerja</label>
                            <input type="text" name="hours_weekday" value="{{ old('hours_weekday', $settings['hours_weekday']) }}" placeholder="09:00 - 22:00" class="w-full bg-cafe-cream/60 focus:bg-white text-sm px-4 py-2.5 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-cafe-text">Jam Akhir Pekan</label>
                            <input type="text" name="hours_weekend" value="{{ old('hours_weekend', $settings['hours_weekend']) }}" placeholder="09:00 - 23:30" class="w-full bg-cafe-cream/60 focus:bg-white text-sm px-4 py-2.5 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-cafe-text">Min. Stok Default</label>
                            <input type="number" min="0" name="default_min_stock" value="{{ old('default_min_stock', $settings['default_min_stock']) }}" class="w-full bg-cafe-cream/60 focus:bg-white text-sm px-4 py-2.5 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all">
                        </div>
                    </div>
                </div>

                <!-- Notifications -->
                <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft p-5 space-y-3">
                    <h3 class="font-serif font-bold text-cafe-dark flex items-center gap-2"><i data-lucide="bell" class="w-4 h-4 text-cafe-gold"></i> Notifikasi</h3>
                    <label class="flex items-center gap-2 cursor-pointer text-xs text-cafe-text select-none">
                        <input type="checkbox" name="notif_low_stock" value="1" {{ $settings['notif_low_stock'] == '1' ? 'checked' : '' }} class="w-4 h-4 rounded accent-[#290024]">
                        <span>Peringatan stok menipis</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer text-xs text-cafe-text select-none">
                        <input type="checkbox" name="notif_daily_report" value="1" {{ $settings['notif_daily_report'] == '1' ? 'checked' : '' }} class="w-4 h-4 rounded accent-[#290024]">
                        <span>Ringkasan laporan harian</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer text-xs text-cafe-text select-none">
                        <input type="checkbox" name="notif_weekly_report" value="1" {{ $settings['notif_weekly_report'] == '1' ? 'checked' : '' }} class="w-4 h-4 rounded accent-[#290024]">
                        <span>Ringkasan laporan mingguan</span>
                    </label>
                </div>

                <!-- Receipt -->
                <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft p-5 space-y-4">
                    <h3 class="font-serif font-bold text-cafe-dark flex items-center gap-2"><i data-lucide="receipt" class="w-4 h-4 text-cafe-gold"></i> Format Nota (Nama Usaha & Footer)</h3>
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-cafe-text">Header Nota</label>
                        <textarea name="receipt_header" rows="3" class="w-full bg-cafe-cream/60 focus:bg-white text-sm px-4 py-2.5 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all">{{ old('receipt_header', $settings['receipt_header']) }}</textarea>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-cafe-text">Footer Nota</label>
                        <textarea name="receipt_footer" rows="3" class="w-full bg-cafe-cream/60 focus:bg-white text-sm px-4 py-2.5 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all">{{ old('receipt_footer', $settings['receipt_footer']) }}</textarea>
                    </div>
                    <label class="flex items-center gap-2 cursor-pointer text-xs text-cafe-text select-none">
                        <input type="checkbox" name="receipt_show_logo" value="1" {{ $settings['receipt_show_logo'] == '1' ? 'checked' : '' }} class="w-4 h-4 rounded accent-[#290024]">
                        <span>Tampilkan logo pada nota</span>
                    </label>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream text-xs font-semibold transition-colors">
                        <i data-lucide="save" class="w-4 h-4"></i> Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
