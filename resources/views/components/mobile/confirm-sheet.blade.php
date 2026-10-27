@props([
    'id' => 'mobile-confirm-sheet',
    'title' => 'Konfirmasi Tindakan',
    'message' => 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
    'confirmText' => 'Ya, Lanjutkan',
    'cancelText' => 'Batal',
    'isDanger' => true,
])

<div id="{{ $id }}" class="fixed inset-0 z-50 hidden transition-all duration-300">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-[#290024]/60 backdrop-blur-sm transition-opacity" 
         onclick="closeConfirmSheet('{{ $id }}')"></div>

    <!-- Sheet Panel -->
    <div class="fixed inset-x-0 bottom-0 bg-[#F2F5E2] rounded-t-[2rem] border-t border-[#E3DEA4] p-6 z-10 transform translate-y-full transition-transform duration-300 ease-out shadow-2xl max-w-lg mx-auto pb-[max(1.5rem,env(safe-area-inset-bottom))]">
        <div class="w-12 h-1 bg-[#775533]/30 rounded-full mx-auto mb-4"></div>

        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-2xl {{ $isDanger ? 'bg-custard/60 text-olive' : 'bg-apricot/20 text-midnight' }} flex items-center justify-center shrink-0">
                <i data-lucide="{{ $isDanger ? 'alert-triangle' : 'help-circle' }}" class="w-6 h-6"></i>
            </div>
            <div class="min-w-0 flex-1">
                <h4 id="{{ $id }}-title" class="font-serif font-bold text-base text-[#290024]">{{ $title }}</h4>
                <p id="{{ $id }}-message" class="text-xs text-[#775533] mt-1 leading-relaxed">{{ $message }}</p>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-2 gap-3">
            <button type="button" 
                    onclick="closeConfirmSheet('{{ $id }}')" 
                    class="w-full py-3 px-4 rounded-full border border-[#775533]/30 text-xs font-semibold text-[#775533] hover:bg-[#E3DEA4]/40 transition-colors">
                {{ $cancelText }}
            </button>
            <button type="button" 
                    id="{{ $id }}-confirm-btn"
                    class="w-full py-3 px-4 rounded-full {{ $isDanger ? 'bg-olive text-beige hover:bg-midnight' : 'bg-[#290024] text-[#F2F5E2] hover:bg-[#775533]' }} text-xs font-semibold shadow-sm transition-colors">
                {{ $confirmText }}
            </button>
        </div>
    </div>
</div>

<script>
    function openConfirmSheet(options = {}) {
        const id = options.id || 'mobile-confirm-sheet';
        const sheet = document.getElementById(id);
        if (!sheet) return;

        if (options.title) {
            const titleEl = document.getElementById(`${id}-title`);
            if (titleEl) titleEl.textContent = options.title;
        }
        if (options.message) {
            const msgEl = document.getElementById(`${id}-message`);
            if (msgEl) msgEl.textContent = options.message;
        }

        const confirmBtn = document.getElementById(`${id}-confirm-btn`);
        if (confirmBtn) {
            if (options.confirmText) confirmBtn.textContent = options.confirmText;
            confirmBtn.onclick = function() {
                closeConfirmSheet(id);
                if (typeof options.onConfirm === 'function') {
                    options.onConfirm();
                } else if (options.form) {
                    options.form.submit();
                }
            };
        }

        sheet.classList.remove('hidden');
        setTimeout(() => {
            const panel = sheet.querySelector('.transform');
            if (panel) {
                panel.classList.remove('translate-y-full');
                panel.classList.add('translate-y-0');
            }
        }, 10);
        if (window.lucide) lucide.createIcons();
    }

    function closeConfirmSheet(id = 'mobile-confirm-sheet') {
        const sheet = document.getElementById(id);
        if (!sheet) return;
        const panel = sheet.querySelector('.transform');
        if (panel) {
            panel.classList.remove('translate-y-0');
            panel.classList.add('translate-y-full');
        }
        setTimeout(() => {
            sheet.classList.add('hidden');
        }, 300);
    }
</script>
