{{--
    Manajemen Menu — Menu & Kategori tab partial
    Mobile  : compact list cards, FAB, sticky search + chips + filter sheet
    Tablet  : 2-column list, max-w-3xl centered, FAB pill
    Desktop : unchanged grid/table views with full toolbar

    All routes, JS modal functions, and data logic are untouched.
--}}

{{-- =========================================================
     MOBILE / TABLET LAYOUT  (hidden on lg+)
     ========================================================= --}}
<div class="lg:hidden space-y-0">

    {{-- Sticky search + filter row --}}
    <div class="sticky top-16 z-20 bg-[#F2F5E2] pt-3 pb-2 -mx-3.5 px-3.5 sm:-mx-5 sm:px-5 border-b border-[#E3DEA4]/60">

        {{-- Search bar + action-sheet trigger --}}
        <div class="flex items-center gap-2">
            <div class="flex-1 min-w-0">
                <x-mobile.search-bar
                    name="search"
                    :value="request('search')"
                    placeholder="Cari nama menu..."
                    :action="route('admin.catalog.index')"
                    :hidden-fields="[
                        'tab'         => 'menu',
                        'view'        => $viewMode,
                        'sort'        => request('sort'),
                        'category_id' => request('category_id'),
                        'status'      => request('status'),
                    ]"
                />
            </div>

            {{-- Filter & Sort sheet --}}
            @php
                $activeFilterCount = collect([
                    request('sort') && request('sort') !== 'terlaris' ? 1 : 0,
                    request('status') ? 1 : 0,
                ])->sum();
            @endphp
            <x-mobile.filter-sheet
                id="catalog-filter-sheet"
                :badge-count="$activeFilterCount"
                label="Filter & Urut"
            >
                {{-- Sort options --}}
                <div class="space-y-2">
                    <p class="text-xs font-bold text-[#290024] uppercase tracking-wide">Urutan</p>
                    @foreach([
                        'terlaris'   => 'Terlaris',
                        'nama'       => 'Nama (A-Z)',
                        'harga_asc'  => 'Harga Terendah',
                        'harga_desc' => 'Harga Tertinggi',
                    ] as $sortKey => $sortLabel)
                        <a
                            href="{{ route('admin.catalog.index', array_merge(request()->query(), ['sort' => $sortKey, 'page' => 1])) }}"
                            class="flex items-center justify-between w-full py-2.5 px-4 rounded-2xl text-sm transition-colors {{ request('sort', 'terlaris') === $sortKey ? 'bg-[#290024] text-[#FAFCF3] font-semibold' : 'text-[#290024] hover:bg-[#E3DEA4]/50' }}"
                            onclick="closeBottomSheet('catalog-filter-sheet')"
                        >
                            <span>{{ $sortLabel }}</span>
                            @if(request('sort', 'terlaris') === $sortKey)
                                <i data-lucide="check" class="w-4 h-4"></i>
                            @endif
                        </a>
                    @endforeach
                </div>

                <div class="h-px bg-[#E3DEA4]/60 my-3"></div>

                {{-- Status filter --}}
                <div class="space-y-2">
                    <p class="text-xs font-bold text-[#290024] uppercase tracking-wide">Status</p>
                    @foreach(['all' => 'Semua', 'available' => 'Tersedia', 'unavailable' => 'Habis'] as $statusKey => $statusLabel)
                        <a
                            href="{{ route('admin.catalog.index', array_merge(request()->query(), ['status' => $statusKey === 'all' ? null : $statusKey, 'page' => 1])) }}"
                            class="flex items-center justify-between w-full py-2.5 px-4 rounded-2xl text-sm transition-colors {{ (request('status', 'all') === $statusKey || ($statusKey === 'all' && !request('status'))) ? 'bg-[#290024] text-[#FAFCF3] font-semibold' : 'text-[#290024] hover:bg-[#E3DEA4]/50' }}"
                            onclick="closeBottomSheet('catalog-filter-sheet')"
                        >
                            <span>{{ $statusLabel }}</span>
                            @if(request('status', 'all') === $statusKey || ($statusKey === 'all' && !request('status')))
                                <i data-lucide="check" class="w-4 h-4"></i>
                            @endif
                        </a>
                    @endforeach
                </div>

                <div class="h-px bg-[#E3DEA4]/60 my-3"></div>

                {{-- View mode --}}
                <div class="space-y-2">
                    <p class="text-xs font-bold text-[#290024] uppercase tracking-wide">Tampilan</p>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.catalog.index', array_merge(request()->query(), ['view' => 'table', 'page' => 1])) }}"
                           class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-2xl text-sm font-semibold transition-colors {{ $viewMode === 'table' ? 'bg-[#290024] text-[#FAFCF3]' : 'bg-[#F2F5E2] text-[#290024] border border-[#E3DEA4]' }}"
                           onclick="closeBottomSheet('catalog-filter-sheet')">
                            <i data-lucide="list" class="w-4 h-4"></i> Daftar
                        </a>
                        <a href="{{ route('admin.catalog.index', array_merge(request()->query(), ['view' => 'grid', 'page' => 1])) }}"
                           class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-2xl text-sm font-semibold transition-colors {{ $viewMode === 'grid' ? 'bg-[#290024] text-[#FAFCF3]' : 'bg-[#F2F5E2] text-[#290024] border border-[#E3DEA4]' }}"
                           onclick="closeBottomSheet('catalog-filter-sheet')">
                            <i data-lucide="layout-grid" class="w-4 h-4"></i> Kotak
                        </a>
                    </div>
                </div>

                <x-slot:footer>
                    <a
                        href="{{ route('admin.catalog.index', ['tab' => 'menu']) }}"
                        class="block w-full text-center py-3 rounded-2xl text-sm font-semibold text-[#290024] border border-[#E3DEA4] hover:bg-[#E3DEA4]/60 transition-colors"
                        onclick="closeBottomSheet('catalog-filter-sheet')"
                    >
                        Atur Ulang Filter
                    </a>
                </x-slot:footer>
            </x-mobile.filter-sheet>

            {{-- More-actions (Gabungkan, Kelola Kategori) --}}
            <x-mobile.action-sheet id="catalog-more-actions" label="Aksi Lainnya">
                <button
                    type="button"
                    onclick="closeBottomSheet('catalog-more-actions'); openMergeMenuModal();"
                    class="flex items-center gap-3 w-full px-4 py-3.5 rounded-2xl text-sm font-semibold text-[#290024] hover:bg-[#E3DEA4]/50 transition-colors text-left min-h-[48px]"
                >
                    <i data-lucide="combine" class="w-5 h-5 text-[#775533] shrink-0"></i>
                    <span>Gabungkan Menu</span>
                </button>
                <a
                    href="{{ route('admin.catalog.index', ['tab' => 'menu', '#' => 'kategori']) }}"
                    onclick="closeBottomSheet('catalog-more-actions')"
                    class="flex items-center gap-3 w-full px-4 py-3.5 rounded-2xl text-sm font-semibold text-[#290024] hover:bg-[#E3DEA4]/50 transition-colors text-left min-h-[48px]"
                >
                    <i data-lucide="folder" class="w-5 h-5 text-[#775533] shrink-0"></i>
                    <span>Kelola Kategori</span>
                </a>
            </x-mobile.action-sheet>
        </div>

        {{-- Category chips --}}
        @php
            $chips = array_merge(
                [[
                    'label'  => 'Semua',
                    'count'  => \App\Models\Menu::count(),
                    'href'   => route('admin.catalog.index', array_merge(request()->query(), ['category_id' => 'all', 'page' => 1])),
                    'active' => !request('category_id') || request('category_id') === 'all',
                ]],
                $categories->map(fn ($cat) => [
                    'label'  => $cat->name,
                    'count'  => $cat->menus()->count(),
                    'href'   => route('admin.catalog.index', array_merge(request()->query(), ['category_id' => $cat->id, 'page' => 1])),
                    'active' => request('category_id') == $cat->id,
                ])->toArray()
            );
        @endphp
        <div class="mt-2">
            <x-mobile.chip-scroller :chips="$chips" id="catalog-chips" />
        </div>
    </div>

    {{-- Results summary --}}
    <div class="flex items-center justify-between pt-3 pb-2 text-xs text-[#775533]">
        <span>Menampilkan {{ $menus->firstItem() }}–{{ $menus->lastItem() }} dari {{ $menus->total() }} menu</span>
        @if(request('search'))
            <span class="text-[#290024] font-semibold">Hasil: "{{ request('search') }}"</span>
        @endif
    </div>

    {{-- ---- LIST VIEW (default) ---- --}}
    @if($viewMode !== 'grid')
        <div class="space-y-2 sm:max-w-3xl sm:mx-auto">
            @forelse($menus as $menu)
                <x-mobile.menu-list-item
                    :menu="$menu"
                    :toggle-available-route="route('admin.catalog.menu.toggle.available', $menu->id)"
                    edit-callback="editMenuModal"
                    delete-callback="openDeleteMenuModal"
                    :duplicate-route="route('admin.catalog.menu.duplicate', $menu->id)"
                />
            @empty
                <x-mobile.empty-state
                    icon="coffee"
                    title="Tidak Ada Menu Ditemukan"
                    message="Coba ubah kata kunci pencarian atau atur ulang filter."
                    action-label="Atur Ulang Filter"
                    :action-href="route('admin.catalog.index', ['tab' => 'menu'])"
                />
            @endforelse
        </div>

    {{-- ---- GRID VIEW ---- --}}
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 sm:max-w-3xl sm:mx-auto">
            @forelse($menus as $menu)
                @php $sheetId = 'menu-grid-action-' . $menu->id; @endphp
                <div class="bg-[#FAFCF3] rounded-2xl border border-[#E3DEA4]/60 shadow-sm overflow-hidden flex flex-col">
                    {{-- Image --}}
                    <button
                        type="button"
                        onclick="openBottomSheet('{{ $sheetId }}')"
                        class="relative w-full aspect-[4/3] bg-[#E3DEA4]/40 overflow-hidden focus-visible:outline-none"
                        aria-label="Aksi untuk {{ $menu->name }}"
                    >
                        <img src="{{ $menu->image }}" alt="{{ $menu->name }}" class="w-full h-full object-cover" loading="lazy">
                        @if($menu->is_best_seller)
                            <span class="absolute bottom-0 left-0 right-0 bg-[#D4954D] text-[#290024] text-[8px] font-bold text-center py-0.5">Best Seller</span>
                        @endif
                    </button>

                    {{-- Body --}}
                    <button
                        type="button"
                        onclick="openBottomSheet('{{ $sheetId }}')"
                        class="flex-1 p-2.5 text-left focus-visible:outline-none"
                    >
                        <p class="font-bold text-xs text-[#290024] line-clamp-2 leading-tight">{{ $menu->name }}</p>
                        <p class="text-[10px] text-[#775533] mt-0.5 truncate">{{ $menu->category?->name }}</p>
                        <p class="font-semibold text-xs text-[#290024] mt-1">{{ $menu->priceRangeLabel() }}</p>
                    </button>

                    {{-- Toggle footer --}}
                    <div class="px-2.5 py-2 border-t border-[#E3DEA4]/40 flex items-center justify-between">
                        <span class="text-[10px] font-semibold {{ $menu->is_available ? 'text-[#4F7A4A]' : 'text-[#B03A2E]' }}">
                            {{ $menu->is_available ? 'Tersedia' : 'Habis' }}
                        </span>
                        <form action="{{ route('admin.catalog.menu.toggle.available', $menu->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-9 h-5 rounded-full p-0.5 transition-colors focus-visible:outline-none {{ $menu->is_available ? 'bg-[#4F7A4A]' : 'bg-[#775533]/30' }}">
                                <div class="w-4 h-4 rounded-full bg-white shadow-sm transition-transform {{ $menu->is_available ? 'translate-x-4' : 'translate-x-0' }}"></div>
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Action sheet for grid item --}}
                <x-mobile.menu-list-item
                    :menu="$menu"
                    :toggle-available-route="route('admin.catalog.menu.toggle.available', $menu->id)"
                    edit-callback="editMenuModal"
                    delete-callback="openDeleteMenuModal"
                    :duplicate-route="route('admin.catalog.menu.duplicate', $menu->id)"
                    class="hidden"
                />
            @empty
                <div class="col-span-2 sm:col-span-3">
                    <x-mobile.empty-state
                        icon="coffee"
                        title="Tidak Ada Menu Ditemukan"
                        message="Coba ubah kata kunci pencarian atau atur ulang filter."
                        action-label="Atur Ulang Filter"
                        :action-href="route('admin.catalog.index', ['tab' => 'menu'])"
                    />
                </div>
            @endforelse
        </div>
    @endif

    {{-- Pagination --}}
    @if($menus->hasPages())
        <div class="pt-4 pb-2 sm:max-w-3xl sm:mx-auto">
            {{ $menus->links() }}
        </div>
    @endif

    {{-- Bottom padding to clear FAB + bottom nav --}}
    <div class="h-24"></div>
</div>

{{-- FAB: Tambah Menu (mobile/tablet only) --}}
<x-mobile.fab
    id="catalog-fab-add"
    icon="plus"
    label="Tambah Menu"
    onclick="openMenuModal()"
/>

{{-- =========================================================
     DESKTOP LAYOUT  (hidden below lg)
     ========================================================= --}}
<div class="hidden lg:block space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="font-serif text-xl sm:text-2xl font-bold text-cafe-dark">Katalog Menu</h2>
            <p class="text-xs text-cafe-muted mt-0.5">Kelola racikan kopi, minuman segar, camilan, dan aneka hidangan ABS Cafe</p>
        </div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
            <button type="button" onclick="openMergeMenuModal()" class="w-full sm:w-auto px-5 py-2.5 rounded-full bg-cafe-card border border-cafe-beige text-cafe-dark font-semibold text-xs hover:bg-cafe-beige/60 transition-all flex items-center justify-center gap-2">
                <i data-lucide="combine" class="w-4 h-4 text-cafe-bronze"></i>
                <span>Gabungkan Menu</span>
            </button>
            <button onclick="openMenuModal()" class="w-full sm:w-auto px-5 py-2.5 rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream font-semibold text-xs shadow-md transition-all flex items-center justify-center gap-2">
                <i data-lucide="plus" class="w-4 h-4 text-[#D4954D]"></i>
                <span>Tambah Menu Baru</span>
            </button>
        </div>
    </div>

    {{-- Toolbar --}}
    <div class="bg-cafe-card p-4 sm:p-5 rounded-2xl shadow-soft border border-cafe-beige/40 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
        {{-- Search --}}
        <form method="GET" action="{{ route('admin.catalog.index') }}" class="relative flex-1 max-w-sm">
            <input type="hidden" name="tab" value="menu">
            <input type="hidden" name="view" value="{{ $viewMode }}">
            <input type="hidden" name="category_id" value="{{ request('category_id') }}">
            <input type="hidden" name="sort" value="{{ request('sort') }}">
            <i data-lucide="search" class="w-4 h-4 text-cafe-muted absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama menu atau deskripsi..." class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs text-cafe-text placeholder:text-cafe-muted/70 pl-9 pr-3.5 py-2.5 rounded-full border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all">
        </form>

        {{-- Category Tabs --}}
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 md:pb-0 scrollbar-none">
            <a href="{{ route('admin.catalog.index', array_merge(request()->query(), ['category_id' => 'all'])) }}" class="px-3.5 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap transition-all {{ !request('category_id') || request('category_id') === 'all' ? 'bg-cafe-dark text-cafe-cream shadow-xs' : 'bg-cafe-beige/40 text-cafe-text hover:bg-cafe-beige/70' }}">
                Semua ({{ \App\Models\Menu::count() }})
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('admin.catalog.index', array_merge(request()->query(), ['category_id' => $cat->id])) }}" class="px-3.5 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap transition-all {{ request('category_id') == $cat->id ? 'bg-cafe-dark text-cafe-cream shadow-xs' : 'bg-cafe-beige/40 text-cafe-text hover:bg-cafe-beige/70' }}">
                    {{ $cat->name }} ({{ $cat->menus()->count() }})
                </a>
            @endforeach
        </div>

        {{-- Sort & View Mode --}}
        <div class="flex items-center gap-2 shrink-0">
            <div class="flex items-center gap-1 bg-cafe-beige/40 px-3 py-1.5 rounded-full border border-cafe-beige">
                <i data-lucide="arrow-up-down" class="w-3.5 h-3.5 text-cafe-muted"></i>
                <select onchange="location = this.value;" class="bg-transparent text-xs text-cafe-dark font-medium focus:outline-none cursor-pointer">
                    <option value="{{ route('admin.catalog.index', array_merge(request()->query(), ['sort' => 'terlaris'])) }}" {{ request('sort') == 'terlaris' ? 'selected' : '' }}>Terlaris</option>
                    <option value="{{ route('admin.catalog.index', array_merge(request()->query(), ['sort' => 'nama'])) }}" {{ request('sort') == 'nama' ? 'selected' : '' }}>Nama (A-Z)</option>
                    <option value="{{ route('admin.catalog.index', array_merge(request()->query(), ['sort' => 'harga_asc'])) }}" {{ request('sort') == 'harga_asc' ? 'selected' : '' }}>Harga Terendah</option>
                    <option value="{{ route('admin.catalog.index', array_merge(request()->query(), ['sort' => 'harga_desc'])) }}" {{ request('sort') == 'harga_desc' ? 'selected' : '' }}>Harga Tertinggi</option>
                </select>
            </div>

            <div class="flex items-center bg-cafe-beige/40 p-1 rounded-full border border-cafe-beige">
                <a href="{{ route('admin.catalog.index', array_merge(request()->query(), ['view' => 'grid'])) }}" class="p-1.5 rounded-full transition-all {{ $viewMode === 'grid' ? 'bg-cafe-dark text-cafe-cream shadow-xs' : 'text-cafe-muted hover:text-cafe-dark' }}" title="Grid">
                    <i data-lucide="layout-grid" class="w-4 h-4"></i>
                </a>
                <a href="{{ route('admin.catalog.index', array_merge(request()->query(), ['view' => 'table'])) }}" class="p-1.5 rounded-full transition-all {{ $viewMode === 'table' ? 'bg-cafe-dark text-cafe-cream shadow-xs' : 'text-cafe-muted hover:text-cafe-dark' }}" title="Tabel">
                    <i data-lucide="list" class="w-4 h-4"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- Content: Grid or Table --}}
    @if($viewMode === 'grid')
        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-5">
            @forelse($menus as $menu)
                <div class="bg-cafe-card rounded-2xl shadow-soft hover:shadow-soft-lg border border-cafe-beige/50 overflow-hidden flex flex-col justify-between transition-all group">
                    <div>
                        <div class="relative h-32 sm:h-44 w-full bg-cafe-beige overflow-hidden">
                            <img src="{{ $menu->image }}" alt="{{ $menu->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">

                            <div class="absolute top-3 left-3">
                                <span class="px-2.5 py-1 rounded-full bg-[#290024]/80 backdrop-blur-md text-[#F2F5E2] text-[10px] font-semibold tracking-wide">
                                    {{ $menu->category->name }}
                                </span>
                            </div>

                            @if($menu->is_best_seller)
                                <div class="absolute top-3 right-3">
                                    <span class="px-2.5 py-1 rounded-full bg-cafe-gold text-cafe-dark text-[10px] font-bold shadow-sm flex items-center gap-1">
                                        <i data-lucide="star" class="w-3 h-3 fill-current"></i>
                                        <span>Best Seller</span>
                                    </span>
                                </div>
                            @endif

                            <div class="absolute bottom-3 right-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold backdrop-blur-md shadow-xs {{ $menu->stock <= 0 ? 'bg-cafe-danger text-white' : ($menu->stock <= $menu->min_stock ? 'bg-cafe-warning text-white' : 'bg-black/60 text-white') }}">
                                    Stok: {{ $menu->stock }} {{ $menu->unit }}
                                </span>
                            </div>
                        </div>

                        <div class="p-4 space-y-2">
                            <div class="flex items-start justify-between gap-2">
                                <h3 class="font-serif font-bold text-base text-cafe-dark leading-snug">{{ $menu->name }}</h3>
                                <span class="font-bold text-sm text-cafe-brown shrink-0">{{ $menu->priceRangeLabel() }}</span>
                            </div>
                            @if($menu->variants->isNotEmpty())
                                <div class="flex flex-wrap gap-1">
                                    @foreach($menu->variants as $variant)
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-semibold border {{ $variant->is_available ? 'bg-cafe-success/10 text-cafe-success border-cafe-success/30' : 'bg-cafe-danger/10 text-cafe-danger border-cafe-danger/30' }}">
                                            {{ $variant->displayName() }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                            <p class="text-xs text-cafe-muted line-clamp-2 leading-relaxed">{{ $menu->description }}</p>
                        </div>
                    </div>

                    <div class="p-4 pt-2 border-t border-cafe-beige/40 flex items-center justify-between text-xs">
                        <form action="{{ route('admin.catalog.menu.toggle.available', $menu->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="flex items-center gap-2 cursor-pointer select-none">
                                <div class="w-8 h-4.5 rounded-full p-0.5 transition-colors {{ $menu->is_available ? 'bg-cafe-success' : 'bg-cafe-muted/40' }}">
                                    <div class="w-3.5 h-3.5 rounded-full bg-white transition-transform {{ $menu->is_available ? 'translate-x-3.5' : 'translate-x-0' }}"></div>
                                </div>
                                <span class="text-[11px] font-medium text-cafe-text">{{ $menu->is_available ? 'Tersedia' : 'Habis' }}</span>
                            </button>
                        </form>

                        <div class="flex items-center gap-1">
                            <form action="{{ route('admin.catalog.menu.toggle.bestseller', $menu->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="p-1.5 rounded-full transition-colors {{ $menu->is_best_seller ? 'text-cafe-gold bg-cafe-gold/20' : 'text-cafe-muted hover:text-cafe-dark hover:bg-cafe-beige/60' }}" title="{{ $menu->is_best_seller ? 'Hapus Best Seller' : 'Tandai Best Seller' }}">
                                    <i data-lucide="star" class="w-3.5 h-3.5 {{ $menu->is_best_seller ? 'fill-current' : '' }}"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.catalog.menu.duplicate', $menu->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="p-1.5 rounded-full text-cafe-muted hover:text-cafe-dark hover:bg-cafe-beige/60 transition-colors" title="Duplikasi Menu">
                                    <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                                </button>
                            </form>
                            <button onclick="editMenuModal({{ Illuminate\Support\Js::from($menu) }})" class="p-1.5 rounded-full text-cafe-brown hover:text-cafe-dark hover:bg-cafe-beige/60 transition-colors" title="Edit Menu">
                                <i data-lucide="edit" class="w-3.5 h-3.5"></i>
                            </button>
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
        {{-- Table View --}}
        <x-responsive-table>
            <x-slot:headers>
                <th class="py-3.5 pl-6">Foto & Nama</th>
                <th class="py-3.5 px-3">Kategori</th>
                <th class="py-3.5 px-3">Harga</th>
                <th class="py-3.5 px-3">Stok</th>
                <th class="py-3.5 px-3">Terjual</th>
                <th class="py-3.5 px-3">Best Seller</th>
                <th class="py-3.5 px-3">Status</th>
                <th class="py-3.5 pr-6 text-right">Aksi</th>
            </x-slot:headers>

            @foreach($menus as $idx => $menu)
                <x-responsive-table.row :index="$idx">
                    <x-slot:title>
                        <div class="flex items-center gap-3">
                            <img src="{{ $menu->image }}" alt="{{ $menu->name }}" class="w-12 h-12 rounded-xl object-cover bg-cafe-beige shrink-0 shadow-xs">
                            <div class="min-w-0 flex-1">
                                <p class="font-bold text-xs sm:text-sm text-cafe-dark truncate">{{ $menu->name }}</p>
                                <p class="text-[11px] text-cafe-muted mt-0.5">{{ $menu->category->name }}</p>
                            </div>
                        </div>
                    </x-slot:title>

                    <x-slot:badge>
                        @if($menu->is_best_seller)
                            <span class="px-2 py-0.5 rounded-full bg-cafe-gold text-cafe-dark text-[10px] font-bold shadow-xs inline-flex items-center gap-0.5">
                                <i data-lucide="star" class="w-3 h-3 fill-current"></i> Best
                            </span>
                        @endif
                        <form action="{{ route('admin.catalog.menu.toggle.available', $menu->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold border transition-all {{ $menu->is_available ? 'bg-cafe-success/10 text-cafe-success border-cafe-success/30' : 'bg-cafe-danger/10 text-cafe-danger border-cafe-danger/30' }}">
                                {{ $menu->is_available ? 'Tersedia' : 'Habis' }}
                            </button>
                        </form>
                    </x-slot:badge>

                    <x-slot:fields>
                        <x-responsive-table.field label="Harga" :value="$menu->priceRangeLabel()" />
                        <x-responsive-table.field label="Stok">
                            <span class="{{ $menu->stock <= 0 ? 'text-cafe-danger font-bold' : ($menu->stock <= $menu->min_stock ? 'text-cafe-warning font-bold' : 'text-cafe-text') }}">
                                {{ $menu->stock }} {{ $menu->unit }}
                            </span>
                        </x-responsive-table.field>
                        <x-responsive-table.field label="Terjual" :value="$menu->sold_count . ' porsi'" />
                        @if($menu->variants->isNotEmpty())
                            <x-responsive-table.field label="Varian">
                                <div class="flex flex-wrap gap-1 mt-0.5">
                                    @foreach($menu->variants as $variant)
                                        <span class="px-1.5 py-0.5 rounded-full text-[9px] font-semibold border {{ $variant->is_available ? 'bg-cafe-success/10 text-cafe-success border-cafe-success/30' : 'bg-cafe-danger/10 text-cafe-danger border-cafe-danger/30' }}">{{ $variant->displayName() }}</span>
                                    @endforeach
                                </div>
                            </x-responsive-table.field>
                        @endif
                    </x-slot:fields>

                    @if($menu->description)
                        <x-slot:content>
                            <p class="text-xs text-cafe-muted">{{ $menu->description }}</p>
                        </x-slot:content>
                    @endif

                    <x-slot:actions>
                        <div class="flex items-center gap-1.5 w-full justify-end">
                            <form action="{{ route('admin.catalog.menu.duplicate', $menu->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="p-2 rounded-full hover:bg-cafe-beige/60 text-cafe-muted hover:text-cafe-dark" title="Duplikasi">
                                    <i data-lucide="copy" class="w-4 h-4"></i>
                                </button>
                            </form>
                            <button onclick="editMenuModal({{ Illuminate\Support\Js::from($menu) }})" class="p-2 rounded-full hover:bg-cafe-beige/60 text-cafe-brown hover:text-cafe-dark" title="Edit">
                                <i data-lucide="edit" class="w-4 h-4"></i>
                            </button>
                            <button onclick="openDeleteMenuModal({{ $menu->id }}, '{{ addslashes($menu->name) }}')" class="p-2 rounded-full hover:bg-cafe-danger/10 text-cafe-danger" title="Hapus">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </x-slot:actions>

                    {{-- Desktop Cells --}}
                    <x-slot:desktop>
                        <td class="hidden md:table-cell py-3 pl-6">
                            <div class="flex items-center gap-3">
                                <img src="{{ $menu->image }}" alt="{{ $menu->name }}" class="w-11 h-11 rounded-xl object-cover bg-cafe-beige shrink-0 shadow-xs">
                                <div>
                                    <p class="font-semibold text-cafe-dark text-xs">{{ $menu->name }}</p>
                                    <p class="text-[11px] text-cafe-muted line-clamp-1 max-w-xs">{{ $menu->description }}</p>
                                    @if($menu->variants->isNotEmpty())
                                        <div class="flex flex-wrap gap-1 mt-1">
                                            @foreach($menu->variants as $variant)
                                                <span class="px-1.5 py-0.5 rounded-full text-[9px] font-semibold border {{ $variant->is_available ? 'bg-cafe-success/10 text-cafe-success border-cafe-success/30' : 'bg-cafe-danger/10 text-cafe-danger border-cafe-danger/30' }}">{{ $variant->displayName() }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="hidden md:table-cell py-3 px-3 font-medium text-cafe-text">{{ $menu->category->name }}</td>
                        <td class="hidden md:table-cell py-3 px-3 font-bold text-cafe-dark">{{ $menu->priceRangeLabel() }}</td>
                        <td class="hidden md:table-cell py-3 px-3 font-medium">
                            <span class="{{ $menu->stock <= 0 ? 'text-cafe-danger font-bold' : ($menu->stock <= $menu->min_stock ? 'text-cafe-warning font-bold' : 'text-cafe-text') }}">
                                {{ $menu->stock }} {{ $menu->unit }}
                            </span>
                        </td>
                        <td class="hidden md:table-cell py-3 px-3 text-cafe-muted">{{ $menu->sold_count }} porsi</td>
                        <td class="hidden md:table-cell py-3 px-3">
                            @if($menu->is_best_seller)
                                <span class="text-xs font-bold text-cafe-gold inline-flex items-center gap-1">
                                    <i data-lucide="star" class="w-3.5 h-3.5 fill-current"></i> Ya
                                </span>
                            @else
                                <span class="text-cafe-muted">-</span>
                            @endif
                        </td>
                        <td class="hidden md:table-cell py-3 px-3">
                            <form action="{{ route('admin.catalog.menu.toggle.available', $menu->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-3 py-1 rounded-full text-[11px] font-semibold border transition-all {{ $menu->is_available ? 'bg-cafe-success/10 text-cafe-success border-cafe-success/30' : 'bg-cafe-danger/10 text-cafe-danger border-cafe-danger/30' }}">
                                    {{ $menu->is_available ? 'Tersedia' : 'Habis' }}
                                </button>
                            </form>
                        </td>
                        <td class="hidden md:table-cell py-3 pr-6 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <form action="{{ route('admin.catalog.menu.duplicate', $menu->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="p-1.5 rounded-full hover:bg-cafe-beige/60 text-cafe-muted hover:text-cafe-dark" title="Duplikasi">
                                        <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                                    </button>
                                </form>
                                <button onclick="editMenuModal({{ Illuminate\Support\Js::from($menu) }})" class="p-1.5 rounded-full hover:bg-cafe-beige/60 text-cafe-brown hover:text-cafe-dark" title="Edit">
                                    <i data-lucide="edit" class="w-3.5 h-3.5"></i>
                                </button>
                                <button onclick="openDeleteMenuModal({{ $menu->id }}, '{{ addslashes($menu->name) }}')" class="p-1.5 rounded-full hover:bg-cafe-danger/10 text-cafe-danger" title="Hapus">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                </button>
                            </div>
                        </td>
                    </x-slot:desktop>
                </x-responsive-table.row>
            @endforeach
        </x-responsive-table>
    @endif

    {{-- Desktop pagination --}}
    @if($menus->hasPages())
        <div class="mt-4">
            {{ $menus->links() }}
        </div>
    @endif
</div>


{{-- =========================================================
     SHARED MODALS (mobile + desktop)
     ========================================================= --}}

{{-- MODAL FORM TAMBAH / EDIT MENU --}}
<div id="menu-modal" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-6 overflow-y-auto hidden">
    <div class="fixed inset-0 bg-[#290024]/60 backdrop-blur-sm" onclick="closeMenuModal()"></div>

    <div class="relative w-full max-w-2xl bg-cafe-card rounded-t-[2rem] sm:rounded-2xl shadow-soft-lg border-t sm:border border-cafe-beige/60 overflow-hidden z-10 pb-[max(1rem,env(safe-area-inset-bottom))]">
        <div class="sm:hidden w-full pt-3 pb-1 flex justify-center cursor-pointer" onclick="closeMenuModal()">
            <div class="w-12 h-1 bg-[#775533]/30 rounded-full"></div>
        </div>

        <div class="flex items-center justify-between px-6 py-4 border-b border-cafe-beige/50 bg-[#F2F5E2]/40">
            <div>
                <h3 class="font-serif text-lg sm:text-xl font-bold text-cafe-dark" id="menu-modal-title">Tambah Menu Baru</h3>
                <p class="text-[10px] sm:text-xs text-cafe-muted mt-0.5">Lengkapi informasi produk, harga, ketersediaan, dan stok kafe.</p>
            </div>
            <button onclick="closeMenuModal()" class="w-8 h-8 rounded-full bg-cafe-beige/60 hover:bg-cafe-beige text-cafe-muted hover:text-cafe-dark flex items-center justify-center transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="menu-form" method="POST" action="{{ route('admin.catalog.menu.store') }}" class="p-6 max-h-[75vh] overflow-y-auto space-y-4 text-xs">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="font-semibold text-cafe-dark">Nama Menu / Produk *</label>
                    <input type="text" name="name" id="menu-input-name" required placeholder="mis. Caramel Macchiato" class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                </div>

                <div class="space-y-1">
                    <label class="font-semibold text-cafe-dark">Kategori *</label>
                    <select name="category_id" id="menu-input-category" required class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none cursor-pointer">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="font-semibold text-cafe-dark">Harga Jual (Rp) *</label>
                    <input type="number" name="price" id="menu-input-price" required min="0" step="500" placeholder="20000" class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                </div>

                <div class="space-y-1">
                    <label class="font-semibold text-cafe-dark">Satuan Produk</label>
                    <input type="text" name="unit" id="menu-input-unit" value="porsi" placeholder="porsi, pcs, cup, pack" class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                </div>
            </div>

            <div class="space-y-1">
                <label class="font-semibold text-cafe-dark">Deskripsi Menu</label>
                <textarea name="description" id="menu-input-desc" rows="2" placeholder="Deskripsikan cita rasa, bahan baku, atau porsi menu..." class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none"></textarea>
            </div>

            <div class="space-y-1">
                <label class="font-semibold text-cafe-dark">URL Foto Menu</label>
                <input type="url" name="image" id="menu-input-image" placeholder="https://images.unsplash.com/..." class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
            </div>

            @include('admin.catalog.partials.variant-form')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="font-semibold text-cafe-dark">Stok Awal</label>
                    <input type="number" name="stock" id="menu-input-stock" value="30" min="0" class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                </div>

                <div class="space-y-1">
                    <label class="font-semibold text-cafe-dark">Batas Minimum Peringatan</label>
                    <input type="number" name="min_stock" id="menu-input-min-stock" value="10" min="1" class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-6 pt-2">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" name="is_available" id="menu-input-available" value="1" checked class="w-4 h-4 rounded text-cafe-dark accent-[#290024]">
                    <span class="font-semibold text-cafe-dark">Status Tersedia (Bisa Dipesan)</span>
                </label>

                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" name="is_best_seller" id="menu-input-bestseller" value="1" class="w-4 h-4 rounded text-cafe-gold accent-[#D4954D]">
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

{{-- MODAL KONFIRMASI HAPUS --}}
<div id="delete-menu-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 overflow-y-auto hidden">
    <div class="fixed inset-0 bg-[#290024]/40 backdrop-blur-sm" onclick="closeDeleteMenuModal()"></div>

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

@include('admin.catalog.partials.merge-menu', ['mergeAction' => route('admin.catalog.menu.merge')])

<script>
    function openMenuModal() {
        document.getElementById('menu-modal-title').innerText = 'Tambah Menu / Produk Baru';
        document.getElementById('menu-form').action = "{{ route('admin.catalog.menu.store') }}";
        document.getElementById('menu-input-name').value = '';
        document.getElementById('menu-input-price').value = '20000';
        document.getElementById('menu-input-desc').value = '';
        document.getElementById('menu-input-image').value = 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=500&auto=format&fit=crop&q=80';
        document.getElementById('menu-input-stock').value = '30';
        document.getElementById('menu-input-min-stock').value = '10';
        document.getElementById('menu-input-unit').value = 'porsi';
        document.getElementById('menu-input-available').checked = true;
        document.getElementById('menu-input-bestseller').checked = false;
        setVariantRows([]);
        document.getElementById('menu-modal').classList.remove('hidden');
    }

    function editMenuModal(menu) {
        document.getElementById('menu-modal-title').innerText = `Edit Menu: ${menu.name}`;
        document.getElementById('menu-form').action = `/admin/katalog/menu/${menu.id}/update`;
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
        setVariantRows(menu.variants || []);
        document.getElementById('menu-modal').classList.remove('hidden');
    }

    function closeMenuModal() {
        document.getElementById('menu-modal').classList.add('hidden');
    }

    function openDeleteMenuModal(id, name) {
        document.getElementById('delete-menu-message').innerText = `Apakah Anda yakin ingin menghapus "${name}"? Tindakan ini tidak dapat dibatalkan.`;
        document.getElementById('delete-menu-form').action = `/admin/katalog/menu/${id}`;
        document.getElementById('delete-menu-modal').classList.remove('hidden');
    }

    function closeDeleteMenuModal() {
        document.getElementById('delete-menu-modal').classList.add('hidden');
    }
</script>
