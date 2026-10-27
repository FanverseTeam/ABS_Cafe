<div class="space-y-6">
    <!-- Page Header & Action (Desktop only) -->
    <div class="hidden lg:flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-serif text-2xl font-bold text-cafe-dark">Supplier &amp; Mitra Bahan</h2>
            <p class="text-xs text-cafe-muted mt-0.5">Kelola kontak supplier, status kerja sama, dan catatan pengadaan bahan kafe.</p>
        </div>

        <button onclick="openSupplierModal()" class="px-5 py-2.5 rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream font-semibold text-xs shadow-md transition-all flex items-center gap-2">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Tambah Supplier</span>
        </button>
    </div>

    {{-- MOBILE / TABLET CONTROLS (lg:hidden) --}}
    <div class="lg:hidden space-y-2.5">
        <x-mobile.search-bar
            name="supplier_search"
            value="{{ request('supplier_search') }}"
            placeholder="Cari supplier..."
            :hiddenFields="[
                'tab' => 'stock',
                'supplier_status' => request('supplier_status', 'all'),
            ]"
        />

        @php
            $suppStatusOptions = [
                ['label' => 'Semua', 'val' => 'all'],
                ['label' => 'Aktif', 'val' => 'aktif'],
                ['label' => 'Nonaktif', 'val' => 'nonaktif'],
            ];
            $currentSuppStatus = request('supplier_status', 'all');
            $suppChips = array_map(function($opt) use ($currentSuppStatus) {
                return [
                    'label'  => $opt['label'],
                    'href'   => route('admin.catalog.index', array_merge(request()->except('page'), ['tab' => 'stock', 'supplier_status' => $opt['val']])),
                    'active' => $currentSuppStatus === $opt['val'],
                ];
            }, $suppStatusOptions);
        @endphp
        <x-mobile.chip-scroller :chips="$suppChips" />
    </div>

    <!-- Filters (Desktop only) -->
    <div class="hidden lg:block bg-cafe-card p-4 sm:p-5 rounded-2xl shadow-soft border border-cafe-beige/40">
        <form method="GET" action="{{ route('admin.catalog.index') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <input type="hidden" name="tab" value="stock">

            <div class="relative">
                <i data-lucide="search" class="w-4 h-4 text-cafe-muted absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                <input type="text" name="supplier_search" value="{{ request('supplier_search') }}" placeholder="Cari nama supplier, kontak, atau telepon..." class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs text-cafe-text placeholder:text-cafe-muted/70 pl-9 pr-3.5 py-2.5 rounded-full border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all">
            </div>

            <div>
                <select name="supplier_status" onchange="this.form.submit()" class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs text-cafe-text py-2.5 px-3.5 rounded-full border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all cursor-pointer">
                    <option value="all">Semua Status Supplier</option>
                    <option value="aktif" {{ request('supplier_status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ request('supplier_status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
        </form>
    </div>

    {{-- Mobile FAB for Tambah Supplier --}}
    <x-mobile.fab icon="plus" label="Tambah Supplier" onclick="openSupplierModal()" />

    <!-- Supplier Table -->
    <div class="bg-cafe-card rounded-2xl shadow-soft border border-cafe-beige/40 overflow-hidden">
        <div class="p-5 border-b border-cafe-beige/50 flex items-center justify-between">
            <h3 class="font-serif font-bold text-base text-cafe-dark">Daftar Supplier</h3>
            <span class="text-xs text-cafe-muted">{{ $suppliers->count() }} Supplier Terdaftar</span>
        </div>

        <x-responsive-table :paginate="$suppliers">
            <x-slot:headers>
                <th class="py-3.5 pl-6">Supplier</th>
                <th class="py-3.5 px-3">Kontak</th>
                <th class="py-3.5 px-3">Telepon / Email</th>
                <th class="py-3.5 px-3">Status</th>
                <th class="py-3.5 pr-6 text-right">Aksi</th>
            </x-slot:headers>

            @forelse($suppliers as $idx => $supplier)
                <x-responsive-table.row :index="$idx">
                    <!-- Mobile Card Layout -->
                    <x-slot:title>
                        <p class="font-bold text-xs sm:text-sm text-cafe-dark truncate">{{ $supplier->name }}</p>
                    </x-slot:title>

                    <x-slot:subtitle>
                        @if($supplier->contact_person)
                            <span class="text-cafe-muted">PIC: {{ $supplier->contact_person }}</span>
                        @endif
                    </x-slot:subtitle>

                    <x-slot:badge>
                        <form action="{{ route('admin.catalog.supplier.toggle.status', $supplier->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold border transition-all {{ $supplier->is_active ? 'bg-cafe-success/10 text-cafe-success border-cafe-success/30' : 'bg-cafe-danger/10 text-cafe-danger border-cafe-danger/30' }}">
                                {{ $supplier->is_active ? 'Aktif' : 'Nonaktif' }}
                            </button>
                        </form>
                    </x-slot:badge>

                    <x-slot:fields>
                        <x-responsive-table.field label="Telepon" :value="$supplier->phone ?: '-'" />
                        <x-responsive-table.field label="Email" :value="$supplier->email ?: '-'" />
                    </x-slot:fields>

                    @if($supplier->address)
                        <x-slot:content>
                            <p class="text-xs text-cafe-muted"><span class="font-semibold text-cafe-dark">Alamat:</span> {{ $supplier->address }}</p>
                        </x-slot:content>
                    @endif

                    <x-slot:actions>
                        <div class="flex items-center gap-1.5 w-full justify-end">
                            <button onclick="editSupplierModal({{ json_encode($supplier) }})" class="p-2 rounded-full hover:bg-cafe-beige/60 text-cafe-brown hover:text-cafe-dark transition-colors" title="Edit Supplier">
                                <i data-lucide="edit" class="w-4 h-4"></i>
                            </button>
                            <form action="{{ route('admin.catalog.supplier.destroy', $supplier->id) }}" method="POST" onsubmit="return confirm('Hapus supplier ini?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 rounded-full text-cafe-danger hover:bg-cafe-danger/10 transition-colors" title="Hapus Supplier">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </x-slot:actions>

                    <!-- Desktop Cells -->
                    <x-slot:desktop>
                        <td class="hidden md:table-cell py-4 pl-6">
                            <p class="font-semibold text-cafe-dark">{{ $supplier->name }}</p>
                            <p class="text-[11px] text-cafe-muted line-clamp-1 max-w-xs">{{ $supplier->address ?: 'Alamat belum diisi' }}</p>
                        </td>
                        <td class="hidden md:table-cell py-4 px-3 text-cafe-text">{{ $supplier->contact_person ?: '-' }}</td>
                        <td class="hidden md:table-cell py-4 px-3 text-cafe-muted">
                            <p>{{ $supplier->phone ?: '-' }}</p>
                            <p class="text-[11px]">{{ $supplier->email ?: '' }}</p>
                        </td>
                        <td class="hidden md:table-cell py-4 px-3">
                            <form action="{{ route('admin.catalog.supplier.toggle.status', $supplier->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-3 py-1 rounded-full text-[11px] font-semibold border transition-all {{ $supplier->is_active ? 'bg-cafe-success/10 text-cafe-success border-cafe-success/30' : 'bg-cafe-danger/10 text-cafe-danger border-cafe-danger/30' }}">
                                    {{ $supplier->is_active ? 'Aktif' : 'Nonaktif' }}
                                </button>
                            </form>
                        </td>
                        <td class="hidden md:table-cell py-4 pr-6 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <button onclick="editSupplierModal({{ json_encode($supplier) }})" class="p-1.5 rounded-full hover:bg-cafe-beige/60 text-cafe-brown hover:text-cafe-dark transition-colors" title="Edit Supplier">
                                    <i data-lucide="edit" class="w-3.5 h-3.5"></i>
                                </button>
                                <form action="{{ route('admin.catalog.supplier.destroy', $supplier->id) }}" method="POST" onsubmit="return confirm('Hapus supplier ini?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-full text-cafe-danger hover:bg-cafe-danger/10 transition-colors" title="Hapus Supplier">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </x-slot:desktop>
                </x-responsive-table.row>
            @empty
                <x-responsive-table.empty colspan="5">
                    Belum ada supplier yang sesuai dengan filter.
                </x-responsive-table.empty>
            @endforelse
        </x-responsive-table>
    </div>
</div>

<!-- MODAL FORM TAMBAH / EDIT SUPPLIER -->
<div id="supplier-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 overflow-y-auto hidden">
    <div class="fixed inset-0 bg-[#290024]/40 backdrop-blur-sm" onclick="closeSupplierModal()"></div>

    <div class="relative w-full max-w-lg bg-cafe-card rounded-2xl shadow-soft-lg border border-cafe-beige/60 overflow-hidden z-10 my-8">
        <div class="flex items-center justify-between px-6 py-5 border-b border-cafe-beige/50 bg-[#F2F5E2]/40">
            <div>
                <h3 class="font-serif text-lg font-bold text-cafe-dark" id="supplier-modal-title">Tambah Supplier Baru</h3>
                <p class="text-xs text-cafe-muted mt-0.5">Lengkapi identitas dan kontak supplier bahan kafe.</p>
            </div>
            <button onclick="closeSupplierModal()" class="w-8 h-8 rounded-full bg-cafe-beige/60 hover:bg-cafe-beige text-cafe-muted hover:text-cafe-dark flex items-center justify-center">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="supplier-form" method="POST" action="{{ route('admin.catalog.supplier.store') }}" class="p-6 space-y-4 text-xs">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="font-semibold text-cafe-dark">Nama Supplier *</label>
                    <input type="text" name="name" id="supplier-input-name" required class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                </div>
                <div class="space-y-1">
                    <label class="font-semibold text-cafe-dark">Nama Kontak</label>
                    <input type="text" name="contact_person" id="supplier-input-contact" class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="font-semibold text-cafe-dark">Telepon</label>
                    <input type="text" name="phone" id="supplier-input-phone" class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                </div>
                <div class="space-y-1">
                    <label class="font-semibold text-cafe-dark">Email</label>
                    <input type="email" name="email" id="supplier-input-email" class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                </div>
            </div>

            <div class="space-y-1">
                <label class="font-semibold text-cafe-dark">Alamat</label>
                <textarea name="address" id="supplier-input-address" rows="2" class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none"></textarea>
            </div>

            <div class="space-y-1">
                <label class="font-semibold text-cafe-dark">Catatan</label>
                <textarea name="notes" id="supplier-input-notes" rows="2" class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none"></textarea>
            </div>

            <label class="flex items-center gap-2 cursor-pointer select-none pt-1">
                <input type="checkbox" name="is_active" id="supplier-input-active" value="1" checked class="w-4 h-4 rounded text-cafe-dark accent-[#290024]">
                <span class="font-semibold text-cafe-dark">Supplier Aktif</span>
            </label>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-cafe-beige">
                <button type="button" onclick="closeSupplierModal()" class="px-5 py-2.5 rounded-full text-xs font-semibold text-cafe-dark hover:bg-cafe-beige/60">Batal</button>
                <button type="submit" class="px-6 py-2.5 rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream font-semibold text-xs shadow-md transition-all">Simpan Supplier</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openSupplierModal() {
        document.getElementById('supplier-modal-title').innerText = 'Tambah Supplier Baru';
        document.getElementById('supplier-form').action = "{{ route('admin.catalog.supplier.store') }}";
        document.getElementById('supplier-input-name').value = '';
        document.getElementById('supplier-input-contact').value = '';
        document.getElementById('supplier-input-phone').value = '';
        document.getElementById('supplier-input-email').value = '';
        document.getElementById('supplier-input-address').value = '';
        document.getElementById('supplier-input-notes').value = '';
        document.getElementById('supplier-input-active').checked = true;
        document.getElementById('supplier-modal').classList.remove('hidden');
    }

    function editSupplierModal(supplier) {
        document.getElementById('supplier-modal-title').innerText = `Edit Supplier: ${supplier.name}`;
        document.getElementById('supplier-form').action = `/admin/katalog/supplier/${supplier.id}/update`;
        document.getElementById('supplier-input-name').value = supplier.name;
        document.getElementById('supplier-input-contact').value = supplier.contact_person || '';
        document.getElementById('supplier-input-phone').value = supplier.phone || '';
        document.getElementById('supplier-input-email').value = supplier.email || '';
        document.getElementById('supplier-input-address').value = supplier.address || '';
        document.getElementById('supplier-input-notes').value = supplier.notes || '';
        document.getElementById('supplier-input-active').checked = Boolean(supplier.is_active);
        document.getElementById('supplier-modal').classList.remove('hidden');
    }

    function closeSupplierModal() {
        document.getElementById('supplier-modal').classList.add('hidden');
    }
</script>
