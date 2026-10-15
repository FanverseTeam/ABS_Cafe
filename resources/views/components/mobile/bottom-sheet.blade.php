@props([
    'id' => 'bottom-sheet-' . uniqid(),
    'title' => '',
    'subtitle' => null,
])

<div id="{{ $id }}" class="fixed inset-0 z-50 hidden transition-all duration-300">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-[#290024]/60 backdrop-blur-sm transition-opacity" 
         onclick="closeBottomSheet('{{ $id }}')"></div>

    <!-- Sheet Container -->
    <div class="fixed inset-x-0 bottom-0 max-h-[90vh] bg-[#FAFCF3] rounded-t-[2rem] border-t border-[#E3DEA4] shadow-2xl flex flex-col z-10 transform translate-y-full transition-transform duration-300 ease-out">
        <!-- Drag Handle Indicator -->
        <div class="w-full pt-3 pb-1 flex justify-center cursor-pointer" onclick="closeBottomSheet('{{ $id }}')">
            <div class="w-12 h-1.5 bg-[#775533]/30 rounded-full"></div>
        </div>

        <!-- Header -->
        <div class="px-5 py-3 border-b border-[#E3DEA4]/60 flex items-center justify-between">
            <div class="min-w-0">
                <h3 class="font-serif font-bold text-lg text-[#290024] truncate">{{ $title }}</h3>
                @if($subtitle)
                    <p class="text-xs text-[#775533] mt-0.5 truncate">{{ $subtitle }}</p>
                @endif
            </div>
            <button type="button" 
                    onclick="closeBottomSheet('{{ $id }}')" 
                    class="w-8 h-8 rounded-full bg-[#E3DEA4]/50 hover:bg-[#E3DEA4] text-[#290024] flex items-center justify-center transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <!-- Scrollable Content -->
        <div class="flex-1 overflow-y-auto p-5 space-y-4 max-h-[calc(90vh-140px)]">
            {{ $slot }}
        </div>

        <!-- Footer / Sticky Actions if provided -->
        @if(isset($footer))
            <div class="p-4 bg-[#F2F5E2]/80 border-t border-[#E3DEA4]/60 pb-[max(1rem,env(safe-area-inset-bottom))]">
                {{ $footer }}
            </div>
        @endif
    </div>
</div>

<script>
    function openBottomSheet(id) {
        const sheet = document.getElementById(id);
        if (!sheet) return;
        sheet.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        setTimeout(() => {
            const container = sheet.querySelector('.transform');
            if (container) {
                container.classList.remove('translate-y-full');
                container.classList.add('translate-y-0');
            }
        }, 10);
        if (window.lucide) lucide.createIcons();
    }

    function closeBottomSheet(id) {
        const sheet = document.getElementById(id);
        if (!sheet) return;
        const container = sheet.querySelector('.transform');
        if (container) {
            container.classList.remove('translate-y-0');
            container.classList.add('translate-y-full');
        }
        setTimeout(() => {
            sheet.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }, 300);
    }
</script>
