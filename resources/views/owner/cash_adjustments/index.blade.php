@extends('owner.layouts.app')

@section('title', 'Penyesuaian Kas Laci')
@section('page_title', 'Penyesuaian Kas Laci')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h2 class="font-serif text-2xl sm:text-3xl font-bold text-cafe-dark">Penyesuaian Kas Laci</h2>
        <p class="text-xs text-cafe-muted mt-1">Tambah atau koreksi saldo kas laci. Penyesuaian akan diterapkan ke kas awal shift berikutnya.</p>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-cafe-success/10 border border-cafe-success/30 text-cafe-success flex items-center gap-3 text-xs font-semibold">
            <i data-lucide="check-circle" class="w-4 h-4 shrink-0"></i> {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form Penyesuaian -->
        <div class="bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft p-5 space-y-4">
            <h3 class="font-serif font-bold text-cafe-dark flex items-center gap-2">
                <i data-lucide="sliders-horizontal" class="w-4 h-4 text-cafe-gold"></i> Buat Penyesuaian Baru
            </h3>

            <!-- Kas Berikutnya -->
            <div class="p-4 rounded-2xl bg-cafe-cream/80 border border-cafe-beige/70 space-y-1">
                <p class="text-[10px] font-semibold text-cafe-muted uppercase tracking-wide">Kas Awal Shift Berikutnya</p>
                <p class="font-serif text-xl font-bold text-cafe-dark">
                    Rp {{ number_format($nextOpening['amount'], 0, ',', '.') }}
                </p>
                <p class="text-[10px] text-cafe-muted leading-snug">{{ $nextOpening['source_text'] }}</p>
            </div>

            <form action="{{ route('owner.cash_adjustments.store') }}" method="POST" class="space-y-4">
                @csrf

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-cafe-text">Jumlah Penyesuaian (Rp)</label>
                    <p class="text-[10px] text-cafe-muted">Gunakan nilai positif untuk menambah, negatif untuk mengurangi. Contoh: -50000</p>
                    <input type="number" name="amount" step="1000" required
                        value="{{ old('amount') }}"
                        placeholder="cth. 50000 atau -50000"
                        class="w-full bg-cafe-cream/60 focus:bg-white text-sm px-4 py-2.5 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all">
                    @error('amount')
                        <p class="text-[11px] text-cafe-danger">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-cafe-text">Alasan <span class="text-cafe-danger">*</span></label>
                    <textarea name="reason" rows="3" required maxlength="500"
                        placeholder="Jelaskan alasan penyesuaian kas..."
                        class="w-full bg-cafe-cream/60 focus:bg-white text-sm px-4 py-2.5 rounded-2xl border border-cafe-beige focus:border-cafe-gold focus:outline-none transition-all resize-none">{{ old('reason') }}</textarea>
                    @error('reason')
                        <p class="text-[11px] text-cafe-danger">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 rounded-full bg-cafe-dark hover:bg-cafe-brown text-cafe-cream text-xs font-semibold transition-colors">
                    <i data-lucide="plus" class="w-4 h-4"></i> Simpan Penyesuaian
                </button>
            </form>
        </div>

        <!-- Riwayat Penyesuaian -->
        <div class="lg:col-span-2 bg-cafe-card rounded-3xl border border-cafe-beige/60 shadow-soft overflow-hidden">
            <div class="px-6 py-4 border-b border-cafe-beige/50 flex items-center justify-between">
                <h3 class="font-serif font-bold text-sm text-cafe-dark flex items-center gap-2">
                    <i data-lucide="history" class="w-4 h-4 text-cafe-gold"></i> Riwayat Penyesuaian
                </h3>
                <span class="text-xs text-cafe-muted">{{ $adjustments->total() }} total entri</span>
            </div>

            @forelse($adjustments as $adj)
                <div class="px-6 py-4 border-b border-cafe-beige/30 last:border-0">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-xs font-bold {{ $adj->amount >= 0 ? 'text-cafe-success' : 'text-cafe-danger' }}">
                                    {{ $adj->amount >= 0 ? '+' : '' }}Rp {{ number_format($adj->amount, 0, ',', '.') }}
                                </span>
                                @if($adj->applied_to_shift_id)
                                    <span class="text-[10px] bg-cafe-success/15 text-cafe-success font-semibold px-2 py-0.5 rounded-full">
                                        Diterapkan → Shift #{{ $adj->applied_to_shift_id }}
                                    </span>
                                @else
                                    <span class="text-[10px] bg-cafe-warning/15 text-cafe-warning font-semibold px-2 py-0.5 rounded-full">
                                        Menunggu shift berikutnya
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-cafe-text mt-1">{{ $adj->reason }}</p>
                            <p class="text-[10px] text-cafe-muted mt-0.5">
                                oleh <strong>{{ $adj->user?->name ?? '-' }}</strong> &bull; {{ $adj->created_at->translatedFormat('d M Y, H:i') }}
                            </p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-6 py-10 text-center text-cafe-muted text-xs">
                    <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 opacity-40"></i>
                    <p>Belum ada penyesuaian kas yang tercatat.</p>
                </div>
            @endforelse

            @if($adjustments->hasPages())
                <div class="px-6 py-4 border-t border-cafe-beige/50">
                    {{ $adjustments->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
