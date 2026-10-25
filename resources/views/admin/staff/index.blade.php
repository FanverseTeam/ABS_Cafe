@extends('admin.layouts.app')

@section('title', 'Manajemen User Staf & Hak Akses')
@section('page_title', 'Manajemen User Staf & Hak Akses')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-1.5 text-xs text-cafe-muted mb-3">
        <a href="{{ route('admin.staff.index') }}" class="hover:text-cafe-dark flex items-center gap-1">
            <i data-lucide="home" class="w-3.5 h-3.5"></i>
            <span>Admin</span>
        </a>
        <i data-lucide="chevron-right" class="w-3 h-3 text-cafe-muted/60"></i>
        <span class="font-semibold text-cafe-dark">Pengguna (Staf/Kasir)</span>
    </nav>

    <!-- Page Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-serif text-2xl font-bold text-cafe-dark">Manajemen Pengguna &amp; Hak Akses</h2>
            <p class="text-xs text-cafe-muted mt-0.5">Kelola akun administrator, kasir kasir, dan staf barista ABS Cafe</p>
        </div>

        <a href="{{ route('admin.staff.create') }}" class="px-5 py-2.5 rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream font-semibold text-xs shadow-md transition-all flex items-center gap-2 active:scale-95">
            <i data-lucide="user-plus" class="w-4 h-4"></i>
            <span>Tambah Staf Baru</span>
        </a>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="bg-cafe-card p-4 sm:p-5 rounded-2xl shadow-soft border border-cafe-beige/40">
        <form method="GET" action="{{ route('admin.staff.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="relative">
                <i data-lucide="search" class="w-4 h-4 text-cafe-muted absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, username..." class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs text-cafe-text placeholder:text-cafe-muted/70 pl-9 pr-3.5 py-2.5 rounded-full border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all">
            </div>

            <div>
                <select name="role" onchange="this.form.submit()" class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs text-cafe-text py-2.5 px-3.5 rounded-full border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all cursor-pointer">
                    <option value="all">Semua Peran</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="kasir" {{ request('role') === 'kasir' ? 'selected' : '' }}>Kasir</option>
                    <option value="staf" {{ request('role') === 'staf' ? 'selected' : '' }}>Staf / Barista</option>
                </select>
            </div>

            <div>
                <select name="status" onchange="this.form.submit()" class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs text-cafe-text py-2.5 px-3.5 rounded-full border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all cursor-pointer">
                    <option value="all">Semua Status</option>
                    <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Unified Responsive Table & Mobile Cards -->
    <x-responsive-table :paginate="$users">
        <x-slot:headers>
            <th class="py-3.5 pl-6">Foto &amp; Nama</th>
            <th class="py-3.5 px-3">Username</th>
            <th class="py-3.5 px-3">No. HP</th>
            <th class="py-3.5 px-3">Peran</th>
            <th class="py-3.5 px-3">Status</th>
            <th class="py-3.5 pr-6 text-right">Aksi</th>
        </x-slot:headers>

        @forelse($users as $idx => $user)
            <x-responsive-table.row :index="$idx">
                <!-- Mobile Card Layout -->
                <x-slot:title>
                    <div class="flex items-center gap-3 min-w-0">
                        <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full object-cover bg-cafe-beige border border-cafe-gold/30 shrink-0">
                        <div class="min-w-0">
                            <h4 class="font-bold text-xs sm:text-sm text-cafe-dark truncate">{{ $user->name }}</h4>
                            <p class="text-[10px] text-cafe-muted font-mono truncate">{{ '@' . $user->username }}</p>
                        </div>
                    </div>
                </x-slot:title>

                <x-slot:badge>
                    @if($user->role === 'admin')
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-cafe-gold/25 text-cafe-dark uppercase">Admin</span>
                    @elseif($user->role === 'kasir')
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-cafe-info/15 text-cafe-info uppercase">Kasir</span>
                    @else
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-cafe-beige/80 text-cafe-dark uppercase">Staf</span>
                    @endif

                    <span class="px-2 py-0.5 rounded-full text-[9px] font-semibold {{ $user->status === 'aktif' ? 'bg-cafe-success/15 text-cafe-success' : 'bg-cafe-muted/15 text-cafe-muted' }}">
                        {{ ucfirst($user->status) }}
                    </span>
                </x-slot:badge>

                <x-slot:fields>
                    <x-responsive-table.field label="Username" :value="$user->username" />
                    <x-responsive-table.field label="No. HP" :value="$user->phone ?? '-'" />
                </x-slot:fields>

                <x-slot:actions>
                    <form action="{{ route('admin.staff.toggle.status', $user->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-cafe-beige/50 text-[10px] font-semibold text-cafe-dark hover:bg-cafe-beige">
                            <i data-lucide="{{ $user->status === 'aktif' ? 'user-x' : 'user-check' }}" class="w-3.5 h-3.5"></i>
                            <span>{{ $user->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}</span>
                        </button>
                    </form>

                    <div class="flex items-center gap-1.5">
                        <button onclick="openResetPassModal({{ $user->id }}, '{{ addslashes($user->name) }}')" class="p-2 rounded-full bg-cafe-gold/20 text-cafe-dark hover:bg-cafe-gold/40" title="Reset Password">
                            <i data-lucide="key-round" class="w-3.5 h-3.5"></i>
                        </button>
                        <a href="{{ route('admin.staff.edit', $user->id) }}" class="p-2 rounded-full bg-cafe-beige/70 text-cafe-dark hover:bg-cafe-beige flex items-center justify-center transition-colors" title="Edit">
                            <i data-lucide="edit" class="w-3.5 h-3.5"></i>
                        </a>
                        @if($user->id !== Auth::id())
                            <form action="{{ route('admin.staff.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Hapus pengguna ini?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 rounded-full bg-cafe-danger/10 text-cafe-danger hover:bg-cafe-danger/20" title="Hapus">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </x-slot:actions>

                <!-- Desktop Cells -->
                <x-slot:desktop>
                    <td class="hidden md:table-cell py-4 pl-6">
                        <div class="flex items-center gap-3">
                            <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="w-9 h-9 rounded-full object-cover bg-cafe-beige border border-cafe-gold/30 shrink-0">
                            <span class="font-semibold text-cafe-dark text-xs">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td class="hidden md:table-cell py-4 px-3">
                        <p class="text-cafe-text font-medium">{{ $user->username }}</p>
                    </td>
                    <td class="hidden md:table-cell py-4 px-3 text-cafe-muted">{{ $user->phone ?? '-' }}</td>
                    <td class="hidden md:table-cell py-4 px-3">
                        @if($user->role === 'admin')
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-cafe-gold/25 text-cafe-dark uppercase">Admin</span>
                        @elseif($user->role === 'kasir')
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-cafe-info/15 text-cafe-info uppercase">Kasir</span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-cafe-beige/80 text-cafe-dark uppercase">Staf</span>
                        @endif
                    </td>
                    <td class="hidden md:table-cell py-4 px-3">
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold {{ $user->status === 'aktif' ? 'bg-cafe-success/15 text-cafe-success' : 'bg-cafe-muted/15 text-cafe-muted' }}">
                            {{ ucfirst($user->status) }}
                        </span>
                    </td>
                    <td class="hidden md:table-cell py-4 pr-6 text-right">
                        <div class="flex items-center justify-end gap-1.5">
                            <form action="{{ route('admin.staff.toggle.status', $user->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="p-1.5 rounded-full hover:bg-cafe-beige/60 text-cafe-muted hover:text-cafe-dark" title="{{ $user->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}">
                                    <i data-lucide="{{ $user->status === 'aktif' ? 'user-x' : 'user-check' }}" class="w-3.5 h-3.5"></i>
                                </button>
                            </form>
                            <button onclick="openResetPassModal({{ $user->id }}, '{{ addslashes($user->name) }}')" class="p-1.5 rounded-full hover:bg-cafe-beige/60 text-cafe-gold hover:text-cafe-dark" title="Reset Password">
                                <i data-lucide="key-round" class="w-3.5 h-3.5"></i>
                            </button>
                            <a href="{{ route('admin.staff.edit', $user->id) }}" class="p-1.5 rounded-full hover:bg-cafe-beige/60 text-cafe-brown hover:text-cafe-dark inline-flex items-center justify-center transition-colors" title="Edit">
                                <i data-lucide="edit" class="w-3.5 h-3.5"></i>
                            </a>
                            @if($user->id !== Auth::id())
                                <form action="{{ route('admin.staff.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Hapus pengguna ini?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-full hover:bg-cafe-danger/10 text-cafe-danger" title="Hapus">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </x-slot:desktop>
            </x-responsive-table.row>
        @empty
            <x-responsive-table.empty colspan="7">
                Tidak ada data pengguna yang sesuai dengan filter.
            </x-responsive-table.empty>
        @endforelse
    </x-responsive-table>
</div>

<!-- MODAL RESET PASSWORD -->
<div id="reset-pass-modal" class="fixed inset-0 z-[70] flex items-end sm:items-center justify-center p-0 sm:p-4 md:p-6 overflow-y-auto hidden">
    <div class="fixed inset-0 bg-[#290024]/50 backdrop-blur-sm transition-opacity" onclick="closeResetPassModal()"></div>

    <div class="relative w-full max-w-sm bg-cafe-card rounded-t-[2rem] sm:rounded-3xl shadow-soft-lg border-t sm:border border-cafe-beige/60 z-10 flex flex-col overflow-hidden animate-in slide-in-from-bottom-5 sm:slide-in-from-bottom-0 sm:zoom-in-95 duration-200">
        <!-- Drag Handle for Mobile -->
        <div class="sm:hidden w-full pt-3 pb-1 flex justify-center cursor-pointer shrink-0" onclick="closeResetPassModal()">
            <div class="w-12 h-1.5 bg-[#775533]/25 rounded-full"></div>
        </div>

        <div class="flex items-center justify-between px-5 sm:px-6 py-4 border-b border-cafe-beige/60 bg-[#F2F5E2]/80 backdrop-blur-md shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-2xl bg-cafe-gold/20 text-cafe-dark flex items-center justify-center shrink-0">
                    <i data-lucide="key-round" class="w-4 h-4 text-cafe-gold"></i>
                </div>
                <div>
                    <h4 class="font-serif text-base font-bold text-cafe-dark">Reset Password</h4>
                    <p class="text-[10px] text-cafe-muted truncate max-w-[12rem] sm:max-w-[14rem]" id="reset-pass-name">-</p>
                </div>
            </div>
            <button type="button" onclick="closeResetPassModal()" class="w-8 h-8 rounded-full bg-cafe-beige/60 hover:bg-cafe-beige text-cafe-muted hover:text-cafe-dark flex items-center justify-center transition-colors active:scale-95" aria-label="Tutup">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="reset-pass-form" method="POST" action="" class="p-5 sm:p-6 space-y-4 text-xs">
            @csrf
            <div class="space-y-1">
                <label class="font-semibold text-cafe-dark block text-[11px] sm:text-xs">Password Baru</label>
                <div class="relative">
                    <i data-lucide="lock" class="w-4 h-4 text-cafe-muted/60 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                    <input type="text" name="new_password" value="password123" required class="w-full bg-[#FAFCF3] focus:bg-white text-xs text-cafe-dark pl-9 pr-3.5 py-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:ring-1 focus:ring-cafe-gold/40 focus:outline-none font-mono transition-all shadow-sm">
                </div>
                <p class="text-[10px] text-cafe-muted mt-1">Default: <code class="font-mono font-bold text-cafe-dark">password123</code> (dapat diganti oleh staf setelah login)</p>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-cafe-beige/60">
                <button type="button" onclick="closeResetPassModal()" class="px-4 py-2.5 rounded-xl border border-cafe-beige bg-cafe-card hover:bg-cafe-beige/60 text-xs font-semibold text-cafe-dark transition-all active:scale-95">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-cafe-dark hover:bg-cafe-brown text-cafe-cream font-semibold text-xs shadow-sm transition-all active:scale-95">Reset Sekarang</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openResetPassModal(id, name) {
        document.getElementById('reset-pass-name').innerText = `Atur password baru untuk ${name}`;
        document.getElementById('reset-pass-form').action = `/admin/staf/${id}/reset-password`;
        document.getElementById('reset-pass-modal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        if (window.lucide) lucide.createIcons();
    }

    function closeResetPassModal() {
        document.getElementById('reset-pass-modal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeResetPassModal();
        }
    });
</script>
@endsection
