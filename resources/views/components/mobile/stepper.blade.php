@props([
    'value' => 1,
    'min' => 1,
    'max' => 99,
    'id' => 'stepper-' . uniqid(),
    'name' => 'quantity',
])

<div class="inline-flex items-center gap-2 bg-[#E3DEA4]/40 p-1 rounded-full border border-[#E3DEA4]">
    <button type="button" 
            onclick="decrementStepper('{{ $id }}', {{ $min }})"
            class="w-8 h-8 rounded-full bg-[#FAFCF3] hover:bg-[#E3DEA4] text-[#290024] flex items-center justify-center font-bold text-sm shadow-sm transition-colors active:scale-95" 
            aria-label="Kurangi">
        <i data-lucide="minus" class="w-3.5 h-3.5"></i>
    </button>

    <input type="number" 
           id="{{ $id }}" 
           name="{{ $name }}" 
           value="{{ $value }}" 
           min="{{ $min }}" 
           max="{{ $max }}" 
           readonly
           class="w-8 text-center bg-transparent text-xs font-bold text-[#290024] focus:outline-none select-none">

    <button type="button" 
            onclick="incrementStepper('{{ $id }}', {{ $max }})"
            class="w-8 h-8 rounded-full bg-[#290024] hover:bg-[#775533] text-[#FAFCF3] flex items-center justify-center font-bold text-sm shadow-sm transition-colors active:scale-95" 
            aria-label="Tambah">
        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
    </button>
</div>

<script>
    if (typeof decrementStepper === 'undefined') {
        window.decrementStepper = function(id, min = 1) {
            const input = document.getElementById(id);
            if (!input) return;
            let val = parseInt(input.value) || 1;
            if (val > min) {
                input.value = val - 1;
                input.dispatchEvent(new Event('change', { bubbles: true }));
            }
        };
        window.incrementStepper = function(id, max = 99) {
            const input = document.getElementById(id);
            if (!input) return;
            let val = parseInt(input.value) || 0;
            if (val < max) {
                input.value = val + 1;
                input.dispatchEvent(new Event('change', { bubbles: true }));
            }
        };
    }
</script>
