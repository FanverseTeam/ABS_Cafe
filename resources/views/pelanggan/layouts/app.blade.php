<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>@yield('title', ($landing['cafe']['name'] ?? 'ABS Cafe') . ' - ' . ($landing['cafe']['tagline'] ?? 'Good Coffee, Better Moments.'))</title>

    <meta name="description" content="@yield('meta_description', 'Nikmati kopi berkualitas, menu lezat, dan suasana nyaman yang hangat di ' . ($landing['cafe']['name'] ?? 'ABS Cafe') . ', Tulungagung.')">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', ($landing['cafe']['name'] ?? 'ABS Cafe') . ' - ' . ($landing['cafe']['tagline'] ?? 'Good Coffee, Better Moments.'))">
    <meta property="og:description" content="@yield('meta_description', 'Nikmati kopi berkualitas, menu lezat, dan suasana nyaman di ABS Cafe Tulungagung.')">
    <meta property="og:image" content="{{ asset('images/logo.png') }}">
    <meta property="og:locale" content="id_ID">
    <meta property="og:site_name" content="{{ $landing['cafe']['name'] ?? 'ABS Cafe' }}">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">
    <meta name="theme-color" content="#290024">

    <!-- Preload Font Lokal -->
    @include('partials.font-preloads')
    <link rel="preload" href="{{ asset('fonts/great-vibes/great-vibes-latin-400-normal.woff2') }}" as="font" type="font/woff2" crossorigin>
    @if(!empty($landing['hero']['bg_photo']['preload']))
        <link rel="preload" href="{{ $landing['hero']['bg_photo']['src'] }}" as="image" fetchpriority="high">
    @endif

    <!-- Stylesheets Lokal -->
    <link rel="stylesheet" href="{{ asset('css/fonts.css') }}">
    @vite(['resources/css/pelanggan.css', 'resources/js/pelanggan.js'])

    <!-- Schema.org JSON-LD -->
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@type": "CafeOrCoffeeShop",
        "name": "{{ $landing['cafe']['name'] ?? 'ABS Cafe' }}",
        "image": "{{ asset('images/logo.png') }}",
        "url": "{{ url('/') }}",
        "telephone": "{{ $landing['cafe']['phone'] ?? '0896-2010-2010' }}",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "{{ $landing['cafe']['street_address'] ?? 'Jl. Letjend Suprapto No.44' }}",
            "addressLocality": "Tulungagung",
            "addressRegion": "Jawa Timur",
            "postalCode": "{{ $landing['cafe']['postal_code'] ?? '66219' }}",
            "addressCountry": "ID"
        },
        "geo": {
            "@type": "GeoCoordinates",
            "latitude": {{ $landing['cafe']['peta_lat'] ?? -8.0646338 }},
            "longitude": {{ $landing['cafe']['peta_lng'] ?? 111.9111798 }}
        },
        "openingHours": "{{ $landing['cafe']['opening_hours_schema'] ?? 'Mo-Su 09:00-23:00' }}",
        "priceRange": "$$"
    }
    </script>

    <style>
        :root {
            --color-bg-primary: #290024;
            --color-surface-olive: #775533;
            --color-accent-gold: #D4954D;
            --color-text-main: #F2F5E2;
            --color-text-sub: #E3DEA4;
        }

        body {
            font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif;
            background-color: var(--color-bg-primary);
            color: var(--color-text-main);
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .font-serif {
            font-family: 'Noto Serif', Georgia, serif;
        }

        .font-script {
            font-family: 'Great Vibes', cursive, 'Noto Serif', serif;
        }

        @media (prefers-reduced-motion: reduce) {
            html {
                scroll-behavior: auto !important;
            }
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
        }
    </style>
</head>
<body class="min-h-screen flex flex-col @yield('body_class', 'bg-[#290024] text-[#F2F5E2]') selection:bg-[#D4954D]/30 selection:text-[#F2F5E2]">
    <!-- Aksesibilitas: Lewati ke konten utama -->
    <a href="#main-content"
       class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2.5 focus:rounded-full focus:bg-[#D4954D] focus:text-[#290024] focus:font-semibold focus:shadow-lg focus:outline-none focus:ring-2 focus:ring-[#F2F5E2]">
        Lewati ke konten utama
    </a>

    <!-- Header Pelanggan -->
    @hasSection('header')
        @yield('header')
    @else
        @include('pelanggan.components.header')
    @endif

    <!-- Konten Utama -->
    <main id="main-content" class="flex-1 w-full" tabindex="-1">
        @yield('content')
    </main>

    <!-- Footer Pelanggan -->
    @hasSection('footer')
        @yield('footer')
    @else
        @include('pelanggan.components.footer')
    @endif

    @stack('scripts')
</body>
</html>
