<div class="space-y-6">

    <!-- Page Header & Action (Desktop only; mobile header handled by layout) -->
    <div class="hidden lg:flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-serif text-2xl font-bold text-cafe-dark">Inventaris &amp; Kontrol Stok</h2>
            <p class="text-xs text-cafe-muted mt-0.5">Pantau ketersediaan bahan, status menipis/habis, serta log pergerakan stok</p>
        </div>

        <button onclick="openHistoryLogsModal()" class="px-5 py-2.5 rounded-full bg-cafe-card hover:bg-cafe-beige text-cafe-dark font-semibold text-xs border border-cafe-beige/80 transition-all flex items-center gap-2 shadow-xs">
            <i data-lucide="history" class="w-4 h-4 text-cafe-muted"></i>
            <span>Riwayat Pergerakan Stok</span>
        </button>
    </div>

    {{-- MOBILE / TABLET CONTROLS (lg:hidden) --}}
    <div class="lg:hidden space-y-2.5">
        {{-- Sticky Search Bar --}}
        <x-mobile.search-bar
            name="stock_search"
            value="{{ request('stock_search') }}"
            placeholder="Cari menu/bahan..."
            :hiddenFields="[
                'tab' => 'stock',
                'stock_category_id' => request('stock_category_id', 'all'),
                'stock_status' => request('stock_status', 'all'),
            ]"
        />

        {{-- Single-row filter: Status Chips + Category Filter Sheet --}}
        <div class="flex items-center gap-2">
            <div class="flex-1 min-w-0">
                @php
                    $statusOptions = [
                        ['label' => 'Semua', 'val' => 'all'],
                        ['label' => 'Aman', 'val' => 'aman'],
                        ['label' => 'Menipis', 'val' => 'menipis'],
                        ['label' => 'Habis', 'val' => 'habis'],
                    ];
                    $currentStatus = request('stock_status', 'all');
                    $chips = array_map(function($opt) use ($currentStatus) {
                        return [
                            'label'  => $opt['label'],
                            'href'   => route('admin.catalog.index', array_merge(request()->except('page'), ['tab' => 'stock', 'stock_status' => $opt['val']])),
                            'active' => $currentStatus === $opt['val'],
                        ];
                    }, $statusOptions);
                @endphp
                <x-mobile.chip-scroller :chips="$chips" />
            </div>

            {{-- Category Filter Sheet --}}
            @php
                $catActive = request('stock_category_id') && request('stock_category_id') !== 'all';
            @endphp
            <x-mobile.filter-sheet id="stock-cat-sheet" :badgeCount="$catActive ? 1 : 0" label="Kategori">
                <form method="GET" action="{{ route('admin.catalog.index') }}" class="space-y-4">
                    <input type="hidden" name="tab" value="stock">
                    @if(request('stock_search'))
                        <input type="hidden" name="stock_search" value="{{ request('stock_search') }}">
                    @endif
                    @if(request('stock_status'))
                        <input type="hidden" name="stock_status" value="{{ request('stock_status') }}">
                    @endif

                    <div>
                        <label class="block text-xs font-bold text-cafe-dark mb-2">Filter Kategori</label>
                        <div class="space-y-1.5 max-h-60 overflow-y-auto">
                            <label class="flex items-center gap-2 p-2.5 rounded-xl border border-cafe-beige/60 bg-cafe-beige/20 text-xs cursor-pointer">
                                <input type="radio" name="stock_category_id" value="all" {{ !request('stock_category_id') || request('stock_category_id') === 'all' ? 'checked' : '' }} class="text-cafe-dark focus:ring-0">
                                <span class="font-semibold text-cafe-dark">Semua Kategori</span>
                            </label>
                            @foreach($categories as $cat)
                                <label class="flex items-center gap-2 p-2.5 rounded-xl border border-cafe-beige/60 bg-cafe-beige/20 text-xs cursor-pointer">
                                    <input type="radio" name="stock_category_id" value="{{ $cat->id }}" {{ request('stock_category_id') == $cat->id ? 'checked' : '' }} class="text-cafe-dark focus:ring-0">
                                    <span class="font-medium text-cafe-text">{{ $cat->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <a href="{{ route('admin.catalog.index', ['tab' => 'stock']) }}" class="flex-1 py-2.5 text-center rounded-xl bg-cafe-beige/40 text-cafe-dark text-xs font-bold">Reset</a>
                        <button type="submit" class="flex-1 py-2.5 text-center rounded-xl bg-cafe-dark text-cafe-cream text-xs font-bold shadow-md">Terapkan</button>
                    </div>
                </form>
            </x-mobile.filter-sheet>
        </div>
    </div>

    <!-- Toolbar Filters (Desktop only) -->
    <div class="hidden lg:block bg-cafe-card p-4 sm:p-5 rounded-2xl shadow-soft border border-cafe-beige/40">
        <form method="GET" action="{{ route('admin.catalog.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <input type="hidden" name="tab" value="stock">

            <div class="relative">
                <i data-lucide="search" class="w-4 h-4 text-cafe-muted absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                <input type="text" name="stock_search" value="{{ request('stock_search') }}" placeholder="Cari nama menu/bahan..." class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs text-cafe-text placeholder:text-cafe-muted/70 pl-9 pr-3.5 py-2.5 rounded-full border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all">
            </div>

            <div>
                <select name="stock_category_id" onchange="this.form.submit()" class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs text-cafe-text py-2.5 px-3.5 rounded-full border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all cursor-pointer">
                    <option value="all">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('stock_category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <select name="stock_status" onchange="this.form.submit()" class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs text-cafe-text py-2.5 px-3.5 rounded-full border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all cursor-pointer">
                    <option value="all">Semua Status Stok</option>
                    <option value="aman" {{ request('stock_status') === 'aman' ? 'selected' : '' }}>Aman</option>
                    <option value="menipis" {{ request('stock_status') === 'menipis' ? 'selected' : '' }}>Menipis</option>
                    <option value="habis" {{ request('stock_status') === 'habis' ? 'selected' : '' }}>Habis</option>
                </select>
            </div>
        </form>
    </div>

    {{-- Mobile FAB for Riwayat Stok --}}
    <x-mobile.fab icon="history" label="Riwayat" onclick="openHistoryLogsModal()" />

    <!-- Table Container -->
    <x-responsive-table :paginate="$menus">
        <x-slot:headers>
            <th class="py-3.5 pl-6">Menu / Bahan</th>
            <th class="py-3.5 px-3">Kategori</th>
            <th class="py-3.5 px-3">Stok Saat Ini</th>
            <th class="py-3.5 px-3">Batas Minimum</th>
            <th class="py-3.5 px-3">Satuan</th>
            <th class="py-3.5 px-3">Status</th>
            <th class="py-3.5 px-3">Terakhir Diperbarui</th>
            <th class="py-3.5 pr-6 text-right">Aksi Penyesuaian</th>
        </x-slot:headers>

        @forelse($menus as $idx => $m)
            <x-responsive-table.row :index="$idx">
                <!-- Mobile Card Layout -->
                <x-slot:title>
                    <div class="flex items-center gap-3">
                        <img src="{{ $m->image }}" alt="{{ $m->name }}" class="w-11 h-11 rounded-xl object-cover bg-cafe-beige shrink-0 shadow-xs">
                        <div class="min-w-0 flex-1">
                            <p class="font-bold text-xs sm:text-sm text-cafe-dark truncate">{{ $m->name }}</p>
                            <p class="text-[11px] text-cafe-muted mt-0.5">{{ $m->category->name }} &bull; {{ $m->updated_at->format('d M, H:i') }}</p>
                        </div>
                    </div>
                </x-slot:title>

                <x-slot:badge>
                    @if($m->stock_status === 'aman')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-[#4F7A4A]/15 text-[#4F7A4A] border border-[#4F7A4A]/30">Aman</span>
                    @elseif($m->stock_status === 'menipis')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-[#C98A2B]/15 text-[#C98A2B] border border-[#C98A2B]/30">Menipis</span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-[#B03A2E]/15 text-[#B03A2E] border border-[#B03A2E]/30">Habis</span>
                    @endif
                </x-slot:badge>

                <x-slot:fields>
                    <x-responsive-table.field label="Stok Saat Ini">
                        <span class="font-bold text-sm {{ $m->stock <= 0 ? 'text-cafe-danger' : ($m->stock <= $m->min_stock ? 'text-cafe-warning' : 'text-cafe-dark') }}">
                            {{ $m->stock }} {{ $m->unit }}
                        </span>
                    </x-responsive-table.field>
                    <x-responsive-table.field label="Batas Minimum" :value="$m->min_stock . ' ' . $m->unit" />
                </x-slot:fields>

                <x-slot:actions>
                    <div class="flex items-center gap-1.5 w-full justify-end">
                        <button onclick="openAdjustModal({{ $m->id }}, '{{ addslashes($m->name) }}', 'masuk', '{{ $m->unit }}')" class="px-3 py-1.5 rounded-full bg-cafe-success/10 hover:bg-cafe-success/20 text-cafe-success font-semibold text-xs transition-colors" title="Tambah Stok">
                            + Masuk
                        </button>
                        <button onclick="openAdjustModal({{ $m->id }}, '{{ addslashes($m->name) }}', 'keluar', '{{ $m->unit }}')" class="px-3 py-1.5 rounded-full bg-cafe-danger/10 hover:bg-cafe-danger/20 text-cafe-danger font-semibold text-xs transition-colors" title="Kurangi Stok">
                            - Keluar
                        </button>
                        <button onclick="openMinStockModal({{ $m->id }}, '{{ addslashes($m->name) }}', {{ $m->min_stock }})" class="p-2 rounded-full bg-cafe-beige/50 hover:bg-cafe-beige text-cafe-muted hover:text-cafe-dark transition-colors" title="Atur Batas Minimum">
                            <i data-lucide="sliders" class="w-4 h-4"></i>
                        </button>
                    </div>
                </x-slot:actions>

                <!-- Desktop Cells -->
                <x-slot:desktop>
                    <td class="hidden md:table-cell py-4 pl-6">
                        <div class="flex items-center gap-3">
                            <img src="{{ $m->image }}" alt="{{ $m->name }}" class="w-10 h-10 rounded-xl object-cover bg-cafe-beige shrink-0 shadow-xs">
                            <span class="font-semibold text-cafe-dark text-xs">{{ $m->name }}</span>
                        </div>
                    </td>
                    <td class="hidden md:table-cell py-4 px-3 font-medium text-cafe-text">{{ $m->category->name }}</td>
                    <td class="hidden md:table-cell py-4 px-3 font-bold text-sm text-cafe-dark">
                        <span class="{{ $m->stock <= 0 ? 'text-cafe-danger' : ($m->stock <= $m->min_stock ? 'text-cafe-warning' : 'text-cafe-dark') }}">
                            {{ $m->stock }}
                        </span>
                    </td>
                    <td class="hidden md:table-cell py-4 px-3 text-cafe-muted">{{ $m->min_stock }}</td>
                    <td class="hidden md:table-cell py-4 px-3 text-cafe-muted">{{ $m->unit }}</td>
                    <td class="hidden md:table-cell py-4 px-3">
                        @if($m->stock_status === 'aman')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#4F7A4A]/15 text-[#4F7A4A] border border-[#4F7A4A]/30">Aman</span>
                        @elseif($m->stock_status === 'menipis')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#C98A2B]/15 text-[#C98A2B] border border-[#C98A2B]/30">Menipis</span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#B03A2E]/15 text-[#B03A2E] border border-[#B03A2E]/30">Habis</span>
                        @endif
                    </td>
                    <td class="hidden md:table-cell py-4 px-3 text-cafe-muted">{{ $m->updated_at->format('d M, H:i') }}</td>
                    <td class="hidden md:table-cell py-4 pr-6 text-right">
                        <div class="flex items-center justify-end gap-1.5">
                            <button onclick="openAdjustModal({{ $m->id }}, '{{ addslashes($m->name) }}', 'masuk', '{{ $m->unit }}')" class="px-2.5 py-1 rounded-full bg-cafe-success/10 hover:bg-cafe-success/20 text-cafe-success font-semibold text-[11px] transition-colors" title="Tambah Stok">
                                + Masuk
                            </button>
                            <button onclick="openAdjustModal({{ $m->id }}, '{{ addslashes($m->name) }}', 'keluar', '{{ $m->unit }}')" class="px-2.5 py-1 rounded-full bg-cafe-danger/10 hover:bg-cafe-danger/20 text-cafe-danger font-semibold text-[11px] transition-colors" title="Kurangi Stok">
                                - Keluar
                            </button>
                            <button onclick="openMinStockModal({{ $m->id }}, '{{ addslashes($m->name) }}', {{ $m->min_stock }})" class="p-1 rounded-full bg-cafe-beige/50 hover:bg-cafe-beige text-cafe-muted hover:text-cafe-dark transition-colors" title="Atur Batas Minimum">
                                <i data-lucide="sliders" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </td>
                </x-slot:desktop>
            </x-responsive-table.row>
        @empty
            <x-responsive-table.empty colspan="8">
                Tidak ada data stok yang sesuai dengan filter.
            </x-responsive-table.empty>
        @endforelse
    </x-responsive-table>
</div>

<!-- MODAL PENYESUAIAN STOK (TAMBAH / KURANGI / KOREKSI) -->
<div id="adjust-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 overflow-y-auto hidden">
    <div class="fixed inset-0 bg-[#290024]/40 backdrop-blur-sm" onclick="closeAdjustModal()"></div>

    <div class="relative w-full max-w-md bg-cafe-card rounded-2xl shadow-soft-lg border border-cafe-beige/60 overflow-hidden z-10 my-8 p-6 space-y-4">
        <div>
            <h4 class="font-serif text-lg font-bold text-cafe-dark" id="adjust-modal-title">Penyesuaian Stok</h4>
            <p class="text-xs text-cafe-muted mt-0.5" id="adjust-modal-subtitle">-</p>
        </div>

        <form id="adjust-form" method="POST" action="" class="space-y-3 text-xs">
            @csrf
            <div class="space-y-1">
                <label class="font-semibold text-cafe-dark">Jenis Penyesuaian *</label>
                <select name="type" id="adjust-type" required class="w-full bg-[#E3DEA4]/50 focus:bg-white text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                    <option value="masuk">Barang Masuk (Restock / Pembelian)</option>
                    <option value="keluar">Barang Keluar (Dipakai / Terjual)</option>
                    <option value="koreksi">Koreksi Fisik (Opname / Cacat)</option>
                </select>
            </div>

            <div class="space-y-1">
                <label class="font-semibold text-cafe-dark">Jumlah (<span id="adjust-unit-label">porsi</span>) *</label>
                <input type="number" name="quantity" required min="1" placeholder="misal: 20" class="w-full bg-[#E3DEA4]/50 focus:bg-white text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
            </div>

            <div class="space-y-1">
                <label class="font-semibold text-cafe-dark">Catatan Alasan</label>
                <textarea name="notes" rows="2" placeholder="Restock bahan supplier, barang cacat, dll..." class="w-full bg-[#E3DEA4]/50 focus:bg-white text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-cafe-beige/60">
                <button type="button" onclick="closeAdjustModal()" class="px-4 py-2 rounded-full text-xs font-semibold text-cafe-dark hover:bg-cafe-beige/60">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream font-semibold text-xs shadow-sm">Simpan Penyesuaian</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL ATUR STOK MINIMUM -->
<div id="min-stock-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 overflow-y-auto hidden">
    <div class="fixed inset-0 bg-[#290024]/40 backdrop-blur-sm" onclick="closeMinStockModal()"></div>

    <div class="relative w-full max-w-sm bg-cafe-card rounded-2xl shadow-soft-lg border border-cafe-beige/60 overflow-hidden z-10 my-8 p-6 space-y-4">
        <div>
            <h4 class="font-serif text-lg font-bold text-cafe-dark">Batas Stok Minimum</h4>
            <p class="text-xs text-cafe-muted mt-0.5" id="min-stock-title">-</p>
        </div>

        <form id="min-stock-form" method="POST" action="" class="space-y-3 text-xs">
            @csrf
            <div class="space-y-1">
                <label class="font-semibold text-cafe-dark">Batas Minimum Peringatan</label>
                <input type="number" name="min_stock" id="min-stock-input" required min="1" class="w-full bg-[#E3DEA4]/50 focus:bg-white text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                <p class="text-[10px] text-cafe-muted">Jika stok berada di bawah angka ini, sistem otomatis menampilkan peringatan.</p>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-cafe-beige/60">
                <button type="button" onclick="closeMinStockModal()" class="px-4 py-2 rounded-full text-xs font-semibold text-cafe-dark hover:bg-cafe-beige/60">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream font-semibold text-xs shadow-sm">Atur Batas</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL RIWAYAT PERGERAKAN STOK -->
<div id="history-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 overflow-y-auto hidden">
    <div class="fixed inset-0 bg-[#290024]/40 backdrop-blur-sm" onclick="closeHistoryLogsModal()"></div>

    <div class="relative w-full max-w-3xl bg-cafe-card rounded-2xl shadow-soft-lg border border-cafe-beige/60 overflow-hidden z-10 my-8">
        <div class="flex items-center justify-between px-6 py-5 border-b border-cafe-beige/50 bg-[#F2F5E2]/40">
            <div>
                <h3 class="font-serif text-lg font-bold text-cafe-dark">Riwayat Pergerakan Stok</h3>
                <p class="text-xs text-cafe-muted mt-0.5">Catatan audit barang masuk, keluar, dan penyesuaian fisik.</p>
            </div>
            <button onclick="closeHistoryLogsModal()" class="w-8 h-8 rounded-full bg-cafe-beige/60 hover:bg-cafe-beige text-cafe-muted hover:text-cafe-dark flex items-center justify-center">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <div class="p-6 max-h-[70vh] overflow-y-auto">
            <x-responsive-table>
                <x-slot:headers>
                    <th class="py-2.5 px-3 text-left">Waktu</th>
                    <th class="py-2.5 px-3 text-left">Nama Menu/Bahan</th>
                    <th class="py-2.5 px-3 text-left">Jenis</th>
                    <th class="py-2.5 px-3 text-left">Perubahan</th>
                    <th class="py-2.5 px-3 text-left">Catatan</th>
                    <th class="py-2.5 px-3 text-left">Oleh</th>
                </x-slot:headers>

                @foreach($stockLogs as $log)
                    <x-responsive-table.row>
                        <x-slot:title>
                            {{ $log->item_name }}
                        </x-slot:title>

                        <x-slot:subtitle>
                            {{ $log->created_at->format('d/m/Y H:i') }}
                        </x-slot:subtitle>

                        <x-slot:badge>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $log->type === 'masuk' ? 'bg-cafe-success/15 text-cafe-success' : ($log->type === 'keluar' ? 'bg-cafe-danger/15 text-cafe-danger' : 'bg-cafe-gold/20 text-cafe-dark') }}">
                                {{ $log->type }}
                            </span>
                        </x-slot:badge>

                        <x-slot:fields>
                            <x-responsive-table.field label="Perubahan" :value="$log->stock_before . ' → ' . $log->stock_after" is-highlight="true" />
                            <x-responsive-table.field label="Oleh" :value="$log->actor" />
                        </x-slot:fields>

                        <x-slot:content>
                            @if($log->notes)
                                <p class="text-xs text-cafe-muted mt-2 break-words">Catatan: {{ $log->notes }}</p>
                            @endif
                        </x-slot:content>

                        <x-slot:desktop>
                            <td class="hidden md:table-cell py-3 px-3 text-cafe-muted">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                            <td class="hidden md:table-cell py-3 px-3 font-semibold text-cafe-dark">{{ $log->item_name }}</td>
                            <td class="hidden md:table-cell py-3 px-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $log->type === 'masuk' ? 'bg-cafe-success/15 text-cafe-success' : ($log->type === 'keluar' ? 'bg-cafe-danger/15 text-cafe-danger' : 'bg-cafe-gold/20 text-cafe-dark') }}">
                                    {{ $log->type }}
                                </span>
                            </td>
                            <td class="hidden md:table-cell py-3 px-3 font-mono font-medium">{{ $log->stock_before }} &rarr; {{ $log->stock_after }}</td>
                            <td class="hidden md:table-cell py-3 px-3 text-cafe-muted">{{ $log->notes ?? '-' }}</td>
                            <td class="hidden md:table-cell py-3 px-3 font-medium text-cafe-text">{{ $log->actor }}</td>
                        </x-slot:desktop>
                    </x-responsive-table.row>
                @endforeach
            </x-responsive-table>
        </div>
    </div>
</div>

<script>
    function openAdjustModal(id, name, defaultType, unit) {
        document.getElementById('adjust-modal-title').innerText = `Penyesuaian Stok: ${name}`;
        document.getElementById('adjust-modal-subtitle').innerText = `Perbarui stok bahan untuk ${name}`;
        document.getElementById('adjust-type').value = defaultType;
        document.getElementById('adjust-unit-label').innerText = unit;
        document.getElementById('adjust-form').action = `/admin/katalog/stok/${id}/adjust`;
        document.getElementById('adjust-modal').classList.remove('hidden');
    }

    function closeAdjustModal() {
        document.getElementById('adjust-modal').classList.add('hidden');
    }

    function openMinStockModal(id, name, min) {
        document.getElementById('min-stock-title').innerText = `Item: ${name}`;
        document.getElementById('min-stock-input').value = min;
        document.getElementById('min-stock-form').action = `/admin/katalog/stok/${id}/min-stock`;
        document.getElementById('min-stock-modal').classList.remove('hidden');
    }

    function closeMinStockModal() {
        document.getElementById('min-stock-modal').classList.add('hidden');
    }

    function openHistoryLogsModal() {
        document.getElementById('history-modal').classList.remove('hidden');
    }

    function closeHistoryLogsModal() {
        document.getElementById('history-modal').classList.add('hidden');
    }
</script>
