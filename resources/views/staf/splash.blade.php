<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Selamat Datang - ABS Cafe</title>
    <meta name="description" content="Portal Staf ABS Cafe. Selamat datang di sistem operasional ABS Cafe.">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#290024">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo-badge.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo-badge.png') }}">

    <!-- Prefetch Form Login -->
    <link rel="prefetch" href="{{ route('login') }}">

    @php
        $duration = (float) config('splash.duration', 3.0);
        $noscriptSeconds = max(1, (int) ceil($duration + 1));
        $loginUrl = route('login');

        // Durasi & Delay timeline (proporsional terhadap $duration)
        $tBgZoom = $duration;
        $dLogo = round($duration * 0.133, 3);
        $tLogo = round($duration * 0.267, 3);
        $dWelcome = round($duration * 0.333, 3);
        $tWelcome = round($duration * 0.2, 3);
        $dBrand = round($duration * 0.417, 3);
        $tBrand = round($duration * 0.2, 3);
        $dTagline = round($duration * 0.533, 3);
        $tTagline = round($duration * 0.267, 3);
        $dProgress = round($duration * 0.133, 3);
        $tProgress = round($duration * 0.8, 3);
        $dFadeout = round($duration * 0.867, 3);
        $tFadeout = round($duration * 0.133, 3);

        // Sumber latar foto teroptimasi
        $landingBgSources = [];
        foreach ([768, 1280, 1920] as $width) {
            $path = "images/landing-bg-{$width}.webp";
            if (is_file(public_path($path))) {
                $landingBgSources[$width] = asset($path).'?v='.filemtime(public_path($path));
            }
        }

        $landingBgFallbackPath = 'images/landing-bg-1920.jpg';
        $landingBgFallbackUrl = is_file(public_path($landingBgFallbackPath))
            ? asset($landingBgFallbackPath).'?v='.filemtime(public_path($landingBgFallbackPath))
            : null;

        $landingBgSrcset = collect($landingBgSources)
            ->map(fn (string $url, int $width): string => "{$url} {$width}w")
            ->implode(', ');

        $landingBgLargestWidth = array_key_last($landingBgSources);
        $landingBgPreloadUrl = $landingBgLargestWidth !== null
            ? $landingBgSources[$landingBgLargestWidth]
            : $landingBgFallbackUrl;

        $landingBgImageUrl = $landingBgFallbackUrl ?? $landingBgPreloadUrl;
        $hasLandingBackground = $landingBgImageUrl !== null;
    @endphp

    <!-- Fallback tanpa JavaScript: otomatis alihkan ke login -->
    <noscript>
        <meta http-equiv="refresh" content="{{ $noscriptSeconds }};url={{ $loginUrl }}">
    </noscript>

    <!-- Font lokal preloads -->
    @include('partials.font-preloads')
    <link rel="stylesheet" href="{{ asset('css/fonts.css') }}">

    @if ($hasLandingBackground)
        <link rel="preload" as="image"
              href="{{ $landingBgPreloadUrl }}"
              @if ($landingBgSrcset !== '') imagesrcset="{{ $landingBgSrcset }}" imagesizes="100vw" @endif
              fetchpriority="high">
    @endif

    <style>
        /* ==========================================================================
           SPLASH SCREEN INLINE STYLES (Zero external CSS dependency)
           Palet: Midnight Violet (#290024), Beige (#F2F5E2),
                  Vanilla Custard (#E3DEA4), Golden Apricot (#D4954D), Olive Wood (#775533)
           ========================================================================== */
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html, body {
            width: 100%;
            height: 100%;
            height: 100vh;
            height: 100svh;
            overflow: hidden;
            background-color: #290024;
            color: #F2F5E2;
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .splash-viewport {
            position: fixed;
            inset: 0;
            width: 100vw;
            height: 100vh;
            height: 100svh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            z-index: 10;
        }

        /* Latar Gradasi Fallback */
        .landing-fallback,
        .splash-fallback {
            position: absolute;
            inset: 0;
            z-index: 1;
            background-image: linear-gradient(135deg, #F2F5E2 0%, #E3DEA4 52%, #D4954D 100%);
        }

        /* Latar Foto Lokal Teroptimasi */
        .landing-bg,
        .splash-bg {
            position: absolute;
            inset: 0;
            z-index: 2;
            width: 100%;
            height: 100%;
            display: block;
            background-size: cover;
            background-position: center;
        }

        .splash-bg-photo {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            opacity: 0;
            transform: scale(1.0);
            animation:
                bgFadeIn 0.6s ease-out forwards,
                bgSlowZoom {{ $tBgZoom }}s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
        }

        .landing-bg-failed .landing-bg,
        .landing-bg-failed .splash-bg {
            display: none !important;
        }

        /* Overlay Hangat Midnight Violet */
        .landing-overlay,
        .splash-overlay {
            position: absolute;
            inset: 0;
            z-index: 3;
            background-image:
                radial-gradient(ellipse 75% 65% at 50% 50%, rgba(41, 0, 36, 0.55) 0%, rgba(22, 0, 19, 0.92) 100%),
                linear-gradient(to bottom, rgba(25, 0, 22, 0.85) 0%, rgba(41, 0, 36, 0.45) 40%, rgba(41, 0, 36, 0.65) 75%, rgba(20, 0, 18, 0.95) 100%);
        }

        /* Konten Utama Terpusat */
        .splash-content {
            position: relative;
            z-index: 10;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            max-width: 480px;
            width: 100%;
            pointer-events: none;
        }

        /* Logo + Cincin Tipis Apricot */
        .splash-logo-container {
            position: relative;
            width: 88px;
            height: 88px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            opacity: 0;
            transform: scale(0.85);
            animation: logoAppear {{ $tLogo }}s cubic-bezier(0.16, 1, 0.3, 1) {{ $dLogo }}s forwards;
        }

        .splash-ring-svg {
            position: absolute;
            inset: 0;
            width: 88px;
            height: 88px;
            transform: rotate(-90deg);
            pointer-events: none;
        }

        .splash-ring-circle {
            stroke-dasharray: 260;
            stroke-dashoffset: 260;
            animation: drawRing {{ $tLogo }}s ease-out {{ $dLogo }}s forwards;
        }

        .splash-logo-inner {
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 9999px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.45);
        }

        /* Teks Staggered */
        .splash-welcome {
            font-size: clamp(0.75rem, 2.5vw, 0.875rem);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.22em;
            color: #E3DEA4;
            margin-bottom: 0.5rem;
            opacity: 0;
            transform: translateY(14px);
            animation: slideUp {{ $tWelcome }}s cubic-bezier(0.16, 1, 0.3, 1) {{ $dWelcome }}s forwards;
        }

        .splash-brand {
            font-family: 'Noto Serif', Georgia, serif;
            font-size: clamp(2.25rem, 8vw, 3.75rem);
            font-weight: 700;
            color: #F2F5E2;
            line-height: 1.1;
            letter-spacing: -0.01em;
            margin-bottom: 0.75rem;
            text-shadow: 0 4px 18px rgba(20, 0, 18, 0.6);
            opacity: 0;
            transform: translateY(18px);
            animation: slideUp {{ $tBrand }}s cubic-bezier(0.16, 1, 0.3, 1) {{ $dBrand }}s forwards;
        }

        .splash-tagline {
            font-size: clamp(0.875rem, 3vw, 1.125rem);
            font-weight: 400;
            color: #E3DEA4;
            letter-spacing: 0.02em;
            text-shadow: 0 2px 8px rgba(20, 0, 18, 0.5);
            opacity: 0;
            transform: translateY(14px);
            animation: slideUp {{ $tTagline }}s cubic-bezier(0.16, 1, 0.3, 1) {{ $dTagline }}s forwards;
        }

        .splash-tagline-accent {
            font-style: italic;
            font-weight: 600;
            color: #D4954D;
        }

        /* Garis Progres Tipis di Bagian Bawah */
        .splash-progress-track {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: rgba(227, 222, 164, 0.15);
            z-index: 20;
            overflow: hidden;
        }

        .splash-progress-bar {
            width: 0%;
            height: 100%;
            background: #D4954D;
            box-shadow: 0 0 10px rgba(212, 149, 77, 0.8);
            animation: progressFill {{ $tProgress }}s cubic-bezier(0.4, 0, 0.2, 1) {{ $dProgress }}s forwards;
        }


        /* Layer Fade-out ke Midnight Violet di akhir animasi */
        .splash-fadeout {
            position: fixed;
            inset: 0;
            z-index: 40;
            background: #290024;
            opacity: 0;
            pointer-events: none;
            animation: fadeToDark {{ $tFadeout }}s ease-in {{ $dFadeout }}s forwards;
        }

        /* Screen reader only */
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border-width: 0;
        }

        /* ==========================================================================
           KEYFRAME ANIMATIONS
           ========================================================================== */
        @keyframes bgFadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }

        @keyframes bgSlowZoom {
            from { transform: scale(1.0); }
            to   { transform: scale(1.06); }
        }

        @keyframes logoAppear {
            0%   { opacity: 0; transform: scale(0.82); }
            100% { opacity: 1; transform: scale(1.0); }
        }

        @keyframes drawRing {
            from { stroke-dashoffset: 260; }
            to   { stroke-dashoffset: 0; }
        }

        @keyframes slideUp {
            0%   { opacity: 0; transform: translateY(16px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        @keyframes progressFill {
            from { width: 0%; }
            to   { width: 100%; }
        }

        @keyframes fadeToDark {
            from { opacity: 0; }
            to   { opacity: 1; }
        }

        /* ==========================================================================
           PREFERS REDUCED MOTION (Aksesibilitas)
           Tampilan statis tanpa gerak, redirect setelah ±1 detik
           ========================================================================== */
        @media (prefers-reduced-motion: reduce) {
            .splash-bg-photo {
                animation: none !important;
                opacity: 1 !important;
                transform: none !important;
            }

            .splash-logo-container,
            .splash-welcome,
            .splash-brand,
            .splash-tagline {
                animation: none !important;
                opacity: 1 !important;
                transform: none !important;
            }

            .splash-ring-circle {
                animation: none !important;
                stroke-dashoffset: 0 !important;
            }

            .splash-progress-bar {
                animation: none !important;
                width: 100% !important;
            }

            .splash-fadeout {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    {{-- Status aksesibilitas --}}
    <div role="status" aria-live="polite" class="sr-only">Memuat halaman login</div>

    <div class="splash-viewport">
        {{-- Latar Fallback Gradasi --}}
        <div class="landing-fallback splash-fallback" aria-hidden="true"></div>

        {{-- Latar Foto Lokal Teroptimasi --}}
        @if ($hasLandingBackground)
            <picture class="landing-bg splash-bg" aria-hidden="true">
                @if ($landingBgSrcset !== '')
                    <source type="image/webp" srcset="{{ $landingBgSrcset }}" sizes="100vw">
                @endif
                <img src="{{ $landingBgImageUrl }}"
                     alt=""
                     aria-hidden="true"
                     width="1920"
                     height="1280"
                     loading="eager"
                     fetchpriority="high"
                     decoding="async"
                     onerror="document.documentElement.classList.add('landing-bg-failed')"
                     class="splash-bg-photo">
            </picture>
        @endif

        {{-- Overlay Hangat Midnight Violet --}}
        <div class="landing-overlay splash-overlay" aria-hidden="true"></div>

        {{-- Konten Splash --}}
        <main class="splash-content">
            {{-- Logo dengan Cincin Tipis Apricot --}}
            <div class="splash-logo-container">
                <svg class="splash-ring-svg" viewBox="0 0 88 88" aria-hidden="true">
                    <circle class="splash-ring-circle" cx="44" cy="44" r="41"
                            fill="none" stroke="#D4954D" stroke-width="2" />
                </svg>
                <div class="splash-logo-inner">
                    <x-logo size="lg" :show-text="false" loading="eager" />
                </div>
            </div>

            {{-- Teks Berurutan (Stagger) --}}
            <p class="splash-welcome">Selamat datang di</p>
            <h1 class="splash-brand">ABS Cafe</h1>
            <p class="splash-tagline">Good Coffee, <em class="splash-tagline-accent">Good Mood.</em></p>
        </main>


        {{-- Garis Progres --}}
        <div class="splash-progress-track" aria-hidden="true">
            <div class="splash-progress-bar"></div>
        </div>

        {{-- Overlay Fadeout Akhir --}}
        <div class="splash-fadeout" aria-hidden="true"></div>
    </div>

    {{-- Script Redirect Otomatis --}}
    <script>
        (function() {
            var targetUrl = "{{ $loginUrl }}";
            var baseDuration = {{ (float) $duration }};
            var prefersReduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            var redirectDelayMs = prefersReduced ? 1000 : Math.round(baseDuration * 1000);

            setTimeout(function() {
                window.location.replace(targetUrl);
            }, redirectDelayMs);
        })();
    </script>
</body>
</html>
