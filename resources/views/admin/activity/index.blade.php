@extends('admin.layouts.app')

@section('title', 'Log Aktivitas Sistem')
@section('page_title', 'Log Aktivitas')

@section('content')
<div class="space-y-4 lg:space-y-6">
    <!-- Breadcrumb (Desktop only) -->
    <x-breadcrumb
        :items="[
            ['label' => 'Admin', 'url' => route('admin.activity.index'), 'icon' => 'home'],
            ['label' => 'Log Aktivitas'],
        ]"
        class="mb-3"
    />

    <!-- Page Header & Action (Desktop only) -->
    <div class="hidden lg:flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-serif text-2xl font-bold text-cafe-dark">Catatan Audit &amp; Aktivitas</h2>
            <p class="text-xs text-cafe-muted mt-0.5">Rekam jejak seluruh aksi admin dan kasir: perubahan harga, pembatalan pesanan, dan penyesuaian stok</p>
        </div>

        <a href="{{ route('admin.activity.export') }}" class="px-4 py-2.5 rounded-full bg-cafe-card hover:bg-cafe-beige text-cafe-dark font-semibold text-xs border border-cafe-beige/80 transition-all flex items-center gap-2 shadow-xs">
            <i data-lucide="download" class="w-4 h-4 text-cafe-muted"></i>
            <span>Ekspor CSV</span>
        </a>
    </div>

    {{-- MOBILE / TABLET CONTROLS (lg:hidden) --}}
    <div class="lg:hidden space-y-2.5">
        <x-mobile.search-bar
            name="search"
            value="{{ request('search') }}"
            placeholder="Cari aktivitas..."
            :hiddenFields="[
                'type' => request('type', 'all'),
                'user' => request('user', 'all'),
            ]"
        />

        {{-- Single-row filter: Type Chips + User Filter Sheet --}}
        <div class="flex items-center gap-2">
            <div class="flex-1 min-w-0">
                @php
                    $typeOptions = [
                        ['label' => 'Semua', 'val' => 'all'],
                        ['label' => 'Pesanan', 'val' => 'pesanan'],
                        ['label' => 'Menu', 'val' => 'menu'],
                        ['label' => 'Stok', 'val' => 'stok'],
                        ['label' => 'Promo', 'val' => 'promo'],
                        ['label' => 'Pengguna', 'val' => 'pengguna'],
                        ['label' => 'Sistem', 'val' => 'sistem'],
                    ];
                    $currentType = request('type', 'all');
                    $typeChips = array_map(function($opt) use ($currentType) {
                        return [
                            'label'  => $opt['label'],
                            'href'   => route('admin.activity.index', array_merge(request()->except('page'), ['type' => $opt['val']])),
                            'active' => $currentType === $opt['val'],
                        ];
                    }, $typeOptions);
                @endphp
                <x-mobile.chip-scroller :chips="$typeChips" />
            </div>

            {{-- User Filter Sheet --}}
            @php
                $userActive = request('user') && request('user') !== 'all';
            @endphp
            <x-mobile.filter-sheet id="activity-user-sheet" :badgeCount="$userActive ? 1 : 0" label="Staf">
                <form method="GET" action="{{ route('admin.activity.index') }}" class="space-y-4">
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    @if(request('type'))
                        <input type="hidden" name="type" value="{{ request('type') }}">
                    @endif

                    <div>
                        <label class="block text-xs font-bold text-cafe-dark mb-2">Filter Berdasarkan Staf</label>
                        <div class="space-y-1.5 max-h-60 overflow-y-auto">
                            <label class="flex items-center gap-2 p-2.5 rounded-xl border border-cafe-beige/60 bg-cafe-beige/20 text-xs cursor-pointer">
                                <input type="radio" name="user" value="all" {{ !request('user') || request('user') === 'all' ? 'checked' : '' }} class="text-cafe-dark focus:ring-0">
                                <span class="font-medium text-cafe-text">Semua Pengguna</span>
                            </label>
                            @foreach($users as $userName)
                                <label class="flex items-center gap-2 p-2.5 rounded-xl border border-cafe-beige/60 bg-cafe-beige/20 text-xs cursor-pointer">
                                    <input type="radio" name="user" value="{{ $userName }}" {{ request('user') === $userName ? 'checked' : '' }} class="text-cafe-dark focus:ring-0">
                                    <span class="font-medium text-cafe-text">{{ $userName }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <a href="{{ route('admin.activity.index', request()->except('user')) }}" class="flex-1 py-2.5 text-center rounded-xl bg-cafe-beige/40 text-cafe-dark text-xs font-bold">Reset</a>
                        <button type="submit" class="flex-1 py-2.5 text-center rounded-xl bg-cafe-dark text-cafe-cream text-xs font-bold shadow-md">Terapkan</button>
                    </div>
                </form>
            </x-mobile.filter-sheet>
        </div>
    </div>

    {{-- Mobile FAB for Export CSV --}}
    <x-mobile.fab icon="download" label="Ekspor" href="{{ route('admin.activity.export') }}" />

    <!-- Filters Toolbar (Desktop only) -->
    <div class="hidden lg:block bg-cafe-card p-4 sm:p-5 rounded-2xl shadow-soft border border-cafe-beige/40">
        <form method="GET" action="{{ route('admin.activity.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="relative">
                <i data-lucide="search" class="w-4 h-4 text-cafe-muted absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari aktivitas atau nama staf..." class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs text-cafe-text placeholder:text-cafe-muted/70 pl-9 pr-3.5 py-2.5 rounded-full border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all">
            </div>

            <div>
                <select name="type" onchange="this.form.submit()" class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs text-cafe-text py-2.5 px-3.5 rounded-full border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all cursor-pointer">
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
                <select name="user" onchange="this.form.submit()" class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs text-cafe-text py-2.5 px-3.5 rounded-full border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all cursor-pointer">
                    <option value="all">Semua Pengguna</option>
                    @foreach($users as $userName)
                        <option value="{{ $userName }}" {{ request('user') === $userName ? 'selected' : '' }}>{{ $userName }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    <!-- Table Container -->
    <x-responsive-table :paginate="$logs">
        <x-slot:headers>
            <th class="py-3.5 pl-6">Waktu Kejadian</th>
            <th class="py-3.5 px-3">Pengguna</th>
            <th class="py-3.5 px-3">Peran</th>
            <th class="py-3.5 px-3">Kategori</th>
            <th class="py-3.5 px-3">Rincian Aktivitas</th>
            <th class="py-3.5 pr-6 text-right">Alamat IP</th>
        </x-slot:headers>

        @forelse($logs as $idx => $log)
            <x-responsive-table.row :index="$idx">
                <!-- Mobile Card Layout -->
                <x-slot:title>
                    <span class="font-mono text-[11px] text-cafe-muted">{{ $log->created_at->format('d M Y, H:i:s') }}</span>
                </x-slot:title>

                <x-slot:badge>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-medium uppercase border border-cafe-beige/80 text-cafe-muted">
                        {{ $log->activity_type }}
                    </span>
                </x-slot:badge>

                <x-slot:subtitle>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="font-bold text-xs text-cafe-dark">{{ $log->user_name }}</span>
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase {{ $log->role === 'admin' ? 'bg-cafe-gold/20 text-cafe-dark' : ($log->role === 'kasir' ? 'bg-cafe-info/15 text-cafe-info' : 'bg-cafe-beige text-cafe-muted') }}">
                            {{ $log->role }}
                        </span>
                    </div>
                </x-slot:subtitle>

                <x-slot:content>
                    <p class="text-xs text-cafe-text font-medium leading-relaxed break-words">{{ $log->activity }}</p>
                </x-slot:content>

                <x-slot:footer>
                    <span class="font-mono text-[10px] text-cafe-muted">IP: {{ $log->ip_address ?? '127.0.0.1' }}</span>
                </x-slot:footer>

                <!-- Desktop Cells -->
                <x-slot:desktop>
                    <td class="hidden md:table-cell py-4 pl-6 text-cafe-muted font-mono whitespace-nowrap">
                        {{ $log->created_at->format('d M Y, H:i:s') }}
                    </td>
                    <td class="hidden md:table-cell py-4 px-3 font-semibold text-cafe-dark whitespace-nowrap">
                        {{ $log->user_name }}
                    </td>
                    <td class="hidden md:table-cell py-4 px-3">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $log->role === 'admin' ? 'bg-cafe-gold/20 text-cafe-dark' : ($log->role === 'kasir' ? 'bg-cafe-info/15 text-cafe-info' : 'bg-cafe-beige text-cafe-muted') }}">
                            {{ $log->role }}
                        </span>
                    </td>
                    <td class="hidden md:table-cell py-4 px-3">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-medium uppercase border border-cafe-beige/80 text-cafe-muted">
                            {{ $log->activity_type }}
                        </span>
                    </td>
                    <td class="hidden md:table-cell py-4 px-3 text-cafe-text font-medium leading-relaxed break-words">
                        {{ $log->activity }}
                    </td>
                    <td class="hidden md:table-cell py-4 pr-6 text-right font-mono text-[11px] text-cafe-muted whitespace-nowrap">
                        {{ $log->ip_address ?? '127.0.0.1' }}
                    </td>
                </x-slot:desktop>
            </x-responsive-table.row>
        @empty
            <x-responsive-table.empty colspan="6">
                Tidak ada log aktivitas yang sesuai dengan filter pencarian.
            </x-responsive-table.empty>
        @endforelse
    </x-responsive-table>
</div>
@endsection
