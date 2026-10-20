<div class="rounded-2xl border border-cafe-beige/70 bg-[#F2F5E2]/50 p-4 space-y-3">
    <input type="hidden" name="variants_present" value="1">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
        <div>
            <label class="font-semibold text-cafe-dark">Varian Menu</label>
            <p class="text-[10px] text-cafe-muted">Tambahkan Hot, Ice, atau ukuran. Setiap varian dapat memiliki harga dan status tersedia sendiri.</p>
        </div>
        <button type="button" onclick="addVariantRow()" class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream text-[10px] font-semibold transition-colors">
            <i data-lucide="plus" class="w-3.5 h-3.5 text-[#D4954D]"></i>
            Tambah Varian
        </button>
    </div>

    <div id="menu-variants-list" class="space-y-2"></div>
    <p class="text-[10px] text-cafe-muted">Biarkan kosong jika menu tidak memiliki varian. Harga menu induk otomatis mengikuti harga varian terendah.</p>
</div>

<template id="menu-variant-template">
    <div class="variant-row grid grid-cols-1 sm:grid-cols-12 gap-2 items-end rounded-xl border border-cafe-beige/70 bg-white/70 p-2.5">
        <input type="hidden" data-variant-field="id" value="">
        <div class="sm:col-span-3">
            <label class="block text-[10px] font-semibold text-cafe-muted mb-1">Nama Varian</label>
            <input type="text" data-variant-field="name" placeholder="Hot / Ice" class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs p-2 rounded-lg border border-cafe-beige focus:border-cafe-gold focus:outline-none">
        </div>
        <div class="sm:col-span-3">
            <label class="block text-[10px] font-semibold text-cafe-muted mb-1">Ukuran</label>
            <input type="text" data-variant-field="size" placeholder="Large (opsional)" class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs p-2 rounded-lg border border-cafe-beige focus:border-cafe-gold focus:outline-none">
        </div>
        <div class="sm:col-span-3">
            <label class="block text-[10px] font-semibold text-cafe-muted mb-1">Harga (Rp)</label>
            <input type="number" data-variant-field="price" min="0" step="500" placeholder="15000" class="w-full bg-[#E3DEA4]/40 focus:bg-white text-xs p-2 rounded-lg border border-cafe-beige focus:border-cafe-gold focus:outline-none">
        </div>
        <div class="sm:col-span-2">
            <label class="flex items-center gap-2 px-2 py-2 cursor-pointer select-none">
                <input type="hidden" data-variant-field="is_available" value="0">
                <input type="checkbox" data-variant-field="is_available" value="1" checked class="w-4 h-4 rounded accent-[#290024]">
                <span class="text-[10px] font-semibold text-cafe-dark">Tersedia</span>
            </label>
        </div>
        <div class="sm:col-span-1 flex sm:justify-end">
            <button type="button" onclick="removeVariantRow(this)" class="w-8 h-8 rounded-full text-cafe-danger hover:bg-cafe-danger/10 flex items-center justify-center" title="Hapus varian">
                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
            </button>
        </div>
    </div>
</template>

<script>
    function addVariantRow(variant = {}) {
        const template = document.getElementById('menu-variant-template');
        const list = document.getElementById('menu-variants-list');

        if (! template || ! list) {
            return;
        }

        const row = template.content.firstElementChild.cloneNode(true);
        row.querySelector('[data-variant-field="id"]').value = variant.id || '';
        row.querySelector('[data-variant-field="name"]').value = variant.name || '';
        row.querySelector('[data-variant-field="size"]').value = variant.size || '';
        row.querySelector('[data-variant-field="price"]').value = variant.price ?? '';
        row.querySelectorAll('[data-variant-field="is_available"]').forEach((input) => {
            if (input.type === 'checkbox') {
                input.checked = variant.is_available !== false;
            } else {
                input.value = variant.is_available === false ? '1' : '0';
            }
        });

        list.appendChild(row);

        if (window.lucide) {
            window.lucide.createIcons();
        }
    }

    function removeVariantRow(button) {
        button.closest('.variant-row')?.remove();
    }

    function setVariantRows(variants) {
        const list = document.getElementById('menu-variants-list');
        if (! list) {
            return;
        }

        list.innerHTML = '';
        (variants || []).forEach((variant) => addVariantRow(variant));
    }

    function variantRowsPayload() {
        return Array.from(document.querySelectorAll('#menu-variants-list .variant-row'))
            .map((row) => ({
                id: row.querySelector('[data-variant-field="id"]').value || null,
                name: row.querySelector('[data-variant-field="name"]').value.trim(),
                size: row.querySelector('[data-variant-field="size"]').value.trim(),
                price: row.querySelector('[data-variant-field="price"]').value,
                is_available: row.querySelector('[data-variant-field="is_available"]:checked') ? '1' : '0',
            }))
            .filter((variant) => variant.name !== '');
    }

    function prepareVariantInputs(form) {
        const rows = Array.from(form.querySelectorAll('#menu-variants-list .variant-row'));

        rows.forEach((row, index) => {
            const name = row.querySelector('[data-variant-field="name"]').value.trim();
            if (name === '') {
                row.querySelectorAll('[data-variant-field]').forEach((input) => input.removeAttribute('name'));

                return;
            }

            row.querySelectorAll('[data-variant-field]').forEach((input) => {
                input.name = `variants[${index}][${input.dataset.variantField}]`;
            });
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('menu-form');
        if (form) {
            form.addEventListener('submit', () => prepareVariantInputs(form));
        }
    });
</script>
