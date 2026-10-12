import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';

Alpine.plugin(collapse);

document.addEventListener('alpine:init', () => {
    Alpine.store('toast', {
        items: [],
        push(message, type = 'success', duration = 4000) {
            const id = Date.now() + Math.random();
            this.items.push({ id, message, type });
            setTimeout(() => this.dismiss(id), duration);
        },
        dismiss(id) {
            this.items = this.items.filter((item) => item.id !== id);
        },
    });

    Alpine.store('confirm', {
        open: false,
        title: 'Konfirmasi',
        message: '',
        confirmLabel: 'Ya, Lanjutkan',
        cancelLabel: 'Batal',
        variant: 'danger',
        form: null,
        ask(options = {}) {
            this.open = true;
            this.title = options.title ?? 'Konfirmasi';
            this.message = options.message ?? 'Apakah Anda yakin ingin melanjutkan?';
            this.confirmLabel = options.confirmLabel ?? 'Ya, Lanjutkan';
            this.cancelLabel = options.cancelLabel ?? 'Batal';
            this.variant = options.variant ?? 'danger';
            this.form = options.form ?? null;
        },
        close() {
            this.open = false;
            this.form = null;
        },
        accept() {
            if (this.form) {
                this.form.submit();
            }
            this.close();
        },
    });
});

window.Alpine = Alpine;
Alpine.start();
