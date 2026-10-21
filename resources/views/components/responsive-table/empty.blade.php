@props([
    'colspan' => 1,
])

<tr class="block md:table-row bg-cafe-card rounded-2xl md:rounded-none border border-cafe-beige/60 md:border-0 shadow-soft md:shadow-none p-8 md:p-0">
    <td class="block md:table-cell p-0 md:p-8 text-center text-xs text-cafe-muted" colspan="{{ $colspan }}">
        {{ $slot }}
    </td>
</tr>
