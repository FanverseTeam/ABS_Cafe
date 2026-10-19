@props([
    'title',
    'description',
    'icon',
    'href',
    'linkLabel' => 'Login',
    'variant' => 'cream',
])

@php
    $surface = $variant === 'custard' ? 'bg-custard' : 'bg-beige-50';
@endphp

<article class="group flex h-full flex-col rounded-2xl border border-olive/20 {{ $surface }} p-6 transition duration-300 ease-out hover:-translate-y-1 hover:border-apricot/60 hover:shadow-[0_22px_45px_-28px_rgba(41,0,36,0.45)] motion-reduce:transition-none motion-reduce:hover:translate-y-0">
    <span class="flex h-12 w-12 items-center justify-center rounded-full bg-apricot text-midnight shadow-sm">
        <x-staf.icon :name="$icon" class="h-6 w-6" />
    </span>

    <h3 class="mt-5 font-serif text-2xl font-bold tracking-tight text-midnight">{{ $title }}</h3>

    <p class="mt-2 flex-1 text-sm leading-relaxed text-olive">{{ $description }}</p>

    <a href="{{ $href }}"
       class="mt-6 inline-flex w-fit items-center gap-2 rounded-full text-sm font-semibold text-midnight underline decoration-apricot decoration-2 underline-offset-4 transition hover:text-olive focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-apricot focus-visible:ring-offset-2 focus-visible:ring-offset-beige-50">
        {{ $linkLabel }}
        <x-staf.icon name="arrow-right" class="h-4 w-4 transition-transform group-hover:translate-x-0.5 motion-reduce:transform-none" />
    </a>
</article>
