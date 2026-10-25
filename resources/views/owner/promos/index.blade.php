@extends('owner.layouts.app')

@section('title', 'Promo & Diskon')
@section('page_title', 'Promo & Diskon')

@php
    $rp = fn ($v) => 'Rp ' . number_format((float) $v, 0, ',', '.');
    $typeLabel = fn ($t) => ['persen' => 'Persen', 'nominal' => 'Nominal', 'buy_x_get_y' => 'Buy X Get Y'][$t] ?? $t;
@endphp

@section('content')
    <div class="space-y-4 lg:space-y-6">
        <!-- Header (Desktop only) -->
        <div class="hidden lg:flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
            <div>
                <h2 class="font-serif text-2xl sm:text-3xl font-bold text-cafe-dark">Kampanye Promo & Diskon</h2>
                <p class="text-xs text-cafe-muted mt-1">Buat, pantau, dan nonaktifkan promo untuk meningkatkan penjualan.</p>
            </div>
            <a href="{{ route('owner.promos.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream text-xs font-semibold transition-colors">
                <i data-lucide="plus" class="w-4 h-4"></i> Buat Promo
            </a>
        </div>

        {{-- Mobile FAB for Buat Promo --}}
        <x-mobile.fab icon="plus" label="Buat Promo" href="{{ route('owner.promos.create') }}" />

        <!-- Stats -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-cafe-card rounded-2xl border border-cafe-beige/60 shadow-soft p-4">
                <p class="text-[10px] uppercase tracking-wide text-cafe-muted font-semibold">Promo Aktif</p>
                <p class="font-serif text-xl font-bold text-cafe-dark mt-1">{{ $stats['active'] }} <span class="text-xs text-cafe-muted font-normal">/ {{ $stats['total'] }}</span></p>
            </div>
            <div class="bg-cafe-card rounded-2xl border border-cafe-beige/60 shadow-soft p-4">
                <p class="text-[10px] uppercase tracking-wide text-cafe-muted font-semibold">Total Diskon Diberikan</p>
                <p class="font-serif text-xl font-bold text-cafe-dark mt-1">{{ $rp($stats['total_discount']) }}</p>
            </div>
            <div class="bg-cafe-card rounded-2xl border border-cafe-beige/60 shadow-soft p-4">
                <p class="text-[10px] uppercase tracking-wide text-cafe-muted font-semibold">Penjualan dari Promo</p>
                <p class="font-serif text-xl font-bold text-cafe-dark mt-1">{{ $rp($stats['generated_sales']) }}</p>
            </div>
            <div class="bg-cafe-card rounded-2xl border border-cafe-beige/60 shadow-soft p-4">
                <p class="text-[10px] uppercase tracking-wide text-cafe-muted font-semibold">Total Kampanye</p>
                <p class="font-serif text-xl font-bold text-cafe-dark mt-1">{{ $stats['total'] }}</p>
            </div>
        </div>

        <!-- Promo List -->
        <x-responsive-table>
            <x-slot:headers>
                <th class="py-3 pl-6">Promo</th>
                <th class="py-3 px-3">Tipe</th>
                <th class="py-3 px-3">Periode</th>
                <th class="py-3 px-3 text-right">Pemakaian</th>
                <th class="py-3 px-3 text-right">Diskon</th>
                <th class="py-3 px-3 text-right">Penjualan</th>
                <th class="py-3 px-3 text-center">Status</th>
                <th class="py-3 pr-6 text-right">Aksi</th>
            </x-slot:headers>

            @forelse($promos as $idx => $promo)
                <x-responsive-table.row :index="$idx">
                    <!-- Mobile Card Layout -->
                    <x-slot:title>
                        <p class="font-bold text-xs sm:text-sm text-cafe-dark truncate">{{ $promo->title }}</p>
                    </x-slot:title>

                    <x-slot:subtitle>
                        <span class="text-cafe-muted">{{ \Carbon\Carbon::parse($promo->start_date)->translatedFormat('d M Y') }} s/d {{ \Carbon\Carbon::parse($promo->end_date)->translatedFormat('d M Y') }}</span>
                    </x-slot:subtitle>

                    <x-slot:badge>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-cafe-gold/15 text-cafe-bronze">{{ $typeLabel($promo->type) }}</span>
                        <form action="{{ route('owner.promos.toggle', $promo->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold transition-colors
                                {{ $promo->is_active ? 'bg-cafe-success/15 text-cafe-success hover:bg-cafe-success/25' : 'bg-cafe-beige/70 text-cafe-muted hover:bg-cafe-beige' }}">
                                {{ $promo->is_active ? 'Aktif' : 'Nonaktif' }}
                            </button>
                        </form>
                    </x-slot:badge>

                    <x-slot:fields>
                        <x-responsive-table.field label="Nilai Diskon" :value="($promo->type === 'persen' ? $promo->value . '%' : $rp($promo->value)) . ' (' . ucfirst($promo->applies_to) . ')'" />
                        <x-responsive-table.field label="Pemakaian">
                            <span class="font-semibold text-cafe-dark">{{ $promo->used_count }}</span>
                            @if($promo->quota !== null)
                                <span class="text-cafe-muted"> / {{ $promo->quota }} (sisa {{ $promo->remaining_quota }})</span>
                            @else
                                <span class="text-cafe-muted"> (tanpa kuota)</span>
                            @endif
                        </x-responsive-table.field>
                        <x-responsive-table.field label="Total Diskon" :value="$rp($promo->total_discount)" />
                        <x-responsive-table.field label="Penjualan Dihasilkan" :value="$rp($promo->generated_sales)" />
                    </x-slot:fields>

                    @if($promo->description || $promo->min_spend > 0)
                        <x-slot:content>
                            @if($promo->description)
                                <p class="text-xs text-cafe-muted">{{ $promo->description }}</p>
                            @endif
                            @if($promo->min_spend > 0)
                                <p class="text-[10px] text-cafe-bronze mt-1 font-semibold">Min. belanja: {{ $rp($promo->min_spend) }}</p>
                            @endif
                        </x-slot:content>
                    @endif

                    <x-slot:actions>
                        <div class="flex items-center gap-1.5 w-full justify-end">
                            <a href="{{ route('owner.promos.edit', $promo->id) }}" class="p-2 rounded-xl text-cafe-muted hover:text-cafe-dark hover:bg-cafe-beige/60 transition-colors" title="Ubah">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                            </a>
                            <form action="{{ route('owner.promos.destroy', $promo->id) }}" method="POST" onsubmit="return confirm('Hapus promo {{ $promo->title }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 rounded-xl text-cafe-muted hover:text-cafe-danger hover:bg-cafe-danger/10 transition-colors" title="Hapus">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </x-slot:actions>

                    <!-- Desktop Cells -->
                    <x-slot:desktop>
                        <td class="hidden md:table-cell py-3 pl-6 max-w-xs">
                            <p class="font-semibold text-cafe-dark truncate">{{ $promo->title }}</p>
                            <p class="text-[10px] text-cafe-muted line-clamp-2">{{ $promo->description }}</p>
                            @if($promo->min_spend > 0)
                                <p class="text-[10px] text-cafe-bronze mt-0.5">Min. belanja {{ $rp($promo->min_spend) }}</p>
                            @endif
                        </td>
                        <td class="hidden md:table-cell py-3 px-3">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-cafe-gold/15 text-cafe-bronze">{{ $typeLabel($promo->type) }}</span>
                            <p class="text-[10px] text-cafe-muted mt-1">
                                {{ $promo->type === 'persen' ? $promo->value . '%' : $rp($promo->value) }} &bull; {{ ucfirst($promo->applies_to) }}
                            </p>
                        </td>
                        <td class="hidden md:table-cell py-3 px-3 text-cafe-muted whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($promo->start_date)->translatedFormat('d M Y') }}<br>
                            <span class="text-[10px]">s/d {{ \Carbon\Carbon::parse($promo->end_date)->translatedFormat('d M Y') }}</span>
                        </td>
                        <td class="hidden md:table-cell py-3 px-3 text-right">
                            <span class="font-semibold text-cafe-dark">{{ $promo->used_count }}</span>
                            @if($promo->quota !== null)
                                <span class="text-cafe-muted"> / {{ $promo->quota }}</span>
                                <div class="mt-1 h-1.5 w-20 ml-auto rounded-full bg-cafe-beige/60 overflow-hidden">
                                    <div class="h-full rounded-full {{ $promo->remaining_quota === 0 ? 'bg-cafe-danger' : 'bg-cafe-gold' }}"
                                         style="width: {{ $promo->quota > 0 ? max(3, round($promo->used_count / $promo->quota * 100)) : 100 }}%"></div>
                                </div>
                                <p class="text-[10px] text-cafe-muted mt-0.5">sisa {{ $promo->remaining_quota }}</p>
                            @else
                                <p class="text-[10px] text-cafe-muted">tanpa kuota</p>
                            @endif
                        </td>
                        <td class="hidden md:table-cell py-3 px-3 text-right font-semibold text-cafe-dark">{{ $rp($promo->total_discount) }}</td>
                        <td class="hidden md:table-cell py-3 px-3 text-right font-semibold text-cafe-bronze">{{ $rp($promo->generated_sales) }}</td>
                        <td class="hidden md:table-cell py-3 px-3 text-center">
                            <form action="{{ route('owner.promos.toggle', $promo->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-2.5 py-1 rounded-full text-[10px] font-bold transition-colors
                                    {{ $promo->is_active ? 'bg-cafe-success/15 text-cafe-success hover:bg-cafe-success/25' : 'bg-cafe-beige/70 text-cafe-muted hover:bg-cafe-beige' }}">
                                    {{ $promo->is_active ? 'Aktif' : 'Nonaktif' }}
                                </button>
                            </form>
                        </td>
                        <td class="hidden md:table-cell py-3 pr-6 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('owner.promos.edit', $promo->id) }}" class="p-2 rounded-xl text-cafe-muted hover:text-cafe-dark hover:bg-cafe-beige/60 transition-colors" title="Ubah">
                                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                </a>
                                <form action="{{ route('owner.promos.destroy', $promo->id) }}" method="POST" onsubmit="return confirm('Hapus promo {{ $promo->title }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 rounded-xl text-cafe-muted hover:text-cafe-danger hover:bg-cafe-danger/10 transition-colors" title="Hapus">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </x-slot:desktop>
                </x-responsive-table.row>
            @empty
                <x-responsive-table.empty colspan="8">
                    Belum ada promo. Buat kampanye pertama Anda.
                </x-responsive-table.empty>
            @endforelse
        </x-responsive-table>
    </div>

@endsection

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
