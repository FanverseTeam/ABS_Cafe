@extends('admin.layouts.app')

@section('title', 'Tambah Menu Baru')
@section('page_title', 'Tambah Menu Baru')

@section('content')
<div class="max-w-3xl mx-auto space-y-5 sm:space-y-6">
    <!-- Breadcrumb -->
    <x-breadcrumb
        :items="[
            ['label' => 'Admin', 'url' => route('admin.dashboard'), 'icon' => 'home'],
            ['label' => 'Manajemen Menu', 'url' => route('admin.catalog.index')],
            ['label' => 'Tambah Baru'],
        ]"
    />

    <!-- Header with Back Button -->
    <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-3 min-w-0">
            <a href="{{ route('admin.catalog.index') }}"
               class="flex h-9 w-9 sm:h-10 sm:w-10 items-center justify-center rounded-2xl bg-cafe-beige/60 text-cafe-dark hover:bg-cafe-beige transition-all active:scale-95 shrink-0"
               title="Kembali ke Daftar Menu">
                <i data-lucide="arrow-left" class="w-4 h-4 sm:w-5 sm:h-5"></i>
            </a>
            <div class="min-w-0">
                <h2 class="font-serif text-lg sm:text-2xl font-bold text-cafe-dark truncate">Tambah Menu Baru</h2>
                <p class="text-[11px] sm:text-xs text-cafe-muted truncate">Tambahkan produk, minuman, atau makanan ke katalog kafe</p>
            </div>
        </div>
    </div>

    <!-- Error Summary if any -->
    @if($errors->any())
        <div class="p-4 rounded-2xl bg-[#775533]/10 border border-[#775533]/30 text-cafe-danger text-xs space-y-1 animate-in fade-in">
            <div class="flex items-center gap-2 font-bold">
                <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
                <span>Terdapat kesalahan pada formulir:</span>
            </div>
            <ul class="list-disc list-inside pl-1 space-y-0.5 text-[11px]">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Card -->
    <div class="bg-cafe-card rounded-2xl sm:rounded-3xl border border-cafe-beige/60 shadow-soft overflow-hidden">
        <form id="menu-form" method="POST" action="{{ route('admin.catalog.menu.store') }}" class="p-5 sm:p-8 space-y-6 text-xs">
            @csrf

            <!-- Section 1: Informasi Dasar -->
            <div>
                <h4 class="font-serif font-bold text-xs sm:text-sm text-cafe-dark flex items-center gap-2 pb-3 border-b border-cafe-beige/40">
                    <span class="w-6 h-6 rounded-lg bg-cafe-gold/20 text-cafe-gold flex items-center justify-center">
                        <i data-lucide="coffee" class="w-3.5 h-3.5"></i>
                    </span>
                    Informasi Produk
                </h4>

                <div class="mt-4 space-y-4">
                    <!-- Nama Menu -->
                    <div class="space-y-1">
                        <label class="font-semibold text-cafe-dark block text-xs">
                            Nama Menu / Produk <span class="text-cafe-danger">*</span>
                        </label>
                        <input type="text"
                               name="name"
                               value="{{ old('name') }}"
                               required
                               placeholder="misal: Caramel Macchiato"
                               class="w-full bg-[#F2F5E2] focus:bg-beige text-xs sm:text-sm text-cafe-dark px-3.5 py-2.5 sm:py-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:ring-1 focus:ring-cafe-gold/40 focus:outline-none transition-all shadow-sm">
                    </div>

                    <!-- Grid: Kategori & Satuan -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="font-semibold text-cafe-dark block text-xs">
                                Kategori <span class="text-cafe-danger">*</span>
                            </label>
                            <select name="category_id" required class="w-full bg-[#F2F5E2] focus:bg-beige text-xs sm:text-sm text-cafe-dark px-3.5 py-2.5 sm:py-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:ring-1 focus:ring-cafe-gold/40 focus:outline-none transition-all shadow-sm cursor-pointer">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-1">
                            <label class="font-semibold text-cafe-dark block text-xs">
                                Satuan Produk
                            </label>
                            <input type="text"
                                   name="unit"
                                   value="{{ old('unit', 'porsi') }}"
                                   placeholder="porsi, cup, pcs, pack"
                                   class="w-full bg-[#F2F5E2] focus:bg-beige text-xs sm:text-sm text-cafe-dark px-3.5 py-2.5 sm:py-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:ring-1 focus:ring-cafe-gold/40 focus:outline-none transition-all shadow-sm">
                        </div>
                    </div>

                    <!-- Harga Jual -->
                    <div class="space-y-1">
                        <label class="font-semibold text-cafe-dark block text-xs">
                            Harga Jual Dasar (Rp) <span class="text-cafe-danger">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-cafe-muted">Rp</span>
                            <input type="number"
                                   name="price"
                                   value="{{ old('price', 20000) }}"
                                   required
                                   min="0"
                                   step="500"
                                   placeholder="20000"
                                   class="w-full bg-[#F2F5E2] focus:bg-beige text-xs sm:text-sm text-cafe-dark pl-10 pr-3.5 py-2.5 sm:py-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:ring-1 focus:ring-cafe-gold/40 focus:outline-none transition-all shadow-sm">
                        </div>
                        <p class="text-[10px] text-cafe-muted">Jika memiliki varian, harga menu akan otomatis mengikuti harga varian terendah.</p>
                    </div>

                    <!-- Deskripsi -->
                    <div class="space-y-1">
                        <label class="font-semibold text-cafe-dark block text-xs">Deskripsi Menu</label>
                        <textarea name="description"
                                  rows="2"
                                  placeholder="Deskripsikan cita rasa, bahan baku, atau porsi menu..."
                                  class="w-full bg-[#F2F5E2] focus:bg-beige text-xs sm:text-sm text-cafe-dark p-3.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:ring-1 focus:ring-cafe-gold/40 focus:outline-none transition-all shadow-sm">{{ old('description') }}</textarea>
                    </div>

                    <!-- Foto Menu URL -->
                    <div class="space-y-1">
                        <label class="font-semibold text-cafe-dark block text-xs">URL Foto Menu</label>
                        <input type="url"
                               name="image"
                               value="{{ old('image', 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=500&auto=format&fit=crop&q=80') }}"
                               placeholder="https://images.unsplash.com/..."
                               class="w-full bg-[#F2F5E2] focus:bg-beige text-xs sm:text-sm text-cafe-dark px-3.5 py-2.5 sm:py-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:ring-1 focus:ring-cafe-gold/40 focus:outline-none transition-all shadow-sm">
                    </div>
                </div>
            </div>

            <!-- Section 2: Varian Menu -->
            <div>
                <h4 class="font-serif font-bold text-xs sm:text-sm text-cafe-dark flex items-center gap-2 pb-3 border-b border-cafe-beige/40">
                    <span class="w-6 h-6 rounded-lg bg-cafe-gold/20 text-cafe-gold flex items-center justify-center">
                        <i data-lucide="layers" class="w-3.5 h-3.5"></i>
                    </span>
                    Varian Menu (Opsional)
                </h4>

                <div class="mt-4">
                    @include('admin.catalog.partials.variant-form')
                </div>
            </div>

            <!-- Section 3: Stok & Ketersediaan -->
            <div>
                <h4 class="font-serif font-bold text-xs sm:text-sm text-cafe-dark flex items-center gap-2 pb-3 border-b border-cafe-beige/40">
                    <span class="w-6 h-6 rounded-lg bg-cafe-gold/20 text-cafe-gold flex items-center justify-center">
                        <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                    </span>
                    Stok &amp; Status
                </h4>

                <div class="mt-4 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="font-semibold text-cafe-dark block text-xs">Stok Awal</label>
                            <input type="number"
                                   name="stock"
                                   value="{{ old('stock', 30) }}"
                                   min="0"
                                   class="w-full bg-[#F2F5E2] focus:bg-beige text-xs sm:text-sm text-cafe-dark px-3.5 py-2.5 sm:py-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:ring-1 focus:ring-cafe-gold/40 focus:outline-none transition-all shadow-sm">
                        </div>

                        <div class="space-y-1">
                            <label class="font-semibold text-cafe-dark block text-xs">Batas Minimum Peringatan</label>
                            <input type="number"
                                   name="min_stock"
                                   value="{{ old('min_stock', 10) }}"
                                   min="1"
                                   class="w-full bg-[#F2F5E2] focus:bg-beige text-xs sm:text-sm text-cafe-dark px-3.5 py-2.5 sm:py-3 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:ring-1 focus:ring-cafe-gold/40 focus:outline-none transition-all shadow-sm">
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-6 pt-2">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox"
                                   name="is_available"
                                   value="1"
                                   {{ old('is_available', '1') == '1' ? 'checked' : '' }}
                                   class="w-4 h-4 rounded text-cafe-dark accent-[#290024]">
                            <span class="font-semibold text-cafe-dark">Status Tersedia (Bisa Dipesan)</span>
                        </label>

                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox"
                                   name="is_best_seller"
                                   value="1"
                                   {{ old('is_best_seller') == '1' ? 'checked' : '' }}
                                   class="w-4 h-4 rounded text-cafe-gold accent-[#D4954D]">
                            <span class="font-semibold text-cafe-dark flex items-center gap-1">
                                <i data-lucide="star" class="w-3.5 h-3.5 fill-current text-cafe-gold"></i>
                                <span>Tandai sebagai Best Seller</span>
                            </span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-cafe-beige/60">
                <a href="{{ route('admin.catalog.index') }}"
                   class="px-5 py-2.5 rounded-full text-xs font-semibold text-cafe-dark hover:bg-cafe-beige/60 transition-all">
                    Batal
                </a>
                <button type="submit"
                        class="px-6 py-2.5 rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream font-semibold text-xs shadow-md transition-all active:scale-95 flex items-center gap-2">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Simpan Menu</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
