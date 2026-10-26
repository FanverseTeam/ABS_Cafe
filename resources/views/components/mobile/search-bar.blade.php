@props([
    'name'        => 'search',
    'value'       => '',
    'placeholder' => 'Cari...',
    'action'      => null,
    'hiddenFields' => [],
    'id'          => 'search-bar-' . uniqid(),
    'autofocus'   => false,
    'class'       => '',
])

{{--
    Search Bar — sticky 48px search input with clear (x) button when filled.
    Renders as a <form> with GET method.

    Usage:
      <x-mobile.search-bar
          name="search"
          :value="request('search')"
          placeholder="Cari nama menu..."
          :action="route('admin.catalog.index')"
          :hidden-fields="['view' => $viewMode, 'sort' => request('sort')]"
      />
--}}

@php
    $inputId = $id . '-input';
@endphp

<form
    method="GET"
    @if($action) action="{{ $action }}" @endif
    class="relative {{ $class }}"
    role="search"
>
    {{-- Hidden fields to preserve current query params --}}
    @foreach($hiddenFields as $fieldName => $fieldValue)
        <input type="hidden" name="{{ $fieldName }}" value="{{ $fieldValue }}">
    @endforeach

    {{-- Search icon --}}
    <label for="{{ $inputId }}" class="sr-only">{{ $placeholder }}</label>
    <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-[#775533] pointer-events-none"></i>

    {{-- Input --}}
    <input
        type="search"
        name="{{ $name }}"
        id="{{ $inputId }}"
        value="{{ $value }}"
        placeholder="{{ $placeholder }}"
        @if($autofocus) autofocus @endif
        autocomplete="off"
        class="w-full h-12 bg-[#F2F5E2] border border-[#E3DEA4] rounded-full pl-10 pr-10 text-sm text-[#290024] placeholder:text-[#775533]/60 focus:outline-none focus:border-[#290024] focus:ring-2 focus:ring-[#290024]/20 transition-all"
    >

    {{-- Clear button (shown when value not empty) --}}
    @if($value)
        <button
            type="button"
            onclick="clearSearchBar('{{ $inputId }}', this.closest('form'))"
            class="absolute right-3 top-1/2 -translate-y-1/2 w-7 h-7 rounded-full bg-[#E3DEA4]/70 hover:bg-[#E3DEA4] text-[#290024] flex items-center justify-center transition-colors"
            aria-label="Hapus pencarian"
        >
            <i data-lucide="x" class="w-3.5 h-3.5"></i>
        </button>
    @else
        <button
            type="button"
            onclick="clearSearchBar('{{ $inputId }}', this.closest('form'))"
            class="absolute right-3 top-1/2 -translate-y-1/2 w-7 h-7 rounded-full bg-[#E3DEA4]/70 hover:bg-[#E3DEA4] text-[#290024] items-center justify-center transition-colors hidden"
            aria-label="Hapus pencarian"
        >
            <i data-lucide="x" class="w-3.5 h-3.5"></i>
        </button>
    @endif
</form>

<script>
    (function () {
        if (window.__absClearSearchBar) { return; }
        window.__absClearSearchBar = true;

        function clearSearchBar(inputId, form) {
            const input = document.getElementById(inputId);
            if (input) {
                input.value = '';
                input.focus();
                toggleClearBtn(input);
            }
        }

        function toggleClearBtn(input) {
            const form = input.closest('form');
            if (!form) { return; }
            const btn = form.querySelector('button[onclick^="clearSearchBar"]');
            if (!btn) { return; }
            if (input.value.length > 0) {
                btn.classList.remove('hidden');
                btn.classList.add('flex');
            } else {
                btn.classList.add('hidden');
                btn.classList.remove('flex');
            }
        }

        document.addEventListener('input', function (e) {
            if (e.target && e.target.type === 'search') {
                toggleClearBtn(e.target);
            }
        });

        window.clearSearchBar = clearSearchBar;
    })();
</script>
