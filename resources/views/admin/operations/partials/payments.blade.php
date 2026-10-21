<div class="space-y-6">
    <!-- Page Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-serif text-2xl font-bold text-cafe-dark">Metode Pembayaran</h2>
            <p class="text-xs text-cafe-muted mt-0.5">Atur opsi pembayaran yang muncul di POS dan proses pembayaran kasir.</p>
        </div>

        <button onclick="openPaymentMethodModal()" class="px-5 py-2.5 rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream font-semibold text-xs shadow-md transition-all flex items-center gap-2">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Tambah Metode</span>
        </button>
    </div>

    <!-- Filters -->
    <div class="bg-cafe-card p-4 sm:p-5 rounded-2xl shadow-soft border border-cafe-beige/40">
        <form method="GET" action="{{ route('admin.operations.index') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <input type="hidden" name="tab" value="pembayaran">

            <div class="relative">
                <i data-lucide="search" class="w-4 h-4 text-cafe-muted absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                <input type="text" name="payment_search" value="{{ request('payment_search') }}" placeholder="Cari nama, kode, atau deskripsi metode..." class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs text-cafe-text placeholder:text-cafe-muted/70 pl-9 pr-3.5 py-2.5 rounded-full border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all">
            </div>

            <div>
                <select name="payment_status" onchange="this.form.submit()" class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs text-cafe-text py-2.5 px-3.5 rounded-full border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all cursor-pointer">
                    <option value="all">Semua Status</option>
                    <option value="aktif" {{ request('payment_status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ request('payment_status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Payment Method Table -->
    <div class="space-y-3">
        <div class="bg-cafe-card p-4 sm:p-5 rounded-2xl shadow-soft border border-cafe-beige/40 flex items-center justify-between">
            <h3 class="font-serif font-bold text-base text-cafe-dark">Daftar Metode Pembayaran</h3>
            <span class="text-xs text-cafe-muted">{{ $paymentMethods->count() }} Metode Terdaftar</span>
        </div>

        <x-responsive-table>
            <x-slot:headers>
                <th class="py-3.5 pl-6">Metode</th>
                <th class="py-3.5 px-3">Kode</th>
                <th class="py-3.5 px-3">Tipe</th>
                <th class="py-3.5 px-3">Urutan</th>
                <th class="py-3.5 px-3">Status</th>
                <th class="py-3.5 pr-6 text-right">Aksi</th>
            </x-slot:headers>

            @forelse($paymentMethods as $idx => $method)
                <x-responsive-table.row :index="$idx">
                    <!-- Mobile Card Layout -->
                    <x-slot:title>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-cafe-beige/70 text-cafe-brown flex items-center justify-center shrink-0">
                                <i data-lucide="{{ $method->icon() }}" class="w-5 h-5"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="font-bold text-xs sm:text-sm text-cafe-dark truncate">{{ $method->name }}</p>
                                <span class="font-mono text-[10px] text-cafe-muted">{{ $method->code }}</span>
                            </div>
                        </div>
                    </x-slot:title>

                    <x-slot:badge>
                        <form action="{{ route('admin.operations.payments.toggle.active', $method->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold border transition-all {{ $method->is_active ? 'bg-cafe-success/10 text-cafe-success border-cafe-success/30' : 'bg-cafe-danger/10 text-cafe-danger border-cafe-danger/30' }}">
                                {{ $method->is_active ? 'Aktif' : 'Nonaktif' }}
                            </button>
                        </form>
                    </x-slot:badge>

                    <x-slot:fields>
                        <x-responsive-table.field label="Tipe" :value="$method->type" />
                        <x-responsive-table.field label="Urutan" :value="$method->sort_order" />
                    </x-slot:fields>

                    @if($method->description)
                        <x-slot:content>
                            <p class="text-xs text-cafe-muted">{{ $method->description }}</p>
                        </x-slot:content>
                    @endif

                    <x-slot:actions>
                        <div class="flex items-center gap-1.5 w-full justify-end">
                            <button onclick="editPaymentMethodModal({{ json_encode($method) }})" class="p-2 rounded-full hover:bg-cafe-beige/60 text-cafe-brown hover:text-cafe-dark transition-colors" title="Edit Metode">
                                <i data-lucide="edit" class="w-4 h-4"></i>
                            </button>
                            <form action="{{ route('admin.operations.payments.destroy', $method->id) }}" method="POST" onsubmit="return confirm('Hapus metode pembayaran ini?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 rounded-full text-cafe-danger hover:bg-cafe-danger/10 transition-colors" title="Hapus Metode">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </x-slot:actions>

                    <!-- Desktop Cells -->
                    <x-slot:desktop>
                        <td class="hidden md:table-cell py-4 pl-6">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-cafe-beige/70 text-cafe-brown flex items-center justify-center shrink-0">
                                    <i data-lucide="{{ $method->icon() }}" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-cafe-dark">{{ $method->name }}</p>
                                    <p class="text-[11px] text-cafe-muted line-clamp-1 max-w-xs">{{ $method->description ?: 'Belum ada deskripsi' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="hidden md:table-cell py-4 px-3 font-mono text-cafe-muted">{{ $method->code }}</td>
                        <td class="hidden md:table-cell py-4 px-3 text-cafe-text">{{ $method->type }}</td>
                        <td class="hidden md:table-cell py-4 px-3 text-cafe-muted">{{ $method->sort_order }}</td>
                        <td class="hidden md:table-cell py-4 px-3">
                            <form action="{{ route('admin.operations.payments.toggle.active', $method->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-3 py-1 rounded-full text-[11px] font-semibold border transition-all {{ $method->is_active ? 'bg-cafe-success/10 text-cafe-success border-cafe-success/30' : 'bg-cafe-danger/10 text-cafe-danger border-cafe-danger/30' }}">
                                    {{ $method->is_active ? 'Aktif' : 'Nonaktif' }}
                                </button>
                            </form>
                        </td>
                        <td class="hidden md:table-cell py-4 pr-6 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <button onclick="editPaymentMethodModal({{ json_encode($method) }})" class="p-1.5 rounded-full hover:bg-cafe-beige/60 text-cafe-brown hover:text-cafe-dark transition-colors" title="Edit Metode">
                                    <i data-lucide="edit" class="w-3.5 h-3.5"></i>
                                </button>
                                <form action="{{ route('admin.operations.payments.destroy', $method->id) }}" method="POST" onsubmit="return confirm('Hapus metode pembayaran ini?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-full text-cafe-danger hover:bg-cafe-danger/10 transition-colors" title="Hapus Metode">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </x-slot:desktop>
                </x-responsive-table.row>
            @empty
                <x-responsive-table.empty colspan="6">
                    Belum ada metode pembayaran yang sesuai dengan filter.
                </x-responsive-table.empty>
            @endforelse
        </x-responsive-table>
    </div>
</div>

<!-- MODAL FORM TAMBAH / EDIT METODE PEMBAYARAN -->
<div id="payment-method-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 overflow-y-auto hidden">
    <div class="fixed inset-0 bg-[#290024]/40 backdrop-blur-sm" onclick="closePaymentMethodModal()"></div>

    <div class="relative w-full max-w-lg bg-cafe-card rounded-2xl shadow-soft-lg border border-cafe-beige/60 overflow-hidden z-10 my-8">
        <div class="flex items-center justify-between px-6 py-5 border-b border-cafe-beige/50 bg-[#F2F5E2]/40">
            <div>
                <h3 class="font-serif text-lg font-bold text-cafe-dark" id="payment-method-modal-title">Tambah Metode Pembayaran</h3>
                <p class="text-xs text-cafe-muted mt-0.5">Metode aktif akan tampil sebagai opsi pembayaran di kasir.</p>
            </div>
            <button onclick="closePaymentMethodModal()" class="w-8 h-8 rounded-full bg-cafe-beige/60 hover:bg-cafe-beige text-cafe-muted hover:text-cafe-dark flex items-center justify-center">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="payment-method-form" method="POST" action="{{ route('admin.operations.payments.store') }}" class="p-6 space-y-4 text-xs">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="font-semibold text-cafe-dark">Nama Metode *</label>
                    <input type="text" name="name" id="payment-input-name" required class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                </div>
                <div class="space-y-1">
                    <label class="font-semibold text-cafe-dark">Kode Unik *</label>
                    <input type="text" name="code" id="payment-input-code" required placeholder="mis. qris" class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="font-semibold text-cafe-dark">Tipe *</label>
                    <select name="type" id="payment-input-type" required class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none cursor-pointer">
                        <option value="cash">Tunai</option>
                        <option value="debit">Kartu Debit / Kredit</option>
                        <option value="e_wallet">E-Wallet</option>
                        <option value="bank_transfer">Transfer Bank</option>
                        <option value="qris">QRIS</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="font-semibold text-cafe-dark">Urutan Tampil</label>
                    <input type="number" name="sort_order" id="payment-input-sort" min="0" value="0" class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                </div>
            </div>

            <div class="space-y-1">
                <label class="font-semibold text-cafe-dark">Deskripsi</label>
                <textarea name="description" id="payment-input-description" rows="2" class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none"></textarea>
            </div>

            <div class="space-y-1">
                <label class="font-semibold text-cafe-dark">Instruksi untuk Kasir</label>
                <textarea name="instructions" id="payment-input-instructions" rows="2" class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none"></textarea>
            </div>

            <label class="flex items-center gap-2 cursor-pointer select-none pt-1">
                <input type="checkbox" name="is_active" id="payment-input-active" value="1" checked class="w-4 h-4 rounded text-cafe-dark accent-[#290024]">
                <span class="font-semibold text-cafe-dark">Metode Aktif</span>
            </label>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-cafe-beige">
                <button type="button" onclick="closePaymentMethodModal()" class="px-5 py-2.5 rounded-full text-xs font-semibold text-cafe-dark hover:bg-cafe-beige/60">Batal</button>
                <button type="submit" class="px-6 py-2.5 rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream font-semibold text-xs shadow-md transition-all">Simpan Metode</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openPaymentMethodModal() {
        document.getElementById('payment-method-modal-title').innerText = 'Tambah Metode Pembayaran';
        document.getElementById('payment-method-form').action = "{{ route('admin.operations.payments.store') }}";
        document.getElementById('payment-input-name').value = '';
        document.getElementById('payment-input-code').value = '';
        document.getElementById('payment-input-type').value = 'cash';
        document.getElementById('payment-input-sort').value = '0';
        document.getElementById('payment-input-description').value = '';
        document.getElementById('payment-input-instructions').value = '';
        document.getElementById('payment-input-active').checked = true;
        document.getElementById('payment-method-modal').classList.remove('hidden');
    }

    function editPaymentMethodModal(method) {
        document.getElementById('payment-method-modal-title').innerText = `Edit Metode: ${method.name}`;
        document.getElementById('payment-method-form').action = `/admin/operasional/pembayaran/${method.id}/update`;
        document.getElementById('payment-input-name').value = method.name;
        document.getElementById('payment-input-code').value = method.code;
        document.getElementById('payment-input-type').value = method.type;
        document.getElementById('payment-input-sort').value = method.sort_order;
        document.getElementById('payment-input-description').value = method.description || '';
        document.getElementById('payment-input-instructions').value = method.instructions || '';
        document.getElementById('payment-input-active').checked = Boolean(method.is_active);
        document.getElementById('payment-method-modal').classList.remove('hidden');
    }

    function closePaymentMethodModal() {
        document.getElementById('payment-method-modal').classList.add('hidden');
    }
</script>
