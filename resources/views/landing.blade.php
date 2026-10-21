<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>ABS Cafe – Good Coffee, Good Mood</title>
    <meta name="description" content="Portal Staf ABS Cafe. Kelola operasional dan pantau bisnis ABS Cafe dalam satu tempat. Akses dashboard Admin, Owner, dan Kasir.">
    <meta name="theme-color" content="#290024">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="ABS Cafe – Good Coffee, Good Mood">
    <meta property="og:description" content="Portal Staf ABS Cafe. Kelola operasional dan pantau bisnis ABS Cafe dalam satu tempat.">
    <meta property="og:image" content="{{ asset('images/logo-og.png') }}">
    <meta property="og:site_name" content="ABS Cafe">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo-badge.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo-badge.png') }}">

    <!-- Fonts & Styles -->
    @include('partials.font-preloads')
    <link rel="stylesheet" href="{{ asset('css/fonts.css') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        beige: '#F2F5E2',
                        custard: '#E3DEA4',
                        apricot: '#D4954D',
                        olive: '#775533',
                        midnight: {
                            DEFAULT: '#290024',
                            400: '#1c0019',
                        }
                    },
                    fontFamily: {
                        serif: ['"Noto Serif"', 'Georgia', 'serif'],
                        sans: ['"Poppins"', '-apple-system', 'BlinkMacSystemFont', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @php
        $currentUser = auth()->user();

        $dashboardRoute = null;

        if ($currentUser) {
            $dashboardRoute = match ($currentUser->primaryRole()) {
                'admin' => config('features.legacy_admin')
                    ? route('admin.dashboard')
                    : route('admin.staff.index'),
                'owner' => route('owner.dashboard'),
                'kasir' => route('kasir.dashboard'),
                default => route('login'),
            };
        }

        $primaryCtaUrl = $dashboardRoute ?? route('login');
        $primaryCtaLabel = $currentUser ? 'Buka Dashboard' : 'Login';

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

    @if ($hasLandingBackground)
        <link rel="preload" as="image"
              href="{{ $landingBgPreloadUrl }}"
              @if ($landingBgSrcset !== '') imagesrcset="{{ $landingBgSrcset }}" imagesizes="100vw" @endif
              fetchpriority="high">
    @endif

    <style>
        html,
        body {
            min-height: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            overflow-x: hidden;
            overflow-y: auto;
            background-color: #290024;
        }

        .landing-screen {
            position: relative;
            display: flex;
            min-height: 100vh;
            min-height: 100svh;
            flex-direction: column;
            justify-content: space-between;
            overflow-x: hidden;
            isolation: isolate;
        }

        .landing-fallback {
            background-image: linear-gradient(135deg, #F2F5E2 0%, #E3DEA4 52%, #D4954D 100%);
        }

        .landing-bg {
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
        }

        .landing-bg-failed .landing-bg {
            display: none;
        }

        .landing-overlay {
            background-image:
                radial-gradient(ellipse 70% 60% at 50% 50%, rgba(41, 0, 36, 0.45) 0%, rgba(22, 0, 19, 0.88) 100%),
                linear-gradient(to bottom, rgba(25, 0, 22, 0.85) 0%, rgba(41, 0, 36, 0.35) 40%, rgba(41, 0, 36, 0.5) 75%, rgba(20, 0, 18, 0.95) 100%);
        }

        /* Glassmorphic Artisanal Sanctuary Card */
        .landing-card {
            position: relative;
            width: 100%;
            max-width: 480px;
            margin: 0 auto;
            padding: 2.25rem 2rem 2rem;
            border-radius: 28px;
            background: rgba(35, 2, 31, 0.78);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(212, 149, 77, 0.32);
            box-shadow:
                0 30px 60px -15px rgba(12, 0, 10, 0.85),
                0 0 50px -10px rgba(212, 149, 77, 0.18),
                inset 0 1px 1px rgba(255, 255, 255, 0.16),
                inset 0 0 20px rgba(212, 149, 77, 0.05);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        @media (max-width: 640px) {
            .landing-card {
                padding: 1.5rem 1.25rem 1.25rem;
                border-radius: 22px;
                max-width: 100%;
            }

            .landing-title {
                font-size: 1.75rem !important;
                line-height: 1.15 !important;
            }

            .landing-subtitle {
                font-size: 0.8125rem !important;
            }

            .landing-cta {
                max-width: 100%;
                min-height: 2.85rem;
                padding: 0.75rem 1.25rem;
                font-size: 0.9375rem;
            }
        }

        /* Warm Corner Accents for Handcrafted Feel */
        .landing-card::before {
            content: '';
            position: absolute;
            inset: -1px;
            border-radius: 29px;
            padding: 1px;
            background: linear-gradient(135deg, rgba(212, 149, 77, 0.55) 0%, rgba(227, 222, 164, 0.15) 35%, rgba(212, 149, 77, 0.08) 70%, rgba(212, 149, 77, 0.45) 100%);
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            pointer-events: none;
        }

        .landing-logo-ring {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px;
            border-radius: 9999px;
            background: radial-gradient(circle, rgba(212, 149, 77, 0.25) 0%, rgba(41, 0, 36, 0.5) 75%);
            border: 1px solid rgba(212, 149, 77, 0.45);
            box-shadow:
                0 10px 25px -4px rgba(0, 0, 0, 0.6),
                0 0 28px rgba(212, 149, 77, 0.3);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .landing-logo-ring:hover {
            transform: scale(1.04);
            box-shadow:
                0 14px 30px -4px rgba(0, 0, 0, 0.7),
                0 0 35px rgba(212, 149, 77, 0.45);
        }

        /* Perfectly sized, tactile CTA Button */
        .landing-cta {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.625rem;
            width: 100%;
            max-width: 260px;
            min-height: 3.25rem;
            padding: 0.875rem 1.75rem;
            border-radius: 9999px;
            background: linear-gradient(135deg, #e8bd85 0%, #d4954d 55%, #b87c37 100%);
            color: #240020;
            font-weight: 700;
            font-size: 1.0625rem;
            letter-spacing: 0.02em;
            text-decoration: none;
            box-shadow:
                0 12px 28px -6px rgba(212, 149, 77, 0.55),
                0 4px 12px -2px rgba(41, 0, 36, 0.5),
                inset 0 1px 0 rgba(255, 255, 255, 0.45);
            transition: all 0.25s cubic-bezier(0.2, 0.8, 0.2, 1);
            overflow: hidden;
        }

        .landing-cta::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.3), transparent 60%);
            opacity: 0;
            transition: opacity 0.25s ease;
        }

        .landing-cta:hover {
            transform: translateY(-2px);
            box-shadow:
                0 18px 36px -6px rgba(212, 149, 77, 0.75),
                0 6px 16px -2px rgba(41, 0, 36, 0.6),
                inset 0 1px 0 rgba(255, 255, 255, 0.65);
            background: linear-gradient(135deg, #f6e3c9 0%, #e8bd85 50%, #d4954d 100%);
        }

        .landing-cta:hover::before {
            opacity: 1;
        }

        .landing-cta:active {
            transform: translateY(0);
            box-shadow: 0 6px 18px -4px rgba(212, 149, 77, 0.5);
        }

        .landing-copy {
            text-shadow:
                0 1px 2px rgba(20, 0, 18, 0.6),
                0 8px 24px rgba(20, 0, 18, 0.4);
        }

        /* Ambient Steam / Aroma Micro-Particles */
        @keyframes steamFloat {
            0% {
                transform: translateY(0) scale(0.8);
                opacity: 0;
            }
            30% {
                opacity: 0.35;
            }
            70% {
                opacity: 0.35;
            }
            100% {
                transform: translateY(-90px) scale(1.2);
                opacity: 0;
            }
        }

        .steam-particle {
            position: absolute;
            border-radius: 9999px;
            background: radial-gradient(circle, rgba(212, 149, 77, 0.4) 0%, rgba(227, 222, 164, 0.12) 60%, transparent 100%);
            pointer-events: none;
            animation: steamFloat infinite ease-in-out;
        }

        @keyframes landing-rise {
            from {
                opacity: 0;
                transform: translateY(18px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes landing-zoom {
            from {
                opacity: 0;
                transform: scale(0.96) translateY(14px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        @media (prefers-reduced-motion: no-preference) {
            .landing-reveal-card {
                animation: landing-zoom 850ms cubic-bezier(0.16, 1, 0.3, 1) both;
            }

            .landing-reveal {
                animation: landing-rise 800ms cubic-bezier(0.16, 1, 0.3, 1) both;
            }

            .landing-reveal-delay-1 {
                animation-delay: 80ms;
            }

            .landing-reveal-delay-2 {
                animation-delay: 160ms;
            }

            .landing-reveal-delay-3 {
                animation-delay: 240ms;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .landing-reveal-card,
            .landing-reveal,
            .steam-particle {
                animation: none;
                opacity: 1;
                transform: none;
            }
        }

        /* Compact screens adjustments */
        @media (max-height: 680px) {
            .landing-card {
                padding: 1.5rem 1.25rem 1.25rem;
                border-radius: 22px;
                max-width: 440px;
            }

            .landing-title {
                font-size: 1.75rem !important;
                margin-top: 0.5rem !important;
            }

            .landing-subtitle {
                font-size: 0.8125rem !important;
                margin-top: 0.5rem !important;
            }

            .landing-cta {
                min-height: 2.85rem;
                padding-top: 0.625rem;
                padding-bottom: 0.625rem;
                font-size: 0.9375rem;
            }

            .landing-kicker {
                margin-top: 0.5rem !important;
            }
        }
    </style>
</head>
<body class="landing-body bg-midnight font-sans text-beige antialiased selection:bg-apricot selection:text-midnight">
    <main class="landing-screen" aria-labelledby="landing-title">
        <div class="landing-fallback absolute inset-0 -z-20" aria-hidden="true"></div>

        @if ($hasLandingBackground)
            <picture class="landing-bg absolute inset-0 -z-20 block h-full w-full">
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
                     class="h-full w-full object-cover object-center">
            </picture>
        @endif

        <div class="landing-overlay absolute inset-0 -z-10" aria-hidden="true"></div>

        <!-- Ambient Floating Steam Particles -->
        <div class="pointer-events-none absolute inset-0 -z-10 overflow-hidden" aria-hidden="true">
            <span class="steam-particle" style="left: 22%; top: 62%; width: 6px; height: 6px; animation-duration: 9s; animation-delay: 0s;"></span>
            <span class="steam-particle" style="left: 38%; top: 70%; width: 8px; height: 8px; animation-duration: 12s; animation-delay: 2s;"></span>
            <span class="steam-particle" style="left: 64%; top: 66%; width: 5px; height: 5px; animation-duration: 8s; animation-delay: 1.5s;"></span>
            <span class="steam-particle" style="left: 78%; top: 58%; width: 7px; height: 7px; animation-duration: 11s; animation-delay: 3s;"></span>
        </div>

        <!-- Subtle Top Brand Bar -->
        <header class="relative z-10 flex w-full items-center justify-between px-5 pt-4 sm:px-8 sm:pt-6">
            <div class="inline-flex items-center gap-2 rounded-full border border-custard/20 bg-midnight-400/60 px-3.5 py-1.5 text-[11px] font-semibold tracking-wider text-beige/85 backdrop-blur-md">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                <span>ABS CAFE</span>
            </div>

            <div id="cafe-clock" class="inline-flex items-center gap-1.5 rounded-full border border-custard/20 bg-midnight-400/60 px-3.5 py-1.5 font-mono text-[11px] tracking-wider text-custard/90 backdrop-blur-md">
                <span class="text-apricot">WIB</span>
                <span id="clock-time">--:--:--</span>
            </div>
        </header>

        <!-- Centerpiece Content -->
        <div class="relative z-10 flex flex-1 items-center justify-center px-4 py-4 sm:py-6">
            <div class="landing-card landing-reveal-card landing-copy">
                <!-- Brand Logo Ring -->
                <div class="landing-logo-ring">
                    <x-logo size="lg"
                            :show-text="false"
                            loading="eager"
                            class="drop-shadow-[0_4px_12px_rgba(41,0,36,0.6)]" />
                </div>

                <!-- Portal Staf ABS Cafe Kicker Badge -->
                <div class="landing-kicker landing-reveal landing-reveal-delay-1 mt-5 inline-flex items-center gap-2 rounded-full border border-apricot/40 bg-midnight-400/80 px-4 py-1.5 text-[11px] font-semibold tracking-[0.18em] uppercase text-beige backdrop-blur-md">
                    <span class="relative flex h-2 w-2" aria-hidden="true">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-apricot opacity-75"></span>
                        <span class="relative inline-flex h-2 w-2 rounded-full bg-apricot"></span>
                    </span>
                    <span>Portal Staf ABS Cafe</span>
                </div>

                <!-- Title & Handcrafted Headline -->
                <h1 id="landing-title"
                    class="landing-title landing-reveal landing-reveal-delay-2 mt-4 font-serif text-3xl font-bold leading-[1.08] tracking-tight sm:text-4xl md:text-[2.65rem]">
                    <span class="block text-beige">Good Coffee,</span>
                    <span class="mt-1 block font-serif italic text-apricot">Good Mood.</span>
                </h1>

                <!-- Subtle Artisanal Divider -->
                <div class="landing-reveal landing-reveal-delay-2 my-3.5 flex items-center justify-center gap-3 opacity-80" aria-hidden="true">
                    <span class="h-px w-10 bg-gradient-to-r from-transparent to-apricot/60"></span>
                    <x-staf.icon name="coffee" class="h-4 w-4 text-apricot/85" />
                    <span class="h-px w-10 bg-gradient-to-l from-transparent to-apricot/60"></span>
                </div>

                <!-- Subtitle -->
                <p class="landing-subtitle landing-reveal landing-reveal-delay-2 max-w-sm text-sm leading-relaxed text-custard/90 sm:text-[15px]">
                    Kelola operasional dan pantau bisnis ABS Cafe dalam satu tempat.
                </p>

                <!-- Dedicated Single Action Button -->
                <div class="landing-reveal landing-reveal-delay-3 mt-6 flex w-full justify-center">
                    <a href="{{ $primaryCtaUrl }}"
                       class="landing-cta group">
                        <span>{{ $primaryCtaLabel }}</span>
                        <x-staf.icon name="arrow-right" class="h-4 w-4 transition-transform duration-200 group-hover:translate-x-1 motion-reduce:transform-none" />
                    </a>
                </div>

                <!-- Subtle Official Access Note -->
                <div class="landing-reveal landing-reveal-delay-3 mt-5 flex items-center justify-center gap-2 text-[11px] font-medium tracking-wide text-beige/55">
                    <x-staf.icon name="sparkles" class="h-3 w-3 text-apricot/75" />
                    <span>Sistem Terpadu • Akses Khusus Staf</span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="relative z-10 shrink-0 px-5 pb-[max(1.25rem,env(safe-area-inset-bottom))] pt-2 text-center">
            <p class="text-[11px] font-medium tracking-[0.16em] text-beige/80 sm:text-xs">ABS Cafe • Tulungagung</p>
        </footer>
    </main>

    <!-- Lightweight Live Clock -->
    <script>
        (function() {
            function updateClock() {
                var el = document.getElementById('clock-time');
                if (!el) return;
                try {
                    var now = new Date();
                    var formatter = new Intl.DateTimeFormat('id-ID', {
                        timeZone: 'Asia/Jakarta',
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit',
                        hour12: false
                    });
                    el.textContent = formatter.format(now);
                } catch (e) {
                    var d = new Date();
                    el.textContent = String(d.getHours()).padStart(2, '0') + ':' +
                                     String(d.getMinutes()).padStart(2, '0') + ':' +
                                     String(d.getSeconds()).padStart(2, '0');
                }
            }
            updateClock();
            setInterval(updateClock, 1000);
        })();
    </script>
</body>
</html>
