@props([
    'status' => 'diproses',
    'label' => null,
    'size' => 'md', // sm, md
])

@php
    $st = strtolower((string)$status);
    $text = $label ?: ucfirst($st);

    // Color mapping
    // Selesai / Lunas / Aktif / Disetujui -> Hijau lembut
    // Diproses / Memasak / Baru -> Golden Apricot
    // Menunggu / Pending -> Custard / Kuning lembut
    // Batal / Ditolak / Nonaktif -> Merah lembut
    // Meja Terisi -> Midnight Violet lembut
    if (in_array($st, ['selesai', 'lunas', 'active', 'aktif', 'disetujui', 'diterima'])) {
        $classes = 'bg-[#4F7A4A]/15 text-[#4F7A4A] border border-[#4F7A4A]/30';
        $dotClass = 'bg-[#4F7A4A]';
    } elseif (in_array($st, ['diproses', 'proses', 'memasak', 'baru', 'siap_saji'])) {
        $classes = 'bg-[#D4954D]/20 text-[#290024] border border-[#D4954D]/40 font-semibold';
        $dotClass = 'bg-[#D4954D]';
    } elseif (in_array($st, ['menunggu', 'menunggu_pembayaran', 'pending', 'terisi'])) {
        $classes = 'bg-[#E3DEA4] text-[#775533] border border-[#775533]/20';
        $dotClass = 'bg-[#775533]';
    } elseif (in_array($st, ['batal', 'dibatalkan', 'ditolak', 'inactive', 'nonaktif', 'habis'])) {
        $classes = 'bg-[#B03A2E]/15 text-[#B03A2E] border border-[#B03A2E]/30';
        $dotClass = 'bg-[#B03A2E]';
    } else {
        $classes = 'bg-[#775533]/15 text-[#775533] border border-[#775533]/20';
        $dotClass = 'bg-[#775533]';
    }

    $pad = $size === 'sm' ? 'px-2 py-0.5 text-[10px]' : 'px-2.5 py-1 text-xs';
@endphp

<span class="inline-flex items-center gap-1.5 rounded-full {{ $pad }} {{ $classes }} font-medium">
    <span class="w-1.5 h-1.5 rounded-full {{ $dotClass }}"></span>
    <span>{{ $text }}</span>
</span>
