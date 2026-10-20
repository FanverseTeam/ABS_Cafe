<div id="merge-menu-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 overflow-y-auto hidden">
    <div class="fixed inset-0 bg-[#290024]/60 backdrop-blur-sm" onclick="closeMergeMenuModal()"></div>

    <div class="relative w-full max-w-2xl bg-cafe-card rounded-2xl shadow-soft-lg border border-cafe-beige/60 overflow-hidden z-10 my-8">
        <div class="flex items-center justify-between px-6 py-4 border-b border-cafe-beige/50 bg-[#F2F5E2]/40">
            <div>
                <h3 class="font-serif text-lg font-bold text-cafe-dark">Gabungkan Menu</h3>
                <p class="text-[10px] sm:text-xs text-cafe-muted mt-0.5">Pilih dua menu atau lebih dengan nama dasar dan kategori yang sama. Menu lama akan diarsipkan, bukan dihapus permanen.</p>
            </div>
            <button type="button" onclick="closeMergeMenuModal()" class="w-8 h-8 rounded-full bg-cafe-beige/60 hover:bg-cafe-beige text-cafe-muted hover:text-cafe-dark flex items-center justify-center transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form method="POST" action="{{ $mergeAction }}" onsubmit="return confirm('Gabungkan menu yang dipilih? Menu lama akan diarsipkan dan riwayat pesanan tetap tersimpan.');">
            @csrf

            <div class="p-5 max-h-[55vh] overflow-y-auto space-y-3">
                @forelse($menus->groupBy(fn ($menu) => $menu->category?->name ?? 'Tanpa Kategori') as $categoryName => $categoryMenus)
                    <div class="rounded-2xl border border-cafe-beige/60 overflow-hidden">
                        <div class="px-3 py-2 bg-[#F2F5E2]/70 text-[10px] font-bold uppercase tracking-wide text-cafe-muted">{{ $categoryName }}</div>
                        <div class="divide-y divide-cafe-beige/40">
                            @foreach($categoryMenus as $menu)
                                <label class="flex items-center gap-3 px-3 py-2.5 cursor-pointer hover:bg-cafe-cream/60 transition-colors">
                                    <input type="checkbox" name="menu_ids[]" value="{{ $menu->id }}" class="w-4 h-4 rounded accent-[#290024]">
                                    <span class="min-w-0 flex-1">
                                        <span class="block text-xs font-semibold text-cafe-dark truncate">{{ $menu->name }}</span>
                                        <span class="block text-[10px] text-cafe-muted">{{ $menu->priceRangeLabel() }}@if($menu->variants->isNotEmpty()) &bull; {{ $menu->variantNameList() }}@endif</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-cafe-muted text-center py-6">Belum ada menu yang dapat digabungkan.</p>
                @endforelse
            </div>

            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-cafe-beige/60 bg-[#F2F5E2]/40">
                <button type="button" onclick="closeMergeMenuModal()" class="px-5 py-2.5 rounded-full text-xs font-semibold text-cafe-dark hover:bg-cafe-beige/60">Batal</button>
                <button type="submit" class="px-6 py-2.5 rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream font-semibold text-xs shadow-md transition-all">Gabungkan Menu</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openMergeMenuModal() {
        document.getElementById('merge-menu-modal')?.classList.remove('hidden');
    }

    function closeMergeMenuModal() {
        document.getElementById('merge-menu-modal')?.classList.add('hidden');
    }
</script>
