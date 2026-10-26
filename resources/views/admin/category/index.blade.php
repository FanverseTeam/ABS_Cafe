@extends('admin.layouts.app')

@section('title', 'Kategori Menu')
@section('page_title', 'Kategori')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <x-breadcrumb
        :items="[
            ['label' => 'Admin', 'url' => route('admin.dashboard'), 'icon' => 'home'],
            ['label' => 'Kategori'],
        ]"
        class="mb-3"
    />

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-serif text-2xl font-bold text-cafe-dark">Kategori Menu &amp; Produk</h2>
            <p class="text-xs text-cafe-muted mt-0.5">Atur pengelompokan menu, urutan tampil, serta ikon visual kafe</p>
        </div>

        <button onclick="openCategoryModal()" class="px-5 py-2.5 rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream font-semibold text-xs shadow-md transition-all flex items-center gap-2">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Tambah Kategori</span>
        </button>
    </div>

    <!-- Categories Grid / Table -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- List Kategori (2 Cols) -->
        <div class="md:col-span-2 bg-cafe-card rounded-2xl shadow-soft border border-cafe-beige/40 overflow-hidden">
            <div class="p-5 border-b border-cafe-beige/50 flex items-center justify-between">
                <h3 class="font-serif font-bold text-base text-cafe-dark">Daftar Kategori Aktif</h3>
                <span class="text-xs text-cafe-muted">{{ $categories->count() }} Kategori Terdaftar</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#F2F5E2]/70 border-b border-cafe-beige text-cafe-muted uppercase font-semibold text-[11px]">
                        <tr>
                            <th class="py-3.5 pl-6">Urutan</th>
                            <th class="py-3.5 px-3">Nama Kategori</th>
                            <th class="py-3.5 px-3">Jumlah Menu</th>
                            <th class="py-3.5 px-3">Status</th>
                            <th class="py-3.5 pr-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-cafe-beige/30">
                        @foreach($categories as $cat)
                            <tr class="hover:bg-cafe-cream/80 transition-colors">
                                <td class="py-4 pl-6 font-mono font-bold text-cafe-gold">#{{ $cat->order }}</td>
                                <td class="py-4 px-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-cafe-beige/80 text-cafe-dark flex items-center justify-center shrink-0">
                                            <i data-lucide="{{ $cat->icon }}" class="w-4 h-4"></i>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-cafe-dark text-xs">{{ $cat->name }}</p>
                                            <p class="text-[11px] text-cafe-muted line-clamp-1 max-w-xs">{{ $cat->description }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-3 font-semibold text-cafe-text">
                                    <span class="px-2.5 py-1 rounded-full bg-cafe-beige/60 text-cafe-dark">
                                        {{ $cat->menus_count }} menu
                                    </span>
                                </td>
                                <td class="py-4 px-3">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold {{ $cat->is_active ? 'bg-cafe-success/15 text-cafe-success' : 'bg-cafe-muted/15 text-cafe-muted' }}">
                                        {{ $cat->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="py-4 pr-6 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button onclick="editCategoryModal({{ json_encode($cat) }})" class="p-1.5 rounded-full hover:bg-cafe-beige/60 text-cafe-brown hover:text-cafe-dark transition-colors" title="Edit">
                                            <i data-lucide="edit" class="w-3.5 h-3.5"></i>
                                        </button>
                                        <button onclick="openDeleteCategoryModal({{ $cat->id }}, '{{ addslashes($cat->name) }}', {{ $cat->menus_count }})" class="p-1.5 rounded-full hover:bg-cafe-danger/10 text-cafe-danger transition-colors" title="Hapus">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Info / Panduan Aturan Kategori (1 Col) -->
        <div class="bg-cafe-card rounded-2xl p-6 shadow-soft border border-cafe-beige/40 space-y-4">
            <div class="flex items-center gap-2 text-cafe-brown">
                <i data-lucide="info" class="w-5 h-5"></i>
                <h4 class="font-serif font-bold text-base text-cafe-dark">Aturan Pengelolaan</h4>
            </div>

            <div class="p-4 rounded-xl bg-cafe-cream/80 border border-cafe-beige space-y-2 text-xs text-cafe-text leading-relaxed">
                <p><strong>1. Larangan Hapus Kategori Berisi Menu:</strong></p>
                <p class="text-cafe-muted">Kategori yang masih memiliki item menu tidak dapat dihapus untuk mencegah error pesanan pelanggan. Pindahkan atau hapus semua menu di dalamnya terlebih dahulu.</p>

                <p class="pt-2"><strong>2. Ikon Visual Kategori:</strong></p>
                <p class="text-cafe-muted">Gunakan nama ikon Lucide yang sesuai, seperti <code>coffee</code>, <code>sparkles</code>, <code>flame</code>, <code>milk</code>, <code>popcorn</code>, atau <code>utensils</code>.</p>

                <p class="pt-2"><strong>3. Urutan Tampilan Menu:</strong></p>
                <p class="text-cafe-muted">Atur nomor urutan kategori agar kelompok minuman dan makanan tersusun rapi serta mudah dipilih oleh kasir di halaman POS.</p>
            </div>
        </div>
    </div>
</div>

<!-- MODAL FORM TAMBAH / EDIT KATEGORI -->
<div id="category-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 overflow-y-auto hidden">
    <div class="fixed inset-0 bg-[#290024]/40 backdrop-blur-sm" onclick="closeCategoryModal()"></div>

    <div class="relative w-full max-w-md bg-cafe-card rounded-2xl shadow-soft-lg border border-cafe-beige/60 overflow-hidden z-10 my-8">
        <div class="flex items-center justify-between px-6 py-5 border-b border-cafe-beige/50 bg-[#F2F5E2]/40">
            <div>
                <h3 class="font-serif text-lg font-bold text-cafe-dark" id="category-modal-title">Tambah Kategori Baru</h3>
                <p class="text-xs text-cafe-muted mt-0.5">Kelompokkan menu agar rapi saat dipesan pelanggan.</p>
            </div>
            <button onclick="closeCategoryModal()" class="w-8 h-8 rounded-full bg-cafe-beige/60 hover:bg-cafe-beige text-cafe-muted hover:text-cafe-dark flex items-center justify-center">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="category-form" method="POST" action="{{ route('admin.category.store') }}" class="p-6 space-y-4 text-xs">
            @csrf
            <div class="space-y-1">
                <label class="font-semibold text-cafe-dark">Nama Kategori *</label>
                <input type="text" name="name" id="cat-input-name" required placeholder="misal: Coffee, Artisan Tea" class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="font-semibold text-cafe-dark">Ikon (Lucide)</label>
                    <input type="text" name="icon" id="cat-input-icon" value="coffee" placeholder="coffee, utensils" class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                </div>
                <div class="space-y-1">
                    <label class="font-semibold text-cafe-dark">Urutan Tampil</label>
                    <input type="number" name="order" id="cat-input-order" value="1" min="1" class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                </div>
            </div>

            <div class="space-y-1">
                <label class="font-semibold text-cafe-dark">Deskripsi Singkat</label>
                <textarea name="description" id="cat-input-desc" rows="2" placeholder="Keterangan singkat kategori ini..." class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none"></textarea>
            </div>

            <div class="pt-1">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" name="is_active" id="cat-input-active" value="1" checked class="w-4 h-4 rounded text-cafe-dark accent-[#290024]">
                    <span class="font-semibold text-cafe-dark">Status Aktif (Tampil di Menu)</span>
                </label>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-cafe-beige">
                <button type="button" onclick="closeCategoryModal()" class="px-5 py-2.5 rounded-full text-xs font-semibold text-cafe-dark hover:bg-cafe-beige/60">Batal</button>
                <button type="submit" class="px-6 py-2.5 rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream font-semibold text-xs shadow-md transition-all">Simpan Kategori</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL KONFIRMASI HAPUS KATEGORI -->
<div id="delete-cat-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 overflow-y-auto hidden">
    <div class="fixed inset-0 bg-[#290024]/40 backdrop-blur-sm" onclick="closeDeleteCategoryModal()"></div>

    <div class="relative w-full max-w-md bg-cafe-card rounded-2xl shadow-soft-lg border border-cafe-beige/60 overflow-hidden z-10 my-8 p-6 space-y-4">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-2xl bg-cafe-danger/10 text-cafe-danger flex items-center justify-center shrink-0">
                <i data-lucide="alert-triangle" class="w-5 h-5"></i>
            </div>
            <div>
                <h4 class="font-serif text-lg font-bold text-cafe-dark">Hapus Kategori</h4>
                <p class="text-xs text-cafe-muted mt-0.5" id="delete-cat-message">Konfirmasi hapus kategori</p>
            </div>
        </div>

        <div id="delete-cat-block-warning" class="hidden p-3 rounded-xl bg-cafe-danger/15 border border-cafe-danger/30 text-cafe-danger text-xs">
            Peringatan: Kategori ini tidak bisa dihapus karena masih menampung item menu! Pindahkan atau hapus menu terlebih dahulu.
        </div>

        <form id="delete-cat-form" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-cafe-beige/60">
                <button type="button" onclick="closeDeleteCategoryModal()" class="px-4 py-2 rounded-full text-xs font-semibold text-cafe-dark hover:bg-cafe-beige/60">Batal</button>
                <button type="submit" id="delete-cat-btn" class="px-5 py-2 rounded-full bg-cafe-danger hover:bg-cafe-danger/90 text-beige font-semibold text-xs shadow-sm">Hapus Kategori</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCategoryModal() {
        document.getElementById('category-modal-title').innerText = 'Tambah Kategori Baru';
        document.getElementById('category-form').action = "{{ route('admin.category.store') }}";
        document.getElementById('cat-input-name').value = '';
        document.getElementById('cat-input-icon').value = 'coffee';
        document.getElementById('cat-input-order').value = '{{ $categories->count() + 1 }}';
        document.getElementById('cat-input-desc').value = '';
        document.getElementById('cat-input-active').checked = true;
        document.getElementById('category-modal').classList.remove('hidden');
    }

    function editCategoryModal(cat) {
        document.getElementById('category-modal-title').innerText = `Edit Kategori: ${cat.name}`;
        document.getElementById('category-form').action = `/admin/kategori/${cat.id}/update`;
        document.getElementById('cat-input-name').value = cat.name;
        document.getElementById('cat-input-icon').value = cat.icon || 'coffee';
        document.getElementById('cat-input-order').value = cat.order;
        document.getElementById('cat-input-desc').value = cat.description || '';
        document.getElementById('cat-input-active').checked = Boolean(cat.is_active);
        document.getElementById('category-modal').classList.remove('hidden');
    }

    function closeCategoryModal() {
        document.getElementById('category-modal').classList.add('hidden');
    }

    function openDeleteCategoryModal(id, name, count) {
        const warning = document.getElementById('delete-cat-block-warning');
        const btn = document.getElementById('delete-cat-btn');

        document.getElementById('delete-cat-message').innerText = `Apakah Anda yakin ingin menghapus kategori "${name}"?`;
        document.getElementById('delete-cat-form').action = `/admin/kategori/${id}`;

        if (count > 0) {
            warning.classList.remove('hidden');
            warning.innerText = `Kategori "${name}" masih berisi ${count} menu! Hapus atau pindahkan menu tersebut terlebih dahulu.`;
            btn.classList.add('opacity-50', 'pointer-events-none');
        } else {
            warning.classList.add('hidden');
            btn.classList.remove('opacity-50', 'pointer-events-none');
        }

        document.getElementById('delete-cat-modal').classList.remove('hidden');
    }

    function closeDeleteCategoryModal() {
        document.getElementById('delete-cat-modal').classList.add('hidden');
    }
</script>
@endsection
