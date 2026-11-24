@extends('admin.layouts.app')

@section('title', 'Manajemen Staf dan Akun')
@section('page_title', 'Manajemen Staf dan Akun')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <x-breadcrumb
        :items="[
            ['label' => 'Admin', 'url' => route('admin.dashboard'), 'icon' => 'home'],
            ['label' => 'Manajemen Staf dan Akun'],
        ]"
        class="mb-3"
    />

    <!-- Page Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-serif text-2xl font-bold text-cafe-dark">Manajemen Staf dan Akun</h2>
            <p class="text-xs text-cafe-muted mt-0.5">Kelola data seluruh staf cafe (Kitchen, Barista, dan Kasir) serta akun login sistem</p>
        </div>

        <button onclick="openAddPenjagaModal()" class="px-5 py-2.5 rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream font-semibold text-xs shadow-md transition-all flex items-center gap-2 active:scale-95">
            <i data-lucide="user-plus" class="w-4 h-4"></i>
            <span>Tambah Staf</span>
        </button>
    </div>

    <!-- Info Banner -->
    <div class="p-4 rounded-2xl bg-cafe-info/10 border border-cafe-info/30 flex items-start gap-3">
        <div class="w-8 h-8 rounded-xl bg-cafe-info/20 text-cafe-info flex items-center justify-center shrink-0 mt-0.5">
            <i data-lucide="info" class="w-4 h-4"></i>
        </div>
        <div class="text-xs text-cafe-text leading-relaxed">
            <p class="font-semibold text-cafe-dark mb-0.5">Daftar Staf Cafe</p>
            <p class="text-cafe-muted">Kelola nama staf yang terdaftar berdasarkan bagian kerjanya (Kitchen, Barista, dan Kasir). Staf kasir yang aktif akan muncul di pilihan Dashboard Kasir dan struk transaksi.</p>
        </div>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="bg-cafe-card p-4 sm:p-5 rounded-2xl shadow-soft border border-cafe-beige/40">
        <form method="GET" action="{{ route('admin.staff.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div class="relative sm:col-span-2">
                <i data-lucide="search" class="w-4 h-4 text-cafe-muted absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama staf..." class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs text-cafe-text placeholder:text-cafe-muted/70 pl-9 pr-3.5 py-2.5 rounded-full border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all">
            </div>

            <div>
                <select name="bagian" onchange="this.form.submit()" class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs text-cafe-text py-2.5 px-3.5 rounded-full border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all cursor-pointer">
                    <option value="all">Semua Bagian</option>
                    <option value="kasir" {{ request('bagian') === 'kasir' ? 'selected' : '' }}>Kasir</option>
                    <option value="barista" {{ request('bagian') === 'barista' ? 'selected' : '' }}>Barista</option>
                    <option value="kitchen" {{ request('bagian') === 'kitchen' ? 'selected' : '' }}>Kitchen</option>
                </select>
            </div>

            <div>
                <select name="status" onchange="this.form.submit()" class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs text-cafe-text py-2.5 px-3.5 rounded-full border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all cursor-pointer">
                    <option value="all">Semua Status</option>
                    <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Responsive Table & Mobile Cards -->
    <x-responsive-table :paginate="$penjagaList">
        <x-slot:headers>
            <th class="py-3.5 pl-6">Nama Staf</th>
            <th class="py-3.5 px-3">Bagian Kerja</th>
            <th class="py-3.5 px-3">Status</th>
            <th class="py-3.5 pr-6 text-right">Aksi</th>
        </x-slot:headers>

        @forelse($penjagaList as $idx => $penjaga)
            <x-responsive-table.row :index="$idx">
                <!-- Mobile Card Layout -->
                <x-slot:title>
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-10 h-10 rounded-full bg-cafe-gold/20 text-cafe-dark font-serif font-bold flex items-center justify-center text-xs shrink-0 border border-cafe-gold/30">
                            {{ strtoupper(substr($penjaga->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <h4 class="font-bold text-xs sm:text-sm text-cafe-dark truncate">{{ $penjaga->name }}</h4>
                            <p class="text-[10px] text-cafe-muted truncate">{{ $penjaga->bagianLabel() }}</p>
                        </div>
                    </div>
                </x-slot:title>

                <x-slot:badge>
                    <span class="px-2 py-0.5 rounded-full text-[9px] font-semibold {{ $penjaga->status === 'aktif' ? 'bg-cafe-success/15 text-cafe-success' : 'bg-cafe-muted/15 text-cafe-muted' }}">
                        {{ ucfirst($penjaga->status) }}
                    </span>
                </x-slot:badge>

                <x-slot:fields>
                    <x-responsive-table.field label="Bagian Kerja" :value="$penjaga->bagianLabel()" />
                </x-slot:fields>

                <x-slot:actions>
                    <form action="{{ route('admin.staff.penjaga.toggle.status', $penjaga->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-cafe-beige/50 text-[10px] font-semibold text-cafe-dark hover:bg-cafe-beige">
                            <i data-lucide="{{ $penjaga->status === 'aktif' ? 'user-x' : 'user-check' }}" class="w-3.5 h-3.5"></i>
                            <span>{{ $penjaga->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}</span>
                        </button>
                    </form>

                    <div class="flex items-center gap-1.5">
                        <button onclick="openEditPenjagaModal({{ $penjaga->id }}, '{{ addslashes($penjaga->name) }}', '{{ $penjaga->bagian ?? 'kasir' }}', '{{ $penjaga->status }}')" class="p-2 rounded-full bg-cafe-beige/70 text-cafe-dark hover:bg-cafe-beige" title="Edit">
                            <i data-lucide="edit" class="w-3.5 h-3.5"></i>
                        </button>
                        <form action="{{ route('admin.staff.penjaga.destroy', $penjaga->id) }}" method="POST" onsubmit="return confirm('Hapus data staf ini?');" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 rounded-full bg-cafe-danger/10 text-cafe-danger hover:bg-cafe-danger/20" title="Hapus">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                            </button>
                        </form>
                    </div>
                </x-slot:actions>

                <!-- Desktop Cells -->
                <x-slot:desktop>
                    <td class="hidden md:table-cell py-4 pl-6">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-cafe-gold/20 text-cafe-dark font-serif font-bold flex items-center justify-center text-xs shrink-0 border border-cafe-gold/30">
                                {{ strtoupper(substr($penjaga->name, 0, 1)) }}
                            </div>
                            <span class="font-semibold text-cafe-dark text-xs">{{ $penjaga->name }}</span>
                        </div>
                    </td>
                    <td class="hidden md:table-cell py-4 px-3">
                        @php
                            $bagianVal = strtolower((string) ($penjaga->bagian ?? 'kasir'));
                            $bagianBadge = match($bagianVal) {
                                'kitchen' => 'bg-emerald-500/15 text-emerald-800 border-emerald-500/30',
                                'barista' => 'bg-amber-800/15 text-amber-900 border-amber-800/30',
                                default => 'bg-[#D4954D]/20 text-[#290024] border-[#D4954D]/40',
                            };
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $bagianBadge }}">
                            {{ $penjaga->bagianLabel() }}
                        </span>
                    </td>
                    <td class="hidden md:table-cell py-4 px-3">
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold {{ $penjaga->status === 'aktif' ? 'bg-cafe-success/15 text-cafe-success' : 'bg-cafe-muted/15 text-cafe-muted' }}">
                            {{ ucfirst($penjaga->status) }}
                        </span>
                    </td>
                    <td class="hidden md:table-cell py-4 pr-6 text-right">
                        <div class="flex items-center justify-end gap-1.5">
                            <form action="{{ route('admin.staff.penjaga.toggle.status', $penjaga->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="p-1.5 rounded-full hover:bg-cafe-beige/60 text-cafe-muted hover:text-cafe-dark" title="{{ $penjaga->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}">
                                    <i data-lucide="{{ $penjaga->status === 'aktif' ? 'user-x' : 'user-check' }}" class="w-3.5 h-3.5"></i>
                                </button>
                            </form>
                            <button onclick="openEditPenjagaModal({{ $penjaga->id }}, '{{ addslashes($penjaga->name) }}', '{{ $penjaga->bagian ?? 'kasir' }}', '{{ $penjaga->status }}')" class="p-1.5 rounded-full hover:bg-cafe-beige/60 text-cafe-brown hover:text-cafe-dark" title="Edit">
                                <i data-lucide="edit" class="w-3.5 h-3.5"></i>
                            </button>
                            <form action="{{ route('admin.staff.penjaga.destroy', $penjaga->id) }}" method="POST" onsubmit="return confirm('Hapus data staf ini?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-full hover:bg-cafe-danger/10 text-cafe-danger" title="Hapus">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </x-slot:desktop>
            </x-responsive-table.row>
        @empty
            <x-responsive-table.empty colspan="4">
                Belum ada data staf. Klik tombol "Tambah Staf" di atas untuk mendaftarkan staf baru.
            </x-responsive-table.empty>
        @endforelse
    </x-responsive-table>

    <!-- SEKTOR AKUN LOGIN PER ROLE (KASIR, OWNER, ADMIN) -->
    <div class="pt-6 border-t border-cafe-beige/60 space-y-4">
        <div>
            <h3 class="font-serif text-xl font-bold text-cafe-dark flex items-center gap-2">
                <i data-lucide="shield-check" class="w-5 h-5 text-cafe-gold"></i>
                <span>Pengaturan Akun Login Role (Kasir, Owner, dan Admin)</span>
            </h3>
            <p class="text-xs text-cafe-muted mt-0.5">Kelola username dan kata sandi login untuk masing-masing role sistem</p>
        </div>

        <!-- Table Akun Per Role -->
        <div class="bg-cafe-card rounded-2xl shadow-soft border border-cafe-beige/50 overflow-hidden max-w-4xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-cafe-beige/60 bg-[#F2F5E2]/80 text-cafe-dark font-semibold text-[11px] uppercase tracking-wider">
                            <th class="py-3.5 px-4 sm:px-6">Role</th>
                            <th class="py-3.5 px-4 sm:px-6">Username</th>
                            <th class="py-3.5 px-4 sm:px-6">Password</th>
                            <th class="py-3.5 px-4 sm:px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-cafe-beige/40">
                        @forelse($roleAccounts as $account)
                            <tr class="hover:bg-cafe-beige/20 transition-colors">
                                <td class="py-4 px-4 sm:px-6 font-semibold">
                                    @if($account->role === 'admin')
                                        <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-[#290024] text-[#F2F5E2] tracking-wide shadow-xs">Admin</span>
                                    @elseif($account->role === 'owner')
                                        <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-[#D4954D] text-[#120a10] tracking-wide shadow-xs">Owner</span>
                                    @elseif($account->role === 'kasir')
                                        <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-[#775533] text-[#F2F5E2] tracking-wide shadow-xs">Kasir</span>
                                    @else
                                        <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-cafe-beige text-cafe-dark">{{ ucfirst($account->role) }}</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 sm:px-6 font-mono font-bold text-cafe-dark text-sm">
                                    {{ $account->username }}
                                </td>
                                <td class="py-4 px-4 sm:px-6 font-mono text-cafe-muted tracking-widest text-sm select-none">
                                    ••••••••
                                </td>
                                <td class="py-4 px-4 sm:px-6 text-right">
                                    <button type="button"
                                            onclick="openEditRoleModal({{ $account->id }}, '{{ ucfirst($account->role) }}', '{{ addslashes($account->username) }}')"
                                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-cafe-dark hover:bg-cafe-brown text-cafe-cream text-xs font-semibold transition-all active:scale-95 shadow-xs cursor-pointer"
                                            title="Edit Akun {{ ucfirst($account->role) }}">
                                        <i data-lucide="edit-3" class="w-3.5 h-3.5 text-cafe-gold"></i>
                                        <span>Edit</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-6 text-center text-xs text-cafe-muted">Belum ada akun role.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH PENJAGA -->
<div id="add-penjaga-modal" class="fixed inset-0 z-[70] flex items-end sm:items-center justify-center p-0 sm:p-4 md:p-6 overflow-y-auto hidden">
    <div class="fixed inset-0 bg-[#290024]/50 backdrop-blur-sm transition-opacity" onclick="closeAddPenjagaModal()"></div>

    <div class="relative w-full max-w-sm bg-cafe-card rounded-t-[2rem] sm:rounded-3xl shadow-soft-lg border-t sm:border border-cafe-beige/60 z-10 flex flex-col overflow-hidden animate-in slide-in-from-bottom-5 sm:slide-in-from-bottom-0 sm:zoom-in-95 duration-200">
        <div class="sm:hidden w-full pt-3 pb-1 flex justify-center cursor-pointer shrink-0" onclick="closeAddPenjagaModal()">
            <div class="w-12 h-1.5 bg-[#775533]/25 rounded-full"></div>
        </div>

        <div class="flex items-center justify-between px-5 sm:px-6 py-4 border-b border-cafe-beige/60 bg-[#F2F5E2]/80 backdrop-blur-md shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-2xl bg-cafe-gold/20 text-cafe-dark flex items-center justify-center shrink-0">
                    <i data-lucide="user-plus" class="w-4 h-4 text-cafe-gold"></i>
                </div>
                <div>
                    <h4 class="font-serif text-base font-bold text-cafe-dark">Tambah Staf</h4>
                    <p class="text-[10px] text-cafe-muted">Daftarkan staf baru (Kitchen, Barista, atau Kasir)</p>
                </div>
            </div>
            <button type="button" onclick="closeAddPenjagaModal()" class="w-8 h-8 rounded-full bg-cafe-beige/60 hover:bg-cafe-beige text-cafe-muted hover:text-cafe-dark flex items-center justify-center transition-colors active:scale-95" aria-label="Tutup">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.staff.penjaga.store') }}" class="p-5 sm:p-6 space-y-4 text-xs">
            @csrf
            <div class="space-y-1">
                <label class="font-semibold text-cafe-dark block text-xs">Nama Staf <span class="text-cafe-danger">*</span></label>
                <div class="relative">
                    <i data-lucide="user" class="w-4 h-4 text-cafe-muted/60 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                    <input type="text" name="name" required placeholder="misal: Siti Rahma" class="w-full bg-[#F2F5E2] focus:bg-beige text-xs text-cafe-dark pl-9 pr-3.5 py-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:ring-1 focus:ring-cafe-gold/40 focus:outline-none transition-all shadow-sm">
                </div>
            </div>
            <div class="space-y-1">
                <label class="font-semibold text-cafe-dark block text-xs">Bagian Kerja <span class="text-cafe-danger">*</span></label>
                <div class="relative">
                    <select name="bagian" required class="w-full bg-[#F2F5E2] focus:bg-beige text-xs text-cafe-dark py-2.5 px-3.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all cursor-pointer">
                        <option value="kasir" selected>Kasir</option>
                        <option value="barista">Barista</option>
                        <option value="kitchen">Kitchen</option>
                    </select>
                </div>
            </div>
            <div class="space-y-1">
                <label class="font-semibold text-cafe-dark block text-xs">Status</label>
                <select name="status" class="w-full bg-[#F2F5E2] focus:bg-beige text-xs text-cafe-dark py-2.5 px-3.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all cursor-pointer appearance-none">
                    <option value="aktif" selected>Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-cafe-beige/60">
                <button type="button" onclick="closeAddPenjagaModal()" class="px-4 py-2.5 rounded-xl border border-cafe-beige bg-cafe-card hover:bg-cafe-beige/60 text-xs font-semibold text-cafe-dark transition-all active:scale-95">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-cafe-dark hover:bg-cafe-brown text-cafe-cream font-semibold text-xs shadow-sm transition-all active:scale-95">Simpan Staf</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT PENJAGA -->
<div id="edit-penjaga-modal" class="fixed inset-0 z-[70] flex items-end sm:items-center justify-center p-0 sm:p-4 md:p-6 overflow-y-auto hidden">
    <div class="fixed inset-0 bg-[#290024]/50 backdrop-blur-sm transition-opacity" onclick="closeEditPenjagaModal()"></div>

    <div class="relative w-full max-w-sm bg-cafe-card rounded-t-[2rem] sm:rounded-3xl shadow-soft-lg border-t sm:border border-cafe-beige/60 z-10 flex flex-col overflow-hidden animate-in slide-in-from-bottom-5 sm:slide-in-from-bottom-0 sm:zoom-in-95 duration-200">
        <div class="sm:hidden w-full pt-3 pb-1 flex justify-center cursor-pointer shrink-0" onclick="closeEditPenjagaModal()">
            <div class="w-12 h-1.5 bg-[#775533]/25 rounded-full"></div>
        </div>

        <div class="flex items-center justify-between px-5 sm:px-6 py-4 border-b border-cafe-beige/60 bg-[#F2F5E2]/80 backdrop-blur-md shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-2xl bg-cafe-gold/20 text-cafe-dark flex items-center justify-center shrink-0">
                    <i data-lucide="edit" class="w-4 h-4 text-cafe-gold"></i>
                </div>
                <div>
                    <h4 class="font-serif text-base font-bold text-cafe-dark">Edit Staf</h4>
                    <p class="text-[10px] text-cafe-muted" id="edit-penjaga-subtitle">Perbarui data staf</p>
                </div>
            </div>
            <button type="button" onclick="closeEditPenjagaModal()" class="w-8 h-8 rounded-full bg-cafe-beige/60 hover:bg-cafe-beige text-cafe-muted hover:text-cafe-dark flex items-center justify-center transition-colors active:scale-95" aria-label="Tutup">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="edit-penjaga-form" method="POST" action="" class="p-5 sm:p-6 space-y-4 text-xs">
            @csrf
            <div class="space-y-1">
                <label class="font-semibold text-cafe-dark block text-xs">Nama Staf <span class="text-cafe-danger">*</span></label>
                <div class="relative">
                    <i data-lucide="user" class="w-4 h-4 text-cafe-muted/60 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                    <input type="text" name="name" id="edit-penjaga-name" required class="w-full bg-[#F2F5E2] focus:bg-beige text-xs text-cafe-dark pl-9 pr-3.5 py-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:ring-1 focus:ring-cafe-gold/40 focus:outline-none transition-all shadow-sm">
                </div>
            </div>
            <div class="space-y-1">
                <label class="font-semibold text-cafe-dark block text-xs">Bagian Kerja <span class="text-cafe-danger">*</span></label>
                <div class="relative">
                    <select name="bagian" id="edit-penjaga-bagian" required class="w-full bg-[#F2F5E2] focus:bg-beige text-xs text-cafe-dark py-2.5 px-3.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all cursor-pointer">
                        <option value="kasir">Kasir</option>
                        <option value="barista">Barista</option>
                        <option value="kitchen">Kitchen</option>
                    </select>
                </div>
            </div>
            <div class="space-y-1">
                <label class="font-semibold text-cafe-dark block text-xs">Status</label>
                <select name="status" id="edit-penjaga-status" class="w-full bg-[#F2F5E2] focus:bg-beige text-xs text-cafe-dark py-2.5 px-3.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all cursor-pointer appearance-none">
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-cafe-beige/60">
                <button type="button" onclick="closeEditPenjagaModal()" class="px-4 py-2.5 rounded-xl border border-cafe-beige bg-cafe-card hover:bg-cafe-beige/60 text-xs font-semibold text-cafe-dark transition-all active:scale-95">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-cafe-dark hover:bg-cafe-brown text-cafe-cream font-semibold text-xs shadow-sm transition-all active:scale-95">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Add Penjaga Modal
    function openAddPenjagaModal() {
        document.getElementById('add-penjaga-modal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        if (window.lucide) lucide.createIcons();
    }

    function closeAddPenjagaModal() {
        document.getElementById('add-penjaga-modal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    // Edit Penjaga Modal
    function openEditPenjagaModal(id, name, bagian, status) {
        document.getElementById('edit-penjaga-form').action = `/admin/staf/penjaga/${id}/update`;
        document.getElementById('edit-penjaga-name').value = name;
        document.getElementById('edit-penjaga-bagian').value = bagian || 'kasir';
        document.getElementById('edit-penjaga-status').value = status;
        document.getElementById('edit-penjaga-subtitle').innerText = `Perbarui data: ${name}`;
        document.getElementById('edit-penjaga-modal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        if (window.lucide) lucide.createIcons();
    }

    function closeEditPenjagaModal() {
        document.getElementById('edit-penjaga-modal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeAddPenjagaModal();
            closeEditPenjagaModal();
            closeEditRoleModal();
        }
    });

    // Edit Role Account Modal (Role, Username, Password)
    function openEditRoleModal(id, role, username) {
        document.getElementById('edit-role-form').action = `/admin/staf/${id}/update`;
        document.getElementById('edit-role-title').innerText = `Edit Akun ${role}`;
        document.getElementById('edit-role-name-display').innerText = `Akun Resmi: ${role} ABS Cafe`;
        document.getElementById('edit-role-username').value = username;
        document.getElementById('edit-role-password').value = '';
        document.getElementById('edit-role-modal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        if (window.lucide) lucide.createIcons();
    }

    function closeEditRoleModal() {
        document.getElementById('edit-role-modal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
</script>

<!-- MODAL EDIT AKUN ROLE (USERNAME & PASSWORD) -->
<div id="edit-role-modal" class="fixed inset-0 z-[70] flex items-end sm:items-center justify-center p-0 sm:p-4 md:p-6 overflow-y-auto hidden">
    <div class="fixed inset-0 bg-[#290024]/50 backdrop-blur-sm transition-opacity" onclick="closeEditRoleModal()"></div>

    <div class="relative w-full max-w-sm bg-cafe-card rounded-t-[2rem] sm:rounded-3xl shadow-soft-lg border-t sm:border border-cafe-beige/60 z-10 flex flex-col overflow-hidden animate-in slide-in-from-bottom-5 sm:slide-in-from-bottom-0 sm:zoom-in-95 duration-200">
        <div class="sm:hidden w-full pt-3 pb-1 flex justify-center cursor-pointer shrink-0" onclick="closeEditRoleModal()">
            <div class="w-12 h-1.5 bg-[#775533]/25 rounded-full"></div>
        </div>

        <div class="flex items-center justify-between px-5 sm:px-6 py-4 border-b border-cafe-beige/60 bg-[#F2F5E2]/80 backdrop-blur-md shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-2xl bg-cafe-gold/20 text-cafe-dark flex items-center justify-center shrink-0">
                    <i data-lucide="key" class="w-4 h-4 text-cafe-gold"></i>
                </div>
                <div>
                    <h4 class="font-serif text-base font-bold text-cafe-dark" id="edit-role-title">Edit Akun Role</h4>
                    <p class="text-[10px] text-cafe-muted" id="edit-role-name-display">Kelola username dan password</p>
                </div>
            </div>
            <button type="button" onclick="closeEditRoleModal()" class="w-8 h-8 rounded-full bg-cafe-beige/60 hover:bg-cafe-beige text-cafe-muted hover:text-cafe-dark flex items-center justify-center transition-colors active:scale-95 cursor-pointer" aria-label="Tutup">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="edit-role-form" method="POST" action="" class="p-5 sm:p-6 space-y-4 text-xs">
            @csrf
            <div class="space-y-1.5">
                <label class="font-semibold text-cafe-dark block text-xs">Username Login <span class="text-cafe-danger">*</span></label>
                <div class="relative">
                    <i data-lucide="at-sign" class="w-4 h-4 text-cafe-muted/60 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                    <input type="text"
                           name="username"
                           id="edit-role-username"
                           required
                           placeholder="username"
                           class="w-full bg-[#F2F5E2] focus:bg-beige text-xs font-mono font-bold text-cafe-dark pl-9 pr-3.5 py-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:ring-1 focus:ring-cafe-gold/40 focus:outline-none transition-all shadow-sm">
                </div>
                <p class="text-[10px] text-cafe-muted">Hanya boleh huruf kecil, angka, titik, atau garis bawah.</p>
            </div>

            <div class="space-y-1.5">
                <label class="font-semibold text-cafe-dark block text-xs">Password Baru</label>
                <div class="relative">
                    <i data-lucide="lock" class="w-4 h-4 text-cafe-muted/60 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                    <input type="password"
                           name="password"
                           id="edit-role-password"
                           minlength="6"
                           placeholder="Kosongkan jika tidak ingin mengubah"
                           class="w-full bg-[#F2F5E2] focus:bg-beige text-xs text-cafe-dark pl-9 pr-3.5 py-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:ring-1 focus:ring-cafe-gold/40 focus:outline-none transition-all shadow-sm">
                </div>
                <p class="text-[10px] text-cafe-muted">Minimal 6 karakter. Biarkan kosong jika tidak ingin mengganti kata sandi.</p>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-cafe-beige/60">
                <button type="button" onclick="closeEditRoleModal()" class="px-4 py-2.5 rounded-xl border border-cafe-beige bg-cafe-card hover:bg-cafe-beige/60 text-xs font-semibold text-cafe-dark transition-all active:scale-95 cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-cafe-dark hover:bg-cafe-brown text-cafe-cream font-semibold text-xs shadow-sm transition-all active:scale-95 cursor-pointer">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
