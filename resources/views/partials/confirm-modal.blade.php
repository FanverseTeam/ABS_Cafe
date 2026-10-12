{{-- Konfirmasi generik bergaya ABS Cafe (menggantikan confirm() bawaan browser) --}}
<div id="cafe-confirm-modal"
     class="fixed inset-0 z-[70] flex items-center justify-center p-4 sm:p-6 overflow-y-auto hidden"
     role="dialog" aria-modal="true" aria-labelledby="cafe-confirm-title">
    <div class="fixed inset-0 bg-[#290024]/40 backdrop-blur-sm" onclick="closeCafeConfirm()"></div>

    <div class="relative w-full max-w-md bg-cafe-card rounded-2xl shadow-soft-lg border border-cafe-beige/60 overflow-hidden z-10 p-6 space-y-4">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-2xl bg-cafe-danger/10 text-cafe-danger flex items-center justify-center shrink-0">
                <i data-lucide="log-out" class="w-5 h-5"></i>
            </div>
            <div>
                <h4 id="cafe-confirm-title" class="font-serif text-lg font-bold text-cafe-dark">Keluar dari Sesi?</h4>
                <p id="cafe-confirm-message" class="text-xs text-cafe-muted mt-0.5">
                    Anda akan keluar dari akun ini. Pastikan pekerjaan dan shift Anda sudah tersimpan sebelum melanjutkan.
                </p>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-3 border-t border-cafe-beige/60">
            <button type="button" onclick="closeCafeConfirm()"
                    class="px-4 py-2 rounded-full text-xs font-semibold text-cafe-dark hover:bg-cafe-beige/60 transition-colors">
                Batal
            </button>
            <button type="button" id="cafe-confirm-accept" onclick="acceptCafeConfirm()"
                    class="px-5 py-2 rounded-full bg-cafe-danger hover:bg-cafe-danger/90 text-white font-semibold text-xs shadow-sm inline-flex items-center gap-1.5 transition-colors">
                <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                <span>Ya, Keluar</span>
            </button>
        </div>
    </div>
</div>

<script>
    (function () {
        let pendingForm = null;

        document.addEventListener('submit', function (event) {
            const form = event.target;
            if (! form.matches('form[data-cafe-confirm]')) {
                return;
            }

            event.preventDefault();
            pendingForm = form;

            const modal = document.getElementById('cafe-confirm-modal');
            const title = form.getAttribute('data-cafe-confirm-title');
            const message = form.getAttribute('data-cafe-confirm-message');
            const acceptLabel = form.getAttribute('data-cafe-confirm-accept');

            if (title) document.getElementById('cafe-confirm-title').textContent = title;
            if (message) document.getElementById('cafe-confirm-message').textContent = message;
            if (acceptLabel) document.getElementById('cafe-confirm-accept').querySelector('span').textContent = acceptLabel;

            modal.classList.remove('hidden');
            if (window.lucide) lucide.createIcons();
        }, true);

        window.closeCafeConfirm = function () {
            const modal = document.getElementById('cafe-confirm-modal');
            if (! modal) return;
            modal.classList.add('hidden');
            pendingForm = null;
        };

        window.acceptCafeConfirm = function () {
            if (pendingForm) {
                pendingForm.submit();
            }
        };

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                window.closeCafeConfirm();
            }
        });
    })();
</script>
