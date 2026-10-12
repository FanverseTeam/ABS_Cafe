@extends('admin.layouts.app')

@section('title', 'Log Aktivitas Sistem')
@section('page_title', 'Log Aktivitas')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-1.5 text-xs text-cafe-muted mb-3">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-cafe-dark flex items-center gap-1">
            <i data-lucide="home" class="w-3.5 h-3.5"></i>
            <span>Admin</span>
        </a>
        <i data-lucide="chevron-right" class="w-3 h-3 text-cafe-muted/60"></i>
        <span class="font-semibold text-cafe-dark">Log Aktivitas</span>
    </nav>

    <!-- Page Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-serif text-2xl font-bold text-cafe-dark">Catatan Audit &amp; Aktivitas</h2>
            <p class="text-xs text-cafe-muted mt-0.5">Rekam jejak seluruh aksi admin dan kasir: perubahan harga, pembatalan pesanan, dan penyesuaian stok</p>
        </div>

        <a href="{{ route('admin.logs.export') }}" class="px-4 py-2.5 rounded-full bg-cafe-card hover:bg-cafe-beige text-cafe-dark font-semibold text-xs border border-cafe-beige/80 transition-all flex items-center gap-2 shadow-xs">
            <i data-lucide="download" class="w-4 h-4 text-cafe-muted"></i>
            <span>Ekspor CSV</span>
        </a>
    </div>

    <!-- Filters Toolbar -->
    <div class="bg-cafe-card p-4 sm:p-5 rounded-2xl shadow-soft border border-cafe-beige/40">
        <form method="GET" action="{{ route('admin.logs.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="relative">
                <i data-lucide="search" class="w-4 h-4 text-cafe-muted absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari aktivitas atau nama staf..." class="w-full bg-[#EFE0CC]/40 focus:bg-white text-xs text-cafe-text placeholder:text-cafe-muted/70 pl-9 pr-3.5 py-2.5 rounded-full border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all">
            </div>

            <div>
                <select name="type" onchange="this.form.submit()" class="w-full bg-[#EFE0CC]/40 focus:bg-white text-xs text-cafe-text py-2.5 px-3.5 rounded-full border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all cursor-pointer">
                    <option value="all">Semua Jenis Aktivitas</option>
                    <option value="pesanan" {{ request('type') === 'pesanan' ? 'selected' : '' }}>Pesanan &amp; Kasir</option>
                    <option value="menu" {{ request('type') === 'menu' ? 'selected' : '' }}>Menu &amp; Harga</option>
                    <option value="stok" {{ request('type') === 'stok' ? 'selected' : '' }}>Inventaris Stok</option>
                    <option value="promo" {{ request('type') === 'promo' ? 'selected' : '' }}>Promo &amp; Diskon</option>
                    <option value="pengguna" {{ request('type') === 'pengguna' ? 'selected' : '' }}>Pengguna &amp; Akun</option>
                    <option value="sistem" {{ request('type') === 'sistem' ? 'selected' : '' }}>Sistem &amp; Pengaturan</option>
                </select>
            </div>

            <div>
                <select name="user" onchange="this.form.submit()" class="w-full bg-[#EFE0CC]/40 focus:bg-white text-xs text-cafe-text py-2.5 px-3.5 rounded-full border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all cursor-pointer">
                    <option value="all">Semua Pengguna</option>
                    @foreach($users as $userName)
                        <option value="{{ $userName }}" {{ request('user') === $userName ? 'selected' : '' }}>{{ $userName }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    <!-- Table Container -->
    <div class="bg-cafe-card rounded-2xl shadow-soft border border-cafe-beige/40 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#F6EDE0]/70 border-b border-cafe-beige text-cafe-muted uppercase font-semibold text-[11px] tracking-wider">
                    <tr>
                        <th class="py-3.5 pl-6">Waktu Kejadian</th>
                        <th class="py-3.5 px-3">Pengguna</th>
                        <th class="py-3.5 px-3">Peran</th>
                        <th class="py-3.5 px-3">Kategori</th>
                        <th class="py-3.5 px-3">Rincian Aktivitas</th>
                        <th class="py-3.5 pr-6 text-right">Alamat IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-cafe-beige/30">
                    @forelse($logs as $idx => $log)
                        <tr class="hover:bg-cafe-cream/80 transition-colors {{ $idx % 2 === 1 ? 'bg-[#FBF6EE]/40' : '' }}">
                            <td class="py-4 pl-6 text-cafe-muted font-mono whitespace-nowrap">
                                {{ $log->created_at->format('d M Y, H:i:s') }}
                            </td>
                            <td class="py-4 px-3 font-semibold text-cafe-dark whitespace-nowrap">
                                {{ $log->user_name }}
                            </td>
                            <td class="py-4 px-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $log->role === 'admin' ? 'bg-cafe-gold/20 text-cafe-dark' : ($log->role === 'kasir' ? 'bg-cafe-info/15 text-cafe-info' : 'bg-cafe-beige text-cafe-muted') }}">
                                    {{ $log->role }}
                                </span>
                            </td>
                            <td class="py-4 px-3">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-medium uppercase border border-cafe-beige/80 text-cafe-muted">
                                    {{ $log->activity_type }}
                                </span>
                            </td>
                            <td class="py-4 px-3 text-cafe-text font-medium leading-relaxed">
                                {{ $log->activity }}
                            </td>
                            <td class="py-4 pr-6 text-right font-mono text-[11px] text-cafe-muted whitespace-nowrap">
                                {{ $log->ip_address ?? '127.0.0.1' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-xs text-cafe-muted">
                                Tidak ada log aktivitas yang sesuai dengan filter pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 sm:p-5 border-t border-cafe-beige/50">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
