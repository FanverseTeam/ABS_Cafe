@extends('admin.layouts.app')

@section('title', 'Manajemen Stok')
@section('page_title', 'Stok')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-1.5 text-xs text-cafe-muted mb-3">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-cafe-dark flex items-center gap-1">
            <i data-lucide="home" class="w-3.5 h-3.5"></i>
            <span>Admin</span>
        </a>
        <i data-lucide="chevron-right" class="w-3 h-3 text-cafe-muted/60"></i>
        <span class="font-semibold text-cafe-dark">Manajemen Stok</span>
    </nav>

    <!-- Page Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-serif text-2xl font-bold text-cafe-dark">Inventaris &amp; Kontrol Stok</h2>
            <p class="text-xs text-cafe-muted mt-0.5">Pantau ketersediaan bahan, status menipis/habis, serta log pergerakan stok</p>
        </div>

        <button onclick="openHistoryLogsModal()" class="px-5 py-2.5 rounded-full bg-cafe-card hover:bg-cafe-beige text-cafe-dark font-semibold text-xs border border-cafe-beige/80 transition-all flex items-center gap-2 shadow-xs">
            <i data-lucide="history" class="w-4 h-4 text-cafe-muted"></i>
            <span>Riwayat Pergerakan Stok</span>
        </button>
    </div>

    <!-- Toolbar Filters -->
    <div class="bg-cafe-card p-4 sm:p-5 rounded-2xl shadow-soft border border-cafe-beige/40">
        <form method="GET" action="{{ route('admin.stock.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="relative">
                <i data-lucide="search" class="w-4 h-4 text-cafe-muted absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama menu/bahan..." class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs text-cafe-text placeholder:text-cafe-muted/70 pl-9 pr-3.5 py-2.5 rounded-full border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all">
            </div>

            <div>
                <select name="category_id" onchange="this.form.submit()" class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs text-cafe-text py-2.5 px-3.5 rounded-full border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all cursor-pointer">
                    <option value="all">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <select name="status" onchange="this.form.submit()" class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs text-cafe-text py-2.5 px-3.5 rounded-full border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all cursor-pointer">
                    <option value="all">Semua Status Stok</option>
                    <option value="aman" {{ request('status') === 'aman' ? 'selected' : '' }}>Aman</option>
                    <option value="menipis" {{ request('status') === 'menipis' ? 'selected' : '' }}>Menipis</option>
                    <option value="habis" {{ request('status') === 'habis' ? 'selected' : '' }}>Habis</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Table Container -->
    <div class="bg-cafe-card rounded-2xl shadow-soft border border-cafe-beige/40 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#F2F5E2]/70 border-b border-cafe-beige text-cafe-muted uppercase font-semibold text-[11px] tracking-wider">
                    <tr>
                        <th class="py-3.5 pl-6">Menu / Bahan</th>
                        <th class="py-3.5 px-3">Kategori</th>
                        <th class="py-3.5 px-3">Stok Saat Ini</th>
                        <th class="py-3.5 px-3">Batas Minimum</th>
                        <th class="py-3.5 px-3">Satuan</th>
                        <th class="py-3.5 px-3">Status</th>
                        <th class="py-3.5 px-3">Terakhir Diperbarui</th>
                        <th class="py-3.5 pr-6 text-right">Aksi Penyesuaian</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-cafe-beige/30">
                    @forelse($menus as $idx => $m)
                        <tr class="hover:bg-cafe-cream/80 transition-colors {{ $idx % 2 === 1 ? 'bg-[#FAFCF3]/40' : '' }}">
                            <td class="py-4 pl-6">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $m->image }}" alt="{{ $m->name }}" class="w-10 h-10 rounded-xl object-cover bg-cafe-beige shrink-0 shadow-xs">
                                    <span class="font-semibold text-cafe-dark text-xs">{{ $m->name }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-3 font-medium text-cafe-text">{{ $m->category->name }}</td>
                            <td class="py-4 px-3 font-bold text-sm text-cafe-dark">
                                <span class="{{ $m->stock <= 0 ? 'text-cafe-danger' : ($m->stock <= $m->min_stock ? 'text-cafe-warning' : 'text-cafe-dark') }}">
                                    {{ $m->stock }}
                                </span>
                            </td>
                            <td class="py-4 px-3 text-cafe-muted">{{ $m->min_stock }}</td>
                            <td class="py-4 px-3 text-cafe-muted">{{ $m->unit }}</td>
                            <td class="py-4 px-3">
                                @if($m->stock_status === 'aman')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#4F7A4A]/15 text-[#4F7A4A] border border-[#4F7A4A]/30">Aman</span>
                                @elseif($m->stock_status === 'menipis')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#C98A2B]/15 text-[#C98A2B] border border-[#C98A2B]/30">Menipis</span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#B03A2E]/15 text-[#B03A2E] border border-[#B03A2E]/30">Habis</span>
                                @endif
                            </td>
                            <td class="py-4 px-3 text-cafe-muted">{{ $m->updated_at->format('d M, H:i') }}</td>
                            <td class="py-4 pr-6 text-right">
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
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-xs text-cafe-muted">
                                Tidak ada data stok yang sesuai dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
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
            <table class="w-full text-left text-xs">
                <thead class="bg-[#F2F5E2]/70 border-b border-cafe-beige text-cafe-muted font-semibold">
                    <tr>
                        <th class="py-2.5 px-3">Waktu</th>
                        <th class="py-2.5 px-3">Nama Menu/Bahan</th>
                        <th class="py-2.5 px-3">Jenis</th>
                        <th class="py-2.5 px-3">Perubahan</th>
                        <th class="py-2.5 px-3">Catatan</th>
                        <th class="py-2.5 px-3">Oleh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-cafe-beige/30">
                    @foreach($stockLogs as $log)
                        <tr>
                            <td class="py-3 px-3 text-cafe-muted">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                            <td class="py-3 px-3 font-semibold text-cafe-dark">{{ $log->item_name }}</td>
                            <td class="py-3 px-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $log->type === 'masuk' ? 'bg-cafe-success/15 text-cafe-success' : ($log->type === 'keluar' ? 'bg-cafe-danger/15 text-cafe-danger' : 'bg-cafe-gold/20 text-cafe-dark') }}">
                                    {{ $log->type }}
                                </span>
                            </td>
                            <td class="py-3 px-3 font-mono font-medium">{{ $log->stock_before }} &rarr; {{ $log->stock_after }}</td>
                            <td class="py-3 px-3 text-cafe-muted">{{ $log->notes ?? '-' }}</td>
                            <td class="py-3 px-3 font-medium text-cafe-text">{{ $log->actor }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function openAdjustModal(id, name, defaultType, unit) {
        document.getElementById('adjust-modal-title').innerText = `Penyesuaian Stok: ${name}`;
        document.getElementById('adjust-modal-subtitle').innerText = `Perbarui stok bahan untuk ${name}`;
        document.getElementById('adjust-type').value = defaultType;
        document.getElementById('adjust-unit-label').innerText = unit;
        document.getElementById('adjust-form').action = `/admin/stok/${id}/adjust`;
        document.getElementById('adjust-modal').classList.remove('hidden');
    }

    function closeAdjustModal() {
        document.getElementById('adjust-modal').classList.add('hidden');
    }

    function openMinStockModal(id, name, min) {
        document.getElementById('min-stock-title').innerText = `Item: ${name}`;
        document.getElementById('min-stock-input').value = min;
        document.getElementById('min-stock-form').action = `/admin/stok/${id}/min-stock`;
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
@endsection
