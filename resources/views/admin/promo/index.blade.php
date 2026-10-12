@extends('admin.layouts.app')

@section('title', 'Promo & Diskon')
@section('page_title', 'Promo')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-1.5 text-xs text-cafe-muted mb-3">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-cafe-dark flex items-center gap-1">
            <i data-lucide="home" class="w-3.5 h-3.5"></i>
            <span>Admin</span>
        </a>
        <i data-lucide="chevron-right" class="w-3 h-3 text-cafe-muted/60"></i>
        <span class="font-semibold text-cafe-dark">Promo &amp; Diskon</span>
    </nav>

    <!-- Page Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-serif text-2xl font-bold text-cafe-dark">Kampanye Promo &amp; Voucher</h2>
            <p class="text-xs text-cafe-muted mt-0.5">Kelola penawaran diskon, bundling Buy 2 Get 1, dan cashback pelanggan kafe</p>
        </div>

        <button onclick="openPromoModal()" class="px-5 py-2.5 rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream font-semibold text-xs shadow-md transition-all flex items-center gap-2">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Buat Promo Baru</span>
        </button>
    </div>

    <!-- Promo Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($promos as $promo)
            <div class="bg-cafe-card rounded-2xl shadow-soft hover:shadow-soft-lg border border-cafe-beige/50 overflow-hidden flex flex-col justify-between transition-all group">
                <div>
                    <!-- Banner Image Container -->
                    <div class="relative h-44 w-full bg-cafe-beige overflow-hidden">
                        <img src="{{ $promo->banner }}" alt="{{ $promo->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>

                        <!-- Status Badge Otomatis -->
                        <div class="absolute top-3 left-3">
                            @if($promo->status_label === 'berjalan')
                                <span class="px-3 py-1 rounded-full bg-[#4F7A4A] text-white text-[10px] font-bold shadow-sm">Berjalan</span>
                            @elseif($promo->status_label === 'terjadwal')
                                <span class="px-3 py-1 rounded-full bg-[#C98A2B] text-white text-[10px] font-bold shadow-sm">Terjadwal</span>
                            @else
                                <span class="px-3 py-1 rounded-full bg-[#775533] text-white text-[10px] font-bold shadow-sm">Berakhir</span>
                            @endif
                        </div>

                        <!-- Tipe Diskon Badge -->
                        <div class="absolute top-3 right-3">
                            <span class="px-3 py-1 rounded-full bg-cafe-gold text-cafe-dark text-[10px] font-bold shadow-sm">
                                @if($promo->type === 'persen')
                                    Diskon {{ intval($promo->value) }}%
                                @elseif($promo->type === 'nominal')
                                    Potongan Rp {{ number_format($promo->value, 0, ',', '.') }}
                                @else
                                    Buy 2 Get 1 Free
                                @endif
                            </span>
                        </div>

                        <!-- Periode Tanggal -->
                        <div class="absolute bottom-3 left-3 right-3 text-white text-xs flex items-center gap-1.5 drop-shadow">
                            <i data-lucide="calendar" class="w-3.5 h-3.5 text-cafe-gold"></i>
                            <span>{{ $promo->start_date->format('d M') }} - {{ $promo->end_date->format('d M Y') }}</span>
                        </div>
                    </div>

                    <!-- Promo Body -->
                    <div class="p-5 space-y-2">
                        <h3 class="font-serif font-bold text-base text-cafe-dark leading-snug">{{ $promo->title }}</h3>
                        <p class="text-xs text-cafe-muted leading-relaxed line-clamp-3">{{ $promo->description }}</p>

                        <div class="pt-2 text-[11px] text-cafe-muted flex items-center gap-1">
                            <span>Berlaku untuk:</span>
                            <strong class="text-cafe-text">
                                {{ $promo->applies_to === 'semua' ? 'Seluruh Menu' : ($promo->targetCategory ? 'Kategori ' . $promo->targetCategory->name : 'Menu Tertentu') }}
                            </strong>
                        </div>
                    </div>
                </div>

                <!-- Footer Action Buttons -->
                <div class="p-4 pt-3 border-t border-cafe-beige/40 flex items-center justify-between text-xs">
                    <!-- Switch Aktif / Nonaktif -->
                    <form action="{{ route('admin.promo.toggle.active', $promo->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="flex items-center gap-2 cursor-pointer select-none">
                            <div class="w-8 h-4.5 rounded-full p-0.5 transition-colors {{ $promo->is_active ? 'bg-cafe-success' : 'bg-cafe-muted/40' }}">
                                <div class="w-3.5 h-3.5 rounded-full bg-white transition-transform {{ $promo->is_active ? 'translate-x-3.5' : 'translate-x-0' }}"></div>
                            </div>
                            <span class="text-[11px] font-medium text-cafe-text">{{ $promo->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                        </button>
                    </form>

                    <div class="flex items-center gap-1">
                        <button onclick="editPromoModal({{ json_encode($promo) }})" class="p-1.5 rounded-full text-cafe-brown hover:text-cafe-dark hover:bg-cafe-beige/60 transition-colors" title="Edit Promo">
                            <i data-lucide="edit" class="w-3.5 h-3.5"></i>
                        </button>
                        <form action="{{ route('admin.promo.destroy', $promo->id) }}" method="POST" onsubmit="return confirm('Hapus promo ini?');" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 rounded-full text-cafe-danger hover:bg-cafe-danger/10 transition-colors" title="Hapus Promo">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-3 p-12 text-center bg-cafe-card/50 rounded-2xl border border-dashed border-cafe-beige/80">
                <i data-lucide="percent" class="w-10 h-10 text-cafe-muted mx-auto mb-2"></i>
                <h4 class="font-serif font-bold text-base text-cafe-dark">Belum Ada Promo</h4>
                <p class="text-xs text-cafe-muted mt-1">Buat kampanye promo pertama Anda untuk menarik lebih banyak pelanggan.</p>
            </div>
        @endforelse
    </div>
</div>

<!-- MODAL FORM TAMBAH / EDIT PROMO -->
<div id="promo-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 overflow-y-auto hidden">
    <div class="fixed inset-0 bg-[#290024]/40 backdrop-blur-sm" onclick="closePromoModal()"></div>

    <div class="relative w-full max-w-xl bg-cafe-card rounded-2xl shadow-soft-lg border border-cafe-beige/60 overflow-hidden z-10 my-8">
        <div class="flex items-center justify-between px-6 py-5 border-b border-cafe-beige/50 bg-[#F2F5E2]/40">
            <div>
                <h3 class="font-serif text-lg font-bold text-cafe-dark" id="promo-modal-title">Buat Promo Baru</h3>
                <p class="text-xs text-cafe-muted mt-0.5">Tentukan nilai diskon, masa berlaku, dan target menu kafe.</p>
            </div>
            <button onclick="closePromoModal()" class="w-8 h-8 rounded-full bg-cafe-beige/60 hover:bg-cafe-beige text-cafe-muted hover:text-cafe-dark flex items-center justify-center">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="promo-form" method="POST" action="{{ route('admin.promo.store') }}" class="p-6 space-y-4 text-xs">
            @csrf
            <div class="space-y-1">
                <label class="font-semibold text-cafe-dark">Judul Promo *</label>
                <input type="text" name="title" id="prm-title" required placeholder="misal: Buy 2 Get 1 Free All Coffee" class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
            </div>

            <div class="space-y-1">
                <label class="font-semibold text-cafe-dark">Deskripsi Promo *</label>
                <textarea name="description" id="prm-desc" required rows="2" placeholder="Jelaskan syarat dan ketentuan promo..." class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="font-semibold text-cafe-dark">Jenis Promo *</label>
                    <select name="type" id="prm-type" required class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                        <option value="buy_x_get_y">Buy X Get Y (Beli 2 Gratis 1)</option>
                        <option value="persen">Diskon Persen (%)</option>
                        <option value="nominal">Potongan Nominal (Rp)</option>
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="font-semibold text-cafe-dark">Nilai / Diskon *</label>
                    <input type="number" name="value" id="prm-value" value="100" min="0" required placeholder="20 atau 10000" class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="font-semibold text-cafe-dark">Berlaku Untuk *</label>
                    <select name="applies_to" id="prm-applies" class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                        <option value="semua">Semua Menu</option>
                        <option value="kategori">Kategori Tertentu</option>
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="font-semibold text-cafe-dark">Kategori Target (Jika Kategori)</label>
                    <select name="target_category_id" id="prm-cat" class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="font-semibold text-cafe-dark">Tanggal Mulai *</label>
                    <input type="date" name="start_date" id="prm-start" required class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                </div>
                <div class="space-y-1">
                    <label class="font-semibold text-cafe-dark">Tanggal Selesai *</label>
                    <input type="date" name="end_date" id="prm-end" required class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                </div>
            </div>

            <div class="space-y-1">
                <label class="font-semibold text-cafe-dark">URL Gambar Banner</label>
                <input type="url" name="banner" id="prm-banner" placeholder="https://images.unsplash.com/..." class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
            </div>

            <div class="pt-1">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" name="is_active" id="prm-active" value="1" checked class="w-4 h-4 rounded text-cafe-dark accent-[#290024]">
                    <span class="font-semibold text-cafe-dark">Aktifkan promo sekarang</span>
                </label>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-cafe-beige">
                <button type="button" onclick="closePromoModal()" class="px-5 py-2.5 rounded-full text-xs font-semibold text-cafe-dark hover:bg-cafe-beige/60">Batal</button>
                <button type="submit" class="px-6 py-2.5 rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream font-semibold text-xs shadow-md transition-all">Simpan Promo</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openPromoModal() {
        document.getElementById('promo-modal-title').innerText = 'Buat Promo Baru';
        document.getElementById('promo-form').action = "{{ route('admin.promo.store') }}";
        document.getElementById('prm-title').value = '';
        document.getElementById('prm-desc').value = '';
        document.getElementById('prm-type').value = 'buy_x_get_y';
        document.getElementById('prm-value').value = '100';
        document.getElementById('prm-applies').value = 'semua';
        document.getElementById('prm-banner').value = 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?w=800&auto=format&fit=crop&q=80';
        document.getElementById('prm-start').value = new Date().toISOString().split('T')[0];
        document.getElementById('prm-end').value = new Date(Date.now() + 30*24*60*60*1000).toISOString().split('T')[0];
        document.getElementById('prm-active').checked = true;
        document.getElementById('promo-modal').classList.remove('hidden');
    }

    function editPromoModal(promo) {
        document.getElementById('promo-modal-title').innerText = `Edit Promo: ${promo.title}`;
        document.getElementById('promo-form').action = `/admin/promo/${promo.id}/update`;
        document.getElementById('prm-title').value = promo.title;
        document.getElementById('prm-desc').value = promo.description;
        document.getElementById('prm-type').value = promo.type;
        document.getElementById('prm-value').value = parseInt(promo.value);
        document.getElementById('prm-applies').value = promo.applies_to;
        document.getElementById('prm-cat').value = promo.target_category_id || '';
        document.getElementById('prm-banner').value = promo.banner || '';
        document.getElementById('prm-start').value = promo.start_date.split('T')[0];
        document.getElementById('prm-end').value = promo.end_date.split('T')[0];
        document.getElementById('prm-active').checked = Boolean(promo.is_active);
        document.getElementById('promo-modal').classList.remove('hidden');
    }

    function closePromoModal() {
        document.getElementById('promo-modal').classList.add('hidden');
    }
</script>
@endsection
