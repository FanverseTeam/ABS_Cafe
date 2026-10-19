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

        <button onclick="openUserModal()" class="px-5 py-2.5 rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream font-semibold text-xs shadow-md transition-all flex items-center gap-2">
            <i data-lucide="user-plus" class="w-4 h-4"></i>
            <span>Tambah Staf Baru</span>
        </button>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="bg-cafe-card p-4 sm:p-5 rounded-2xl shadow-soft border border-cafe-beige/40">
        <form method="GET" action="{{ route('admin.staff.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="relative">
                <i data-lucide="search" class="w-4 h-4 text-cafe-muted absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, username..." class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs text-cafe-text placeholder:text-cafe-muted/70 pl-9 pr-3.5 py-2.5 rounded-full border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all">
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

    <!-- Mobile Staff Cards (Hanya Tampil di Layar < md) -->
    <div class="md:hidden space-y-3">
        @forelse($users as $user)
            <div class="bg-cafe-card rounded-2xl p-4 shadow-soft border border-cafe-beige/60 space-y-3">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-3 min-w-0">
                        <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full object-cover bg-cafe-beige border border-cafe-gold/30 shrink-0">
                        <div class="min-w-0">
                            <h4 class="font-bold text-xs sm:text-sm text-cafe-dark truncate">{{ $user->name }}</h4>
                            <p class="text-[10px] text-cafe-muted font-mono truncate">{{ '@' . $user->username }}</p>
                        </div>
                    </div>

                    <div class="shrink-0 flex items-center gap-1.5">
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
                    </div>
                </div>

                <div class="text-[11px] text-cafe-muted space-y-0.5 pt-1 border-t border-cafe-beige/40">
                    <p class="flex items-center gap-1.5">
                        <i data-lucide="mail" class="w-3.5 h-3.5 text-cafe-gold shrink-0"></i>
                        <span class="truncate">{{ $user->email }}</span>
                    </p>
                    @if($user->phone)
                        <p class="flex items-center gap-1.5">
                            <i data-lucide="phone" class="w-3.5 h-3.5 text-cafe-gold shrink-0"></i>
                            <span>{{ $user->phone }}</span>
                        </p>
                    @endif
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-2 border-t border-cafe-beige/40 gap-2">
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
                        <button onclick="editUserModal({{ json_encode($user) }})" class="p-2 rounded-full bg-cafe-beige/70 text-cafe-dark hover:bg-cafe-beige" title="Edit">
                            <i data-lucide="edit" class="w-3.5 h-3.5"></i>
                        </button>
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
                </div>
            </div>
        @empty
            <div class="p-8 text-center text-xs text-cafe-muted bg-cafe-card rounded-2xl border border-cafe-beige/60">
                Tidak ada data staf yang sesuai dengan filter.
            </div>
        @endforelse
    </div>

    <!-- Users Table Container (Tampil di Layar >= md) -->
    <div class="hidden md:block bg-cafe-card rounded-2xl shadow-soft border border-cafe-beige/40 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#F2F5E2]/70 border-b border-cafe-beige text-cafe-muted uppercase font-semibold text-[11px] tracking-wider">
                    <tr>
                        <th class="py-3.5 pl-6">Foto &amp; Nama</th>
                        <th class="py-3.5 px-3">Email / Username</th>
                        <th class="py-3.5 px-3">No. HP</th>
                        <th class="py-3.5 px-3">Peran</th>
                        <th class="py-3.5 px-3">Status</th>
                        <th class="py-3.5 px-3">Login Terakhir</th>
                        <th class="py-3.5 pr-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-cafe-beige/30">
                    @forelse($users as $idx => $user)
                        <tr class="hover:bg-cafe-cream/80 transition-colors {{ $idx % 2 === 1 ? 'bg-[#FAFCF3]/40' : '' }}">
                            <td class="py-4 pl-6">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="w-9 h-9 rounded-full object-cover bg-cafe-beige border border-cafe-gold/30 shrink-0">
                                    <span class="font-semibold text-cafe-dark text-xs">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-3">
                                <p class="text-cafe-text font-medium">{{ $user->email }}</p>
                                <p class="text-[11px] text-cafe-muted font-mono">{{ '@' . $user->username }}</p>
                            </td>
                            <td class="py-4 px-3 text-cafe-muted">{{ $user->phone ?? '-' }}</td>
                            <td class="py-4 px-3">
                                @if($user->role === 'admin')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-cafe-gold/25 text-cafe-dark uppercase">Admin</span>
                                @elseif($user->role === 'kasir')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-cafe-info/15 text-cafe-info uppercase">Kasir</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-cafe-beige/80 text-cafe-dark uppercase">Staf</span>
                                @endif
                            </td>
                            <td class="py-4 px-3">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold {{ $user->status === 'aktif' ? 'bg-cafe-success/15 text-cafe-success' : 'bg-cafe-muted/15 text-cafe-muted' }}">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>
                            <td class="py-4 px-3 text-cafe-muted">
                                {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Belum pernah' }}
                            </td>
                            <td class="py-4 pr-6 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Toggle Aktif/Nonaktif -->
                                    <form action="{{ route('admin.staff.toggle.status', $user->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="p-1.5 rounded-full hover:bg-cafe-beige/60 text-cafe-muted hover:text-cafe-dark" title="{{ $user->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}">
                                            <i data-lucide="{{ $user->status === 'aktif' ? 'user-x' : 'user-check' }}" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </form>

                                    <!-- Reset Password -->
                                    <button onclick="openResetPassModal({{ $user->id }}, '{{ addslashes($user->name) }}')" class="p-1.5 rounded-full hover:bg-cafe-beige/60 text-cafe-gold hover:text-cafe-dark" title="Reset Password">
                                        <i data-lucide="key-round" class="w-3.5 h-3.5"></i>
                                    </button>

                                    <!-- Edit -->
                                    <button onclick="editUserModal({{ json_encode($user) }})" class="p-1.5 rounded-full hover:bg-cafe-beige/60 text-cafe-brown hover:text-cafe-dark" title="Edit">
                                        <i data-lucide="edit" class="w-3.5 h-3.5"></i>
                                    </button>

                                    <!-- Delete -->
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
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-xs text-cafe-muted">
                                Tidak ada data pengguna yang sesuai dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 sm:p-5 border-t border-cafe-beige/50">
            {{ $users->links() }}
        </div>
    </div>
</div>

<!-- MODAL FORM TAMBAH / EDIT USER (Bottom Sheet on Mobile) -->
<div id="user-modal" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-6 overflow-y-auto hidden">
    <div class="fixed inset-0 bg-[#290024]/60 backdrop-blur-sm" onclick="closeUserModal()"></div>

    <div class="relative w-full max-w-lg bg-cafe-card rounded-t-[2rem] sm:rounded-2xl shadow-soft-lg border-t sm:border border-cafe-beige/60 overflow-hidden z-10 pb-[max(1rem,env(safe-area-inset-bottom))]">
        <!-- Handle for mobile -->
        <div class="sm:hidden w-full pt-3 pb-1 flex justify-center cursor-pointer" onclick="closeUserModal()">
            <div class="w-12 h-1 bg-[#775533]/30 rounded-full"></div>
        </div>

        <div class="flex items-center justify-between px-6 py-4 border-b border-cafe-beige/50 bg-[#F2F5E2]/40">
            <div>
                <h3 class="font-serif text-lg font-bold text-cafe-dark" id="user-modal-title">Tambah Staf Baru</h3>
                <p class="text-[10px] sm:text-xs text-cafe-muted mt-0.5">Daftarkan akun kasir, barista, atau admin operasional.</p>
            </div>
            <button onclick="closeUserModal()" class="w-8 h-8 rounded-full bg-cafe-beige/60 hover:bg-cafe-beige text-cafe-muted hover:text-cafe-dark flex items-center justify-center">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="user-form" method="POST" action="{{ route('admin.staff.store') }}" class="p-6 space-y-4 text-xs">
            @csrf
            <div class="space-y-1">
                <label class="font-semibold text-cafe-dark">Nama Lengkap *</label>
                <input type="text" name="name" id="u-name" required placeholder="misal: Siti Rahma" class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="font-semibold text-cafe-dark">Username *</label>
                    <input type="text" name="username" id="u-username" required placeholder="siti_kasir" class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                </div>
                <div class="space-y-1">
                    <label class="font-semibold text-cafe-dark">Email *</label>
                    <input type="email" name="email" id="u-email" required placeholder="siti@abscafe.com" class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="font-semibold text-cafe-dark">Peran / Jabatan *</label>
                    <select name="role" id="u-role" required class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                        <option value="kasir">Kasir</option>
                        <option value="staf">Staf (Barista/Kitchen)</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="font-semibold text-cafe-dark">Nomor Telepon (WhatsApp)</label>
                    <input type="text" name="phone" id="u-phone" placeholder="08123456789" class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                </div>
            </div>

            <div class="space-y-1" id="u-pass-container">
                <label class="font-semibold text-cafe-dark">Password Awal *</label>
                <input type="password" name="password" id="u-pass" placeholder="••••••••" class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                <p class="text-[10px] text-cafe-muted" id="u-pass-note">Kosongkan jika tidak ingin mengubah password saat mengedit.</p>
            </div>

            <div class="space-y-1">
                <label class="font-semibold text-cafe-dark">Status Akun *</label>
                <select name="status" id="u-status" required class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-cafe-beige">
                <button type="button" onclick="closeUserModal()" class="px-5 py-2.5 rounded-full text-xs font-semibold text-cafe-dark hover:bg-cafe-beige/60">Batal</button>
                <button type="submit" class="px-6 py-2.5 rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream font-semibold text-xs shadow-md transition-all">Simpan Pengguna</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL RESET PASSWORD -->
<div id="reset-pass-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 overflow-y-auto hidden">
    <div class="fixed inset-0 bg-[#290024]/40 backdrop-blur-sm" onclick="closeResetPassModal()"></div>

    <div class="relative w-full max-w-sm bg-cafe-card rounded-2xl shadow-soft-lg border border-cafe-beige/60 overflow-hidden z-10 my-8 p-6 space-y-4">
        <div>
            <h4 class="font-serif text-lg font-bold text-cafe-dark">Reset Password</h4>
            <p class="text-xs text-cafe-muted mt-0.5" id="reset-pass-name">-</p>
        </div>

        <form id="reset-pass-form" method="POST" action="" class="space-y-3 text-xs">
            @csrf
            <div class="space-y-1">
                <label class="font-semibold text-cafe-dark">Password Baru</label>
                <input type="text" name="new_password" value="password123" required class="w-full bg-[#E3DEA4]/50 focus:bg-white text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none font-mono">
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-cafe-beige/60">
                <button type="button" onclick="closeResetPassModal()" class="px-4 py-2 rounded-full text-xs font-semibold text-cafe-dark hover:bg-cafe-beige/60">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream font-semibold text-xs shadow-sm">Reset Sekarang</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openUserModal() {
        document.getElementById('user-modal-title').innerText = 'Tambah Staf Baru';
        document.getElementById('user-form').action = "{{ route('admin.staff.store') }}";
        document.getElementById('u-name').value = '';
        document.getElementById('u-username').value = '';
        document.getElementById('u-email').value = '';
        document.getElementById('u-role').value = 'kasir';
        document.getElementById('u-phone').value = '';
        document.getElementById('u-pass').required = true;
        document.getElementById('u-pass').value = '';
        document.getElementById('u-pass-note').classList.add('hidden');
        document.getElementById('u-status').value = 'aktif';
        document.getElementById('user-modal').classList.remove('hidden');
    }

    function editUserModal(user) {
        document.getElementById('user-modal-title').innerText = `Edit Staf: ${user.name}`;
        document.getElementById('user-form').action = `/admin/staf/${user.id}/update`;
        document.getElementById('u-name').value = user.name;
        document.getElementById('u-username').value = user.username || '';
        document.getElementById('u-email').value = user.email;
        document.getElementById('u-role').value = user.role;
        document.getElementById('u-phone').value = user.phone || '';
        document.getElementById('u-pass').required = false;
        document.getElementById('u-pass').value = '';
        document.getElementById('u-pass-note').classList.remove('hidden');
        document.getElementById('u-status').value = user.status;
        document.getElementById('user-modal').classList.remove('hidden');
    }

    function closeUserModal() {
        document.getElementById('user-modal').classList.add('hidden');
    }

    function openResetPassModal(id, name) {
        document.getElementById('reset-pass-name').innerText = `Atur password baru untuk ${name}`;
        document.getElementById('reset-pass-form').action = `/admin/staf/${id}/reset-password`;
        document.getElementById('reset-pass-modal').classList.remove('hidden');
    }

    function closeResetPassModal() {
        document.getElementById('reset-pass-modal').classList.add('hidden');
    }
</script>
@endsection
