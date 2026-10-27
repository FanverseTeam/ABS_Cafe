@extends('admin.layouts.app')

@section('title', 'Promo / Voucher')
@section('page_title', 'Promo / Voucher')

@section('content')
<div class="space-y-4 lg:space-y-6">
    <!-- Breadcrumb (Desktop only) -->
    <x-breadcrumb
        :items="[
            ['label' => 'Admin', 'url' => route('admin.vouchers.index'), 'icon' => 'home'],
            ['label' => 'Promo & Diskon'],
        ]"
        class="mb-3"
    />

    <!-- Page Header & Action (Desktop only) -->
    <div class="hidden lg:flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-serif text-2xl font-bold text-cafe-dark">Kampanye Promo &amp; Voucher</h2>
            <p class="text-xs text-cafe-muted mt-0.5">Kelola penawaran diskon, bundling Buy 2 Get 1, dan cashback pelanggan kafe</p>
        </div>

        <a href="{{ route('admin.vouchers.create') }}" class="px-5 py-2.5 rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream font-semibold text-xs shadow-md transition-all flex items-center gap-2">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Buat Promo Baru</span>
        </a>
    </div>

    {{-- Mobile FAB for Buat Promo Baru --}}
    <x-mobile.fab icon="plus" label="Buat Promo" href="{{ route('admin.vouchers.create') }}" />

    <!-- Promo Cards Grid (1 col on mobile, 2 cols on tablet, 3 cols on desktop) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
        @forelse($promos as $promo)
            <div class="bg-cafe-card rounded-2xl shadow-soft hover:shadow-soft-lg border border-cafe-beige/50 overflow-hidden flex flex-col justify-between transition-all group">
                <div>
                    <!-- Banner Image Container (compact on mobile) -->
                    <div class="relative h-32 sm:h-36 lg:h-44 w-full bg-cafe-beige overflow-hidden">
                        <img src="{{ $promo->banner }}" alt="{{ $promo->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>

                        <!-- Status Badge Otomatis -->
                        <div class="absolute top-3 left-3">
                            @if($promo->status_label === 'berjalan')
                                <span class="px-3 py-1 rounded-full bg-[#775533] text-beige text-[10px] font-bold shadow-sm">Berjalan</span>
                            @elseif($promo->status_label === 'terjadwal')
                                <span class="px-3 py-1 rounded-full bg-[#D4954D] text-beige text-[10px] font-bold shadow-sm">Terjadwal</span>
                            @else
                                <span class="px-3 py-1 rounded-full bg-[#775533] text-beige text-[10px] font-bold shadow-sm">Berakhir</span>
                            @endif
                        </div>

                        <!-- Tipe Diskon Badge -->
                        <div class="absolute top-3 right-3">
                            <span class="px-3 py-1 rounded-full bg-cafe-gold text-cafe-dark text-[10px] font-bold shadow-sm">
                                @if($promo->type === 'persen')
                                    Diskon {{ intval($promo->value) }}%
                                @elseif($promo->type === 'nominal')
                                    Potongan Rp {{ number_format($promo->value, 0, ',', '.') }}
                                @else
                                    Buy 2 Get 1 Free
                                @endif
                            </span>
                        </div>

                        <!-- Periode Tanggal -->
                        <div class="absolute bottom-3 left-3 right-3 text-beige text-xs flex items-center gap-1.5 drop-shadow">
                            <i data-lucide="calendar" class="w-3.5 h-3.5 text-cafe-gold"></i>
                            <span>{{ $promo->start_date->format('d M') }} - {{ $promo->end_date->format('d M Y') }}</span>
                        </div>
                    </div>

                    <!-- Promo Body -->
                    <div class="p-5 space-y-2">
                        <h3 class="font-serif font-bold text-base text-cafe-dark leading-snug">{{ $promo->title }}</h3>
                        <p class="text-xs text-cafe-muted leading-relaxed line-clamp-3">{{ $promo->description }}</p>

                        <div class="pt-2 text-[11px] text-cafe-muted flex items-center gap-1">
                            <span>Berlaku untuk:</span>
                            <strong class="text-cafe-text">
                                {{ $promo->applies_to === 'semua' ? 'Seluruh Menu' : ($promo->targetCategory ? 'Kategori ' . $promo->targetCategory->name : 'Menu Tertentu') }}
                            </strong>
                        </div>
                    </div>
                </div>

                <!-- Footer Action Buttons -->
                <div class="p-4 pt-3 border-t border-cafe-beige/40 flex items-center justify-between text-xs">
                    <!-- Switch Aktif / Nonaktif -->
                    <form action="{{ route('admin.vouchers.toggle.active', $promo->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="flex items-center gap-2 cursor-pointer select-none">
                            <div class="w-8 h-4.5 rounded-full p-0.5 transition-colors {{ $promo->is_active ? 'bg-cafe-success' : 'bg-cafe-muted/40' }}">
                                <div class="w-3.5 h-3.5 rounded-full bg-beige transition-transform {{ $promo->is_active ? 'translate-x-3.5' : 'translate-x-0' }}"></div>
                            </div>
                            <span class="text-[11px] font-medium text-cafe-text">{{ $promo->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                        </button>
                    </form>

                    <div class="flex items-center gap-1">
                        <a href="{{ route('admin.vouchers.edit', $promo->id) }}" class="p-1.5 rounded-full text-cafe-brown hover:text-cafe-dark hover:bg-cafe-beige/60 transition-colors" title="Ubah Promo">
                            <i data-lucide="edit" class="w-3.5 h-3.5"></i>
                        </a>
                        <form action="{{ route('admin.vouchers.destroy', $promo->id) }}" method="POST" onsubmit="return confirm('Hapus promo ini?');" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 rounded-full text-cafe-danger hover:bg-cafe-danger/10 transition-colors" title="Hapus Promo">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-3 p-12 text-center bg-cafe-card/50 rounded-2xl border border-dashed border-cafe-beige/80">
                <i data-lucide="percent" class="w-10 h-10 text-cafe-muted mx-auto mb-2"></i>
                <h4 class="font-serif font-bold text-base text-cafe-dark">Belum Ada Promo</h4>
                <p class="text-xs text-cafe-muted mt-1">Buat kampanye promo pertama Anda untuk menarik lebih banyak pelanggan.</p>
            </div>
        @endforelse
    </div>
</div>

@endsection
