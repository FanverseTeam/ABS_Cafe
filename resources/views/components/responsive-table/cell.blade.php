@props([
    'class' => '',
])

<td {{ $attributes->merge(['class' => 'hidden md:table-cell ' . $class]) }}>
    {{ $slot }}
</td>
