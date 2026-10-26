@props([
    'size' => 'md',
    'onDark' => false,
    'thermal' => false,
    'showText' => true,
    'title' => 'ABS Cafe',
    'subtitle' => 'Accessories & Cafe',
    'loading' => 'eager',
])

@php
    $sizeConfig = [
        'sm' => [
            'box' => 36,
            'padding' => 4,
            'gap' => 10,
            'title' => 14,
            'subtitle' => 8,
            'image' => 28,
        ],
        'md' => [
            'box' => 44,
            'padding' => 5,
            'gap' => 12,
            'title' => 18,
            'subtitle' => 9,
            'image' => 34,
        ],
        'lg' => [
            'box' => 56,
            'padding' => 6,
            'gap' => 14,
            'title' => 24,
            'subtitle' => 10,
            'image' => 44,
        ],
    ][$size] ?? [];

    $configuredLogo = \App\Models\CafeSetting::get('cafe_logo', 'images/logo.png');
    $configuredLogo = is_string($configuredLogo) && trim($configuredLogo) !== ''
        ? trim($configuredLogo)
        : 'images/logo.png';

    $isRemoteLogo = \Illuminate\Support\Str::startsWith($configuredLogo, ['http://', 'https://', '//']);

    if (! $isRemoteLogo && ! is_file(public_path(ltrim($configuredLogo, '/')))) {
        $configuredLogo = 'images/logo.png';
    }

    $logoSrc = $isRemoteLogo
        ? $configuredLogo
        : asset(ltrim($configuredLogo, '/'));

    $badgeBackground = '#290024';
    if ($thermal) {
        $badgeBackground = '#290024';
    }

    $badgeStyle = $onDark
        ? 'background-color:transparent;'
        : "background-color:{$badgeBackground};padding:{$sizeConfig['padding']}px;-webkit-print-color-adjust:exact;print-color-adjust:exact;";

    $titleColor = $onDark ? '#F2F5E2' : '#290024';
    $subtitleColor = $onDark ? '#D4954D' : '#775533';
@endphp

<div {{ $attributes->merge(['class' => 'abs-cafe-logo']) }}
     style="display:flex;width:fit-content;align-items:center;gap:{{ $sizeConfig['gap'] }}px;flex-shrink:0;text-decoration:none;">
    <span style="display:inline-flex;width:{{ $sizeConfig['box'] }}px;height:{{ $sizeConfig['box'] }}px;flex-shrink:0;align-items:center;justify-content:center;border-radius:9999px;box-sizing:border-box;{{ $badgeStyle }}">
        <img src="{{ $logoSrc }}"
             alt="ABS Cafe"
             width="{{ $sizeConfig['image'] }}"
             height="{{ $sizeConfig['image'] }}"
             loading="{{ $loading }}"
             decoding="async"
             style="display:block;width:100%;height:100%;object-fit:contain;">
    </span>

    @if ($showText)
        <span style="display:flex;min-width:0;flex-direction:column;line-height:1.05;">
            <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-family:'Noto Serif',Georgia,serif;font-weight:700;letter-spacing:-0.01em;font-size:{{ $sizeConfig['title'] }}px;color:{{ $titleColor }};">
                {{ $title }}
            </span>
            <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;margin-top:2px;font-size:{{ $sizeConfig['subtitle'] }}px;font-weight:500;letter-spacing:0.18em;text-transform:uppercase;color:{{ $subtitleColor }};">
                {{ $subtitle }}
            </span>
        </span>
    @endif
</div>
