@extends($layout)

@section('title', $isEdit ? 'Ubah Promo' : 'Buat Promo Baru')
@section('page_title', $isEdit ? 'Ubah Promo' : 'Buat Promo Baru')
@section('has_form_header', true)

@section('content')
<div class="max-w-[800px] mx-auto pb-28 lg:pb-10">
    <!-- Bilah Header Ringkas Reusable -->
    <x-form-header :title="$isEdit ? 'Ubah Promo' : 'Buat Promo Baru'" :back-url="$backUrl" />

    <!-- Error Summary (Tampil bila ada kegagalan validasi) -->
    @if($errors->any())
        <div class="mt-4 mb-2 p-4 rounded-2xl bg-[#B03A2E]/10 border border-[#B03A2E]/30 text-cafe-text animate-in fade-in">
            <div class="flex items-center gap-2 mb-2 text-cafe-danger font-semibold text-xs sm:text-sm">
                <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
                <span>Terdapat kesalahan pada isian form:</span>
            </div>
            <ul class="list-disc list-inside text-xs text-cafe-text/90 space-y-1 pl-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Utama Promo (Mulai langsung 16px di bawah bilah) -->
    <form id="promo-form" method="POST" action="{{ $action }}" class="space-y-6 mt-4">
        @csrf

        <div class="bg-cafe-card rounded-2xl shadow-soft border border-cafe-beige/60 p-5 sm:p-7 space-y-6">
            <!-- 1. Informasi Promo -->
            <div class="space-y-4">
                <div class="border-b border-cafe-beige/50 pb-2">
                    <h3 class="font-serif font-bold text-sm sm:text-base text-cafe-dark">Informasi Promo</h3>
                    <p class="text-[11px] text-cafe-muted">Nama judul kampanye dan penjelasan promo</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2 space-y-1.5">
                        <label for="title" class="block text-xs font-semibold text-cafe-text">Judul Promo <span class="text-cafe-danger">*</span></label>
                        <input type="text"
                               name="title"
                               id="title"
                               value="{{ old('title', $promo->title) }}"
                               required
                               placeholder="misal: Buy 2 Get 1 Free All Coffee"
                               class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs sm:text-sm text-cafe-text px-4 py-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none focus:ring-2 focus:ring-cafe-gold/20 transition-all min-h-[44px]">
                        @error('title')
                            <p class="text-[11px] text-cafe-danger mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-2 space-y-1.5">
                        <label for="code" class="block text-xs font-semibold text-cafe-text">Kode Promo / Voucher <span class="text-cafe-muted font-normal">(opsional)</span></label>
                        <input type="text"
                               name="code"
                               id="code"
                               value="{{ old('code', $promo->code) }}"
                               placeholder="misal: KOPIHEMAT (kosongkan jika tanpa kode)"
                               class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs sm:text-sm text-cafe-text px-4 py-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none focus:ring-2 focus:ring-cafe-gold/20 transition-all uppercase min-h-[44px]">
                        <p class="text-[10px] text-cafe-muted">Otomatis diubah menjadi huruf besar saat disimpan.</p>
                        @error('code')
                            <p class="text-[11px] text-cafe-danger mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-2 space-y-1.5">
                        <label for="description" class="block text-xs font-semibold text-cafe-text">Deskripsi Promo <span class="text-cafe-danger">*</span></label>
                        <textarea name="description"
                                  id="description"
                                  rows="3"
                                  required
                                  placeholder="Jelaskan syarat dan ketentuan promo kafe..."
                                  class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs sm:text-sm text-cafe-text px-4 py-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none focus:ring-2 focus:ring-cafe-gold/20 transition-all">{{ old('description', $promo->description) }}</textarea>
                        @error('description')
                            <p class="text-[11px] text-cafe-danger mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- 2. Diskon -->
            <div class="space-y-4">
                <div class="border-b border-cafe-beige/50 pb-2">
                    <h3 class="font-serif font-bold text-sm sm:text-base text-cafe-dark">Diskon</h3>
                    <p class="text-[11px] text-cafe-muted">Mekanisme potongan harga, nilai diskon, dan cakupan menu</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Jenis Promo -->
                    <div class="space-y-1.5">
                        <label for="type" class="block text-xs font-semibold text-cafe-text">Jenis Promo <span class="text-cafe-danger">*</span></label>
                        <select name="type"
                                id="type"
                                required
                                onchange="updateTypeFields()"
                                class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs sm:text-sm text-cafe-text px-4 py-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none focus:ring-2 focus:ring-cafe-gold/20 transition-all min-h-[44px]">
                            @php
                                $selectedType = old('type', $promo->type ?: 'buy_x_get_y');
                            @endphp
                            <option value="buy_x_get_y" {{ $selectedType === 'buy_x_get_y' ? 'selected' : '' }}>Buy X Get Y (Beli 2 Gratis 1)</option>
                            <option value="persen" {{ $selectedType === 'persen' ? 'selected' : '' }}>Diskon Persen (%)</option>
                            <option value="nominal" {{ $selectedType === 'nominal' ? 'selected' : '' }}>Potongan Nominal (Rp)</option>
                        </select>
                        @error('type')
                            <p class="text-[11px] text-cafe-danger mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nilai / Diskon -->
                    <div class="space-y-1.5">
                        <label for="value" id="value-label" class="block text-xs font-semibold text-cafe-text">
                            Nilai / Diskon <span class="text-cafe-danger">*</span>
                        </label>
                        <div class="relative flex items-center">
                            <span id="value-prefix" class="hidden absolute left-4 text-xs font-bold text-cafe-muted">Rp</span>
                            <input type="number"
                                   step="any"
                                   min="0"
                                   name="value"
                                   id="value"
                                   value="{{ old('value', $promo->value !== null ? (float) $promo->value : 100) }}"
                                   required
                                   placeholder="100"
                                   class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs sm:text-sm text-cafe-text px-4 py-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none focus:ring-2 focus:ring-cafe-gold/20 transition-all min-h-[44px]">
                            <span id="value-suffix" class="absolute right-4 text-xs font-bold text-cafe-muted">%</span>
                        </div>
                        <p id="value-helper" class="text-[10px] text-cafe-muted mt-1 leading-snug"></p>
                        @error('value')
                            <p class="text-[11px] text-cafe-danger mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Berlaku Untuk -->
                    <div class="space-y-1.5">
                        <label for="applies_to" class="block text-xs font-semibold text-cafe-text">Berlaku Untuk <span class="text-cafe-danger">*</span></label>
                        <select name="applies_to"
                                id="applies_to"
                                required
                                onchange="updateAppliesToFields()"
                                class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs sm:text-sm text-cafe-text px-4 py-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none focus:ring-2 focus:ring-cafe-gold/20 transition-all min-h-[44px]">
                            @php
                                $selectedApplies = old('applies_to', $promo->applies_to ?: 'semua');
                            @endphp
                            <option value="semua" {{ $selectedApplies === 'semua' ? 'selected' : '' }}>Semua Menu</option>
                            <option value="kategori" {{ $selectedApplies === 'kategori' ? 'selected' : '' }}>Kategori Tertentu</option>
                            <option value="menu" {{ $selectedApplies === 'menu' ? 'selected' : '' }}>Menu Tertentu</option>
                        </select>
                        @error('applies_to')
                            <p class="text-[11px] text-cafe-danger mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kategori Target (Hanya bila Berlaku Untuk = Kategori) -->
                    <div class="space-y-1.5" id="target-category-container">
                        <label for="target_category_id" class="block text-xs font-semibold text-cafe-text">
                            Kategori Target <span class="text-cafe-danger">*</span>
                        </label>
                        <select name="target_category_id"
                                id="target_category_id"
                                class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs sm:text-sm text-cafe-text px-4 py-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none focus:ring-2 focus:ring-cafe-gold/20 transition-all min-h-[44px]">
                            <option value="">Pilih Kategori</option>
                            @php
                                $selectedCat = old('target_category_id', $promo->target_category_id);
                            @endphp
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ (string) $selectedCat === (string) $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-cafe-muted">Wajib dipilih jika promo berlaku untuk kategori tertentu.</p>
                        @error('target_category_id')
                            <p class="text-[11px] text-cafe-danger mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Minimal Belanja -->
                    <div class="space-y-1.5">
                        <label for="min_spend" class="block text-xs font-semibold text-cafe-text">Minimal Belanja (Rp) <span class="text-cafe-muted font-normal">(opsional)</span></label>
                        <input type="number"
                               step="any"
                               min="0"
                               name="min_spend"
                               id="min_spend"
                               value="{{ old('min_spend', $promo->min_spend !== null ? (float) $promo->min_spend : 0) }}"
                               placeholder="0"
                               class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs sm:text-sm text-cafe-text px-4 py-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none focus:ring-2 focus:ring-cafe-gold/20 transition-all min-h-[44px]">
                        @error('min_spend')
                            <p class="text-[11px] text-cafe-danger mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kuota Pemakaian -->
                    <div class="space-y-1.5">
                        <label for="quota" class="block text-xs font-semibold text-cafe-text">Kuota Pemakaian <span class="text-cafe-muted font-normal">(opsional)</span></label>
                        <input type="number"
                               min="0"
                               name="quota"
                               id="quota"
                               value="{{ old('quota', $promo->quota) }}"
                               placeholder="Kosongkan jika tanpa batas"
                               class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs sm:text-sm text-cafe-text px-4 py-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none focus:ring-2 focus:ring-cafe-gold/20 transition-all min-h-[44px]">
                        @error('quota')
                            <p class="text-[11px] text-cafe-danger mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- 3. Periode -->
            <div class="space-y-4">
                <div class="border-b border-cafe-beige/50 pb-2">
                    <h3 class="font-serif font-bold text-sm sm:text-base text-cafe-dark">Periode</h3>
                    <p class="text-[11px] text-cafe-muted">Rentang tanggal aktif berlakunya promo</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="start_date" class="block text-xs font-semibold text-cafe-text">Tanggal Mulai <span class="text-cafe-danger">*</span></label>
                        <input type="date"
                               name="start_date"
                               id="start_date"
                               value="{{ old('start_date', $promo->start_date ? \Carbon\Carbon::parse($promo->start_date)->toDateString() : now()->toDateString()) }}"
                               required
                               class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs sm:text-sm text-cafe-text px-4 py-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none focus:ring-2 focus:ring-cafe-gold/20 transition-all min-h-[44px]">
                        @error('start_date')
                            <p class="text-[11px] text-cafe-danger mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label for="end_date" class="block text-xs font-semibold text-cafe-text">Tanggal Selesai <span class="text-cafe-danger">*</span></label>
                        <input type="date"
                               name="end_date"
                               id="end_date"
                               value="{{ old('end_date', $promo->end_date ? \Carbon\Carbon::parse($promo->end_date)->toDateString() : now()->addDays(30)->toDateString()) }}"
                               required
                               class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs sm:text-sm text-cafe-text px-4 py-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none focus:ring-2 focus:ring-cafe-gold/20 transition-all min-h-[44px]">
                        @error('end_date')
                            <p class="text-[11px] text-cafe-danger mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- 4. Tampilan -->
            <div class="space-y-4">
                <div class="border-b border-cafe-beige/50 pb-2">
                    <h3 class="font-serif font-bold text-sm sm:text-base text-cafe-dark">Tampilan</h3>
                    <p class="text-[11px] text-cafe-muted">Gambar banner untuk kartu dan halaman promo</p>
                </div>

                <div class="space-y-3">
                    <div class="space-y-1.5">
                        <label for="banner" class="block text-xs font-semibold text-cafe-text">URL Gambar Banner <span class="text-cafe-muted font-normal">(opsional)</span></label>
                        <input type="url"
                               name="banner"
                               id="banner"
                               value="{{ old('banner', $promo->banner ?: 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?w=800&auto=format&fit=crop&q=80') }}"
                               oninput="updateBannerPreview()"
                               placeholder="https://images.unsplash.com/..."
                               class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs sm:text-sm text-cafe-text px-4 py-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none focus:ring-2 focus:ring-cafe-gold/20 transition-all min-h-[44px]">
                        @error('banner')
                            <p class="text-[11px] text-cafe-danger mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Pratinjau Banner Kecil -->
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-[#E3DEA4]/30 border border-cafe-beige/60">
                        <div class="w-20 h-14 sm:w-28 sm:h-16 rounded-lg overflow-hidden bg-cafe-beige shrink-0 border border-cafe-beige">
                            <img id="banner-preview"
                                 src="{{ old('banner', $promo->banner ?: 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?w=800&auto=format&fit=crop&q=80') }}"
                                 alt="Pratinjau Banner"
                                 onerror="this.src='https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?w=800&auto=format&fit=crop&q=80'"
                                 class="w-full h-full object-cover">
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold text-cafe-dark">Pratinjau Banner</p>
                            <p class="text-[10px] text-cafe-muted mt-0.5">Gambar banner akan ditampilkan pada kartu daftar promo dan aplikasi kasir.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 5. Status -->
            <div class="space-y-4">
                <div class="border-b border-cafe-beige/50 pb-2">
                    <h3 class="font-serif font-bold text-sm sm:text-base text-cafe-dark">Status</h3>
                    <p class="text-[11px] text-cafe-muted">Status ketersediaan promo dalam sistem</p>
                </div>

                <div>
                    @php
                        $isActiveChecked = old('is_active', $promo->exists ? (bool) $promo->is_active : true);
                    @endphp
                    <label class="inline-flex items-center gap-3 cursor-pointer select-none">
                        <input type="checkbox"
                               name="is_active"
                               id="is_active"
                               value="1"
                               {{ $isActiveChecked ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="w-11 h-6 bg-cafe-beige/80 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-cafe-gold/50 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-cafe-success relative"></div>
                        <span class="font-semibold text-xs sm:text-sm text-cafe-dark">Aktifkan promo sekarang</span>
                    </label>
                    <p class="text-[10px] text-cafe-muted mt-1">Jika dinonaktifkan, promo tidak dapat diterapkan pada transaksi pelanggan.</p>
                </div>
            </div>
        </div>

        <!-- Bilah Aksi: Sticky di bawah pada Mobile & Tablet (dengan safe-area); di Desktop berada di bawah kartu -->
        <div class="fixed inset-x-0 bottom-0 z-30 bg-[#FAFCF3]/95 backdrop-blur-md border-t border-cafe-beige/60 p-4 pb-[max(1rem,env(safe-area-inset-bottom))] shadow-soft-lg lg:static lg:bg-transparent lg:border-t-0 lg:p-0 lg:shadow-none lg:mt-6">
            <div class="max-w-[800px] mx-auto flex items-center justify-end gap-3">
                <a href="{{ $backUrl }}"
                   id="btn-cancel"
                   class="min-h-[44px] px-6 py-2.5 rounded-full border border-cafe-beige/80 bg-cafe-card hover:bg-cafe-beige/50 text-cafe-dark font-semibold text-xs sm:text-sm transition-all flex items-center justify-center">
                    Batal
                </a>
                <button type="submit"
                        id="btn-submit"
                        class="min-h-[44px] px-7 py-2.5 rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream font-semibold text-xs sm:text-sm shadow-md transition-all flex items-center justify-center gap-2">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Simpan Promo</span>
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    let isDirty = false;
    let isSubmitting = false;

    function updateTypeFields() {
        const typeEl = document.getElementById('type');
        if (!typeEl) return;
        const type = typeEl.value;
        const valueLabel = document.getElementById('value-label');
        const valueHelper = document.getElementById('value-helper');
        const valuePrefix = document.getElementById('value-prefix');
        const valueSuffix = document.getElementById('value-suffix');
        const valueInput = document.getElementById('value');

        if (type === 'persen') {
            valueLabel.innerHTML = 'Nilai Diskon (%) <span class="text-cafe-danger">*</span>';
            valueHelper.textContent = 'Persentase potongan harga dari total belanja (contoh: 20 untuk diskon 20%).';
            valuePrefix.classList.add('hidden');
            valueSuffix.classList.remove('hidden');
            valueSuffix.textContent = '%';
            valueInput.placeholder = '20';
            valueInput.classList.remove('pl-11');
            valueInput.classList.add('pr-10');
        } else if (type === 'nominal') {
            valueLabel.innerHTML = 'Nominal Potongan (Rp) <span class="text-cafe-danger">*</span>';
            valueHelper.textContent = 'Jumlah potongan harga langsung dalam Rupiah (contoh: 15000 untuk Rp 15.000).';
            valuePrefix.classList.remove('hidden');
            valueSuffix.classList.add('hidden');
            valueInput.placeholder = '15000';
            valueInput.classList.add('pl-11');
            valueInput.classList.remove('pr-10');
        } else {
            // buy_x_get_y
            valueLabel.innerHTML = 'Diskon Item Gratis (%) <span class="text-cafe-danger">*</span>';
            valueHelper.textContent = 'Buy X Get Y: masukkan 100 untuk gratis 100% pada item bonus (Beli 2 Gratis 1).';
            valuePrefix.classList.add('hidden');
            valueSuffix.classList.remove('hidden');
            valueSuffix.textContent = '%';
            valueInput.placeholder = '100';
            valueInput.classList.remove('pl-11');
            valueInput.classList.add('pr-10');
        }
    }

    function updateAppliesToFields() {
        const appliesEl = document.getElementById('applies_to');
        if (!appliesEl) return;
        const applies = appliesEl.value;
        const catContainer = document.getElementById('target-category-container');
        const catSelect = document.getElementById('target_category_id');

        if (applies === 'kategori') {
            catContainer.classList.remove('hidden');
            catSelect.setAttribute('required', 'required');
        } else {
            catContainer.classList.add('hidden');
            catSelect.removeAttribute('required');
        }
    }

    function updateBannerPreview() {
        const bannerInput = document.getElementById('banner');
        const preview = document.getElementById('banner-preview');
        if (!bannerInput || !preview) return;
        const url = bannerInput.value.trim();
        preview.src = url || 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?w=800&auto=format&fit=crop&q=80';
    }

    document.addEventListener('DOMContentLoaded', function () {
        updateTypeFields();
        updateAppliesToFields();
        updateBannerPreview();

        const form = document.getElementById('promo-form');
        if (form) {
            form.addEventListener('input', function () {
                isDirty = true;
            });
            form.addEventListener('change', function () {
                isDirty = true;
            });
            form.addEventListener('submit', function () {
                isSubmitting = true;
                isDirty = false;
            });
        }

        window.addEventListener('beforeunload', function (e) {
            if (isDirty && !isSubmitting) {
                e.preventDefault();
                e.returnValue = '';
            }
        });

        document.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function (e) {
                if (isDirty && !isSubmitting) {
                    const confirmed = window.confirm('Perubahan belum disimpan. Yakin ingin meninggalkan halaman ini?');
                    if (!confirmed) {
                        e.preventDefault();
                    }
                }
            });
        });

        if (window.lucide) {
            lucide.createIcons();
        }
    });
</script>
@endpush
