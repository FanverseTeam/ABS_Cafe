@php
    $showSplash = ! $errors->any() && ! session('error') && ! session('status');
@endphp

@if ($showSplash)
    @php
        $duration = (float) config('splash.duration', 3.0);

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

    <style>
        .staf-splash-viewport {
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
            z-index: 9999;
            background-color: #290024;
            color: #F2F5E2;
            transition: opacity 0.45s cubic-bezier(0.22, 1, 0.36, 1), visibility 0.45s cubic-bezier(0.22, 1, 0.36, 1);
            cursor: pointer;
        }

        .staf-splash-viewport.is-leaving {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }

        .staf-splash-fallback {
            position: absolute;
            inset: 0;
            z-index: 1;
            background-image: linear-gradient(135deg, #F2F5E2 0%, #E3DEA4 52%, #D4954D 100%);
        }

        .staf-splash-bg {
            position: absolute;
            inset: 0;
            z-index: 2;
            width: 100%;
            height: 100%;
            display: block;
        }

        .staf-splash-bg-photo {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            opacity: 0;
            transform: scale(1.0);
            animation:
                stafBgFadeIn 0.6s ease-out forwards,
                stafBgSlowZoom {{ $tBgZoom }}s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
        }

        .staf-splash-bg-failed .staf-splash-bg {
            display: none !important;
        }

        .staf-splash-overlay {
            position: absolute;
            inset: 0;
            z-index: 3;
            background-image:
                radial-gradient(ellipse 75% 65% at 50% 50%, rgba(41, 0, 36, 0.55) 0%, rgba(22, 0, 19, 0.92) 100%),
                linear-gradient(to bottom, rgba(25, 0, 22, 0.85) 0%, rgba(41, 0, 36, 0.45) 40%, rgba(41, 0, 36, 0.65) 75%, rgba(20, 0, 18, 0.95) 100%);
        }

        .staf-splash-content {
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

        .staf-splash-logo-container {
            position: relative;
            width: 88px;
            height: 88px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            opacity: 0;
            transform: scale(0.85);
            animation: stafLogoAppear {{ $tLogo }}s cubic-bezier(0.16, 1, 0.3, 1) {{ $dLogo }}s forwards;
        }

        .staf-splash-ring-svg {
            position: absolute;
            inset: 0;
            width: 88px;
            height: 88px;
            transform: rotate(-90deg);
            pointer-events: none;
        }

        .staf-splash-ring-circle {
            stroke-dasharray: 260;
            stroke-dashoffset: 260;
            animation: stafDrawRing {{ $tLogo }}s ease-out {{ $dLogo }}s forwards;
        }

        .staf-splash-logo-inner {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .staf-splash-welcome {
            font-size: clamp(0.75rem, 2.5vw, 0.875rem);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.22em;
            color: #E3DEA4;
            margin-bottom: 0.5rem;
            opacity: 0;
            transform: translateY(14px);
            animation: stafSlideUp {{ $tWelcome }}s cubic-bezier(0.16, 1, 0.3, 1) {{ $dWelcome }}s forwards;
        }

        .staf-splash-brand {
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
            animation: stafSlideUp {{ $tBrand }}s cubic-bezier(0.16, 1, 0.3, 1) {{ $dBrand }}s forwards;
        }

        .staf-splash-tagline {
            font-size: clamp(0.875rem, 3vw, 1.125rem);
            font-weight: 400;
            color: #E3DEA4;
            letter-spacing: 0.02em;
            text-shadow: 0 2px 8px rgba(20, 0, 18, 0.5);
            opacity: 0;
            transform: translateY(14px);
            animation: stafSlideUp {{ $tTagline }}s cubic-bezier(0.16, 1, 0.3, 1) {{ $dTagline }}s forwards;
        }

        .staf-splash-tagline-accent {
            font-style: italic;
            font-weight: 600;
            color: #D4954D;
        }

        .staf-splash-progress-track {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: rgba(227, 222, 164, 0.15);
            z-index: 20;
            overflow: hidden;
        }

        .staf-splash-progress-bar {
            width: 0%;
            height: 100%;
            background: #D4954D;
            box-shadow: 0 0 10px rgba(212, 149, 77, 0.8);
            animation: stafProgressFill {{ $tProgress }}s cubic-bezier(0.4, 0, 0.2, 1) {{ $dProgress }}s forwards;
        }

        @keyframes stafBgFadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }

        @keyframes stafBgSlowZoom {
            from { transform: scale(1.0); }
            to   { transform: scale(1.06); }
        }

        @keyframes stafLogoAppear {
            0%   { opacity: 0; transform: scale(0.82); }
            100% { opacity: 1; transform: scale(1.0); }
        }

        @keyframes stafDrawRing {
            from { stroke-dashoffset: 260; }
            to   { stroke-dashoffset: 0; }
        }

        @keyframes stafSlideUp {
            0%   { opacity: 0; transform: translateY(16px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        @keyframes stafProgressFill {
            from { width: 0%; }
            to   { width: 100%; }
        }

        @media (prefers-reduced-motion: reduce) {
            .staf-splash-bg-photo,
            .staf-splash-logo-container,
            .staf-splash-welcome,
            .staf-splash-brand,
            .staf-splash-tagline {
                animation: none !important;
                opacity: 1 !important;
                transform: none !important;
            }

            .staf-splash-ring-circle {
                animation: none !important;
                stroke-dashoffset: 0 !important;
            }

            .staf-splash-progress-bar {
                animation: none !important;
                width: 100% !important;
            }
        }
    </style>

    <div id="staf-splash-overlay" class="staf-splash-viewport" aria-hidden="true">
        <div class="staf-splash-fallback" aria-hidden="true"></div>

        @if ($hasLandingBackground)
            <picture class="staf-splash-bg" aria-hidden="true">
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
                     onerror="document.getElementById('staf-splash-overlay')?.classList.add('staf-splash-bg-failed')"
                     class="staf-splash-bg-photo">
            </picture>
        @endif

        <div class="staf-splash-overlay" aria-hidden="true"></div>

        <div class="staf-splash-content">
            <div class="staf-splash-logo-container">
                <svg class="staf-splash-ring-svg" viewBox="0 0 88 88" aria-hidden="true">
                    <circle class="staf-splash-ring-circle" cx="44" cy="44" r="41"
                            fill="none" stroke="#D4954D" stroke-width="2" />
                </svg>
                <div class="staf-splash-logo-inner">
                    <x-logo on-dark size="lg" :show-text="false" loading="eager" />
                </div>
            </div>

            <p class="staf-splash-welcome">Selamat datang di</p>
            <h2 class="staf-splash-brand">ABS Cafe</h2>
            <p class="staf-splash-tagline">Good Coffee, <em class="staf-splash-tagline-accent">Good Mood.</em></p>
        </div>

        <div class="staf-splash-progress-track" aria-hidden="true">
            <div class="staf-splash-progress-bar"></div>
        </div>
    </div>

    <script>
        (function () {
            var splash = document.getElementById('staf-splash-overlay');
            if (!splash) return;

            var baseDuration = {{ (float) $duration }};
            var prefersReduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            var dismissDelayMs = prefersReduced ? 600 : Math.round(baseDuration * 1000);
            var dismissed = false;

            function dismissSplash() {
                if (dismissed) return;
                dismissed = true;
                splash.classList.add('is-leaving');
                setTimeout(function () {
                    splash.style.display = 'none';
                }, 460);
            }

            splash.addEventListener('click', dismissSplash);
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') dismissSplash();
            });
            setTimeout(dismissSplash, dismissDelayMs);
        })();
    </script>
@endif
