@extends('owner.layouts.app')

@section('title', 'Promo & Diskon')
@section('page_title', 'Promo & Diskon')

@php
    $rp = fn ($v) => 'Rp ' . number_format((float) $v, 0, ',', '.');
    $typeLabel = fn ($t) => ['persen' => 'Persen', 'nominal' => 'Nominal', 'buy_x_get_y' => 'Buy X Get Y'][$t] ?? $t;
@endphp

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
            <div>
                <h2 class="font-serif text-2xl sm:text-3xl font-bold text-cafe-dark">Kampanye Promo & Diskon</h2>
                <p class="text-xs text-cafe-muted mt-1">Buat, pantau, dan nonaktifkan promo untuk meningkatkan penjualan.</p>
            </div>
            <button onclick="openPromoModal()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream text-xs font-semibold transition-colors">
                <i data-lucide="plus" class="w-4 h-4"></i> Buat Promo
            </button>
        </div>

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
                            <button type="button" onclick="editPromo({{ $promo->id }})" class="p-2 rounded-xl text-cafe-muted hover:text-cafe-dark hover:bg-cafe-beige/60 transition-colors" title="Ubah">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                            </button>
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
                                <button type="button" onclick="editPromo({{ $promo->id }})" class="p-2 rounded-xl text-cafe-muted hover:text-cafe-dark hover:bg-cafe-beige/60 transition-colors" title="Ubah">
                                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                </button>
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

    <!-- Promo Modal -->
    <div id="promo-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-[#290024]/60 backdrop-blur-sm" onclick="closePromoModal()"></div>
        <div class="relative bg-cafe-card rounded-3xl shadow-soft-lg border border-cafe-beige/60 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-cafe-card border-b border-cafe-beige/50 px-6 py-4 flex items-center justify-between">
                <h3 id="promo-modal-title" class="font-serif font-bold text-cafe-dark">Buat Promo Baru</h3>
                <button onclick="closePromoModal()" class="p-2 rounded-xl text-cafe-muted hover:text-cafe-dark hover:bg-cafe-beige/60 transition-colors">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <form id="promo-form" method="POST" action="{{ route('owner.promos.store') }}" class="p-6 space-y-4">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2 space-y-1.5">
                        <label class="text-xs font-semibold text-cafe-text">Judul Promo</label>
                        <input type="text" name="title" id="p-title" required class="w-full bg-cafe-cream/60 focus:bg-white text-sm text-cafe-text px-4 py-2.5 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none focus:ring-2 focus:ring-cafe-gold/20 transition-all">
                    </div>

                    <div class="sm:col-span-2 space-y-1.5">
                        <label class="text-xs font-semibold text-cafe-text">Deskripsi</label>
                        <textarea name="description" id="p-description" rows="2" required class="w-full bg-cafe-cream/60 focus:bg-white text-sm text-cafe-text px-4 py-2.5 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none focus:ring-2 focus:ring-cafe-gold/20 transition-all"></textarea>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-cafe-text">Tipe</label>
                        <select name="type" id="p-type" class="w-full bg-cafe-cream/60 focus:bg-white text-sm text-cafe-text px-4 py-2.5 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all">
                            <option value="persen">Persen (%)</option>
                            <option value="nominal">Nominal (Rp)</option>
                            <option value="buy_x_get_y">Buy X Get Y</option>
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-cafe-text">Nilai</label>
                        <input type="number" step="0.01" min="0" name="value" id="p-value" required class="w-full bg-cafe-cream/60 focus:bg-white text-sm text-cafe-text px-4 py-2.5 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all">
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-cafe-text">Berlaku Untuk</label>
                        <select name="applies_to" id="p-applies" onchange="toggleCategoryField()" class="w-full bg-cafe-cream/60 focus:bg-white text-sm text-cafe-text px-4 py-2.5 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all">
                            <option value="semua">Semua Menu</option>
                            <option value="kategori">Kategori Tertentu</option>
                            <option value="menu">Menu Tertentu</option>
                        </select>
                    </div>

                    <div class="space-y-1.5" id="p-category-wrap">
                        <label class="text-xs font-semibold text-cafe-text">Target Kategori</label>
                        <select name="target_category_id" id="p-category" class="w-full bg-cafe-cream/60 focus:bg-white text-sm text-cafe-text px-4 py-2.5 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all">
                            <option value="">- Pilih Kategori -</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-cafe-text">Mulai</label>
                        <input type="date" name="start_date" id="p-start" required class="w-full bg-cafe-cream/60 focus:bg-white text-sm text-cafe-text px-4 py-2.5 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all">
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-cafe-text">Berakhir</label>
                        <input type="date" name="end_date" id="p-end" required class="w-full bg-cafe-cream/60 focus:bg-white text-sm text-cafe-text px-4 py-2.5 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all">
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-cafe-text">Min. Belanja (Rp)</label>
                        <input type="number" step="0.01" min="0" name="min_spend" id="p-minspend" value="0" class="w-full bg-cafe-cream/60 focus:bg-white text-sm text-cafe-text px-4 py-2.5 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all">
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-cafe-text">Kuota Pemakaian <span class="text-cafe-muted font-normal">(opsional)</span></label>
                        <input type="number" min="0" name="quota" id="p-quota" placeholder="Tanpa batas" class="w-full bg-cafe-cream/60 focus:bg-white text-sm text-cafe-text px-4 py-2.5 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all">
                    </div>

                    <div class="sm:col-span-2 space-y-1.5">
                        <label class="text-xs font-semibold text-cafe-text">Banner URL <span class="text-cafe-muted font-normal">(opsional)</span></label>
                        <input type="text" name="banner" id="p-banner" placeholder="https://..." class="w-full bg-cafe-cream/60 focus:bg-white text-sm text-cafe-text px-4 py-2.5 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all">
                    </div>
                </div>

                <label class="flex items-center gap-2 cursor-pointer text-xs text-cafe-text select-none">
                    <input type="checkbox" name="is_active" id="p-active" value="1" checked class="w-4 h-4 rounded accent-[#290024]">
                    <span>Aktifkan promo</span>
                </label>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" onclick="closePromoModal()" class="px-4 py-2.5 rounded-full bg-cafe-beige/60 hover:bg-cafe-beige text-cafe-dark text-xs font-semibold transition-colors">Batal</button>
                    <button type="submit" class="px-5 py-2.5 rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream text-xs font-semibold transition-colors">Simpan Promo</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@php
    $promoData = $promos->mapWithKeys(fn ($p) => [$p->id => [
        'title' => $p->title,
        'description' => $p->description,
        'type' => $p->type,
        'value' => $p->value,
        'applies_to' => $p->applies_to,
        'target_category_id' => $p->target_category_id,
        'start_date' => \Carbon\Carbon::parse($p->start_date)->toDateString(),
        'end_date' => \Carbon\Carbon::parse($p->end_date)->toDateString(),
        'banner' => $p->banner,
        'min_spend' => $p->min_spend,
        'quota' => $p->quota,
        'is_active' => (bool) $p->is_active,
    ]]);
@endphp

@push('scripts')
<script>
    lucide.createIcons();

    const promos = @json($promoData);
    const storeUrl = "{{ route('owner.promos.store') }}";
    const updateUrlTpl = "{{ route('owner.promos.update', ':id') }}";

    function toggleCategoryField() {
        const applies = document.getElementById('p-applies').value;
        document.getElementById('p-category-wrap').style.display = applies === 'kategori' ? '' : 'none';
    }

    function openPromoModal() {
        document.getElementById('promo-form').reset();
        document.getElementById('promo-form').action = storeUrl;
        document.getElementById('promo-modal-title').textContent = 'Buat Promo Baru';
        document.getElementById('p-active').checked = true;
        document.getElementById('p-minspend').value = 0;
        toggleCategoryField();
        document.getElementById('promo-modal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function editPromo(id) {
        const p = promos[id];
        if (!p) return;
        document.getElementById('promo-form').action = updateUrlTpl.replace(':id', id);
        document.getElementById('promo-modal-title').textContent = 'Ubah Promo';
        document.getElementById('p-title').value = p.title;
        document.getElementById('p-description').value = p.description;
        document.getElementById('p-type').value = p.type;
        document.getElementById('p-value').value = p.value;
        document.getElementById('p-applies').value = p.applies_to;
        document.getElementById('p-category').value = p.target_category_id || '';
        document.getElementById('p-start').value = p.start_date;
        document.getElementById('p-end').value = p.end_date;
        document.getElementById('p-banner').value = p.banner || '';
        document.getElementById('p-minspend').value = p.min_spend;
        document.getElementById('p-quota').value = p.quota ?? '';
        document.getElementById('p-active').checked = p.is_active;
        toggleCategoryField();
        document.getElementById('promo-modal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closePromoModal() {
        document.getElementById('promo-modal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
</script>
@endpush
