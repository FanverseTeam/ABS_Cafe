@props([
    'title' => 'Pembayaran Berhasil!',
    'message' => 'Pesanan telah berhasil diproses oleh sistem.',
    'orderCode' => null,
    'paymentMethod' => null,
    'total' => null,
    'primaryBtnText' => 'Lihat Struk',
    'primaryBtnAction' => null,
    'primaryBtnHref' => null,
    'secondaryBtnText' => 'Kembali ke Beranda',
    'secondaryBtnHref' => null,
    'secondaryBtnAction' => null,
])

<div {{ $attributes->merge(['class' => 'p-6 sm:p-8 flex flex-col items-center text-center max-w-sm mx-auto']) }}>
    <!-- Success Icon Circle -->
    <div class="relative mb-5">
        <div class="w-20 h-20 rounded-full bg-[#290024] text-[#D4954D] flex items-center justify-center shadow-soft-lg animate-in zoom-in-75 duration-300">
            <i data-lucide="check" class="w-10 h-10 stroke-[3]"></i>
        </div>
        <!-- Confetti/Sparkle dots decoration -->
        <span class="absolute -top-1 -right-1 w-3 h-3 rounded-full bg-[#D4954D] animate-ping opacity-75"></span>
        <span class="absolute -bottom-1 -left-1 w-2.5 h-2.5 rounded-full bg-[#E3DEA4]"></span>
    </div>

    <!-- Title & Description -->
    <h3 class="font-serif font-bold text-2xl text-[#290024]">{{ $title }}</h3>
    <p class="text-xs text-[#775533] mt-1.5 max-w-xs leading-relaxed">{{ $message }}</p>

    <!-- Details Card -->
    @if($orderCode || $paymentMethod || $total !== null)
        <div class="w-full mt-6 p-4 rounded-2xl bg-[#FAFCF3] border border-[#E3DEA4] text-xs space-y-2.5 text-left shadow-sm">
            @if($orderCode)
                <div class="flex items-center justify-between text-[#775533]">
                    <span>No. Pesanan</span>
                    <span class="font-bold text-[#290024]">{{ $orderCode }}</span>
                </div>
            @endif
            @if($paymentMethod)
                <div class="flex items-center justify-between text-[#775533]">
                    <span>Metode Pembayaran</span>
                    <span class="font-semibold text-[#290024] uppercase">{{ $paymentMethod }}</span>
                </div>
            @endif
            @if($total !== null)
                <div class="pt-2 border-t border-[#E3DEA4]/60 flex items-center justify-between">
                    <span class="font-semibold text-[#290024]">Total Pembayaran</span>
                    <span class="font-serif font-bold text-base text-[#290024]">Rp {{ number_format((float)$total, 0, ',', '.') }}</span>
                </div>
            @endif
            {{ $slot }}
        </div>
    @endif

    <!-- Actions -->
    <div class="w-full mt-6 space-y-2.5">
        @if($primaryBtnHref)
            <a href="{{ $primaryBtnHref }}" class="w-full py-3.5 px-4 rounded-full bg-[#290024] text-[#FAFCF3] hover:bg-[#775533] font-semibold text-xs flex items-center justify-center gap-2 shadow-sm transition-all active:scale-[0.98]">
                <i data-lucide="receipt" class="w-4 h-4 text-[#D4954D]"></i>
                {{ $primaryBtnText }}
            </a>
        @elseif($primaryBtnAction)
            <button type="button" onclick="{{ $primaryBtnAction }}" class="w-full py-3.5 px-4 rounded-full bg-[#290024] text-[#FAFCF3] hover:bg-[#775533] font-semibold text-xs flex items-center justify-center gap-2 shadow-sm transition-all active:scale-[0.98]">
                <i data-lucide="receipt" class="w-4 h-4 text-[#D4954D]"></i>
                {{ $primaryBtnText }}
            </button>
        @endif

        @if($secondaryBtnHref)
            <a href="{{ $secondaryBtnHref }}" class="w-full py-3 px-4 rounded-full bg-transparent border border-[#775533]/40 text-[#290024] hover:bg-[#E3DEA4]/40 font-medium text-xs flex items-center justify-center transition-all">
                {{ $secondaryBtnText }}
            </a>
        @elseif($secondaryBtnAction)
            <button type="button" onclick="{{ $secondaryBtnAction }}" class="w-full py-3 px-4 rounded-full bg-transparent border border-[#775533]/40 text-[#290024] hover:bg-[#E3DEA4]/40 font-medium text-xs flex items-center justify-center transition-all">
                {{ $secondaryBtnText }}
            </button>
        @endif
    </div>
</div>
