@extends('admin.layouts.app')

@section('title', 'Katalog Menu & Aksesoris')
@section('page_title', 'Menu')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-1.5 text-xs text-cafe-muted mb-3">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-cafe-dark flex items-center gap-1">
            <i data-lucide="home" class="w-3.5 h-3.5"></i>
            <span>Admin</span>
        </a>
        <i data-lucide="chevron-right" class="w-3 h-3 text-cafe-muted/60"></i>
        <span class="font-semibold text-cafe-dark">Menu &amp; Produk</span>
    </nav>

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-serif text-2xl font-bold text-cafe-dark">Katalog Menu &amp; Aksesoris</h2>
            <p class="text-xs text-cafe-muted mt-0.5">Kelola racikan kopi, makanan, minuman non-kopi, dan merchandise resmi ABS Cafe</p>
        </div>

        <div class="flex items-center gap-3">
            <button onclick="openMenuModal()" class="px-5 py-2.5 rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream font-semibold text-xs shadow-md transition-all flex items-center gap-2">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Tambah Menu Baru</span>
            </button>
        </div>
    </div>

    <!-- Toolbar Filters & Grid/Table Toggle -->
    <div class="bg-cafe-card p-4 sm:p-5 rounded-2xl shadow-soft border border-cafe-beige/40 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
        <!-- Search -->
        <form method="GET" action="{{ route('admin.menu.index') }}" class="relative flex-1 max-w-sm">
            <input type="hidden" name="view" value="{{ $viewMode }}">
            <input type="hidden" name="category_id" value="{{ request('category_id') }}">
            <input type="hidden" name="sort" value="{{ request('sort') }}">
            <i data-lucide="search" class="w-4 h-4 text-cafe-muted absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama menu atau deskripsi..." class="w-full bg-[#EFE0CC]/40 focus:bg-white text-xs text-cafe-text placeholder:text-cafe-muted/70 pl-9 pr-3.5 py-2.5 rounded-full border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all">
        </form>

        <!-- Category Tabs -->
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 md:pb-0 scrollbar-none">
            <a href="{{ route('admin.menu.index', array_merge(request()->query(), ['category_id' => 'all'])) }}" class="px-3.5 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap transition-all {{ !request('category_id') || request('category_id') === 'all' ? 'bg-cafe-dark text-cafe-cream shadow-xs' : 'bg-cafe-beige/40 text-cafe-text hover:bg-cafe-beige/70' }}">
                Semua ({{ \App\Models\Menu::count() }})
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('admin.menu.index', array_merge(request()->query(), ['category_id' => $cat->id])) }}" class="px-3.5 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap transition-all {{ request('category_id') == $cat->id ? 'bg-cafe-dark text-cafe-cream shadow-xs' : 'bg-cafe-beige/40 text-cafe-text hover:bg-cafe-beige/70' }}">
                    {{ $cat->name }} ({{ $cat->menus()->count() }})
                </a>
            @endforeach
        </div>

        <!-- Sort & View Mode Toggle -->
        <div class="flex items-center gap-2 shrink-0">
            <div class="flex items-center gap-1 bg-cafe-beige/40 px-3 py-1.5 rounded-full border border-cafe-beige">
                <i data-lucide="arrow-up-down" class="w-3.5 h-3.5 text-cafe-muted"></i>
                <select onchange="location = this.value;" class="bg-transparent text-xs text-cafe-dark font-medium focus:outline-none cursor-pointer">
                    <option value="{{ route('admin.menu.index', array_merge(request()->query(), ['sort' => 'terlaris'])) }}" {{ request('sort') == 'terlaris' ? 'selected' : '' }}>Terlaris</option>
                    <option value="{{ route('admin.menu.index', array_merge(request()->query(), ['sort' => 'nama'])) }}" {{ request('sort') == 'nama' ? 'selected' : '' }}>Nama (A-Z)</option>
                    <option value="{{ route('admin.menu.index', array_merge(request()->query(), ['sort' => 'harga_asc'])) }}" {{ request('sort') == 'harga_asc' ? 'selected' : '' }}>Harga Terendah</option>
                    <option value="{{ route('admin.menu.index', array_merge(request()->query(), ['sort' => 'harga_desc'])) }}" {{ request('sort') == 'harga_desc' ? 'selected' : '' }}>Harga Tertinggi</option>
                </select>
            </div>

            <div class="flex items-center bg-cafe-beige/40 p-1 rounded-full border border-cafe-beige">
                <a href="{{ route('admin.menu.index', array_merge(request()->query(), ['view' => 'grid'])) }}" class="p-1.5 rounded-full transition-all {{ $viewMode === 'grid' ? 'bg-cafe-dark text-cafe-cream shadow-xs' : 'text-cafe-muted hover:text-cafe-dark' }}" title="Grid">
                    <i data-lucide="layout-grid" class="w-4 h-4"></i>
                </a>
                <a href="{{ route('admin.menu.index', array_merge(request()->query(), ['view' => 'table'])) }}" class="p-1.5 rounded-full transition-all {{ $viewMode === 'table' ? 'bg-cafe-dark text-cafe-cream shadow-xs' : 'text-cafe-muted hover:text-cafe-dark' }}" title="Tabel">
                    <i data-lucide="list" class="w-4 h-4"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Content: Grid View or Table View -->
    @if($viewMode === 'grid')
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
            @forelse($menus as $menu)
                <div class="bg-cafe-card rounded-2xl shadow-soft hover:shadow-soft-lg border border-cafe-beige/50 overflow-hidden flex flex-col justify-between transition-all group">
                    <div>
                        <!-- Image Container -->
                        <div class="relative h-44 w-full bg-cafe-beige overflow-hidden">
                            <img src="{{ $menu->image }}" alt="{{ $menu->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">

                            <!-- Category Badge -->
                            <div class="absolute top-3 left-3">
                                <span class="px-2.5 py-1 rounded-full bg-[#4A1F14]/80 backdrop-blur-md text-[#F6EDE0] text-[10px] font-semibold tracking-wide">
                                    {{ $menu->category->name }}
                                </span>
                            </div>

                            <!-- Best Seller Badge -->
                            @if($menu->is_best_seller)
                                <div class="absolute top-3 right-3">
                                    <span class="px-2.5 py-1 rounded-full bg-cafe-gold text-cafe-dark text-[10px] font-bold shadow-sm flex items-center gap-1">
                                        <i data-lucide="star" class="w-3 h-3 fill-current"></i>
                                        <span>Best Seller</span>
                                    </span>
                                </div>
                            @endif

                            <!-- Stock Pill -->
                            <div class="absolute bottom-3 right-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold backdrop-blur-md shadow-xs {{ $menu->stock <= 0 ? 'bg-cafe-danger text-white' : ($menu->stock <= $menu->min_stock ? 'bg-cafe-warning text-white' : 'bg-black/60 text-white') }}">
                                    Stok: {{ $menu->stock }} {{ $menu->unit }}
                                </span>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="p-4 space-y-2">
                            <div class="flex items-start justify-between gap-2">
                                <h3 class="font-serif font-bold text-base text-cafe-dark leading-snug">{{ $menu->name }}</h3>
                                <span class="font-bold text-sm text-cafe-brown shrink-0">Rp {{ number_format($menu->price, 0, ',', '.') }}</span>
                            </div>
                            <p class="text-xs text-cafe-muted line-clamp-2 leading-relaxed">{{ $menu->description }}</p>
                        </div>
                    </div>

                    <!-- Card Actions -->
                    <div class="p-4 pt-2 border-t border-cafe-beige/40 flex items-center justify-between text-xs">
                        <!-- Switch Tersedia / Habis -->
                        <form action="{{ route('admin.menu.toggle.available', $menu->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="flex items-center gap-2 cursor-pointer select-none">
                                <div class="w-8 h-4.5 rounded-full p-0.5 transition-colors {{ $menu->is_available ? 'bg-cafe-success' : 'bg-cafe-muted/40' }}">
                                    <div class="w-3.5 h-3.5 rounded-full bg-white transition-transform {{ $menu->is_available ? 'translate-x-3.5' : 'translate-x-0' }}"></div>
                                </div>
                                <span class="text-[11px] font-medium text-cafe-text">{{ $menu->is_available ? 'Tersedia' : 'Habis' }}</span>
                            </button>
                        </form>

                        <div class="flex items-center gap-1">
                            <!-- Toggle Best Seller -->
                            <form action="{{ route('admin.menu.toggle.bestseller', $menu->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="p-1.5 rounded-full transition-colors {{ $menu->is_best_seller ? 'text-cafe-gold bg-cafe-gold/20' : 'text-cafe-muted hover:text-cafe-dark hover:bg-cafe-beige/60' }}" title="{{ $menu->is_best_seller ? 'Hapus Best Seller' : 'Tandai Best Seller' }}">
                                    <i data-lucide="star" class="w-3.5 h-3.5 {{ $menu->is_best_seller ? 'fill-current' : '' }}"></i>
                                </button>
                            </form>

                            <!-- Duplicate -->
                            <form action="{{ route('admin.menu.duplicate', $menu->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="p-1.5 rounded-full text-cafe-muted hover:text-cafe-dark hover:bg-cafe-beige/60 transition-colors" title="Duplikasi Menu">
                                    <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                                </button>
                            </form>

                            <!-- Edit -->
                            <button onclick="editMenuModal({{ json_encode($menu) }})" class="p-1.5 rounded-full text-cafe-brown hover:text-cafe-dark hover:bg-cafe-beige/60 transition-colors" title="Edit Menu">
                                <i data-lucide="edit" class="w-3.5 h-3.5"></i>
                            </button>

                            <!-- Delete -->
                            <button onclick="openDeleteMenuModal({{ $menu->id }}, '{{ addslashes($menu->name) }}')" class="p-1.5 rounded-full text-cafe-danger hover:bg-cafe-danger/10 transition-colors" title="Hapus Menu">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-4 p-12 text-center bg-cafe-card/50 rounded-2xl border border-dashed border-cafe-beige/80">
                    <i data-lucide="coffee" class="w-10 h-10 text-cafe-muted mx-auto mb-2"></i>
                    <h4 class="font-serif font-bold text-base text-cafe-dark">Tidak Ada Menu Ditemukan</h4>
                    <p class="text-xs text-cafe-muted mt-1">Coba ganti filter atau tambahkan menu baru sekarang.</p>
                </div>
            @endforelse
        </div>
    @else
        <!-- Table View -->
        <div class="bg-cafe-card rounded-2xl shadow-soft border border-cafe-beige/40 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#F6EDE0]/70 border-b border-cafe-beige text-cafe-muted uppercase font-semibold text-[11px]">
                        <tr>
                            <th class="py-3.5 pl-6">Foto &amp; Nama</th>
                            <th class="py-3.5 px-3">Kategori</th>
                            <th class="py-3.5 px-3">Harga</th>
                            <th class="py-3.5 px-3">Stok</th>
                            <th class="py-3.5 px-3">Terjual</th>
                            <th class="py-3.5 px-3">Best Seller</th>
                            <th class="py-3.5 px-3">Status</th>
                            <th class="py-3.5 pr-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-cafe-beige/30">
                        @foreach($menus as $idx => $menu)
                            <tr class="hover:bg-cafe-cream/80 transition-colors {{ $idx % 2 === 1 ? 'bg-[#FBF6EE]/40' : '' }}">
                                <td class="py-3 pl-6">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $menu->image }}" alt="{{ $menu->name }}" class="w-11 h-11 rounded-xl object-cover bg-cafe-beige shrink-0 shadow-xs">
                                        <div>
                                            <p class="font-semibold text-cafe-dark text-xs">{{ $menu->name }}</p>
                                            <p class="text-[11px] text-cafe-muted line-clamp-1 max-w-xs">{{ $menu->description }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-3 font-medium text-cafe-text">{{ $menu->category->name }}</td>
                                <td class="py-3 px-3 font-bold text-cafe-dark">Rp {{ number_format($menu->price, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 font-medium">
                                    <span class="{{ $menu->stock <= 0 ? 'text-cafe-danger font-bold' : ($menu->stock <= $menu->min_stock ? 'text-cafe-warning font-bold' : 'text-cafe-text') }}">
                                        {{ $menu->stock }} {{ $menu->unit }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-cafe-muted">{{ $menu->sold_count }} porsi</td>
                                <td class="py-3 px-3">
                                    @if($menu->is_best_seller)
                                        <span class="text-xs font-bold text-cafe-gold inline-flex items-center gap-1">
                                            <i data-lucide="star" class="w-3.5 h-3.5 fill-current"></i> Ya
                                        </span>
                                    @else
                                        <span class="text-cafe-muted">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3">
                                    <form action="{{ route('admin.menu.toggle.available', $menu->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 rounded-full text-[11px] font-semibold border transition-all {{ $menu->is_available ? 'bg-cafe-success/10 text-cafe-success border-cafe-success/30' : 'bg-cafe-danger/10 text-cafe-danger border-cafe-danger/30' }}">
                                            {{ $menu->is_available ? 'Tersedia' : 'Habis' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="py-3 pr-6 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <form action="{{ route('admin.menu.duplicate', $menu->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-1.5 rounded-full hover:bg-cafe-beige/60 text-cafe-muted hover:text-cafe-dark" title="Duplikasi">
                                                <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                                            </button>
                                        </form>
                                        <button onclick="editMenuModal({{ json_encode($menu) }})" class="p-1.5 rounded-full hover:bg-cafe-beige/60 text-cafe-brown hover:text-cafe-dark" title="Edit">
                                            <i data-lucide="edit" class="w-3.5 h-3.5"></i>
                                        </button>
                                        <button onclick="openDeleteMenuModal({{ $menu->id }}, '{{ addslashes($menu->name) }}')" class="p-1.5 rounded-full hover:bg-cafe-danger/10 text-cafe-danger" title="Hapus">
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
    @endif
</div>

<!-- MODAL FORM TAMBAH / EDIT MENU -->
<div id="menu-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 overflow-y-auto hidden">
    <div class="fixed inset-0 bg-[#3B1E14]/40 backdrop-blur-sm" onclick="closeMenuModal()"></div>

    <div class="relative w-full max-w-2xl bg-cafe-card rounded-2xl shadow-soft-lg border border-cafe-beige/60 overflow-hidden z-10 my-8">
        <div class="flex items-center justify-between px-6 py-5 border-b border-cafe-beige/50 bg-[#F6EDE0]/40">
            <div>
                <h3 class="font-serif text-xl font-bold text-cafe-dark" id="menu-modal-title">Tambah Menu Baru</h3>
                <p class="text-xs text-cafe-muted mt-0.5">Lengkapi informasi produk, harga, ketersediaan, dan stok kafe.</p>
            </div>
            <button onclick="closeMenuModal()" class="w-8 h-8 rounded-full bg-cafe-beige/60 hover:bg-cafe-beige text-cafe-muted hover:text-cafe-dark flex items-center justify-center transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="menu-form" method="POST" action="{{ route('admin.menu.store') }}" class="p-6 max-h-[75vh] overflow-y-auto space-y-4 text-xs">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="font-semibold text-cafe-dark">Nama Menu / Produk *</label>
                    <input type="text" name="name" id="menu-input-name" required placeholder="mis. Caramel Macchiato" class="w-full bg-[#EFE0CC]/40 focus:bg-white text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                </div>

                <div class="space-y-1">
                    <label class="font-semibold text-cafe-dark">Kategori *</label>
                    <select name="category_id" id="menu-input-category" required class="w-full bg-[#EFE0CC]/40 focus:bg-white text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none cursor-pointer">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="font-semibold text-cafe-dark">Harga Jual (Rp) *</label>
                    <input type="number" name="price" id="menu-input-price" required min="0" step="500" placeholder="20000" class="w-full bg-[#EFE0CC]/40 focus:bg-white text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                </div>

                <div class="space-y-1">
                    <label class="font-semibold text-cafe-dark">Satuan Produk</label>
                    <input type="text" name="unit" id="menu-input-unit" value="porsi" placeholder="porsi, pcs, cup, pack" class="w-full bg-[#EFE0CC]/40 focus:bg-white text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                </div>
            </div>

            <div class="space-y-1">
                <label class="font-semibold text-cafe-dark">Deskripsi Menu</label>
                <textarea name="description" id="menu-input-desc" rows="2" placeholder="Deskripsikan cita rasa, bahan baku, atau spesifikasi aksesoris..." class="w-full bg-[#EFE0CC]/40 focus:bg-white text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none"></textarea>
            </div>

            <div class="space-y-1">
                <label class="font-semibold text-cafe-dark">URL Foto Menu</label>
                <input type="url" name="image" id="menu-input-image" placeholder="https://images.unsplash.com/..." class="w-full bg-[#EFE0CC]/40 focus:bg-white text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="font-semibold text-cafe-dark">Stok Awal</label>
                    <input type="number" name="stock" id="menu-input-stock" value="30" min="0" class="w-full bg-[#EFE0CC]/40 focus:bg-white text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                </div>

                <div class="space-y-1">
                    <label class="font-semibold text-cafe-dark">Batas Minimum Peringatan</label>
                    <input type="number" name="min_stock" id="menu-input-min-stock" value="10" min="1" class="w-full bg-[#EFE0CC]/40 focus:bg-white text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-6 pt-2">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" name="is_available" id="menu-input-available" value="1" checked class="w-4 h-4 rounded text-cafe-dark accent-[#4A1F14]">
                    <span class="font-semibold text-cafe-dark">Status Tersedia (Bisa Dipesan)</span>
                </label>

                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" name="is_best_seller" id="menu-input-bestseller" value="1" class="w-4 h-4 rounded text-cafe-gold accent-[#D4A76A]">
                    <span class="font-semibold text-cafe-dark flex items-center gap-1">
                        <i data-lucide="star" class="w-3.5 h-3.5 fill-current text-cafe-gold"></i>
                        <span>Tandai sebagai Best Seller</span>
                    </span>
                </label>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-cafe-beige">
                <button type="button" onclick="closeMenuModal()" class="px-5 py-2.5 rounded-full text-xs font-semibold text-cafe-dark hover:bg-cafe-beige/60">Batal</button>
                <button type="submit" class="px-6 py-2.5 rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream font-semibold text-xs shadow-md transition-all">Simpan Menu</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL KONFIRMASI HAPUS -->
<div id="delete-menu-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 overflow-y-auto hidden">
    <div class="fixed inset-0 bg-[#3B1E14]/40 backdrop-blur-sm" onclick="closeDeleteMenuModal()"></div>

    <div class="relative w-full max-w-md bg-cafe-card rounded-2xl shadow-soft-lg border border-cafe-beige/60 overflow-hidden z-10 my-8 p-6 space-y-4">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-2xl bg-cafe-danger/10 text-cafe-danger flex items-center justify-center shrink-0">
                <i data-lucide="alert-circle" class="w-5 h-5"></i>
            </div>
            <div>
                <h4 class="font-serif text-lg font-bold text-cafe-dark">Hapus Menu</h4>
                <p class="text-xs text-cafe-muted mt-0.5" id="delete-menu-message">Apakah Anda yakin ingin menghapus menu ini?</p>
            </div>
        </div>

        <form id="delete-menu-form" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-cafe-beige/60">
                <button type="button" onclick="closeDeleteMenuModal()" class="px-4 py-2 rounded-full text-xs font-semibold text-cafe-dark hover:bg-cafe-beige/60">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-full bg-cafe-danger hover:bg-cafe-danger/90 text-white font-semibold text-xs shadow-sm">Hapus Permanen</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openMenuModal() {
        document.getElementById('menu-modal-title').innerText = 'Tambah Menu / Produk Baru';
        document.getElementById('menu-form').action = "{{ route('admin.menu.store') }}";
        document.getElementById('menu-input-name').value = '';
        document.getElementById('menu-input-price').value = '20000';
        document.getElementById('menu-input-desc').value = '';
        document.getElementById('menu-input-image').value = 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=500&auto=format&fit=crop&q=80';
        document.getElementById('menu-input-stock').value = '30';
        document.getElementById('menu-input-min-stock').value = '10';
        document.getElementById('menu-input-unit').value = 'porsi';
        document.getElementById('menu-input-available').checked = true;
        document.getElementById('menu-input-bestseller').checked = false;
        document.getElementById('menu-modal').classList.remove('hidden');
    }

    function editMenuModal(menu) {
        document.getElementById('menu-modal-title').innerText = `Edit Menu: ${menu.name}`;
        document.getElementById('menu-form').action = `/admin/menu/${menu.id}/update`;
        document.getElementById('menu-input-name').value = menu.name;
        document.getElementById('menu-input-category').value = menu.category_id;
        document.getElementById('menu-input-price').value = parseInt(menu.price);
        document.getElementById('menu-input-desc').value = menu.description || '';
        document.getElementById('menu-input-image').value = menu.image || '';
        document.getElementById('menu-input-stock').value = menu.stock;
        document.getElementById('menu-input-min-stock').value = menu.min_stock;
        document.getElementById('menu-input-unit').value = menu.unit;
        document.getElementById('menu-input-available').checked = Boolean(menu.is_available);
        document.getElementById('menu-input-bestseller').checked = Boolean(menu.is_best_seller);
        document.getElementById('menu-modal').classList.remove('hidden');
    }

    function closeMenuModal() {
        document.getElementById('menu-modal').classList.add('hidden');
    }

    function openDeleteMenuModal(id, name) {
        document.getElementById('delete-menu-message').innerText = `Apakah Anda yakin ingin menghapus "${name}"? Tindakan ini tidak dapat dibatalkan.`;
        document.getElementById('delete-menu-form').action = `/admin/menu/${id}`;
        document.getElementById('delete-menu-modal').classList.remove('hidden');
    }

    function closeDeleteMenuModal() {
        document.getElementById('delete-menu-modal').classList.add('hidden');
    }
</script>
@endsection
