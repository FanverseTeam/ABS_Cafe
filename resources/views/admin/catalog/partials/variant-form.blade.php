<div class="rounded-2xl border border-cafe-beige/70 bg-[#F2F5E2]/50 p-4 space-y-3">
    <div>
        <label class="font-semibold text-cafe-dark">Pengelompokan Varian Menu</label>
        <p class="text-[10px] text-cafe-muted">Gunakan untuk menghubungkan menu versi berbeda (misal Hot &amp; Ice). Setiap menu tetap merupakan produk mandiri dengan Primary Key sendiri di database.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div>
            <label class="block text-xs font-semibold text-cafe-dark mb-1">Nama Grup Varian (opsional)</label>
            <input type="text"
                   name="variant_group"
                   value="{{ old('variant_group', $menu->variant_group ?? '') }}"
                   placeholder="Contoh: Latte, Cappucino, Vietnam Drip"
                   class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
            <p class="text-[9px] text-cafe-muted mt-0.5">Nama tampilan yang menyatukan menu-menu ini di halaman pelanggan &amp; POS.</p>
        </div>
        <div>
            <label class="block text-xs font-semibold text-cafe-dark mb-1">Label Varian Ini (opsional)</label>
            <input type="text"
                   name="variant_label"
                   value="{{ old('variant_label', $menu->variant_label ?? '') }}"
                   placeholder="Contoh: Hot, Ice"
                   class="w-full bg-[#E3DEA4]/40 focus:bg-beige text-xs p-2.5 rounded-xl border border-cafe-beige focus:border-cafe-gold focus:outline-none">
            <p class="text-[9px] text-cafe-muted mt-0.5">Label pada tombol pemilih varian (misal: 'Hot' untuk menu ini).</p>
        </div>
    </div>
</div>
