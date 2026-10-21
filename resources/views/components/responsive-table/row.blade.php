@props([
    'index' => 0,
    'rowClass' => '',
])

<tr {{ $attributes->merge(['class' => 'block md:table-row bg-cafe-card rounded-2xl md:rounded-none border border-cafe-beige/60 md:border-0 shadow-soft md:shadow-none p-4 md:p-0 hover:bg-cafe-cream/80 transition-colors ' . ($index % 2 === 1 ? 'md:bg-[#FAFCF3]/40 ' : '') . $rowClass]) }} data-responsive-row>
    <!-- Mobile Card View (< md) -->
    <td class="block md:hidden p-0 border-0" colspan="100%" data-mobile-card>
        @if(isset($card))
            {{ $card }}
        @else
            <div class="space-y-3">
                <!-- Baris Atas: Judul di kiri, Badge di kanan -->
                @if(isset($judul) || isset($title) || isset($badge) || isset($subjudul) || isset($subtitle))
                    <div class="flex items-start justify-between gap-2.5">
                        <div class="min-w-0 flex-1">
                            @if(isset($judul) || isset($title))
                                <div class="font-bold text-xs sm:text-sm text-cafe-dark truncate leading-tight" data-card-title>
                                    {{ $judul ?? $title }}
                                </div>
                            @endif
                            @if(isset($subjudul) || isset($subtitle))
                                <div class="text-[11px] text-cafe-muted mt-0.5 leading-snug break-words" data-card-subtitle>
                                    {{ $subjudul ?? $subtitle }}
                                </div>
                            @endif
                        </div>
                        @if(isset($badge))
                            <div class="shrink-0 flex items-center gap-1.5 flex-wrap justify-end" data-card-badge>
                                {{ $badge }}
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Pasangan Label-Nilai: Grid 2 kolom, label kecil Olive Wood di atas nilai -->
                @if(isset($fields) || isset($labelNilai))
                    <div class="grid grid-cols-2 gap-2.5 pt-2 border-t border-cafe-beige/40 text-xs" data-card-fields>
                        {{ $fields ?? $labelNilai }}
                    </div>
                @endif

                <!-- Teks Panjang: Lebar penuh, boleh wrap -->
                @if(isset($content) || isset($teksPanjang))
                    <div class="text-xs text-cafe-text leading-relaxed break-words pt-1" data-card-content>
                        {{ $content ?? $teksPanjang }}
                    </div>
                @endif

                <!-- Aksi / Footer: Tombol di footer kartu atau menu -->
                @if(isset($actions) || isset($aksi) || isset($footer))
                    <div class="flex items-center justify-between pt-2 border-t border-cafe-beige/40 gap-2 flex-wrap" data-card-actions>
                        {{ $actions ?? $aksi ?? $footer }}
                    </div>
                @endif
            </div>
        @endif
    </td>

    <!-- Desktop Table Cells (>= md) -->
    {{ $desktop ?? $slot }}
</tr>
