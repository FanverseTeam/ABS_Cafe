@props([
    'status' => 'diproses',
    'label' => null,
    'size' => 'md', // sm, md
])

@php
    $st = strtolower((string)$status);
    $text = $label ?: ucfirst($st);
    $icon = null;

    // Color mapping (palette only)
    if (in_array($st, ['selesai', 'lunas', 'berhasil', 'active', 'aktif', 'disetujui', 'diterima', 'tersedia'])) {
        $classes = 'bg-olive/90 text-beige border border-olive font-semibold';
        $dotClass = 'bg-beige';
        $icon = 'check';
    } elseif (in_array($st, ['diproses', 'proses', 'memasak', 'baru'])) {
        $classes = 'bg-apricot/90 text-midnight border border-apricot font-semibold';
        $dotClass = 'bg-midnight';
    } elseif (in_array($st, ['siap', 'siap_saji', 'disajikan'])) {
        $classes = 'bg-custard text-olive border border-olive/60 font-semibold';
        $dotClass = 'bg-olive';
        $icon = 'check';
    } elseif (in_array($st, ['menunggu', 'menunggu_pembayaran', 'pending', 'terisi'])) {
        $classes = 'bg-custard text-olive border border-olive/40';
        $dotClass = 'bg-olive';
    } elseif (in_array($st, ['peringatan'])) {
        $classes = 'bg-apricot/20 text-midnight border border-apricot/60 font-semibold';
        $dotClass = 'bg-apricot';
        $icon = 'triangle-alert';
    } elseif (in_array($st, ['batal', 'dibatalkan', 'ditolak', 'inactive', 'nonaktif', 'tidak_aktif', 'habis', 'gagal', 'error'])) {
        $classes = 'bg-custard/60 text-olive border border-olive/40 line-through';
        $dotClass = 'bg-olive';
        $icon = 'x';
    } else {
        $classes = 'bg-custard/60 text-olive border border-olive/40';
        $dotClass = 'bg-olive';
    }

    $pad = $size === 'sm' ? 'px-2 py-0.5 text-[10px]' : 'px-2.5 py-1 text-xs';
@endphp

<span class="inline-flex items-center gap-1.5 rounded-full {{ $pad }} {{ $classes }} font-medium">
    @if($icon)
        <i data-lucide="{{ $icon }}" class="w-3 h-3"></i>
    @else
        <span class="w-1.5 h-1.5 rounded-full {{ $dotClass }}"></span>
    @endif
    <span>{{ $text }}</span>
</span>
